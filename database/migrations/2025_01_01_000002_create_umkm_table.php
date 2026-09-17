<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: umkm
     * Menyimpan profil fisik dan titik spasial UMKM kandidat pemohon bantuan.
     * Relasi: belongsTo users (id_user).
     * Sesuai PRD Section 6 - Database Schema & ERD.
     */
    public function up(): void
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->id('id_umkm');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_umkm', 150);
            $table->string('nama_pemilik', 100);
            $table->string('nik', 16)->unique()->nullable();
            $table->string('no_telepon', 20);
            $table->text('alamat');

            // Koordinat Spasial WebGIS (PRD Section 3 - Modul Pemetaan)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->timestamps();                   // created_at & updated_at

            // Foreign Key Constraint
            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
    }
};
