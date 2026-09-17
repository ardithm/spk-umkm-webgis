<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Umkm;
use App\Models\Kriteria;
use App\Models\KonversiNilai;
use App\Models\Proses;
use App\Models\Pengajuan;
use App\Models\Dokumen;
use App\Models\DetailPenilaian;
use App\Models\HasilPerhitungan;
use App\Services\ProfileMatchingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── 1. Akun Admin Default ──────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Administrator SPK UMKM Dinas',
                'password'     => Hash::make('admin123'),
                'role'         => 'admin',
            ]
        );

        // ── 2. Kriteria SPK Profile Matching (5 Kriteria Standard PRD) ─────────
        $kriteriaData = [
            [
                'kode_kriteria' => 'K1',
                'nama_kriteria' => 'Omzet Usaha Tahunan',
                'jenis_faktor'  => 'core',
                'target_ideal'  => 3,
            ],
            [
                'kode_kriteria' => 'K2',
                'nama_kriteria' => 'Nilai Aset Usaha',
                'jenis_faktor'  => 'core',
                'target_ideal'  => 4,
            ],
            [
                'kode_kriteria' => 'K3',
                'nama_kriteria' => 'Jumlah Tenaga Kerja',
                'jenis_faktor'  => 'secondary',
                'target_ideal'  => 3,
            ],
            [
                'kode_kriteria' => 'K4',
                'nama_kriteria' => 'Jangkauan Pemasaran',
                'jenis_faktor'  => 'core',
                'target_ideal'  => 3,
            ],
            [
                'kode_kriteria' => 'K5',
                'nama_kriteria' => 'Status Perizinan Legalitas',
                'jenis_faktor'  => 'secondary',
                'target_ideal'  => 4,
            ],
        ];

        $kriteriaModels = [];
        foreach ($kriteriaData as $kd) {
            $kriteriaModels[$kd['kode_kriteria']] = Kriteria::updateOrCreate(
                ['kode_kriteria' => $kd['kode_kriteria']],
                $kd
            );
        }

        // ── 3. Konversi Skor Rentang Nilai (K1 s/d K5) ─────────────────────────
        // Clear existing to avoid duplicates on re-seed
        KonversiNilai::truncate();

        $konversiRules = [
            // K1: Omzet Tahunan (Rupiah)
            ['kode' => 'K1', 'min' => 0,          'max' => 10000000,   'skor' => 1],
            ['kode' => 'K1', 'min' => 10000001,   'max' => 25000000,   'skor' => 2],
            ['kode' => 'K1', 'min' => 25000001,   'max' => 50000000,   'skor' => 3],
            ['kode' => 'K1', 'min' => 50000001,   'max' => 100000000,  'skor' => 4],
            ['kode' => 'K1', 'min' => 100000001,  'max' => 9999999999, 'skor' => 5],

            // K2: Nilai Aset (Rupiah)
            ['kode' => 'K2', 'min' => 0,          'max' => 15000000,   'skor' => 1],
            ['kode' => 'K2', 'min' => 15000001,   'max' => 35000000,   'skor' => 2],
            ['kode' => 'K2', 'min' => 35000001,   'max' => 75000000,   'skor' => 3],
            ['kode' => 'K2', 'min' => 75000001,   'max' => 150000000,  'skor' => 4],
            ['kode' => 'K2', 'min' => 150000001,  'max' => 9999999999, 'skor' => 5],

            // K3: Tenaga Kerja (Orang)
            ['kode' => 'K3', 'min' => 0, 'max' => 1,   'skor' => 1],
            ['kode' => 'K3', 'min' => 2, 'max' => 3,   'skor' => 2],
            ['kode' => 'K3', 'min' => 4, 'max' => 5,   'skor' => 3],
            ['kode' => 'K3', 'min' => 6, 'max' => 10,  'skor' => 4],
            ['kode' => 'K3', 'min' => 11,'max' => 500, 'skor' => 5],

            // K4: Jangkauan Pemasaran (Ordinal 1-5)
            ['kode' => 'K4', 'min' => 1, 'max' => 1, 'skor' => 1], // Kelurahan
            ['kode' => 'K4', 'min' => 2, 'max' => 2, 'skor' => 2], // Kecamatan
            ['kode' => 'K4', 'min' => 3, 'max' => 3, 'skor' => 3], // Kota
            ['kode' => 'K4', 'min' => 4, 'max' => 4, 'skor' => 4], // Provinsi
            ['kode' => 'K4', 'min' => 5, 'max' => 5, 'skor' => 5], // Nasional

            // K5: Status Perizinan (Ordinal 1-5)
            ['kode' => 'K5', 'min' => 1, 'max' => 1, 'skor' => 1], // Tidak ada
            ['kode' => 'K5', 'min' => 2, 'max' => 2, 'skor' => 2], // SKU RT
            ['kode' => 'K5', 'min' => 3, 'max' => 3, 'skor' => 3], // SKU Kelurahan
            ['kode' => 'K5', 'min' => 4, 'max' => 4, 'skor' => 4], // NIB
            ['kode' => 'K5', 'min' => 5, 'max' => 5, 'skor' => 5], // NIB Lengkap
        ];

        foreach ($konversiRules as $rule) {
            $krModel = $kriteriaModels[$rule['kode']] ?? null;
            if ($krModel) {
                KonversiNilai::create([
                    'id_kriteria' => $krModel->id_kriteria,
                    'nilai_min'   => $rule['min'],
                    'nilai_max'   => $rule['max'],
                    'skor'        => $rule['skor'],
                ]);
            }
        }

        // ── 4. Sesi Proses Gelombang Seleksi SPK ───────────────────────────────
        $proses = Proses::create([
            'periode'        => 'Gelombang I - Program Bantuan Modal 2026',
            'tanggal_proses' => now(),
            'passing_grade'  => 3.50,
            'kuota'          => 3,
            'keterangan'     => 'Sesi seleksi tahap pertama penerima dana bantuan modal usaha UMKM Kota Banjarmasin.',
        ]);

        // ── 5. 5 Data Dummy UMKM Pemohon & Pengajuan Spasial Banjarmasin ──────
        $dummyUmkm = [
            [
                'username'     => 'umkm_aminah',
                'nama_lengkap' => 'Hj. Aminah',
                'nama_umkm'    => 'Kue Khas Banjar Hj. Aminah',
                'nik'          => '6371025508820001',
                'no_telepon'   => '081251234567',
                'alamat'       => 'Jl. Ahmad Yani Km. 4.5 No. 88, Pemurus Luar, Banjarmasin Timur',
                'latitude'     => -3.332150,
                'longitude'    => 114.605200,
                'omzet'        => 48000000.00,
                'aset'         => 30000000.00,
                'sdm'          => 4,
                'pemasaran'    => 'kota',
                'perizinan'    => 'nib',
                'status'       => Pengajuan::STATUS_TERVERIFIKASI,
                'docs' => [
                    'KTP' => ['status' => 'disetujui'],
                    'KK'  => ['status' => 'disetujui'],
                    'NIB' => ['status' => 'disetujui'],
                    'SKU' => ['status' => 'disetujui'],
                    'FOTO'=> ['status' => 'disetujui'],
                ]
            ],
            [
                'username'     => 'umkm_mira',
                'nama_lengkap' => 'Mira Kartika',
                'nama_umkm'    => 'Sasirangan Heritage Banjarmasin',
                'nik'          => '6371014412900003',
                'no_telepon'   => '082155667788',
                'alamat'       => 'Jl. Sejahtera No. 15, Kampung Melayu, Banjarmasin Tengah',
                'latitude'     => -3.321400,
                'longitude'    => 114.594500,
                'omzet'        => 120000000.00,
                'aset'         => 85000000.00,
                'sdm'          => 8,
                'pemasaran'    => 'provinsi',
                'perizinan'    => 'nib_lengkap',
                'status'       => Pengajuan::STATUS_TERVERIFIKASI,
                'docs' => [
                    'KTP' => ['status' => 'disetujui'],
                    'KK'  => ['status' => 'disetujui'],
                    'NIB' => ['status' => 'disetujui'],
                    'SKU' => ['status' => 'disetujui'],
                    'FOTO'=> ['status' => 'disetujui'],
                ]
            ],
            [
                'username'     => 'umkm_amat',
                'nama_lengkap' => 'Muhammad Amat',
                'nama_umkm'    => 'Waroeng Soto Banjar Bang Amat',
                'nik'          => '6371031203850002',
                'no_telepon'   => '081348991122',
                'alamat'       => 'Jl. Banua Anyar No. 45, Banua Anyar, Banjarmasin Timur',
                'latitude'     => -3.310800,
                'longitude'    => 114.608300,
                'omzet'        => 65000000.00,
                'aset'         => 40000000.00,
                'sdm'          => 5,
                'pemasaran'    => 'kota',
                'perizinan'    => 'nib',
                'status'       => Pengajuan::STATUS_TERVERIFIKASI,
                'docs' => [
                    'KTP' => ['status' => 'disetujui'],
                    'KK'  => ['status' => 'disetujui'],
                    'NIB' => ['status' => 'disetujui'],
                    'SKU' => ['status' => 'disetujui'],
                    'FOTO'=> ['status' => 'disetujui'],
                ]
            ],
            [
                'username'     => 'umkm_zainal',
                'nama_lengkap' => 'Zainal Abidin',
                'nama_umkm'    => 'Kerajinan Rotan & Purun Sungai Andai',
                'nik'          => '6371041907870005',
                'no_telepon'   => '085249003344',
                'alamat'       => 'Jl. Sungai Andai Komplek Mulawarman Blok C No. 12, Banjarmasin Utara',
                'latitude'     => -3.298500,
                'longitude'    => 114.601200,
                'omzet'        => 18000000.00,
                'aset'         => 12000000.00,
                'sdm'          => 2,
                'pemasaran'    => 'kecamatan',
                'perizinan'    => 'sku_kelurahan',
                'status'       => Pengajuan::STATUS_MENUNGGU, // Menunggu Verifikasi untuk Uji Modul Dokumen
                'docs' => [
                    'KTP' => ['status' => 'disetujui'],
                    'KK'  => ['status' => 'disetujui'],
                    'SKU' => ['status' => 'menunggu'],
                    'NIB' => ['status' => 'menunggu'],
                ]
            ],
            [
                'username'     => 'umkm_noor',
                'nama_lengkap' => 'Hj. Siti Noor',
                'nama_umkm'    => 'Ikan Asin Khas Kuin Hj. Noor',
                'nik'          => '6371056111830004',
                'no_telepon'   => '081952778899',
                'alamat'       => 'Jl. Kuin Utara No. 27, Kuin Utara, Banjarmasin Barat',
                'latitude'     => -3.295200,
                'longitude'    => 114.576800,
                'omzet'        => 28000000.00,
                'aset'         => 20000000.00,
                'sdm'          => 3,
                'pemasaran'    => 'kota',
                'perizinan'    => 'sku_kelurahan',
                'status'       => Pengajuan::STATUS_REVISI, // Perlu Revisi untuk Uji Modul Dokumen
                'docs' => [
                    'KTP' => ['status' => 'disetujui'],
                    'KK'  => ['status' => 'disetujui'],
                    'SKU' => ['status' => 'ditolak', 'catatan' => 'Masa berlaku SKU habis per Desember 2025, mohon unggah surat perpanjangan terbaru.'],
                    'FOTO'=> ['status' => 'disetujui'],
                ]
            ],
        ];

        foreach ($dummyUmkm as $item) {
            // Create User Account
            $u = User::create([
                'nama_lengkap' => $item['nama_lengkap'],
                'username'     => $item['username'],
                'password'     => Hash::make('password123'),
                'role'         => 'umkm',
            ]);

            // Create UMKM Profile
            $umkm = Umkm::create([
                'id_user'      => $u->id_user,
                'nama_umkm'    => $item['nama_umkm'],
                'nama_pemilik' => $item['nama_lengkap'],
                'nik'          => $item['nik'],
                'no_telepon'   => $item['no_telepon'],
                'alamat'       => $item['alamat'],
                'latitude'     => $item['latitude'],
                'longitude'    => $item['longitude'],
            ]);

            // Create Pengajuan Bantuan
            $pengajuan = \App\Models\Pengajuan::create([
                'id_umkm'             => $umkm->id_umkm,
                'omzet_tahunan'       => $item['omzet'],
                'aset'                => $item['aset'],
                'jumlah_tenaga_kerja' => $item['sdm'],
                'jangkauan_pemasaran' => $item['pemasaran'],
                'status_perizinan'    => $item['perizinan'],
                'tanggal_pengajuan'   => now()->subDays(rand(1, 10)),
                'status'              => $item['status'],
            ]);

            // Create Dummy Uploaded Documents
            foreach ($item['docs'] as $jenis => $docData) {
                \App\Models\Dokumen::create([
                    'id_pengajuan'      => $pengajuan->id_pengajuan,
                    'jenis_dokumen'     => $jenis,
                    'file_dokumen'      => "dokumen-umkm/sample_{$jenis}.pdf",
                    'status_verifikasi' => $docData['status'],
                    'catatan_admin'     => $docData['catatan'] ?? null,
                ]);
            }
        }

        // ── 6. Eksekusi Kalkulasi SPK Profile Matching Otomatis ────────────────
        try {
            app(\App\Services\ProfileMatchingService::class)->jalankanKalkulasi($proses);
        } catch (\Throwable $e) {
            \Log::warning("[Seeder] Kalkulasi SPK otomatis dilewati: " . $e->getMessage());
        }
    }
}
