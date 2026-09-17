<?php

namespace App\Http\Requests\Umkm;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'umkm';
    }

    /**
     * Aturan validasi untuk upload foto profil/avatar.
     */
    public function rules(): array
    {
        return [
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Pesan kesalahan kustom.
     */
    public function messages(): array
    {
        return [
            'foto.required' => 'Silakan pilih berkas foto terlebih dahulu.',
            'foto.image'    => 'Berkas yang diunggah harus berupa gambar.',
            'foto.mimes'    => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max'      => 'Ukuran foto profil tidak boleh melebihi 2MB (2048 KB).',
        ];
    }
}
