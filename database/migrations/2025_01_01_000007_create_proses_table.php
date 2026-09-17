<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: proses
     * Master table sesi kelulusan / gelombang program bantuan modal usaha.
     * Relasi: hasMany hasil_perhitungan (id_proses).
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('proses', function (Blueprint $table) {
            $table->id('id_proses');
            $table->string('periode', 50);          // Identitas gelombang, contoh: "2025-GEL-01"
            $table->dateTime('tanggal_proses');     // Waktu kalkulasi SPK dijalankan oleh Admin
            $table->text('keterangan')->nullable(); // Deskripsi pelaksanaan seleksi & notes admin

            // Passing grade yang berlaku pada gelombang ini (PRD Section 3 - Modul SPK)
            $table->decimal('passing_grade', 5, 2)->default(3.80);

            // Kuota penerima bantuan pada gelombang ini (PRD Section 3 - Modul Manajemen Periode)
            $table->integer('kuota')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proses');
    }
};
