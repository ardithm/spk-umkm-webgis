<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\Pengajuan;
use App\Models\HasilPerhitungan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebgisController extends Controller
{
    // Koordinat Default Markas: Dinas Koperasi, Usaha Mikro dan Tenaga Kerja Kota Banjarmasin
    const KANTOR_DINAS = [
        'nama'      => 'Kantor Dinas Koperasi & UKM Kota Banjarmasin',
        'alamat'    => 'Jl. Pramuka / Jl. Kapten Piere Tendean, Banjarmasin',
        'latitude'  => -3.328320,
        'longitude' => 114.591240,
    ];

    /**
     * Halaman Utama Peta WebGIS Sebaran UMKM & Rute Survei.
     */
    public function index(): View
    {
        // Ambil metrik ringkasan untuk header dashboard WebGIS
        $totalUmkm = Umkm::terplotting()->count();

        $terverifikasi = Umkm::terplotting()->whereHas('pengajuan', function ($q) {
            $q->where('status', Pengajuan::STATUS_TERVERIFIKASI);
        })->count();

        $menunggu = Umkm::terplotting()->whereHas('pengajuan', function ($q) {
            $q->where('status', Pengajuan::STATUS_MENUNGGU);
        })->count();

        $revisi = Umkm::terplotting()->whereHas('pengajuan', function ($q) {
            $q->where('status', Pengajuan::STATUS_REVISI);
        })->count();

        $lolosBantuan = HasilPerhitungan::where('status_seleksi', HasilPerhitungan::STATUS_DITERIMA)
            ->whereHas('pengajuan.umkm', function ($q) {
                $q->terplotting();
            })->count();

        $kantorDinas = self::KANTOR_DINAS;

        return view('admin.webgis.index', compact(
            'totalUmkm',
            'terverifikasi',
            'menunggu',
            'revisi',
            'lolosBantuan',
            'kantorDinas'
        ));
    }

    /**
     * API JSON Endpoint Penyedia Data Seluruh UMKM dengan Filter Spasial & Status.
     */
    public function data(Request $request): JsonResponse
    {
        // Query seluruh UMKM yang sudah memiliki koordinat spasial
        $query = Umkm::with([
            'pengajuan' => function ($q) {
                $q->latest('id_pengajuan');
            },
            'pengajuan.hasilPerhitungan'
        ])->terplotting();

        // 1. Filter Status Verifikasi Dokumen / Pengajuan
        if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'semua') {
            if ($request->status_verifikasi === 'belum_mengajukan') {
                $query->doesntHave('pengajuan');
            } else {
                $query->whereHas('pengajuan', function ($q) use ($request) {
                    $q->where('status', $request->status_verifikasi);
                });
            }
        }

        // 2. Filter Status Penerima Bantuan (Hasil SPK Profile Matching)
        if ($request->filled('status_bantuan') && $request->status_bantuan !== 'semua') {
            if ($request->status_bantuan === 'belum_dinilai') {
                $query->where(function ($q) {
                    $q->doesntHave('pengajuan.hasilPerhitungan')
                      ->orWhereHas('pengajuan', function ($sub) {
                          $sub->where('status', '!=', Pengajuan::STATUS_TERVERIFIKASI);
                      });
                });
            } else {
                $query->whereHas('pengajuan.hasilPerhitungan', function ($q) use ($request) {
                    $q->where('status_seleksi', $request->status_bantuan);
                });
            }
        }

        // 3. Filter Pencarian Keyword (Nama Usaha, Pemilik, NIK, Alamat)
        if ($request->filled('q')) {
            $keyword = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_umkm', 'LIKE', $keyword)
                  ->orWhere('nama_pemilik', 'LIKE', $keyword)
                  ->orWhere('nik', 'LIKE', $keyword)
                  ->orWhere('alamat', 'LIKE', $keyword);
            });
        }

        $umkmList = $query->get();

        // Format data respons secara kaya & terstruktur
        $data = $umkmList->map(function ($item) {
            /** @var Umkm $item */
            $pengajuan = $item->pengajuan->first();
            $hasil = $pengajuan?->hasilPerhitungan;

            // Tentukan status verifikasi & label
            $statusVerifikasi = $pengajuan ? $pengajuan->status : 'belum_mengajukan';
            $statusVerifikasiLabel = match ($statusVerifikasi) {
                Pengajuan::STATUS_TERVERIFIKASI => 'Terverifikasi',
                Pengajuan::STATUS_MENUNGGU      => 'Menunggu Verifikasi',
                Pengajuan::STATUS_REVISI        => 'Perlu Revisi',
                Pengajuan::STATUS_DRAFT         => 'Draft',
                Pengajuan::STATUS_DIPROSES      => 'Sedang Diproses SPK',
                Pengajuan::STATUS_SELESAI       => 'Selesai',
                default                         => 'Belum Mengajukan',
            };

            // Tentukan status bantuan & label
            $statusBantuan = $hasil ? $hasil->status_seleksi : ($pengajuan ? 'belum_dinilai' : 'tidak_ada');
            $statusBantuanLabel = match ($statusBantuan) {
                HasilPerhitungan::STATUS_DITERIMA       => 'Lolos Bantuan (Layak)',
                HasilPerhitungan::STATUS_TIDAK_DITERIMA => 'Tidak Lolos (Tidak Layak)',
                HasilPerhitungan::STATUS_CADANGAN       => 'Cadangan',
                'belum_dinilai'                         => 'Belum Dinilai SPK',
                default                                 => 'Belum Mengajukan',
            };

            // Kategori warna marker
            // 'lolos' = emerald/hijau, 'terverifikasi' = biru, 'menunggu' = amber/kuning, 'revisi' = merah, 'draft' = abu
            $markerType = 'draft';
            if ($statusBantuan === HasilPerhitungan::STATUS_DITERIMA) {
                $markerType = 'lolos';
            } elseif ($statusVerifikasi === Pengajuan::STATUS_TERVERIFIKASI) {
                $markerType = 'terverifikasi';
            } elseif ($statusVerifikasi === Pengajuan::STATUS_MENUNGGU) {
                $markerType = 'menunggu';
            } elseif ($statusVerifikasi === Pengajuan::STATUS_REVISI) {
                $markerType = 'revisi';
            }

            return [
                'id_umkm'                 => $item->id_umkm,
                'nama_umkm'               => $item->nama_umkm,
                'nama_pemilik'            => $item->nama_pemilik,
                'nik'                     => $item->nik,
                'no_telepon'              => $item->no_telepon,
                'alamat'                  => $item->alamat,
                'latitude'                => (float) $item->latitude,
                'longitude'               => (float) $item->longitude,
                'id_pengajuan'            => $pengajuan?->id_pengajuan,
                'status_verifikasi'       => $statusVerifikasi,
                'status_verifikasi_label' => $statusVerifikasiLabel,
                'status_bantuan'          => $statusBantuan,
                'status_bantuan_label'    => $statusBantuanLabel,
                'marker_type'             => $markerType,
                'ranking'                 => $hasil?->ranking,
                'nilai_akhir'             => $hasil ? number_format((float) $hasil->nilai_akhir, 4) : null,
                'nilai_ncf'               => $hasil ? number_format((float) $hasil->nilai_ncf, 4) : null,
                'nilai_nsf'               => $hasil ? number_format((float) $hasil->nilai_nsf, 4) : null,
                'omzet_tahunan'           => $pengajuan ? (float) $pengajuan->omzet_tahunan : null,
                'omzet_formatted'         => $pengajuan ? 'Rp ' . number_format($pengajuan->omzet_tahunan, 0, ',', '.') : '-',
                'aset'                    => $pengajuan ? (float) $pengajuan->aset : null,
                'aset_formatted'          => $pengajuan ? 'Rp ' . number_format($pengajuan->aset, 0, ',', '.') : '-',
                'jumlah_tenaga_kerja'     => $pengajuan?->jumlah_tenaga_kerja ?? '-',
                'jangkauan_pemasaran'     => $pengajuan ? ucfirst($pengajuan->jangkauan_pemasaran) : '-',
                'status_perizinan'        => $pengajuan ? strtoupper(str_replace('_', ' ', $pengajuan->status_perizinan)) : '-',
                'tanggal_pengajuan'       => $pengajuan?->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d/m/Y') : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'total'   => $data->count(),
            'umkm'    => $data,
        ]);
    }

    /**
     * Endpoint Kalkulasi Rute OSRM Server-Side (sebagai proxy / fallback handal).
     */
    public function rute(Request $request): JsonResponse
    {
        $request->validate([
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
            'end_lat'   => 'required|numeric',
            'end_lng'   => 'required|numeric',
        ]);

        $startLng = $request->start_lng;
        $startLat = $request->start_lat;
        $endLng   = $request->end_lng;
        $endLat   = $request->end_lat;

        $url = "https://router.project-osrm.org/route/v1/driving/{$startLng},{$startLat};{$endLng},{$endLat}?overview=full&geometries=geojson&steps=true";

        try {
            $response = Http::timeout(8)->get($url);

            if ($response->successful() && isset($response->json()['routes'][0])) {
                $route = $response->json()['routes'][0];
                $distanceKm = round($route['distance'] / 1000, 2);
                $durationMin = round($route['duration'] / 60);

                return response()->json([
                    'success'      => true,
                    'distance_km'  => $distanceKm,
                    'duration_min' => $durationMin,
                    'geometry'     => $route['geometry'],
                    'steps'        => $route['legs'][0]['steps'] ?? [],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("OSRM backend proxy error: " . $e->getMessage());
        }

        // Jika OSRM server sedang sibuk, fallback hitung jarak garis lurus (Haversine Formula)
        $jarakLurus = $this->haversineGreatCircleDistance($startLat, $startLng, $endLat, $endLng);
        $estimasiMenit = round(($jarakLurus / 30) * 60); // Asumsi kecepatan rata-rata 30 km/jam di kota Banjarmasin

        return response()->json([
            'success'      => true,
            'is_fallback'  => true,
            'distance_km'  => round($jarakLurus, 2),
            'duration_min' => max(2, $estimasiMenit),
            'message'      => 'Kalkulasi jarak estimasi darat (fallback mode)',
        ]);
    }

    /**
     * Hitung Jarak Garis Lurus (Haversine Formula) dalam Kilometer.
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
