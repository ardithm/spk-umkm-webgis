<?php

namespace App\Exports;

use App\Models\HasilPerhitungan;
use App\Models\Proses;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * RekapitulasiExport — Export Data Hasil Seleksi SPK UMKM ke Excel (.xlsx)
 *
 * Mengimplementasikan:
 * - WithCustomValueBinder: Mengikat NIK dan No. Telepon sebagai TYPE_STRING
 *   agar tidak terjadi notasi ilmiah (scientific notation) seperti 6.37E+15.
 * - WithStyles: Styling header tabel resmi warna brand, text bold, dan grid border.
 * - ShouldAutoSize: Kolom otomatis menyesuaikan lebar isi.
 */
class RekapitulasiExport extends DefaultValueBinder implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithCustomValueBinder,
    WithTitle
{
    private int $rowNumber = 0;
    private ?Proses $proses = null;

    public function __construct(
        private readonly int $idProses,
        private readonly ?string $statusSeleksi = null
    ) {
        $this->proses = Proses::find($this->idProses);
    }

    /**
     * Kustomisasi pengikatan nilai sel agar string panjang (NIK/Telepon)
     * tidak dikonversi menjadi format ilmiah oleh Excel.
     */
    public function bindValue(Cell $cell, $value): bool
    {
        // Kolom D = NIK, Kolom F = No Telepon, atau nilai angka >= 10 digit
        if (in_array($cell->getColumn(), ['D', 'F']) || (is_string($value) && is_numeric($value) && strlen($value) >= 10)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * Mengambil kumpulan data hasil perhitungan yang sesuai filter.
     */
    public function collection(): Collection
    {
        $query = HasilPerhitungan::where('id_proses', $this->idProses)
            ->with(['pengajuan.umkm'])
            ->orderBy('ranking');

        if (!empty($this->statusSeleksi) && in_array($this->statusSeleksi, [
            HasilPerhitungan::STATUS_DITERIMA,
            HasilPerhitungan::STATUS_CADANGAN,
            HasilPerhitungan::STATUS_TIDAK_DITERIMA,
        ])) {
            $query->where('status_seleksi', $this->statusSeleksi);
        }

        return $query->get();
    }

    /**
     * Definisi Header Tabel Spreadsheet
     */
    public function headings(): array
    {
        return [
            'NO',
            'PERINGKAT (RANK)',
            'NAMA PEMILIK',
            'NIK',
            'NAMA USAHA (UMKM)',
            'NO. TELEPON / WA',
            'LEGALITAS / PERIZINAN',
            'ALAMAT DOMISILI USAHA',
            'OMZET TAHUNAN (RP)',
            'NILAI ASET (RP)',
            'TENAGA KERJA',
            'SKOR NCF (60%)',
            'SKOR NSF (40%)',
            'NILAI AKHIR SPK',
            'STATUS KELAYAKAN',
        ];
    }

    /**
     * Mapping baris data dari entitas HasilPerhitungan
     *
     * @param HasilPerhitungan $hasil
     */
    public function map($hasil): array
    {
        $this->rowNumber++;
        $pengajuan = $hasil->pengajuan;
        $umkm = $pengajuan?->umkm;

        // Label status kelayakan
        $statusLabel = match ($hasil->status_seleksi) {
            HasilPerhitungan::STATUS_DITERIMA => 'DITERIMA (LOLOS)',
            HasilPerhitungan::STATUS_CADANGAN => 'CADANGAN (LUAR KUOTA)',
            HasilPerhitungan::STATUS_TIDAK_DITERIMA => 'TIDAK DITERIMA',
            default => strtoupper(str_replace('_', ' ', $hasil->status_seleksi ?? '-')),
        };

        // Format izin usaha
        $izinLabel = match ($pengajuan?->status_perizinan) {
            'nib_lengkap' => 'NIB Lengkap',
            'nib' => 'NIB Standar',
            'sku_kelurahan' => 'SKU Kelurahan',
            'sku_rt' => 'SKU RT/RW',
            'belum_ada' => 'Belum Ada',
            default => strtoupper(str_replace('_', ' ', $pengajuan?->status_perizinan ?? '-')),
        };

        return [
            $this->rowNumber,
            $hasil->ranking ?? '-',
            $umkm?->nama_pemilik ?? '-',
            $umkm?->nik ? (string) $umkm->nik : '-',
            $umkm?->nama_umkm ?? '-',
            $umkm?->no_telepon ? (string) $umkm->no_telepon : '-',
            $izinLabel,
            $umkm?->alamat ?? '-',
            $pengajuan?->omzet_tahunan ? (float) $pengajuan->omzet_tahunan : 0,
            $pengajuan?->aset ? (float) $pengajuan->aset : 0,
            $pengajuan?->jumlah_tenaga_kerja ? (int) $pengajuan->jumlah_tenaga_kerja : 0,
            (float) $hasil->nilai_ncf,
            (float) $hasil->nilai_nsf,
            (float) $hasil->nilai_akhir,
            $statusLabel,
        ];
    }

    /**
     * Styling spreadsheet: warna header, font bold, border sel, dan alignment
     */
    public function styles(Worksheet $sheet): array
    {
        $totalRows = $this->rowNumber + 1; // +1 untuk header row

        // Styling Baris Header (Baris 1)
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5E3B8A'], // Brand purple formal
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Jika ada baris data
        if ($totalRows > 1) {
            // Border tipis ke seluruh grid tabel
            $sheet->getStyle("A1:O{$totalRows}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ]);

            // Alignment kolom
            $sheet->getStyle("A2:B{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D2:D{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:F{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K2:N{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("O2:O{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Number formatting untuk omzet & aset
            $sheet->getStyle("I2:J{$totalRows}")->getNumberFormat()->setFormatCode('#,##0');
            // Number formatting untuk skor
            $sheet->getStyle("L2:N{$totalRows}")->getNumberFormat()->setFormatCode('0.0000');
        }

        return [];
    }

    /**
     * Nama Tab Sheet
     */
    public function title(): string
    {
        $periodeSlug = $this->proses ? substr($this->proses->periode, 0, 25) : 'Rekapitulasi';
        return preg_replace('/[^A-Za-z0-9 _-]/', '', $periodeSlug);
    }
}
