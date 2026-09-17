<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'target_ideal',
        'jenis_faktor',
    ];

    protected $casts = [
        'target_ideal' => 'integer',
    ];

    // =========================================================================
    // Konstanta Jenis Faktor Profile Matching (PRD Section 3 - Mesin SPK)
    // =========================================================================

    const FAKTOR_CORE      = 'core';      // Bobot: 60%
    const FAKTOR_SECONDARY = 'secondary'; // Bobot: 40%

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Satu kriteria memiliki banyak rentang konversi GAP.
     * ERD: kriteria ||--o{ konversi_nilai ("memiliki rentang")
     */
    public function konversiNilai(): HasMany
    {
        return $this->hasMany(KonversiNilai::class, 'id_kriteria', 'id_kriteria');
    }

    /**
     * Satu kriteria menjadi dasar banyak detail penilaian kandidat.
     * ERD: kriteria ||--o{ detail_penilaian ("menjadi dasar")
     */
    public function detailPenilaian(): HasMany
    {
        return $this->hasMany(DetailPenilaian::class, 'id_kriteria', 'id_kriteria');
    }

    // =========================================================================
    // Scope Helper
    // =========================================================================

    /**
     * Scope: filter hanya kriteria jenis Core Factor.
     */
    public function scopeCoreFactor($query)
    {
        return $query->where('jenis_faktor', self::FAKTOR_CORE);
    }

    /**
     * Scope: filter hanya kriteria jenis Secondary Factor.
     */
    public function scopeSecondaryFactor($query)
    {
        return $query->where('jenis_faktor', self::FAKTOR_SECONDARY);
    }

    /**
     * Accessor: Bobot persentase faktor ini dalam formula Profile Matching.
     */
    public function getBobotFaktorAttribute(): float
    {
        return $this->jenis_faktor === self::FAKTOR_CORE ? 0.60 : 0.40;
    }
}
