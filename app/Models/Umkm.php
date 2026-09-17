<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    use HasFactory;

    /**
     * Nama tabel & Primary Key kustom sesuai skema PRD.
     */
    protected $table = 'umkm';
    protected $primaryKey = 'id_umkm';

    protected $fillable = [
        'id_user',
        'nama_umkm',
        'nama_pemilik',
        'nik',
        'no_telepon',
        'alamat',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Setiap profil UMKM dimiliki oleh satu User.
     * ERD: users ||--o{ umkm ("memiliki")
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Satu profil UMKM dapat memiliki banyak Pengajuan (lintas gelombang).
     * ERD: umkm ||--o{ pengajuan ("memiliki")
     */
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_umkm', 'id_umkm');
    }

    /**
     * Shortcut: Ambil pengajuan terbaru / aktif dari UMKM ini.
     */
    public function pengajuanAktif()
    {
        return $this->hasOne(Pengajuan::class, 'id_umkm', 'id_umkm')
                    ->latestOfMany('tanggal_pengajuan');
    }

    // =========================================================================
    // Helper Scope WebGIS
    // =========================================================================

    /**
     * Scope: hanya UMKM yang sudah memiliki koordinat spasial (untuk peta WebGIS).
     */
    public function scopeTerplotting($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Cek apakah kelengkapan data dasar profil UMKM telah terpenuhi.
     */
    public function isProfileComplete(): bool
    {
        return !empty($this->nama_umkm) &&
               !empty($this->nama_pemilik) &&
               !empty($this->nik) &&
               !empty($this->no_telepon) &&
               !empty($this->alamat);
    }
}
