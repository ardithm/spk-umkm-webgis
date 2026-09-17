# SPK UMKM WebGIS

Sistem pendukung keputusan (SPK) untuk UMKM berbasis WebGIS.

## Fitur utama
- Pendaftaran dan pengelolaan UMKM
- Penilaian berbasis kriteria dan profil matching
- Dashboard hasil perhitungan
- Integrasi peta dan visualisasi data lokasi

## Teknologi
- Laravel 13
- PHP 8.3+
- Vite + Tailwind CSS
- SQLite / database Laravel default

## Persiapan lokal
1. Salin file `.env.example` menjadi `.env`
2. Jalankan:
   ```bash
   composer install
   npm install
   php artisan key:generate
   php artisan migrate
   npm run build
   ```
3. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```

## Standar siap push ke GitHub
- `.gitignore` sudah dibuat agar file sensitif dan artefak build tidak ikut dalam commit
- `.env` tidak disimpan ke repository
- `composer.lock` dan `package-lock.json` disarankan tetap dilacak untuk reproduksi build
- Gunakan branch yang jelas seperti `main`, `develop`, atau fitur tertentu sebelum push

## Catatan
Sesuaikan konfigurasi database dan variabel lingkungan pada file `.env` sebelum menjalankan aplikasi di lingkungan masing-masing.
