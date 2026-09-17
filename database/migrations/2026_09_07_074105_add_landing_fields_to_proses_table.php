<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proses', function (Blueprint $table) {
            $table->enum('status', ['Draft', 'Buka', 'Tutup'])->default('Draft')->after('periode');
            $table->text('deskripsi_alur')->nullable()->after('status');
            $table->date('tgl_pendaftaran_mulai')->nullable();
            $table->date('tgl_pendaftaran_selesai')->nullable();
            $table->date('tgl_verifikasi_mulai')->nullable();
            $table->date('tgl_verifikasi_selesai')->nullable();
            $table->date('tgl_spk_mulai')->nullable();
            $table->date('tgl_spk_selesai')->nullable();
            $table->date('tgl_survei_mulai')->nullable();
            $table->date('tgl_survei_selesai')->nullable();
            $table->date('tgl_pengumuman')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proses', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'deskripsi_alur',
                'tgl_pendaftaran_mulai',
                'tgl_pendaftaran_selesai',
                'tgl_verifikasi_mulai',
                'tgl_verifikasi_selesai',
                'tgl_spk_mulai',
                'tgl_spk_selesai',
                'tgl_survei_mulai',
                'tgl_survei_selesai',
                'tgl_pengumuman',
            ]);
        });
    }
};
