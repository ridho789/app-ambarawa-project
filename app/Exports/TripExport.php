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

class TripExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $trip;

    public function __construct($mode, $trip)
    {
        $this->mode = $mode;
        $this->trip = $trip;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            // Kelompokkan data berdasarkan tahun dan bulan
            $groupedData = $this->trip->groupBy(function ($item) {
                $date = Carbon::parse($item->tanggal);
                return $date->format('Y-m');
            });

            $sheets = [];
            foreach ($groupedData as $period => $data) {
                $sheets[] = new class($period, $data) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
                {
                    protected $period;
                    protected $trip;

                    public function __construct($period, $trip)
                    {
                        $this->period = $period;
                        $this->trip = $trip;
                    }

                    public function collection()
                    {
                        $totalKeseluruhan = $this->trip->sum(function ($item) {
                            return $item->total ?? 0;
                        });

                        $data = $this->trip->map(function ($item) {
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                            return [
                                'tanggal' => $item->tanggal,
                                'kota' => $item->kota,
                                'ket' => $item->ket,
                                'uraian' => $item->uraian,
                                'nopol' => $item->nopol,
                                'merk' => $item->merk,
                                'qty' => $item->qty,
                                'unit' => $item->unit,
                                'km_awal' => $item->km_awal,
                                'km_isi' => $item->km_isi,
                                'km_akhir' => $item->km_akhir,
                                'km_ltr' => $item->km_ltr,
                                'harga' => $harga,
                                'total' => $total
                            ];
                        });

                        // Menambahkan baris total keseluruhan
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'kota' => '',
                            'ket' => '',
                            'uraian' => '',
                            'nopol' => '',
                            'merk' => '',
                            'qty' => '',
                            'unit' => '',
                            'km_awal' => '',
                            'km_isi' => '',
                            'km_akhir' => '',
                            'km_ltr' => '',
                            'harga' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.')
                        ]);

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            'Tanggal',
                            'Kota',
                            'Keterangan',
                            'Uraian',
                            'Nopol',
                            'Merk',
                            'Jumlah',
                            'Satuan',
                            'KM Awal',
                            'KM Pengisian',
                            'KM Akhir',
                            'KM/Liter',
                            'Harga',
                            'Total Harga',
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
                        $sheet->getStyle('A:M')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle('Trip Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:L$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:L$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("M$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:M$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->trip) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $trip;

                public function __construct($mode, $trip)
                {
                    $this->trip = $trip;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->trip->sum(function ($item) {
                        return $item->total ?? 0;
                    });

                    $data = $this->trip->map(function ($item) {
                        $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                        return [
                            'tanggal' => $item->tanggal,
                            'kota' => $item->kota,
                            'ket' => $item->ket,
                            'uraian' => $item->uraian,
                            'nopol' => $item->nopol,
                            'merk' => $item->merk,
                            'qty' => $item->qty,
                            'unit' => $item->unit,
                            'km_awal' => $item->km_awal,
                            'km_isi' => $item->km_isi,
                            'km_akhir' => $item->km_akhir,
                            'km_ltr' => $item->km_ltr,
                            'harga' => $harga,
                            'total' => $total
                        ];
                    });

                    // Menambahkan baris total keseluruhan
                    $data->push([
                        'tanggal' => 'Total Keseluruhan',
                        'kota' => '',
                        'ket' => '',
                        'uraian' => '',
                        'nopol' => '',
                        'merk' => '',
                        'qty' => '',
                        'unit' => '',
                        'km_awal' => '',
                        'km_isi' => '',
                        'km_akhir' => '',
                        'km_ltr' => '',
                        'harga' => '',
                        'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.')
                    ]);

                    return $data;
                }

                public function headings(): array
                {
                    return [
                        'Tanggal',
                        'Kota',
                        'Keterangan',
                        'Uraian',
                        'Nopol',
                        'Merk',
                        'Jumlah',
                        'Satuan',
                        'KM Awal',
                        'KM Pengisian',
                        'KM Akhir',
                        'KM/Liter',
                        'Harga',
                        'Total Harga',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->getStyle('A1:M1')->getFont()->setBold(true);
                    $sheet->getStyle('A:M')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran Trip');

                    // Menyempurnakan styling baris total
                    $totalRowIndex = $sheet->getHighestRow();
                    $sheet->mergeCells("A$totalRowIndex:L$totalRowIndex");
                    $sheet->getStyle("A$totalRowIndex:L$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("M$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("A$totalRowIndex:M$totalRowIndex")->getAlignment()->setHorizontal('center');
                }
            }
        ];
    }
}
