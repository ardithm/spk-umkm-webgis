<?php

namespace App\Services;

use App\Models\DetailPenilaian;
use App\Models\HasilPerhitungan;
use App\Models\Kriteria;
use App\Models\KonversiNilai;
use App\Models\Pengajuan;
use App\Models\Proses;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ProfileMatchingService — Mesin Kalkulasi SPK Metode Profile Matching
 *
 * Mengimplementasikan seluruh tahapan algoritma Profile Matching sesuai
 * PRD Section 3 (Modul Sistem Pendukung Keputusan) dan Section 6 (Database Schema).
 *
 * Alur Komputasi:
 *  1. Ambil semua pengajuan berstatus 'terverifikasi' untuk gelombang (Proses) ini.
 *  2. Untuk setiap pengajuan, per kriteria:
 *     a. Konversi nilai aktual UMKM → skor 1–5 via tabel konversi_nilai.
 *     b. Hitung GAP = skor_aktual − target_ideal.
 *     c. Konversi GAP → bobot_gap menggunakan tabel standar Profile Matching.
 *     d. Simpan ke tabel detail_penilaian.
 *  3. Pisahkan bobot_gap ke Core Factor (CF) dan Secondary Factor (SF).
 *     NCF = rata-rata(bobot_gap kriteria CF)
 *     NSF = rata-rata(bobot_gap kriteria SF)
 *  4. Nilai Akhir = (NCF × 60%) + (NSF × 40%)
 *  5. Simpan NCF, NSF, nilai_akhir ke tabel hasil_perhitungan.
 *  6. Urutkan nilai_akhir secara DESC → tetapkan ranking dan status_seleksi
 *     berdasarkan kuota dan passing_grade milik sesi Proses.
 *
 * @see PRD Section 3 — Modul Sistem Pendukung Keputusan (Mesin SPK)
 * @see PRD Section 6 — Database Schema: kriteria, konversi_nilai, detail_penilaian, hasil_perhitungan
 */
class ProfileMatchingService
{
    // =========================================================================
    // Tabel Bobot GAP Standar Profile Matching
    // Sumber: Metode Profile Matching / Penilaian Kompetensi Karyawan
    // =========================================================================

    /**
     * Tabel bobot interpolasi berdasarkan nilai selisih GAP.
     * GAP = Skor Aktual − Target Ideal
     *
     * GAP  0 → 5.0 : Tidak ada selisih, persis sesuai profil target
     * GAP +1 → 4.5 : Kelebihan 1 tingkat dari target
     * GAP -1 → 4.0 : Kekurangan 1 tingkat dari target
     * GAP +2 → 3.5 : dst.
     * GAP -2 → 3.0
     * GAP +3 → 2.5
     * GAP -3 → 2.0
     * GAP +4 → 1.5
     * GAP -4 → 1.0
     * Di luar rentang → 1.0 (bobot minimum)
     */
    private const TABEL_BOBOT_GAP = [
         0 => 5.0,
         1 => 4.5,
        -1 => 4.0,
         2 => 3.5,
        -2 => 3.0,
         3 => 2.5,
        -3 => 2.0,
         4 => 1.5,
        -4 => 1.0,
    ];

    /**
     * Bobot persentase Core Factor dalam formula nilai akhir.
     * PRD Section 3: NCF dikalikan 60%.
     */
    private const BOBOT_CORE      = 0.60;

    /**
     * Bobot persentase Secondary Factor dalam formula nilai akhir.
     * PRD Section 3: NSF dikalikan 40%.
     */
    private const BOBOT_SECONDARY = 0.40;

    // =========================================================================
    // Pemetaan Kode Kriteria → Field Pengajuan
    //
    // Konvensi: kode_kriteria pada tabel kriteria dipetakan ke field
    // di tabel pengajuan. Jika nilai adalah ENUM, dikonversi ke ordinal
    // sebelum dikonsultasikan ke tabel konversi_nilai.
    //
    // K1 = omzet_tahunan        (DECIMAL, dalam Rupiah)
    // K2 = aset                 (DECIMAL, dalam Rupiah)
    // K3 = jumlah_tenaga_kerja  (INT, jumlah orang)
    // K4 = jangkauan_pemasaran  (ENUM → ordinal)
    // K5 = status_perizinan     (ENUM → ordinal)
    // =========================================================================

    /**
     * Pemetaan nilai ENUM jangkauan_pemasaran ke nilai numerik ordinal.
     * Diurutkan dari jangkauan terkecil ke terbesar.
     */
    private const ORDINAL_JANGKAUAN = [
        'kelurahan' => 1,
        'kecamatan' => 2,
        'kota'      => 3,
        'provinsi'  => 4,
        'nasional'  => 5,
    ];

