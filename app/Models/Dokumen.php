<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';
    protected $primaryKey = 'id_dokumen';

    protected $fillable = [
        'id_pengajuan',
        'jenis_dokumen',
        'file_dokumen',
        'status_verifikasi',
        'catatan_admin',
    ];

    // =========================================================================
    // Konstanta Jenis Dokumen & Status (PRD Section 2 & 3)
    // =========================================================================

    const JENIS_KTP  = 'KTP';
    const JENIS_KK   = 'KK';
    const JENIS_NIB  = 'NIB';
    const JENIS_SKU  = 'SKU';
    const JENIS_FOTO = 'FOTO';

    const STATUS_MENUNGGU  = 'menunggu';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK   = 'ditolak';

    // =========================================================================
    // Relasi Eloquent (sesuai ERD)
    // =========================================================================

    /**
     * Setiap dokumen dimiliki oleh satu Pengajuan.
     * ERD: pengajuan ||--o{ dokumen ("melampirkan")
     */
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan', 'id_pengajuan');
    }

    // =========================================================================
    // Accessor: URL File (Direktori Private Non-Public, PRD Section 2)
    // =========================================================================

    /**
     * Accessor: Dapatkan URL file dari direktori storage private.
     * File disimpan di storage/app/private/dokumen-umkm/ (non-public).
     */
    public function getFileUrlAttribute(): string
    {
        return route('admin.dokumen.download', ['id_dokumen' => $this->id_dokumen]);
    }

    public function isDitolak(): bool
    {
        return $this->status_verifikasi === self::STATUS_DITOLAK;
    }

    public function isDisetujui(): bool
    {
        return $this->status_verifikasi === self::STATUS_DISETUJUI;
    }

    public function isMenunggu(): bool
    {
        return $this->status_verifikasi === self::STATUS_MENUNGGU;
    }

    public function getNamaJenisAttribute(): string
    {
        $map = [
            self::JENIS_KTP  => 'KTP Pemilik Usaha',
            self::JENIS_KK   => 'Kartu Keluarga (KK)',
            self::JENIS_NIB  => 'Nomor Induk Berusaha (NIB)',
            self::JENIS_SKU  => 'Surat Keterangan Usaha (SKU)',
            self::JENIS_FOTO => 'Foto Fisik Tempat Usaha',
        ];

        return $map[$this->jenis_dokumen] ?? $this->jenis_dokumen;
    }

    /**
     * Accessor: Cek apakah file fisik masih ada di storage.
     */
    public function getFileExistsAttribute(): bool
    {
        return Storage::disk('local')->exists($this->file_dokumen);
    }
}
