<?php

namespace App\Http\Requests\Umkm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'umkm';
    }

    /**
     * Aturan validasi untuk pembaruan kata sandi.
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    /**
     * Pesan kesalahan kustom.
     */
    public function messages(): array
    {
        return [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'                      => 'Kata sandi baru minimal harus terdiri dari 8 karakter.',
        ];
    }
}
