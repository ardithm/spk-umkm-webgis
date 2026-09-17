<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: kriteria
     * Dasar standar nilai acuan komputasi Dinas untuk algoritma Profile Matching.
     * Dibuat lebih awal karena digunakan sebagai referensi oleh tabel lain.
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id('id_kriteria');
            $table->string('kode_kriteria', 10)->unique(); // K1, K2, K3, K4, K5
            $table->string('nama_kriteria', 150);
            $table->integer('target_ideal');                // Titik nilai absolut referensi instansi

            // Jenis faktor untuk Profile Matching (PRD Section 3 - Modul SPK)
            $table->enum('jenis_faktor', ['core', 'secondary']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
