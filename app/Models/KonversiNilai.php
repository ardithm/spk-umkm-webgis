<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonversiNilai extends Model
{
    use HasFactory;

    protected $table = 'konversi_nilai';
    protected $primaryKey = 'id_konversi';

    protected $fillable = [
        'id_kriteria',
        'nilai_min',
        'nilai_max',
        'skor',
    ];

    protected $casts = [
        'nilai_min' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'skor'      => 'integer',
    ];

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Setiap rentang konversi dimiliki oleh satu Kriteria.
     * ERD: kriteria ||--o{ konversi_nilai ("memiliki rentang")
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria', 'id_kriteria');
    }

    // =========================================================================
    // Helper Static Method
    // =========================================================================

    /**
     * Dapatkan skor konversi untuk nilai aktual dan kriteria tertentu.
     * Digunakan oleh Mesin SPK saat menjalankan kalkulasi massal (PRD Section 3).
     *
     * @param int $id_kriteria
     * @param float $nilai_aktual
     * @return int Skor konversi (1-5), atau 1 jika tidak ditemukan
     */
    public static function getSkorKonversi(int $id_kriteria, float $nilai_aktual): int
    {
        $konversi = static::where('id_kriteria', $id_kriteria)
            ->where('nilai_min', '<=', $nilai_aktual)
            ->where('nilai_max', '>=', $nilai_aktual)
            ->first();

        return $konversi ? $konversi->skor : 1;
    }
}
