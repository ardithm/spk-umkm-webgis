<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: konversi_nilai
     * Tabel pemetaan jarak GAP menjadi nilai (skor) kualitatif.
     * Relasi: belongsTo kriteria (id_kriteria).
     * Sesuai PRD Section 6 - Database Schema & ERD (kriteria ||--o{ konversi_nilai).
     */
    public function up(): void
    {
        Schema::create('konversi_nilai', function (Blueprint $table) {
            $table->id('id_konversi');
            $table->unsignedBigInteger('id_kriteria');
            $table->decimal('nilai_min', 15, 2);    // Batas bawah rentang
            $table->decimal('nilai_max', 15, 2);    // Batas atas rentang
            $table->integer('skor');                // Skor hasil pemetaan gap

            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('id_kriteria')
                  ->references('id_kriteria')
                  ->on('kriteria')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi_nilai');
    }
};
