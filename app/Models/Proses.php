<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proses extends Model
{
    use HasFactory;

    protected $table = 'proses';
    protected $primaryKey = 'id_proses';

    protected $fillable = [
        'periode',
        'status',
        'deskripsi_alur',
        'tanggal_proses',
        'keterangan',
        'passing_grade',
        'kuota',
        'tgl_pendaftaran_mulai',
        'tgl_pendaftaran_selesai',
        'tgl_verifikasi_mulai',
        'tgl_verifikasi_selesai',
        'tgl_spk_mulai',
        'tgl_spk_selesai',
        'tgl_survei_mulai',
        'tgl_survei_selesai',
        'tgl_pengumuman',
    ];

    protected $casts = [
        'tanggal_proses' => 'datetime',
        'passing_grade'  => 'decimal:2',
        'kuota'          => 'integer',
        'tgl_pendaftaran_mulai' => 'date',
        'tgl_pendaftaran_selesai' => 'date',
        'tgl_verifikasi_mulai' => 'date',
        'tgl_verifikasi_selesai' => 'date',
        'tgl_spk_mulai' => 'date',
        'tgl_spk_selesai' => 'date',
        'tgl_survei_mulai' => 'date',
        'tgl_survei_selesai' => 'date',
        'tgl_pengumuman' => 'date',
    ];

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Satu sesi proses seleksi menghasilkan banyak hasil perhitungan.
     * ERD: proses ||--o{ hasil_perhitungan ("menghasilkan")
     */
    public function hasilPerhitungan(): HasMany
    {
        return $this->hasMany(HasilPerhitungan::class, 'id_proses', 'id_proses');
    }

    // =========================================================================
    // Helper Scope
    // =========================================================================

    /**
     * Scope: ambil hanya daftar hasil yang diterima pada proses ini.
     */
    public function hasilDiterima(): HasMany
    {
        return $this->hasMany(HasilPerhitungan::class, 'id_proses', 'id_proses')
                    ->where('status_seleksi', 'diterima')
                    ->orderBy('ranking');
    }
}
