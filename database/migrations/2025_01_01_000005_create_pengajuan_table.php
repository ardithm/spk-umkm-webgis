<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: pengajuan
     * Menampung entitas permintaan pencairan dana per periode gelombang bantuan.
     * Relasi: belongsTo umkm (id_umkm).
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->unsignedBigInteger('id_umkm');

            // 5 Kriteria SPK Profile Matching (PRD Section 3 - Modul Pendaftaran & Pemberkasan)
            $table->decimal('omzet_tahunan', 15, 2)->nullable();
            $table->decimal('aset', 15, 2)->nullable();
            $table->integer('jumlah_tenaga_kerja')->nullable();
            $table->enum('jangkauan_pemasaran', [
                'kelurahan',
                'kecamatan',
                'kota',
                'provinsi',
                'nasional',
            ])->nullable();
            $table->enum('status_perizinan', [
                'belum_ada',
                'sku_rt',
                'sku_kelurahan',
                'nib',
                'nib_lengkap',
            ])->nullable();

            $table->dateTime('tanggal_pengajuan')->nullable();

            // Status alur pengajuan (PRD Section 4 - User Flow)
            $table->enum('status', [
                'draft',            // Belum disubmit pemohon
                'menunggu',         // Sudah disubmit, menunggu verifikasi admin
                'revisi',           // Dikembalikan, butuh perbaikan berkas
                'terverifikasi',    // Berkas disetujui admin, masuk antrian SPK
                'diproses',         // Sedang dalam kalkulasi Profile Matching
                'selesai',          // Hasil SPK sudah ada
            ])->default('draft');

            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('id_umkm')
                  ->references('id_umkm')
                  ->on('umkm')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
