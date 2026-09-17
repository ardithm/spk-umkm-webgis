<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: hasil_perhitungan
     * Representasi akhir kedudukan persaingan / ranking rekomendasi SPK.
     * Relasi: belongsTo proses (id_proses), belongsTo pengajuan (id_pengajuan).
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('hasil_perhitungan', function (Blueprint $table) {
            $table->id('id_hasil');
            $table->unsignedBigInteger('id_proses');
            $table->unsignedBigInteger('id_pengajuan');

            // Akumulasi hasil Profile Matching (PRD Section 3 - Mesin SPK)
            $table->decimal('nilai_ncf', 10, 4);    // Nilai Core Factor (bobot 60%)
            $table->decimal('nilai_nsf', 10, 4);    // Nilai Secondary Factor (bobot 40%)
            $table->decimal('nilai_akhir', 10, 4);  // Total: (NCF × 0.6) + (NSF × 0.4)

            $table->integer('ranking')->nullable();  // Kedudukan peringkat akhir (1 = terbaik)

            $table->enum('status_seleksi', ['diterima', 'tidak_diterima', 'cadangan'])
                  ->default('tidak_diterima');

            $table->dateTime('tanggal_proses');     // Perekaman komputasi selesai

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_proses')
                  ->references('id_proses')
                  ->on('proses')
                  ->onDelete('cascade');

            $table->foreign('id_pengajuan')
                  ->references('id_pengajuan')
                  ->on('pengajuan')
                  ->onDelete('cascade');

            // Satu pengajuan hanya boleh punya satu hasil per gelombang proses
            $table->unique(['id_proses', 'id_pengajuan'], 'uq_hasil_proses_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_perhitungan');
    }
};
