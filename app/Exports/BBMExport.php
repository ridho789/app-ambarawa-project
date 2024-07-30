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

class BBMExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $bbm;

    public function __construct($mode, $bbm)
    {
        $this->mode = $mode;
        $this->bbm = $bbm;
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
                $sheets[] = new class($period, $data) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $tot_harga = 'Rp ' . number_format($item->tot_harga ?? 0, 0, ',', '.');

                            return [
                                'nama' => $item->nama,
                                'tanggal' => $item->tanggal,
                                'kode_unit' => $item->kode_unit,
                                'nopol' => $item->nopol,
                                'jns_mobil' => $item->jns_mobil,
                                'jns_bbm' => $item->jns_bbm,
                                'liter' => $item->liter,
                                'km_awal' => $item->km_awal,
                                'km_isi' => $item->km_isi,
                                'km_akhir' => $item->km_akhir,
                                'km_ltr' => $item->km_ltr,
                                'harga' => $harga,
                                'ket' => $item->ket,
                                'tot_km' => $item->tot_km,
                                'tot_harga' => $tot_harga
                            ];
                        });

                        // Menambahkan baris total keseluruhan
                        $data->push([
                            'nama' => 'Total Keseluruhan',
                            'tanggal' => '',
                            'kode_unit' => '',
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
                            'Nama',
                            'Tanggal',
                            'Kode Unit',
                            'Nopol',
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
                            'Total Harga',
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
                        $sheet->getStyle('A:O')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle('BBM Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:N$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:N$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("O$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->bbm) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $bbm;

                public function __construct($mode, $bbm)
                {
                    $this->bbm = $bbm;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->bbm->sum(function ($item) {
                        return $item->tot_harga ?? 0;
                    });

                    $data = $this->bbm->map(function ($item) {
                        $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                        $tot_harga = 'Rp ' . number_format($item->tot_harga ?? 0, 0, ',', '.');

                        return [
                            'nama' => $item->nama,
                            'tanggal' => $item->tanggal,
                            'kode_unit' => $item->kode_unit,
                            'nopol' => $item->nopol,
                            'jns_mobil' => $item->jns_mobil,
                            'jns_bbm' => $item->jns_bbm,
                            'liter' => $item->liter,
                            'km_awal' => $item->km_awal,
                            'km_isi' => $item->km_isi,
                            'km_akhir' => $item->km_akhir,
                            'km_ltr' => $item->km_ltr,
                            'harga' => $harga,
                            'ket' => $item->ket,
                            'tot_km' => $item->tot_km,
                            'tot_harga' => $tot_harga
                        ];
                    });

                    // Menambahkan baris total keseluruhan
                    $data->push([
                        'nama' => 'Total Keseluruhan',
                        'tanggal' => '',
                        'kode_unit' => '',
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
                        'Nama',
                        'Tanggal',
                        'Kode Unit',
                        'Nopol',
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
                        'Total Harga',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->getStyle('A1:O1')->getFont()->setBold(true);
                    $sheet->getStyle('A:O')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran BBM');

                    // Menyempurnakan styling baris total
                    $totalRowIndex = $sheet->getHighestRow();
                    $sheet->mergeCells("A$totalRowIndex:N$totalRowIndex");
                    $sheet->getStyle("A$totalRowIndex:N$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("O$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getAlignment()->setHorizontal('center');
                }
            }
        ];
    }
}