    /**
     * Pemetaan nilai ENUM status_perizinan ke nilai numerik ordinal.
     * Diurutkan dari kelengkapan perizinan paling rendah ke paling lengkap.
     */
    private const ORDINAL_PERIZINAN = [
        'tidak_ada' => 1,
        'sku'       => 2,
        'nib'       => 3,
        'nib_sku'   => 4,
        'lengkap'   => 5,
    ];

    // =========================================================================
    // Entry Point Publik
    // =========================================================================

    /**
     * Jalankan seluruh pipeline kalkulasi Profile Matching untuk satu sesi proses.
     *
     * Dipanggil oleh SpkController::hitungMassal(). Menggunakan DB transaction
     * agar jika terjadi kegagalan di tengah proses, tidak ada data parsial yang
     * tersimpan (semua-atau-tidak-ada).
     *
     * @param  Proses $proses Entitas sesi gelombang seleksi yang akan dihitung.
     * @return array  Ringkasan hasil: ['total_dihitung', 'total_diterima', 'total_tidak_diterima']
     *
     * @throws \Throwable Jika terjadi error saat transaksi database.
     */
    public function jalankanKalkulasi(Proses $proses): array
    {
        return DB::transaction(function () use ($proses) {

            // ── Langkah 0: Bersihkan hasil kalkulasi sebelumnya (jika ada re-run) ──
            $this->bersihkanHasilSebelumnya($proses);

            // ── Langkah 1: Ambil semua kriteria aktif (beserta konversi nilai) ──
            $kriteria = Kriteria::with('konversiNilai')->get();

            if ($kriteria->isEmpty()) {
                throw new \RuntimeException(
                    'Tidak ada data kriteria yang terdaftar. ' .
                    'Harap input kriteria SPK terlebih dahulu.'
                );
            }

            // ── Langkah 2: Ambil semua pengajuan berstatus terverifikasi ──
            $pengajuanList = Pengajuan::terverifikasi()
                ->with(['umkm', 'detailPenilaian'])
                ->get();

            if ($pengajuanList->isEmpty()) {
                throw new \RuntimeException(
                    'Tidak ada pengajuan berstatus "terverifikasi" yang dapat dihitung. ' .
                    'Pastikan Admin telah memverifikasi dokumen terlebih dahulu.'
                );
            }

            // ── Langkah 3: Hitung Profile Matching per kandidat ──
            $hasilList = [];

            foreach ($pengajuanList as $pengajuan) {
                $hasilList[] = $this->hitungPerPengajuan($pengajuan, $kriteria, $proses);
            }

            // ── Langkah 4: Tetapkan ranking dan status seleksi ──
            $this->tetapkanRanking($proses, $hasilList);

            // ── Ringkasan untuk flash message controller ──
            $totalDiterima      = collect($hasilList)->where('status_seleksi', HasilPerhitungan::STATUS_DITERIMA)->count();
            $totalCadangan      = collect($hasilList)->where('status_seleksi', HasilPerhitungan::STATUS_CADANGAN)->count();
            $totalTidakDiterima = collect($hasilList)->where('status_seleksi', HasilPerhitungan::STATUS_TIDAK_DITERIMA)->count();

            Log::info("[SPK] Kalkulasi selesai untuk Proses #{$proses->id_proses}.", [
                'periode'           => $proses->periode,
                'total_dihitung'    => count($hasilList),
                'total_diterima'    => $totalDiterima,
                'total_cadangan'    => $totalCadangan,
                'total_tidak_diterima' => $totalTidakDiterima,
            ]);

            return [
                'total_dihitung'       => count($hasilList),
                'total_diterima'       => $totalDiterima,
                'total_cadangan'       => $totalCadangan,
                'total_tidak_diterima' => $totalTidakDiterima,
            ];
        });
    }

    // =========================================================================
    // Kalkulasi Per Kandidat
    // =========================================================================

