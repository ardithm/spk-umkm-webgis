<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPerhitungan extends Model
{
    use HasFactory;

    protected $table = 'hasil_perhitungan';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_proses',
        'id_pengajuan',
        'nilai_ncf',
        'nilai_nsf',
        'nilai_akhir',
        'ranking',
        'status_seleksi',
        'tanggal_proses',
    ];

    protected $casts = [
        'nilai_ncf'      => 'decimal:4',
        'nilai_nsf'      => 'decimal:4',
        'nilai_akhir'    => 'decimal:4',
        'ranking'        => 'integer',
        'tanggal_proses' => 'datetime',
    ];

    // =========================================================================
    // Konstanta Status Seleksi
    // =========================================================================

    const STATUS_DITERIMA       = 'diterima';
    const STATUS_TIDAK_DITERIMA = 'tidak_diterima';
    const STATUS_CADANGAN       = 'cadangan';

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Hasil perhitungan ini dihasilkan oleh satu sesi Proses seleksi.
     * ERD: proses ||--o{ hasil_perhitungan ("menghasilkan")
     */
    public function proses(): BelongsTo
    {
        return $this->belongsTo(Proses::class, 'id_proses', 'id_proses');
    }

    /**
     * Hasil perhitungan ini berhubungan dengan satu Pengajuan UMKM.
     * ERD: pengajuan ||--|| hasil_perhitungan ("memiliki hasil")
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    // =========================================================================
    // Accessor & Helper
    // =========================================================================

    /**
     * Accessor: Dapatkan profil UMKM dari hasil ini via eager loading pengajuan.
     */
    public function getUmkmAttribute()
    {
        return $this->pengajuan?->umkm;
    }

    /**
     * Accessor: Apakah kandidat ini dinyatakan diterima.
     */
    public function getDiterimaAttribute(): bool
    {
        return $this->status_seleksi === self::STATUS_DITERIMA;
    }

    /**
     * Static: Hitung nilai akhir Profile Matching dari NCF dan NSF.
     * Formula: Nilai Akhir = (NCF × 60%) + (NSF × 40%)
     * (PRD Section 3 - Mesin SPK, PRD Section 6)
     *
     * @param float $ncf Nilai Core Factor
     * @param float $nsf Nilai Secondary Factor
     * @return float Nilai akhir total
     */
    public static function hitungNilaiAkhir(float $ncf, float $nsf): float
    {
        return round(($ncf * 0.60) + ($nsf * 0.40), 4);
    }
}
