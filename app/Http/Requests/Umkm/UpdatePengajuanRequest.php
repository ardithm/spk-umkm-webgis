<?php

namespace App\Http\Requests\Umkm;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'umkm';
    }

    public function rules(): array
    {
        return [
            // Kriteria SPK Profile Matching
            'omzet_tahunan'       => ['required', 'numeric', 'min:0'],
            'aset'                => ['required', 'numeric', 'min:0'],
            'jumlah_tenaga_kerja' => ['required', 'integer', 'min:1'],
            'jangkauan_pemasaran' => ['required', 'in:kelurahan,kecamatan,kota,provinsi,nasional'],
            'status_perizinan'    => ['required', 'in:belum_ada,sku_rt,sku_kelurahan,nib,nib_lengkap'],

            // Koordinat Lokasi Fisik WebGIS
            'latitude'            => ['required', 'numeric', 'between:-90,90'],
            'longitude'           => ['required', 'numeric', 'between:-180,180'],

            // Dokumen bersifat opsional saat edit (hanya jika ingin mengganti)
            'file_ktp'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_kk'             => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_nib'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_sku'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_foto'           => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'omzet_tahunan.required'       => 'Estimasi omzet tahunan wajib diisi.',
            'omzet_tahunan.numeric'        => 'Nilai omzet harus berupa angka numerik.',
            'aset.required'                => 'Nilai estimasi aset usaha wajib diisi.',
            'aset.numeric'                 => 'Nilai aset harus berupa angka numerik.',
            'jumlah_tenaga_kerja.required' => 'Jumlah tenaga kerja wajib diisi.',
            'jumlah_tenaga_kerja.min'      => 'Jumlah tenaga kerja minimal 1 orang.',
            'jangkauan_pemasaran.required' => 'Pilih salah satu skala jangkauan pemasaran.',
            'status_perizinan.required'    => 'Pilih status kelengkapan legalitas perizinan.',
            'latitude.required'            => 'Titik koordinat Latitude wajib ditentukan pada peta.',
            'longitude.required'           => 'Titik koordinat Longitude wajib ditentukan pada peta.',
            '*.max'                        => 'Ukuran file berkas tidak boleh melebihi 2MB (2048 KB).',
            '*.mimes'                      => 'Format file yang diperbolehkan hanya PDF, JPG, JPEG, atau PNG.',
        ];
    }
}
