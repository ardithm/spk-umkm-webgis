<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: dokumen
     * Tempat lampiran arsip bukti fisik UMKM yang diunggah pemohon.
     * Relasi: belongsTo pengajuan (id_pengajuan).
     * File disimpan di direktori non-public (PRD Section 2 - Storage Subsystem).
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen');
            $table->unsignedBigInteger('id_pengajuan');

            // Jenis berkas persyaratan wajib (PRD Section 2 & 3)
            $table->enum('jenis_dokumen', ['KTP', 'KK', 'NIB', 'SKU', 'FOTO']);

            // Path disimpan di storage/app/private/ (non-public, protected)
            $table->string('file_dokumen', 255);

            // Status verifikasi dokumen per jenis berkas (PRD Section 3 - Modul Pemberkasan)
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');

            // Catatan perbaikan dari Admin jika dokumen ditolak
            $table->text('catatan_admin')->nullable();

            $table->timestamps();                   // created_at = waktu unggah

            // Foreign Key Constraint
            $table->foreign('id_pengajuan')
                  ->references('id_pengajuan')
                  ->on('pengajuan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
