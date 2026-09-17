<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Primary Key kustom sesuai skema PRD.
     */
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_lengkap',
        'username',
        'email',
        'password',
        'role',
        'foto',
    ];

    /**
     * Accessor untuk URL foto profil
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto)) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',     // Otomatis di-hash Bcrypt (PRD Section 2)
        ];
    }

    // =========================================================================
    // Relasi Eloquent (sesuai ERD: users ||--o{ umkm)
    // =========================================================================

    /**
     * Satu User Pemohon memiliki satu profil UMKM.
     */
    public function umkm(): HasOne
    {
        return $this->hasOne(Umkm::class, 'id_user', 'id_user');
    }

    // =========================================================================
    // Scope Helper
    // =========================================================================

    /**
     * Scope: filter hanya pengguna dengan role 'umkm'.
     */
    public function scopePemohon($query)
    {
        return $query->where('role', 'umkm');
    }

    /**
     * Scope: filter hanya pengguna dengan role 'admin'.
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Accessor: cek apakah user ini adalah Admin.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }
}
