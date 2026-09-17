<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Sistem Pendukung Keputusan Seleksi UMKM Penerima Bantuan Modal Usaha Berbasis WebGIS - Dinas Koperasi, Usaha Mikro dan Tenaga Kerja Kota Banjarmasin">
  <title>SiBantuan UMKM WebGIS — Dinas Koperasi & UMKM Kota Banjarmasin</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">

  <!-- Lucide Icons Script (Loaded Early for Headless Support) -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Custom Design System Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  <!-- ========================================================================
       Header & Navigation Bar (design.md 4.1)
       ======================================================================== -->
  <header class="header-wrapper">
    <nav class="navbar" aria-label="Navigasi Utama">
      <!-- Nav Left: Brand Logo & Navigation Links -->
      <div class="nav-left">
        <a href="#beranda" class="brand-logo-link">
          <div class="brand-logo-badge">
            <!-- Geometric Golden/Purple SVG Icon -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
              <polyline points="2 17 12 22 22 17"></polyline>
              <polyline points="2 12 12 17 22 12"></polyline>
            </svg>
          </div>
          <div class="brand-text-wrapper">
            <span class="brand-title">SiBantuan UMKM</span>
            <span class="brand-subtitle">WebGIS Banjarmasin</span>
          </div>
        </a>

        <div class="nav-links">
          <a href="#beranda" class="nav-link active">Beranda</a>
          <a href="#jadwal-gelombang" class="nav-link">Jadwal Seleksi</a>
          <a href="#faq" class="nav-link">FAQ & Bantuan</a>
        </div>
      </div>

      <!-- Nav Right: Auth Actions -->
      <div class="nav-right">
        @guest
          <a href="{{ route('login') }}" class="btn btn-outline" style="border-radius: 999px;">
            <span>Masuk</span>
          </a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-with-arrow">
            <span>Daftar Sekarang</span>
            <span class="btn-arrow-circle" style="background:#FFFFFF; color:#1F1F1F;">
              <i data-lucide="arrow-right" style="width:14px; height:14px;"></i>
            </span>
          </a>
        @endguest

        @auth
          @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-with-arrow">
              <span>Panel Admin</span>
              <span class="btn-arrow-circle" style="background:#FFFFFF; color:#1F1F1F;">
                <i data-lucide="layout-dashboard" style="width:14px; height:14px;"></i>
              </span>
            </a>
          @else
            <a href="{{ route('umkm.dashboard') }}" class="btn btn-primary btn-with-arrow">
              <span>Dashboard Saya</span>
              <span class="btn-arrow-circle" style="background:#FFFFFF; color:#1F1F1F;">
                <i data-lucide="user" style="width:14px; height:14px;"></i>
              </span>
            </a>
          @endif
          
          <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-outline" style="border-radius: 999px; padding: 8px 16px; font-size: 0.85rem;" title="Keluar Akun">
              <span>Logout</span>
            </button>
          </form>
        @endauth

        <button type="button" class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Menu Navigasi">
          <i data-lucide="menu" style="width:20px; height:20px;"></i>
        </button>
      </div>
    </nav>
  </header>

  <!-- Mobile Nav Drawer -->
  <div class="mobile-nav-drawer" id="mobile-nav-drawer">
    <a href="#beranda" class="nav-link">Beranda</a>
    <a href="#jadwal-gelombang" class="nav-link">Jadwal Seleksi</a>
    <a href="#faq" class="nav-link">FAQ & Bantuan</a>
    @guest
      <div style="display: flex; gap: 8px; margin-top: 16px;">
        <a href="{{ route('login') }}" class="btn btn-outline" style="flex: 1; text-align: center;">Masuk</a>
        <a href="{{ route('register') }}" class="btn btn-primary" style="flex: 1; text-align: center;">Daftar</a>
      </div>
    @else
      <div style="margin-top: 16px;">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('umkm.dashboard') }}" class="btn btn-primary" style="width: 100%; text-align: center; margin-bottom: 8px;">
          Buka Dashboard ({{ Auth::user()->nama_lengkap }})
        </a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline" style="width: 100%;">Logout</button>
        </form>
      </div>
    @endguest
  </div>

  <main>
    <!-- ======================================================================
         Hero Section (design.md 4.2) with Smooth Entrance Animation
         ====================================================================== -->
    <section class="hero-section" id="beranda">
      <!-- 4-point Star Sparkles scattered in whitespace (design.md 5.2) -->
      <div class="sparkle sparkle-1" aria-hidden="true">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
        </svg>
      </div>
      <div class="sparkle sparkle-2" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
        </svg>
      </div>
      <div class="sparkle sparkle-3" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
        </svg>
      </div>
      <div class="sparkle sparkle-4" aria-hidden="true">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/>
        </svg>
      </div>

      <div class="container">
        <!-- Floating Tags (design.md 4.2) with Entrance Animation -->
        <div class="hero-top-tags animate-hero-up animate-delay-1">
          <span class="pill-tag">
            <i data-lucide="landmark" style="width:14px; height:14px; margin-right:4px;"></i>
            Dinas Koperasi & UMKM Kota Banjarmasin
          </span>
          @if($prosesAktif)
              @php
                  $now = now();
                  $isBerlangsung = $prosesAktif->tgl_pendaftaran_mulai && $now >= $prosesAktif->tgl_pendaftaran_mulai && $now <= $prosesAktif->tgl_pendaftaran_selesai;
                  $isSegera = $prosesAktif->tgl_pendaftaran_mulai && $now < $prosesAktif->tgl_pendaftaran_mulai;
              @endphp
              @if($isBerlangsung)
                  <span class="pill-tag orange">
                    <i data-lucide="zap" style="width:14px; height:14px; margin-right:4px;"></i>
                    Pendaftaran Sedang Berlangsung: {{ $prosesAktif->periode }}
                  </span>
              @elseif($isSegera)
                  <span class="pill-tag" style="background: var(--bg-blue-light); color: #1E6DB5;">
                    <i data-lucide="clock" style="width:14px; height:14px; margin-right:4px;"></i>
                    Segera Dibuka: {{ $prosesAktif->periode }}
                  </span>
              @else
                  <span class="pill-tag orange">
                    <i data-lucide="check-circle" style="width:14px; height:14px; margin-right:4px;"></i>
                    Periode Aktif: {{ $prosesAktif->periode }}
                  </span>
              @endif
          @else
              <span class="pill-tag" style="background: #fee2e2; color: #991b1b;">
                <i data-lucide="alert-circle" style="width:14px; height:14px; margin-right:4px;"></i>
                Belum Ada Gelombang Aktif
              </span>
          @endif
        </div>

        <!-- Headline with decorative pill & purple swoosh -->
        <div class="hero-content-center">
          <h1 class="hero-headline animate-hero-up animate-delay-2">
            Salurkan Bantuan Modal 
            <span class="highlight-pill">Tepat Sasaran</span> 
            dengan Sistem Seleksi 
            <span class="highlight-swoosh">WebGIS Cerdas</span>
          </h1>

          <p class="animate-hero-up animate-delay-3" style="font-size: 1.15rem; color: var(--text-secondary); max-width: 740px; margin: 0 auto; line-height: 1.6;">
            Platform terpadu seleksi bantuan modal usaha menggunakan algoritma <strong>Profile Matching</strong> otomatis dan pemetaan spasial <strong>WebGIS</strong> untuk memastikan proses objektif, transparan, serta verifikasi lapangan efisien di 5 Kecamatan Kota Banjarmasin.
          </p>

          <!-- Sub-information & CTA group row (design.md 4.2) -->
          <div class="hero-sub-row animate-hero-up animate-delay-4">
            <div class="hero-sub-stats">
              <div class="stat-item">
                <span class="stat-number">1.450+</span>
                <span class="stat-label">UMKM Terdata</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <span class="stat-number">5 Wilayah</span>
                <span class="stat-label">Kecamatan</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Obyektif & Adil</span>
              </div>
            </div>

            <div class="hero-cta-group">
              @guest
                @if($prosesAktif)
                  <a href="{{ route('register') }}" class="btn btn-primary btn-with-arrow">
                    <span>Daftar Pengajuan Modal</span>
                    <span class="btn-arrow-circle">
                      <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
                    </span>
                  </a>
                @else
                  <button type="button" class="btn btn-primary" style="opacity: 0.6; cursor: not-allowed;" onclick="showToast('Pendaftaran belum dibuka!')">
                    <span>Pendaftaran Ditutup</span>
                  </button>
                @endif
              @else
                @if(Auth::user()->role === 'umkm')
                  <a href="{{ route('umkm.pengajuan.create') }}" class="btn btn-primary btn-with-arrow">
                    <span>Formulir Pendaftaran UMKM</span>
                    <span class="btn-arrow-circle">
                      <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
                    </span>
                  </a>
                @else
                  <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-with-arrow">
                    <span>Buka Panel Admin</span>
                    <span class="btn-arrow-circle">
                      <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
                    </span>
                  </a>
                @endif
              @endguest
            </div>
          </div>
        </div>

        <!-- Feature Carousel / 4 Rounded Square Tiles (design.md 4.2) -->
        <div class="hero-tiles-wrapper animate-hero-up animate-delay-5">
          <div class="tiles-grid">
            <!-- Tile 1: Pastel Pink -->
            <div class="tile-card tile-pink">
              <div class="tile-icon-box" style="color: #D1456A;">
                <i data-lucide="file-check-2" style="width:30px; height:30px;"></i>
              </div>
              <span class="tile-tag">Registrasi Mandiri</span>
              <h3 class="tile-title">Validasi NIK 16 Digit</h3>
              <p class="tile-desc">
                Cegah duplikasi pendaftaran di satu gelombang dengan verifikasi otomatis NIK KTP, KK, dan nomor WhatsApp aktif.
              </p>
            </div>

            <!-- Tile 2: Pastel Purple -->
            <div class="tile-card tile-purple">
              <div class="tile-icon-box" style="color: var(--accent-purple);">
                <i data-lucide="scale" style="width:30px; height:30px;"></i>
              </div>
              <span class="tile-tag">Mesin SPK Otomatis</span>
              <h3 class="tile-title">Metode Profile Matching</h3>
              <p class="tile-desc">
                Perhitungan GAP matematis Core Factor (60%) & Secondary Factor (40%) untuk perangkingan kelayakan yang tidak bias.
              </p>
            </div>

            <!-- Tile 3: Pastel Blue -->
            <div class="tile-card tile-blue">
              <div class="tile-icon-box" style="color: #1E6DB5;">
                <i data-lucide="map-pin" style="width:30px; height:30px;"></i>
              </div>
              <span class="tile-tag">Spasial & Routing</span>
              <h3 class="tile-title">WebGIS & OSRM Engine</h3>
              <p class="tile-desc">
                Visualisasi titik koordinat latitude/longitude usaha dan optimasi rute survei fisik lapangan terpendek antar UMKM.
              </p>
            </div>

            <!-- Tile 4: Pastel Orange -->
            <div class="tile-card tile-orange">
              <div class="tile-icon-box" style="color: #CC6600;">
                <i data-lucide="award" style="width:30px; height:30px;"></i>
              </div>
              <span class="tile-tag">Transparansi Publik</span>
              <h3 class="tile-title">Notifikasi & Rekap SK</h3>
              <p class="tile-desc">
                Status pengajuan dipantau secara transparan, dilengkapi notifikasi WhatsApp terverifikasi serta penetapan SK penerima.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======================================================================
         Jadwal & Tahapan Gelombang Bantuan Modal (PRD Section 3)
         ====================================================================== -->
    <section class="container" id="jadwal-gelombang">
      <div class="section-rounded-container reveal-on-scroll" style="background: #FFFFFF;">
        <div class="section-header-split">
          <div class="section-title-group">
            <span class="section-badge-pill">
              <i data-lucide="calendar" style="width:14px; height:14px; margin-right:4px;"></i>
              Timeline Program
            </span>
            <h2 class="section-main-title">Tahapan Pelaksanaan {{ $prosesAktif ? $prosesAktif->periode : 'Gelombang Seleksi' }}</h2>
          </div>
          <p class="section-description">
            Perhatikan batas waktu setiap tahapan gelombang agar tidak terlewatkan. Pastikan akun UMKM Anda telah aktif dan berkas digital sudah terunggah sebelum batas akhir penutupan pendaftaran.
          </p>
        </div>

        @if($prosesAktif)
        <div class="timeline-grid reveal-stagger">
          <!-- Step 1 -->
          <div class="timeline-step reveal-on-scroll">
            <span class="step-badge">1</span>
            <span class="step-date">
                {{ $prosesAktif->tgl_pendaftaran_mulai ? \Carbon\Carbon::parse($prosesAktif->tgl_pendaftaran_mulai)->translatedFormat('d M') : 'TBA' }} - 
                {{ $prosesAktif->tgl_pendaftaran_selesai ? \Carbon\Carbon::parse($prosesAktif->tgl_pendaftaran_selesai)->translatedFormat('d M Y') : 'TBA' }}
            </span>
            <h4 class="step-title">Pendaftaran Mandiri</h4>
            <p class="step-desc">Pembuatan akun UMKM dengan validasi NIK 16 digit, pengisian form data profil usaha, dan penentuan titik koordinat peta.</p>
          </div>

          <!-- Step 2 -->
          <div class="timeline-step reveal-on-scroll">
            <span class="step-badge">2</span>
            <span class="step-date">
                {{ $prosesAktif->tgl_verifikasi_mulai ? \Carbon\Carbon::parse($prosesAktif->tgl_verifikasi_mulai)->translatedFormat('d M') : 'TBA' }} - 
                {{ $prosesAktif->tgl_verifikasi_selesai ? \Carbon\Carbon::parse($prosesAktif->tgl_verifikasi_selesai)->translatedFormat('d M Y') : 'TBA' }}
            </span>
            <h4 class="step-title">Verifikasi Dokumen</h4>
            <p class="step-desc">Tim pemeriksa Dinas Koperasi meninjau kelayakan berkas administratif KTP, KK, NIB/SKU, dan foto tempat usaha.</p>
          </div>

          <!-- Step 3 -->
          <div class="timeline-step reveal-on-scroll">
            <span class="step-badge">3</span>
            <span class="step-date">
                {{ $prosesAktif->tgl_spk_mulai ? \Carbon\Carbon::parse($prosesAktif->tgl_spk_mulai)->translatedFormat('d M') : 'TBA' }} - 
                {{ $prosesAktif->tgl_spk_selesai ? \Carbon\Carbon::parse($prosesAktif->tgl_spk_selesai)->translatedFormat('d M Y') : 'TBA' }}
            </span>
            <h4 class="step-title">Kalkulasi SPK Massal</h4>
            <p class="step-desc">Algoritma Profile Matching mengolah GAP kriteria dan menyusun ranking prioritas penerima berdasarkan kuota alokasi.</p>
          </div>

          <!-- Step 4 -->
          <div class="timeline-step reveal-on-scroll">
            <span class="step-badge">4</span>
            <span class="step-date">
                {{ $prosesAktif->tgl_survei_mulai ? \Carbon\Carbon::parse($prosesAktif->tgl_survei_mulai)->translatedFormat('d M') : 'TBA' }} - 
                {{ $prosesAktif->tgl_survei_selesai ? \Carbon\Carbon::parse($prosesAktif->tgl_survei_selesai)->translatedFormat('d M Y') : 'TBA' }}
            </span>
            <h4 class="step-title">Survei Lapangan</h4>
            <p class="step-desc">Petugas mengunjungi lokasi fisik usaha terverifikasi menggunakan optimasi rute peta WebGIS untuk validasi faktual.</p>
          </div>

          <!-- Step 5 -->
          <div class="timeline-step reveal-on-scroll">
            <span class="step-badge">5</span>
            <span class="step-date">
                {{ $prosesAktif->tgl_pengumuman ? \Carbon\Carbon::parse($prosesAktif->tgl_pengumuman)->translatedFormat('d M Y') : 'TBA' }}
            </span>
            <h4 class="step-title">Pengumuman & SK</h4>
            <p class="step-desc">Penerbitan Surat Keputusan (SK) penerima resmi dan penyaluran bantuan modal usaha kepada UMKM yang lolos.</p>
          </div>
        </div>
        @else
        <div class="reveal-on-scroll" style="text-align: center; padding: 56px 20px; background: #fafafa; border-radius: 20px; border: 1px dashed #e5e7eb;">
            <div style="margin-bottom: 16px; color: var(--accent-purple);">
              <i data-lucide="calendar-off" style="width:48px; height:48px;"></i>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">Belum Ada Jadwal Aktif</h3>
            <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto;">Saat ini belum ada gelombang pendaftaran yang dibuka oleh Dinas Koperasi, Usaha Mikro dan Tenaga Kerja Kota Banjarmasin. Silakan pantau terus halaman ini.</p>
        </div>
        @endif
      </div>
    </section>

    <!-- ======================================================================
         FAQ Section (Pertanyaan Umum)
         ====================================================================== -->
    <section class="container" id="faq">
      <div class="section-rounded-container reveal-on-scroll">
        <div style="text-align: center; max-width: 680px; margin: 0 auto;">
          <span class="section-badge-pill">
            <i data-lucide="help-circle" style="width:14px; height:14px; margin-right:4px;"></i>
            Informasi Bantuan
          </span>
          <h2 class="section-main-title">Pertanyaan Sering Diajukan (FAQ)</h2>
          <p class="section-description" style="margin: 12px auto 0;">
            Temukan jawaban cepat seputar persyaratan, sistem penilaian SPK, dan tahapan pencairan bantuan modal usaha.
          </p>
        </div>

        <div class="faq-list" style="margin-top: 32px;">
          <!-- FAQ 1 -->
          <div class="faq-item active reveal-on-scroll">
            <div class="faq-question">
              <span>Siapa saja yang berhak mendaftar program bantuan modal ini?</span>
              <span class="faq-icon">
                <i data-lucide="chevron-down" style="width:18px; height:18px;"></i>
              </span>
            </div>
            <div class="faq-answer">
              Pelaku Usaha Mikro dan Kecil yang berdomisili dan memiliki lokasi usaha fisik di wilayah administratif Kota Banjarmasin, memiliki NIK KTP Banjarmasin yang valid, serta memiliki legalitas usaha berupa NIB atau Surat Keterangan Usaha (SKU).
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="faq-item reveal-on-scroll">
            <div class="faq-question">
              <span>Bagaimana algoritma Profile Matching menjamin objektivitas seleksi?</span>
              <span class="faq-icon">
                <i data-lucide="chevron-down" style="width:18px; height:18px;"></i>
              </span>
            </div>
            <div class="faq-answer">
              Sistem menghitung selisih (GAP) antara kondisi riil pendaftar dan target ideal yang telah ditetapkan oleh Dinas Koperasi. Data diproses secara terotomatisasi menggunakan formula baku Core Factor (60%) dan Secondary Factor (40%) tanpa intervensi manual yang subjektif.
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="faq-item reveal-on-scroll">
            <div class="faq-question">
              <span>Mengapa pemohon wajib menandai titik koordinat tempat usaha?</span>
              <span class="faq-icon">
                <i data-lucide="chevron-down" style="width:18px; height:18px;"></i>
              </span>
            </div>
            <div class="faq-answer">
              Titik koordinat (Latitude dan Longitude) digunakan oleh sistem untuk memetakan persebaran spasial dan memudahkan tim dinas merencanakan kunjungan verifikasi faktual lapangan secara efisien dan akurat.
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="faq-item reveal-on-scroll">
            <div class="faq-question">
              <span>Bagaimana jika berkas administrasi saya memerlukan perbaikan?</span>
              <span class="faq-icon">
                <i data-lucide="chevron-down" style="width:18px; height:18px;"></i>
              </span>
            </div>
            <div class="faq-answer">
              Pendaftar akan menerima notifikasi status dokumen pada dashboard akun UMKM dengan catatan perbaikan dari verifikator Dinas. Pemohon diberikan waktu untuk mengunggah ulang dokumen revisi sebelum batas akhir penutupan verifikasi.
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======================================================================
         Community / Footer Section (design.md 4.4)
         ====================================================================== -->
    <section class="container" style="margin-bottom: 60px;">
      <div class="section-rounded-container footer-cta-container reveal-on-scroll">
        <!-- Section Tag (design.md 4.4) -->
        <div class="footer-section-tag">
          - Dinas Koperasi, Usaha Mikro dan Tenaga Kerja Kota Banjarmasin -
        </div>

        <h2 class="footer-headline">
          Wujudkan Kemandirian Usaha Melalui Bantuan Tepat Sasaran Bersama 
          <span class="highlight-pill">SiBantuan WebGIS</span>
        </h2>

        <p class="footer-subheadline">
          Mari kembangkan potensi wirausaha lokal Banua dengan tata kelola penyaluran modal yang transparan, akuntabel, dan berkeadilan melalui keunggulan teknologi digital.
        </p>

        <!-- Footer Actions -->
        <div class="footer-actions">
          @guest
            <a href="{{ route('register') }}" class="btn btn-primary btn-with-arrow">
              <span>Daftar Bantuan Sekarang</span>
              <span class="btn-arrow-circle">
                <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
              </span>
            </a>
          @else
            <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('umkm.dashboard') }}" class="btn btn-primary btn-with-arrow">
              <span>Buka Dashboard Saya</span>
              <span class="btn-arrow-circle">
                <i data-lucide="arrow-right" style="width:15px; height:15px;"></i>
              </span>
            </a>
          @endguest

          <a href="#beranda" class="btn btn-outline btn-with-arrow">
            <span>Kembali ke Atas</span>
            <span class="btn-arrow-circle purple">
              <i data-lucide="arrow-up" style="width:15px; height:15px;"></i>
            </span>
          </a>
        </div>

        <!-- Bottom Bar (design.md 4.4) -->
        <div class="footer-bottom-bar">
          <div>
            <strong>Pemerintah Kota Banjarmasin</strong> • Dinas Koperasi, Usaha Mikro & Tenaga Kerja
          </div>

          <div class="footer-social-icons">
            <a href="mailto:diskopumkm@banjarmasinkota.go.id" class="social-icon-btn" title="Email Dinas" aria-label="Kirim Email">
              <i data-lucide="mail" style="width:18px; height:18px;"></i>
            </a>
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="social-icon-btn" title="WhatsApp Hotline" aria-label="WhatsApp Hotline">
              <i data-lucide="message-square" style="width:18px; height:18px;"></i>
            </a>
            <a href="tel:05113251234" class="social-icon-btn" title="Telepon Kantor" aria-label="Telepon Kantor">
              <i data-lucide="phone" style="width:18px; height:18px;"></i>
            </a>
            <a href="https://banjarmasinkota.go.id" target="_blank" rel="noopener noreferrer" class="social-icon-btn" title="Portal Pemkot" aria-label="Portal Resmi Pemkot">
              <i data-lucide="globe" style="width:18px; height:18px;"></i>
            </a>
          </div>

          <div>
            SPK UMKM WebGIS Banjarmasin &copy; {{ date('Y') }} • Hak Cipta Dilindungi
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Core Application Scripts -->
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
