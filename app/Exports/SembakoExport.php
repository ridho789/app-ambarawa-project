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

class SembakoExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $sembako;

    public function __construct($mode, $sembako)
    {
        $this->mode = $mode;
        $this->sembako = $sembako;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            // Kelompokkan data berdasarkan tahun dan bulan
            $groupedData = $this->sembako->groupBy(function ($item) {
                $date = Carbon::parse($item->tanggal);
                return $date->format('Y-m');
            });

            $sheets = [];
            foreach ($groupedData as $period => $data) {
                $sheets[] = new class($period, $data) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
                {
                    protected $period;
                    protected $sembako;

                    public function __construct($period, $sembako)
                    {
                        $this->period = $period;
                        $this->sembako = $sembako;
                    }

                    public function collection()
                    {
                        $totalKeseluruhan = $this->sembako->sum(function ($item) {
                            return $item->total ?? 0;
                        });

                        $data = $this->sembako->map(function ($item) {
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                            return [
                                'tanggal' => $item->tanggal,
                                'nama' => $item->nama,
                                'jumlah' => $item->qty,
                                'satuan' => $item->unit,
                                'harga' => $harga,
                                'total' => $total,
                            ];
                        });

                        // Menambahkan baris total keseluruhan
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'nama' => '',
                            'jumlah' => '',
                            'satuan' => '',
                            'harga' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            'Tanggal',
                            'Nama Barang',
                            'Jumlah',
                            'Satuan',
                            'Harga',
                            'Total'
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                        $sheet->getStyle('A:F')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle('Sembako Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:E$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:E$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("F$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:F$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->sembako) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $sembako;

                public function __construct($mode, $sembako)
                {
                    $this->sembako = $sembako;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->sembako->sum(function ($item) {
                        return $item->total ?? 0;
                    });

                    $data = $this->sembako->map(function ($item) {
                        $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                        return [
                            'tanggal' => $item->tanggal,
                            'nama' => $item->nama,
                            'jumlah' => $item->qty,
                            'satuan' => $item->unit,
                            'harga' => $harga,
                            'total' => $total,
                        ];
                    });

                    // Menambahkan baris total keseluruhan
                    $data->push([
                        'tanggal' => 'Total Keseluruhan',
                        'nama' => '',
                        'jumlah' => '',
                        'satuan' => '',
                        'harga' => '',
                        'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                    ]);

                    return $data;
                }

                public function headings(): array
                {
                    return [
                        'Tanggal',
                        'Nama Barang',
                        'Jumlah',
                        'Satuan',
                        'Harga',
                        'Total'
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                    $sheet->getStyle('A:F')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran Sembako');

                    // Menyempurnakan styling baris total
                    $totalRowIndex = $sheet->getHighestRow();
                    $sheet->mergeCells("A$totalRowIndex:E$totalRowIndex");
                    $sheet->getStyle("A$totalRowIndex:E$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("F$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("A$totalRowIndex:F$totalRowIndex")->getAlignment()->setHorizontal('center');
                }
            }
        ];
    }
}