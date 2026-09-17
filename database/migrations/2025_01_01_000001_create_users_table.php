<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel: users
     * Menyimpan kredensial akses seluruh pengguna (Admin & Pelaku UMKM).
     * Sesuai PRD Section 6 - Database Schema.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama_lengkap', 100);
            $table->string('username', 50)->unique();
            $table->string('password');             // Di-hash dengan Bcrypt (PRD Section 2)
            $table->enum('role', ['admin', 'umkm'])->default('umkm');
            $table->rememberToken();
            $table->timestamps();                   // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
