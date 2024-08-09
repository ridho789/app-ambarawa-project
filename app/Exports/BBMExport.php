<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;
use App\Models\Kendaraan;

class BBMExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $bbm;
    protected $rangeDate;

    public function __construct($mode, $bbm, $rangeDate)
    {
        $this->mode = $mode;
        $this->bbm = $bbm;
        $this->rangeDate = $rangeDate;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            // Kelompokkan data berdasarkan tahun dan bulan
            $groupedData = $this->bbm->groupBy(function ($item) {
                $date = Carbon::parse($item->tanggal);
                return $date->format('Y-m');
            });

            $sheets = [];
            foreach ($groupedData as $period => $data) {
                $sheets[] = new class($period, $data) implements FromCollection, WithHeadings, WithStyles
                {
                    protected $period;
                    protected $bbm;

                    public function __construct($period, $bbm)
                    {
                        $this->period = $period;
                        $this->bbm = $bbm;
                    }

                    public function collection()
                    {
                        $totalKeseluruhan = $this->bbm->sum(function ($item) {
                            return $item->tot_harga ?? 0;
                        });

                        
                        $data = $this->bbm->map(function ($item) {
                            $kendaraan = Kendaraan::find($item->id_kendaraan);
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $tot_harga = 'Rp ' . number_format($item->tot_harga ?? 0, 0, ',', '.');

                            return [
                                'nama' => $item->nama,
                                'tanggal' => $item->tanggal,
                                'nopol' => $kendaraan->nopol ?? '-',
                                'jns_mobil' => $kendaraan->merk ?? '-',
                                'jns_bbm' => $kendaraan->jns_bbm ?? '-',
                                'liter' => $item->liter ? $item->liter : '-',
                                'km_awal' => $item->km_awal ? $item->km_awal : '-',
                                'km_isi' => $item->km_isi ? $item->km_isi : '-',
                                'km_akhir' => $item->km_akhir ? $item->km_akhir : '-',
                                'km_ltr' => $item->km_ltr ? $item->km_ltr : '-',
                                'harga' => $harga ? $harga : '-',
                                'ket' => $item->ket ?? '-',
                                'tot_km' => $item->tot_km ? $item->tot_km : '-',
                                'tot_harga' => $tot_harga
                            ];
                        });

                        // Menambahkan baris total keseluruhan
                        $data->push([
                            'nama' => 'Total Keseluruhan',
                            'tanggal' => '',
                            'nopol' => '',
                            'jns_mobil' => '',
                            'jns_bbm' => '',
                            'liter' => '',
                            'km_awal' => '',
                            'km_isi' => '',
                            'km_akhir' => '',
                            'km_ltr' => '',
                            'harga' => '',
                            'ket' => '',
                            'tot_km' => '',
                            'tot_harga' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.')
                        ]);

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            ['Pengeluaran BBM ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                            ['Nama',
                            'Tanggal',
                            'Nopol / Kode Unit',
                            'Jenis Mobil',
                            'Jenis BBM',
                            'Liter',
                            'KM Awal',
                            'KM Pengisian',
                            'KM Akhir',
                            'KM/Liter',
                            'Harga',
                            'Keterangan',
                            'Total KM',
                            'Total Harga',]
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        // Header
                        $sheet->mergeCells("A1:N1");
                        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:N2')->getFont()->setBold(true);
                        $sheet->getStyle('A:N')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle('BBM Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                        // Mengatur kolom L
                        $sheet->getColumnDimension('L')->setWidth(50);
                        $sheet->getStyle('L3:L' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                        // Menyesuaikan lebar kolom lainnya
                        foreach (range('A', 'N') as $column) {
                            if ($column !== 'L') {
                                $sheet->getColumnDimension($column)->setAutoSize(true);
                                $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                            }
                        }

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:M$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:M$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("N$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:N$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->bbm, $this->rangeDate) implements FromCollection, WithHeadings, WithStyles
            {
                protected $bbm;
                protected $rangeDate;

                public function __construct($mode, $bbm, $rangeDate)
                {
                    $this->bbm = $bbm;
                    $this->rangeDate = $rangeDate;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->bbm->sum(function ($item) {
                        return $item->tot_harga ?? 0;
                    });

                    $data = $this->bbm->map(function ($item) {
                        $kendaraan = Kendaraan::find($item->id_kendaraan);
                        $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                        $tot_harga = 'Rp ' . number_format($item->tot_harga ?? 0, 0, ',', '.');

                        return [
                            'nama' => $item->nama,
                            'tanggal' => $item->tanggal,
                            'nopol' => $kendaraan->nopol ?? '-',
                            'jns_mobil' => $kendaraan->merk ?? '-',
                            'jns_bbm' => $kendaraan->jns_bbm ?? '-',
                            'liter' => $item->liter ? $item->liter : '-',
                            'km_awal' => $item->km_awal ? $item->km_awal : '-',
                            'km_isi' => $item->km_isi ? $item->km_isi : '-',
                            'km_akhir' => $item->km_akhir ? $item->km_akhir : '-',
                            'km_ltr' => $item->km_ltr ? $item->km_ltr : '-',
                            'harga' => $harga ? $harga : '-',
                            'ket' => $item->ket ?? '-',
                            'tot_km' => $item->tot_km ? $item->tot_km : '-',
                            'tot_harga' => $tot_harga
                        ];
                    });

                    // Menambahkan baris total keseluruhan
                    $data->push([
                        'nama' => 'Total Keseluruhan',
                        'tanggal' => '',
                        'nopol' => '',
                        'jns_mobil' => '',
                        'jns_bbm' => '',
                        'liter' => '',
                        'km_awal' => '',
                        'km_isi' => '',
                        'km_akhir' => '',
                        'km_ltr' => '',
                        'harga' => '',
                        'ket' => '',
                        'tot_km' => '',
                        'tot_harga' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.')
                    ]);

                    return $data;
                }

                public function headings(): array
                {
                    return [
                        ['Pengeluaran BBM ' . $this->rangeDate],
                        ['Nama',
                        'Tanggal',
                        'Nopol / Kode Unit',
                        'Jenis Mobil',
                        'Jenis BBM',
                        'Liter',
                        'KM Awal',
                        'KM Pengisian',
                        'KM Akhir',
                        'KM/Liter',
                        'Harga',
                        'Keterangan',
                        'Total KM',
                        'Total Harga',]
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Header
                    $sheet->mergeCells("A1:N1");
                    $sheet->getStyle('A1:N1')->getFont()->setBold(true);

                    $sheet->getStyle('A2:N2')->getFont()->setBold(true);
                    $sheet->getStyle('A:N')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran BBM');

                    // Mengatur kolom L
                    $sheet->getColumnDimension('L')->setWidth(50);
                    $sheet->getStyle('L3:L' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                    // Menyesuaikan lebar kolom lainnya
                    foreach (range('A', 'N') as $column) {
                        if ($column !== 'L') {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
                            $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                        }
                    }

                    // Menyempurnakan styling baris total
                    $totalRowIndex = $sheet->getHighestRow();
                    $sheet->mergeCells("A$totalRowIndex:M$totalRowIndex");
                    $sheet->getStyle("A$totalRowIndex:M$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("N$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("A$totalRowIndex:N$totalRowIndex")->getAlignment()->setHorizontal('center');
                }
            }
        ];
    }
}
