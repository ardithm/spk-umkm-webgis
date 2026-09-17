<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penerima Bantuan UMKM - {{ $proses->periode }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 14mm 12mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        /* ── Kop Kedinasan Resmi ── */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .kop-logo {
            width: 75px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-logo-box {
            width: 58px;
            height: 58px;
            margin: 0 auto;
            border-radius: 8px;
            background-color: #5E3B8A;
            color: #ffffff;
            text-align: center;
            font-size: 26pt;
            font-weight: bold;
            line-height: 58px;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
            padding-right: 75px; /* Kompensasi agar teks tepat di tengah */
        }
        .kop-text h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #111827;
        }
        .kop-text h2 {
            margin: 1px 0;
            font-size: 13.5pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #5E3B8A;
        }
        .kop-text p {
            margin: 0;
            font-size: 7.5pt;
            color: #4B5563;
            line-height: 1.35;
        }

        /* Garis Pemisah Kop Ganda Resmi */
        .kop-divider-thick {
            border-top: 2.5px solid #111827;
            margin-top: 4px;
            margin-bottom: 1.5px;
        }
        .kop-divider-thin {
            border-top: 0.75px solid #111827;
            margin-bottom: 10px;
        }

        /* ── Judul & Parameter Dokumen ── */
        .doc-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 10.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #111827;
            margin: 0 0 2px 0;
        }
        .doc-subtitle {
            font-size: 9pt;
            font-weight: 600;
            color: #4B5563;
            margin: 0 0 6px 0;
        }
        .doc-meta {
            margin: 0 auto;
            background-color: #F3F4F6;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 4px 10px;
            display: inline-block;
            font-size: 8pt;
        }
        .doc-meta span {
            margin: 0 8px;
            font-weight: 600;
            color: #374151;
        }
        .doc-meta strong {
            color: #5E3B8A;
        }

        /* ── Tabel Data Rekapitulasi ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 12px;
        }
        table.data-table thead {
            display: table-header-group;
        }
        table.data-table tr {
            page-break-inside: avoid;
        }
        table.data-table th {
            background-color: #4A286D;
            color: #ffffff;
            font-weight: 700;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px 4px;
            border: 1px solid #371E52;
            text-align: center;
            vertical-align: middle;
        }
        table.data-table td {
            padding: 4.5px 5px;
            border: 1px solid #D1D5DB;
            vertical-align: middle;
            font-size: 7.5pt;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }

        /* Badge Status */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-diterima {
            background-color: #D1FAE5;
            color: #065F46;
            border: 0.5px solid #A7F3D0;
        }
        .badge-cadangan {
            background-color: #FEF3C7;
            color: #92400E;
            border: 0.5px solid #FDE68A;
        }
        .badge-ditolak {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 0.5px solid #FECACA;
        }

        .rank-circle {
            display: inline-block;
            width: 16px;
            height: 16px;
            line-height: 16px;
            border-radius: 50%;
            background-color: #EDE9FE;
            color: #5E3B8A;
            font-weight: bold;
            font-size: 7pt;
            text-align: center;
        }

        /* ── Titimangsa & Blok Tanda Tangan ── */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 10px;
        }
        .signature-table td {
            vertical-align: top;
            padding: 0;
        }
        .catatan-sk {
            font-size: 7.5pt;
            color: #4B5563;
            line-height: 1.4;
            padding-right: 30px;
        }
        .catatan-sk ol {
            margin: 4px 0 0 16px;
            padding: 0;
        }
        .catatan-sk li {
            margin-bottom: 2px;
        }

        .ttd-box {
            width: 280px;
            text-align: center;
            font-size: 8pt;
            line-height: 1.35;
        }
        .ttd-space {
            height: 52px;
        }

        /* ── Footer Cetak ── */
        .footer-note {
            position: fixed;
            bottom: -5mm;
            left: 0;
            right: 0;
            font-size: 7pt;
            color: #9CA3AF;
            border-top: 0.5px solid #E5E7EB;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi Kedinasan -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <div class="kop-logo-box">
                    <span>B</span>
                </div>
            </td>
            <td class="kop-text">
                <h3>Pemerintah Kota Banjarmasin</h3>
                <h2>Dinas Koperasi, Usaha Mikro dan Tenaga Kerja</h2>
                <p>
                    Jl. Pramuka No. 17, Pemurus Luar, Kec. Banjarmasin Timur, Kota Banjarmasin, Kalimantan Selatan 70249<br>
                    Laman Resmi: <em>www.koperasi.banjarmasinkota.go.id</em> | Pos-el: <em>dinaskoperasi@banjarmasinkota.go.id</em>
                </p>
            </td>
        </tr>
    </table>

    <div class="kop-divider-thick"></div>
    <div class="kop-divider-thin"></div>

    <!-- Judul Dokumen & Parameter Seleksi -->
    <div class="doc-header">
        <h1 class="doc-title">REKAPITULASI HASIL SELEKSI DAN PENETAPAN CALON PENERIMA BANTUAN MODAL USAHA UMKM</h1>
        <p class="doc-subtitle">Landasan Penetapan Surat Keputusan (SK) Kepala Dinas Koperasi, Usaha Mikro dan Tenaga Kerja</p>
        
        <div class="doc-meta">
            <span>Periode: <strong>{{ $proses->periode }}</strong></span>
            <span>Kategori Data: <strong>{{ $statusLabel }}</strong></span>
            <span>Passing Grade: <strong>{{ number_format($proses->passing_grade, 2) }}</strong></span>
            <span>Kuota Ditetapkan: <strong>{{ $proses->kuota ? $proses->kuota . ' UMKM' : 'Tidak Dibatasi' }}</strong></span>
            <span>Total Data: <strong>{{ $hasilList->count() }} Pendaftar</strong></span>
        </div>
    </div>

    <!-- Tabel Data Terstruktur -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 32px;">Rank</th>
                <th style="width: 120px;">Nama Pemilik</th>
                <th style="width: 100px;">NIK</th>
                <th style="width: 130px;">Nama Usaha (UMKM)</th>
                <th style="width: 75px;">Izin Usaha</th>
                <th>Alamat Lokasi Usaha</th>
                <th style="width: 50px;">NCF (60%)</th>
                <th style="width: 50px;">NSF (40%)</th>
                <th style="width: 55px;">Nilai Akhir</th>
                <th style="width: 90px;">Status Kelayakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hasilList as $index => $hasil)
                @php
                    $pengajuan = $hasil->pengajuan;
                    $umkm = $pengajuan?->umkm;
                    
                    $izinBadge = match ($pengajuan?->status_perizinan) {
                        'nib_lengkap' => 'NIB Lengkap',
                        'nib' => 'NIB Standar',
                        'sku_kelurahan' => 'SKU Kelurahan',
                        'sku_rt' => 'SKU RT/RW',
                        'belum_ada' => 'Belum Ada',
                        default => strtoupper(str_replace('_', ' ', $pengajuan?->status_perizinan ?? '-')),
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <span class="rank-circle">{{ $hasil->ranking ?? '-' }}</span>
                    </td>
                    <td class="text-left font-bold">{{ $umkm?->nama_pemilik ?? '-' }}</td>
                    <td class="text-center font-mono">{{ $umkm?->nik ?? '-' }}</td>
                    <td class="text-left">{{ $umkm?->nama_umkm ?? '-' }}</td>
                    <td class="text-center">{{ $izinBadge }}</td>
                    <td class="text-left">{{ $umkm?->alamat ?? '-' }}</td>
                    <td class="text-center">{{ number_format($hasil->nilai_ncf, 4) }}</td>
                    <td class="text-center">{{ number_format($hasil->nilai_nsf, 4) }}</td>
                    <td class="text-center font-bold" style="color: #4A286D;">
                        {{ number_format($hasil->nilai_akhir, 4) }}
                    </td>
                    <td class="text-center">
                        @if($hasil->status_seleksi === 'diterima')
                            <span class="badge badge-diterima">LOLOS / DITERIMA</span>
                        @elseif($hasil->status_seleksi === 'cadangan')
                            <span class="badge badge-cadangan">CADANGAN</span>
                        @else
                            <span class="badge badge-ditolak">TIDAK LOLOS</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #6B7280;">
                        Tidak ada data rekapitulasi yang memenuhi kriteria filter terpilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Titimangsa & Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td style="width: 58%;">
                <div class="catatan-sk">
                    <strong>Catatan Penting Landasan Penetapan SK:</strong>
                    <ol>
                        <li>Hasil evaluasi dihitung secara objektif menggunakan Algoritma <em>Profile Matching</em> berdasarkan 5 kriteria standar kedinasan (Omzet, Aset, SDM, Pemasaran, Perizinan).</li>
                        <li>Kandidat berstatus <strong>LOLOS / DITERIMA</strong> telah melampaui nilai ambang batas (Passing Grade: {{ number_format($proses->passing_grade, 2) }}) serta masuk dalam alokasi kuota penerima bantuan modal.</li>
                        <li>Kandidat berstatus <strong>CADANGAN</strong> berhak dipromosikan apabila kandidat di atasnya mengundurkan diri atau gugur pada tahap verifikasi faktual lapangan.</li>
                    </ol>
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="ttd-box" style="margin-left: auto;">
                    Banjarmasin, {{ $tanggalPengesahan }}<br>
                    Mengetahui dan Mengesahkan,<br>
                    <strong>Kepala Dinas Koperasi, Usaha Mikro dan Tenaga Kerja<br>Kota Banjarmasin</strong>
                    
                    <div class="ttd-space"></div>
                    
                    <strong><u>H. ISWANTO, S.Sos., M.AP.</u></strong><br>
                    <span>Pembina Utama Muda (IV/c)</span><br>
                    <span>NIP. 19740512 199803 1 004</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Catatan Cetak Kaki (Footer) -->
    <div class="footer-note">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="text-align: left; color: #6B7280;">
                    Dokumen Resmi Sistem Pendukung Keputusan Bantuan UMKM Kota Banjarmasin | Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WITA
                </td>
                <td style="text-align: right; color: #6B7280;">
                    A4 Landscape — Halaman SiBantuan UMKM
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
