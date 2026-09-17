<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenilaian extends Model
{
    use HasFactory;

    protected $table = 'detail_penilaian';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pengajuan',
        'id_kriteria',
        'nilai_aktual',
        'skor',
        'gap',
        'bobot_gap',
    ];

    protected $casts = [
        'nilai_aktual' => 'decimal:2',
        'skor'         => 'integer',
        'gap'          => 'integer',
        'bobot_gap'    => 'decimal:2',
    ];

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Setiap detail penilaian dimiliki oleh satu Pengajuan.
     * ERD: pengajuan ||--o{ detail_penilaian ("dinilai pada")
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Setiap detail penilaian didasari oleh satu Kriteria.
     * ERD: kriteria ||--o{ detail_penilaian ("menjadi dasar")
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria', 'id_kriteria');
    }

    // =========================================================================
    // Tabel Bobot GAP (Standar Profile Matching) - PRD Section 3
    // =========================================================================

    /**
     * Hitung dan kembalikan bobot interpolasi berdasarkan nilai selisih GAP.
     * Menggunakan tabel konversi standar algoritma Profile Matching.
     *
     * @param int $gap Nilai selisih (skor_aktual - target_ideal)
     * @return float Bobot GAP
     */
    public static function getBobotGap(int $gap): float
    {
        $tabel = [
             0 => 5.0,   // Tidak ada gap (persis sesuai target)
             1 => 4.5,   // Kelebihan 1 tingkat
            -1 => 4.0,   // Kekurangan 1 tingkat
             2 => 3.5,   // Kelebihan 2 tingkat
            -2 => 3.0,   // Kekurangan 2 tingkat
             3 => 2.5,   // Kelebihan 3 tingkat
            -3 => 2.0,   // Kekurangan 3 tingkat
             4 => 1.5,   // Kelebihan 4 tingkat
            -4 => 1.0,   // Kekurangan 4 tingkat
        ];

        return $tabel[$gap] ?? 1.0; // Default 1.0 untuk gap di luar rentang
    }
}