    /**
     * Jalankan seluruh tahapan Profile Matching untuk satu pengajuan UMKM.
     *
     * Tahapan:
     *  1. Petakan nilai aktual ke skor via tabel konversi_nilai.
     *  2. Hitung GAP = skor − target.
     *  3. Konversi GAP ke bobot_gap.
     *  4. Pisahkan ke Core Factor dan Secondary Factor.
     *  5. Hitung NCF, NSF, dan Nilai Akhir.
     *  6. Simpan baris detail_penilaian (per kriteria).
     *  7. Simpan baris hasil_perhitungan (agregat).
     *
     * @param  Pengajuan  $pengajuan Entitas pengajuan yang dihitung.
     * @param  Collection $kriteria  Koleksi semua Kriteria (sudah eager-load konversiNilai).
     * @param  Proses     $proses    Sesi proses seleksi aktif.
     * @return array      Data hasil untuk dipakai proses ranking selanjutnya.
     */
    private function hitungPerPengajuan(
        Pengajuan  $pengajuan,
        Collection $kriteria,
        Proses     $proses
    ): array {
        $bobotCore      = []; // bobot_gap untuk kriteria CF
        $bobotSecondary = []; // bobot_gap untuk kriteria SF
        $detailBatch    = []; // data untuk bulk insert ke detail_penilaian

        foreach ($kriteria as $k) {
            // — a. Ambil nilai aktual dari field pengajuan yang sesuai kode —
            $nilaiAktual = $this->getNilaiAktual($pengajuan, $k->kode_kriteria);

            // — b. Konversi nilai aktual ke skor 1–5 via tabel konversi_nilai —
            $skor = KonversiNilai::getSkorKonversi($k->id_kriteria, $nilaiAktual);

            // — c. Hitung GAP = skor_aktual − target_ideal —
            $gap = $this->hitungGap($skor, $k->target_ideal);

            // — d. Konversi GAP ke bobot interpolasi (tabel standar PM) —
            $bobotGap = $this->getBobotGap($gap);

            // — e. Kelompokkan bobot ke CF atau SF —
            if ($k->jenis_faktor === Kriteria::FAKTOR_CORE) {
                $bobotCore[] = $bobotGap;
            } else {
                $bobotSecondary[] = $bobotGap;
            }

            // — f. Siapkan data untuk batch insert detail_penilaian —
            $detailBatch[] = [
                'id_pengajuan' => $pengajuan->id_pengajuan,
                'id_kriteria'  => $k->id_kriteria,
                'nilai_aktual' => $nilaiAktual,
                'skor'         => $skor,
                'gap'          => $gap,
                'bobot_gap'    => $bobotGap,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        // — g. Hitung NCF, NSF, dan Nilai Akhir —
        $ncf        = $this->hitungNCF($bobotCore);
        $nsf        = $this->hitungNSF($bobotSecondary);
        $nilaiAkhir = $this->hitungNilaiAkhir($ncf, $nsf);

        // — h. Simpan semua detail_penilaian sekaligus (bulk insert efisien) —
        DetailPenilaian::insert($detailBatch);

        // — i. Simpan baris hasil_perhitungan (tanpa ranking dulu; ranking ditetapkan massal) —
        $hasil = HasilPerhitungan::create([
            'id_proses'      => $proses->id_proses,
            'id_pengajuan'   => $pengajuan->id_pengajuan,
            'nilai_ncf'      => $ncf,
            'nilai_nsf'      => $nsf,
            'nilai_akhir'    => $nilaiAkhir,
            'ranking'        => null, // diisi oleh tetapkanRanking()
            'status_seleksi' => HasilPerhitungan::STATUS_TIDAK_DITERIMA, // sementara
            'tanggal_proses' => now(),
        ]);

        Log::debug("[SPK] Kandidat #{$pengajuan->id_pengajuan} dihitung.", [
            'ncf'         => $ncf,
            'nsf'         => $nsf,
            'nilai_akhir' => $nilaiAkhir,
        ]);

        return [
            'id_hasil'       => $hasil->id_hasil,
            'id_pengajuan'   => $pengajuan->id_pengajuan,
            'nilai_akhir'    => $nilaiAkhir,
            'status_seleksi' => HasilPerhitungan::STATUS_TIDAK_DITERIMA,
        ];
    }

    // =========================================================================
    // Penetapan Ranking Massal
    // =========================================================================

    /**
     * Urutkan semua hasil kalkulasi secara descending (nilai_akhir tertinggi = ranking 1),
     * lalu tetapkan status_seleksi berdasarkan kuota dan passing_grade sesi Proses.
     *
     * Logika Penentuan Status Seleksi:
     *  - nilai_akhir < passing_grade → STATUS_TIDAK_DITERIMA (tidak memenuhi syarat minimum)
     *  - nilai_akhir ≥ passing_grade AND ranking ≤ kuota → STATUS_DITERIMA
     *  - nilai_akhir ≥ passing_grade AND ranking > kuota → STATUS_CADANGAN
     *  - Jika kuota = null (tidak dibatasi), semua yang ≥ passing_grade → DITERIMA
     *
     * @param  Proses $proses    Sesi proses yang memiliki kuota & passing_grade.
     * @param  array  $hasilList Array hasil dari hitungPerPengajuan() (mutable by reference).
     */
    private function tetapkanRanking(Proses $proses, array &$hasilList): void
    {
        // Urutkan DESC berdasarkan nilai_akhir; jika sama, id_pengajuan lebih kecil duluan
        usort($hasilList, function ($a, $b) {
            if ($b['nilai_akhir'] == $a['nilai_akhir']) {
                return $a['id_pengajuan'] <=> $b['id_pengajuan'];
            }
            return $b['nilai_akhir'] <=> $a['nilai_akhir'];
        });

        $passingGrade = (float) $proses->passing_grade;
        $kuota        = $proses->kuota; // null = tidak terbatas

        foreach ($hasilList as $urutan => $item) {
            $ranking = $urutan + 1; // 1-based ranking

            // Tentukan status seleksi
            if ($item['nilai_akhir'] < $passingGrade) {
                // Tidak memenuhi nilai minimum → langsung gugur
                $status = HasilPerhitungan::STATUS_TIDAK_DITERIMA;
            } elseif ($kuota === null || $ranking <= $kuota) {
                // Dalam kuota → diterima
                $status = HasilPerhitungan::STATUS_DITERIMA;
            } else {
                // Memenuhi passing grade tapi di luar kuota → cadangan
                $status = HasilPerhitungan::STATUS_CADANGAN;
            }

            // Update record di database
            HasilPerhitungan::where('id_hasil', $item['id_hasil'])->update([
                'ranking'        => $ranking,
                'status_seleksi' => $status,
            ]);

            // Update array lokal agar data ringkasan di jalankanKalkulasi() akurat
            $hasilList[$urutan]['ranking']        = $ranking;
            $hasilList[$urutan]['status_seleksi'] = $status;
        }
    }

    // =========================================================================
    // Helper: Ambil Nilai Aktual dari Field Pengajuan
    // =========================================================================

    /**
     * Petakan kode_kriteria ke nilai numerik dari field pengajuan yang sesuai.
     *
     * Konvensi kode_kriteria (ditetapkan di seeder/admin kriteria):
     *  K1 → omzet_tahunan       (float, dalam Rupiah)
     *  K2 → aset                (float, dalam Rupiah)
     *  K3 → jumlah_tenaga_kerja (int)
     *  K4 → jangkauan_pemasaran (ENUM → ordinal 1–5)
     *  K5 → status_perizinan    (ENUM → ordinal 1–5)
     *
     * @param  Pengajuan $pengajuan   Entitas pengajuan yang dievaluasi.
     * @param  string    $kodeCriteria Kode kriteria (K1, K2, K3, K4, K5).
     * @return float     Nilai numerik aktual untuk dikonsultasikan ke konversi_nilai.
     */
    private function getNilaiAktual(Pengajuan $pengajuan, string $kodeCriteria): float
    {
        return match (strtoupper($kodeCriteria)) {
            'K1'    => (float) $pengajuan->omzet_tahunan,
            'K2'    => (float) $pengajuan->aset,
            'K3'    => (float) $pengajuan->jumlah_tenaga_kerja,
            'K4'    => (float) $this->konversiJangkauan($pengajuan->jangkauan_pemasaran),
            'K5'    => (float) $this->konversiPerizinan($pengajuan->status_perizinan),
            default => throw new \InvalidArgumentException(
                "Kode kriteria '{$kodeCriteria}' tidak dikenal. " .
                "Periksa kode_kriteria pada tabel kriteria (nilai valid: K1–K5)."
            ),
        };
    }

    /**
     * Konversi nilai ENUM jangkauan_pemasaran ke angka ordinal untuk lookup konversi_nilai.
     *
     * @param  string|null $nilai Nilai ENUM dari field jangkauan_pemasaran.
     * @return int         Ordinal 1–5.
     */
    private function konversiJangkauan(?string $nilai): int
    {
        $key = strtolower((string) $nilai);
        return self::ORDINAL_JANGKAUAN[$key] ?? 1;
    }

    /**
     * Konversi nilai ENUM status_perizinan ke angka ordinal untuk lookup konversi_nilai.
     *
     * @param  string|null $nilai Nilai ENUM dari field status_perizinan.
     * @return int         Ordinal 1–5.
     */
    private function konversiPerizinan(?string $nilai): int
    {
        $key = strtolower((string) $nilai);
        return self::ORDINAL_PERIZINAN[$key] ?? 1;
    }

    // =========================================================================
    // Helper: Kalkulasi Matematis Profile Matching
    // =========================================================================

    /**
     * Hitung nilai selisih GAP antara skor aktual dan target ideal.
     *
     * GAP = skor_aktual − target_ideal
     * Nilai positif: kandidat melebihi target.
     * Nilai negatif: kandidat kurang dari target.
     * Nilai nol: kandidat persis sesuai target.
     *
     * @param  int $skor        Skor hasil konversi nilai aktual (1–5).
     * @param  int $targetIdeal Nilai target standar instansi untuk kriteria ini.
     * @return int Nilai GAP (bisa negatif, nol, atau positif).
     */
    public function hitungGap(int $skor, int $targetIdeal): int
    {
        return $skor - $targetIdeal;
    }

    /**
     * Konversi nilai GAP ke bobot interpolasi berdasarkan tabel standar Profile Matching.
     *
     * Jika GAP berada di luar rentang tabel (|gap| > 4), dikembalikan bobot minimum 1.0.
     *
     * @param  int   $gap Nilai GAP yang akan dikonversi.
     * @return float Bobot GAP (skala 1.0 – 5.0).
     */
    public function getBobotGap(int $gap): float
    {
        return self::TABEL_BOBOT_GAP[$gap] ?? 1.0;
    }

    /**
     * Hitung Nilai Core Factor (NCF) sebagai rata-rata bobot_gap semua kriteria CF.
     *
     * Formula: NCF = Σ(bobot_gap CF) / jumlah_kriteria_CF
     *
     * @param  array $bobotCore Array float bobot_gap dari semua kriteria Core Factor.
     * @return float Nilai NCF, dibulatkan 4 desimal. Mengembalikan 0 jika tidak ada kriteria CF.
     */
    public function hitungNCF(array $bobotCore): float
    {
        if (empty($bobotCore)) {
            return 0.0;
        }
        return round(array_sum($bobotCore) / count($bobotCore), 4);
    }

    /**
     * Hitung Nilai Secondary Factor (NSF) sebagai rata-rata bobot_gap semua kriteria SF.
     *
     * Formula: NSF = Σ(bobot_gap SF) / jumlah_kriteria_SF
     *
     * @param  array $bobotSecondary Array float bobot_gap dari semua kriteria Secondary Factor.
     * @return float Nilai NSF, dibulatkan 4 desimal. Mengembalikan 0 jika tidak ada kriteria SF.
     */
    public function hitungNSF(array $bobotSecondary): float
    {
        if (empty($bobotSecondary)) {
            return 0.0;
        }
        return round(array_sum($bobotSecondary) / count($bobotSecondary), 4);
    }

    /**
     * Hitung Nilai Akhir Profile Matching dari NCF dan NSF.
     *
     * Formula: Nilai Akhir = (NCF × 60%) + (NSF × 40%)
     * PRD Section 3, sesuai konstanta BOBOT_CORE dan BOBOT_SECONDARY.
     *
     * @param  float $ncf Nilai Core Factor.
     * @param  float $nsf Nilai Secondary Factor.
     * @return float Nilai akhir total, dibulatkan 4 desimal.
     */
    public function hitungNilaiAkhir(float $ncf, float $nsf): float
    {
        return round(($ncf * self::BOBOT_CORE) + ($nsf * self::BOBOT_SECONDARY), 4);
    }

    // =========================================================================
    // Helper: Manajemen Data
    // =========================================================================

    /**
     * Hapus semua data hasil kalkulasi sebelumnya untuk proses ini.
     *
     * Dipanggil sebelum kalkulasi dimulai untuk mendukung fitur re-run kalkulasi
     * (Admin menjalankan ulang perhitungan setelah ada pengajuan baru yang disetujui).
     *
     * Data yang dibersihkan:
     *  - detail_penilaian: semua baris yang id_pengajuan-nya terhubung ke proses ini.
     *  - hasil_perhitungan: semua baris dengan id_proses ini.
     *
     * @param  Proses $proses Sesi proses yang datanya akan dibersihkan.
     */
    private function bersihkanHasilSebelumnya(Proses $proses): void
    {
        // Ambil id_pengajuan yang pernah masuk ke proses ini
        $idPengajuanTerdampak = HasilPerhitungan::where('id_proses', $proses->id_proses)
            ->pluck('id_pengajuan');

        if ($idPengajuanTerdampak->isNotEmpty()) {
            // Hapus detail penilaian per kriteria
            DetailPenilaian::whereIn('id_pengajuan', $idPengajuanTerdampak)->delete();

            Log::info("[SPK] Bersihkan {$idPengajuanTerdampak->count()} detail_penilaian lama untuk Proses #{$proses->id_proses}.");
        }

        // Hapus hasil perhitungan (summary)
        HasilPerhitungan::where('id_proses', $proses->id_proses)->delete();
    }
}
