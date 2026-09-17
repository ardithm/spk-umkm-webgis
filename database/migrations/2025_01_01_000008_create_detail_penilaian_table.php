<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: detail_penilaian
     * Menyimpan hasil pindaian matematis per kriteria per kandidat.
     * Diisi otomatis oleh Mesin SPK saat Admin menjalankan kalkulasi massal.
     * Relasi: belongsTo pengajuan, belongsTo kriteria.
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('detail_penilaian', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_pengajuan');
            $table->unsignedBigInteger('id_kriteria');

            // Kolom output kalkulasi Profile Matching per kriteria (PRD Section 3 - Mesin SPK)
            $table->decimal('nilai_aktual', 15, 2);     // Nilai input sesungguhnya dari pemohon
            $table->integer('skor');                    // Hasil konversi nilai ke skor standar (1-5)
            $table->integer('gap');                     // GAP = skor - target_ideal
            $table->decimal('bobot_gap', 4, 2);         // Bobot interpolasi berdasarkan tabel GAP

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_pengajuan')
                  ->references('id_pengajuan')
                  ->on('pengajuan')
                  ->onDelete('cascade');

            $table->foreign('id_kriteria')
                  ->references('id_kriteria')
                  ->on('kriteria')
                  ->onDelete('restrict');

            // Composite Unique: satu pengajuan hanya punya satu penilaian per kriteria
            $table->unique(['id_pengajuan', 'id_kriteria'], 'uq_detail_pengajuan_kriteria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penilaian');
    }
};
