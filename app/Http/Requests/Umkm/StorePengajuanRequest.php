<?php

namespace App\Http\Requests\Umkm;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
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

            // 5 Dokumen Persyaratan Wajib (Format: PDF, JPG, PNG; Maks: 2MB)
            'file_ktp'            => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_kk'             => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_nib'            => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_sku'            => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'file_foto'           => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
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
            'file_ktp.required'            => 'Berkas KTP pemilik wajib diunggah.',
            'file_kk.required'             => 'Berkas Kartu Keluarga (KK) wajib diunggah.',
            'file_nib.required'            => 'Berkas NIB / Keterangan Usaha wajib diunggah.',
            'file_sku.required'            => 'Berkas Surat Keterangan Usaha (SKU) wajib diunggah.',
            'file_foto.required'           => 'Foto tempat usaha wajib diunggah.',
            '*.max'                        => 'Ukuran file berkas tidak boleh melebihi 2MB (2048 KB).',
            '*.mimes'                      => 'Format file yang diperbolehkan hanya PDF, JPG, JPEG, atau PNG.',
        ];
    }
}
