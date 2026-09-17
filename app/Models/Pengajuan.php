<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'id_umkm',
        'omzet_tahunan',
        'aset',
        'jumlah_tenaga_kerja',
        'jangkauan_pemasaran',
        'status_perizinan',
        'tanggal_pengajuan',
        'status',
        'catatan_revisi',
        'batas_revisi',
    ];

    protected $casts = [
        'omzet_tahunan'       => 'decimal:2',
        'aset'                => 'decimal:2',
        'jumlah_tenaga_kerja' => 'integer',
        'tanggal_pengajuan'   => 'datetime',
        'batas_revisi'        => 'datetime',
    ];

    /**
     * Cek apakah pengajuan dapat diedit / diubah oleh pemohon.
     * Hanya diizinkan pada status Draft dan Revisi (PRD Lock Mechanism).
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISI]);
    }

    /**
     * Cek apakah data form pengajuan sedang dikunci karena dalam proses seleksi.
     */
    public function isLocked(): bool
    {
        return !$this->isEditable();
    }

    // =========================================================================
    // Konstanta Status Alur Pengajuan (PRD Section 4 - User Flow)
    // =========================================================================

    const STATUS_DRAFT         = 'draft';
    const STATUS_MENUNGGU      = 'menunggu';
    const STATUS_REVISI        = 'revisi';
    const STATUS_TERVERIFIKASI = 'terverifikasi';
    const STATUS_DIPROSES      = 'diproses';
    const STATUS_SELESAI       = 'selesai';

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Setiap pengajuan dimiliki oleh satu profil UMKM.
     * ERD: umkm ||--o{ pengajuan ("memiliki")
     */
    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class, 'id_umkm', 'id_umkm');
    }

    /**
     * Satu pengajuan dapat memiliki banyak dokumen lampiran.
     * ERD: pengajuan ||--o{ dokumen ("melampirkan")
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Satu pengajuan dapat memiliki banyak detail penilaian (per kriteria SPK).
     * ERD: pengajuan ||--o{ detail_penilaian ("dinilai pada")
     */
    public function detailPenilaian(): HasMany
    {
        return $this->hasMany(DetailPenilaian::class, 'id_pengajuan', 'id_pengajuan');
    }

    /**
     * Satu pengajuan memiliki tepat satu hasil perhitungan akhir SPK.
     * ERD: pengajuan ||--|| hasil_perhitungan ("memiliki hasil")
     */
    public function hasilPerhitungan(): HasOne
    {
        return $this->hasOne(HasilPerhitungan::class, 'id_pengajuan', 'id_pengajuan');
    }

    // =========================================================================
    // Helper Scope & Accessor
    // =========================================================================

    /**
     * Scope: hanya pengajuan dengan status 'terverifikasi' (siap masuk kalkulasi SPK).
     */
    public function scopeTerverifikasi($query)
    {
        return $query->where('status', self::STATUS_TERVERIFIKASI);
    }

    /**
     * Accessor: Cek apakah semua dokumen wajib sudah disetujui.
     */
    public function getDokumenLengkapAttribute(): bool
    {
        $jenis_wajib = ['KTP', 'KK', 'NIB', 'SKU', 'FOTO'];
        $jumlah_disetujui = $this->dokumen()
            ->whereIn('jenis_dokumen', $jenis_wajib)
            ->where('status_verifikasi', 'disetujui')
            ->count();

        return $jumlah_disetujui === count($jenis_wajib);
    }
}
