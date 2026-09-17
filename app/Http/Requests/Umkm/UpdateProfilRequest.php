<?php

namespace App\Http\Requests\Umkm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfilRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     * Hanya pengguna yang login dan memiliki role 'umkm' yang diizinkan.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'umkm';
    }

    /**
     * Aturan validasi untuk pembaruan profil data diri dan kontak UMKM.
     */
    public function rules(): array
    {
        $user = $this->user();
        $idUser = $user->id_user;
        $umkm = $user->umkm;
        $idUmkm = $umkm ? $umkm->id_umkm : null;

        return [
            // Data Akun Pengguna
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email'        => ['nullable', 'email:rfc,dns', 'max:100', Rule::unique('users', 'email')->ignore($idUser, 'id_user')],

            // Data Usaha & Pemilik UMKM
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'nama_umkm'    => ['required', 'string', 'max:150'],
            'nik'          => ['required', 'digits:16', Rule::unique('umkm', 'nik')->ignore($idUmkm, 'id_umkm')],
            'no_telepon'   => ['required', 'string', 'max:20', Rule::unique('umkm', 'no_telepon')->ignore($idUmkm, 'id_umkm')],
            'alamat'       => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * Pesan kesalahan kustom yang ramah pengguna.
     */
    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.email'           => 'Format alamat email tidak valid.',
            'email.unique'          => 'Alamat email ini sudah terdaftar pada akun lain.',
            'nama_pemilik.required' => 'Nama pemilik usaha wajib diisi.',
            'nama_umkm.required'    => 'Nama entitas UMKM wajib diisi.',
            'nik.required'          => 'NIK wajib diisi sesuai KTP.',
            'nik.digits'            => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique'            => 'NIK ini sudah terdaftar pada akun lain.',
            'no_telepon.required'   => 'Nomor telepon / WhatsApp wajib diisi.',
            'no_telepon.unique'     => 'Nomor telepon / WhatsApp ini sudah terdaftar pada akun lain.',
            'alamat.required'       => 'Alamat domisili / tempat usaha wajib diisi.',
            'alamat.max'            => 'Alamat tidak boleh melebihi 500 karakter.',
        ];
    }
}
