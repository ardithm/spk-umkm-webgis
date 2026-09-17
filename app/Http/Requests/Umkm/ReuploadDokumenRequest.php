<?php

namespace App\Http\Requests\Umkm;

use Illuminate\Foundation\Http\FormRequest;

class ReuploadDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'umkm';
    }

    public function rules(): array
    {
        return [
            'file_dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'file_dokumen.required' => 'Silakan pilih berkas dokumen pengganti terlebih dahulu.',
            'file_dokumen.file'     => 'Unggahan harus berupa berkas valid.',
            'file_dokumen.mimes'    => 'Format dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_dokumen.max'      => 'Ukuran file dokumen perbaikan tidak boleh melebihi 2MB (2048 KB).',
        ];
    }
}
