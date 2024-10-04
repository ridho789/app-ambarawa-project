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

class TripExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $trip;
    protected $rangeDate;

    public function __construct($mode, $trip, $rangeDate)
    {
        $this->mode = $mode;
        $this->trip = $trip;
        $this->rangeDate = $rangeDate;
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
                $sheets[] = new class($period, $data) implements FromCollection, WithHeadings, WithStyles
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
                            $kendaraan = Kendaraan::find($item->id_kendaraan);
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                            return [
                                'tanggal' => $item->tanggal,
                                'kota' => $item->kota,
                                'nama' => $item->nama,
                                'ket' => $item->ket,
                                'uraian' => $item->uraian,
                                'nopol' => $kendaraan->nopol ?? '-',
                                'merk' => $kendaraan->merk ?? '-',
                                'qty' => $item->qty ?? '-',
                                'unit' => $item->unit ?? '-',
                                'km_awal' => $item->km_awal ? $item->km_awal : '-',
                                'km_isi_seb' => $item->km_isi_seb ? $item->km_isi_seb : '-',
                                'km_isi_sek' => $item->km_isi_sek ? $item->km_isi_sek : '-',
                                'km_akhir' => $item->km_akhir ? $item->km_akhir : '-',
                                'km_ltr' => $item->km_ltr ? $item->km_ltr : '-',
                                'harga' => $harga ? $harga : '-',
                                'total' => $total
                            ];
                        });

                        // Menambahkan baris total keseluruhan
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'kota' => '',
                            'nama' => '',
                            'ket' => '',
                            'uraian' => '',
                            'nopol' => '',
                            'merk' => '',
                            'qty' => '',
                            'unit' => '',
                            'km_awal' => '',
                            'km_isi_seb' => '',
                            'km_isi_sek' => '',
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
                            ['Pengeluaran Trip ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                            ['Tanggal',
                            'Kota',
                            'Nama',
                            'Keterangan',
                            'Uraian',
                            'Nopol / Kode Unit',
                            'Merk',
                            'Jumlah',
                            'Satuan',
                            'KM Awal',
                            'KM Pengisian (Sebelumnya)',
                            'KM Pengisian (Saat ini)',
                            'KM Akhir',
                            'KM/Liter',
                            'Harga',
                            'Total Harga',]
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        // Header
                        $sheet->mergeCells("A1:P1");
                        $sheet->getStyle('A1:P1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:P2')->getFont()->setBold(true);
                        $sheet->getStyle('A:P')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle('Trip Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                        // Mengatur kolom
                        $sheet->getColumnDimension('K')->setWidth(15);
                        $sheet->getStyle('K2:K2')->getAlignment()->setWrapText(true);

                        $sheet->getColumnDimension('L')->setWidth(15);
                        $sheet->getStyle('L2:L2')->getAlignment()->setWrapText(true);

                        $sheet->getColumnDimension('E')->setWidth(50);
                        $sheet->getStyle('E3:E' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                        // Menyesuaikan lebar kolom lainnya
                        foreach (range('A', 'P') as $column) {
                            if (!in_array($column, ['E', 'K', 'L'])) {
                                $sheet->getColumnDimension($column)->setAutoSize(true);
                            }
                            $sheet->getStyle($column . '2:' . $column . '2') ->getAlignment()->setHorizontal('center')->setVertical('center');
                            $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())
                                  ->getAlignment()
                                  ->setHorizontal('center')
                                  ->setVertical('center');
                        }

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:O$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("P$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->trip, $this->rangeDate) implements FromCollection, WithHeadings, WithStyles
            {
                protected $trip;
                protected $rangeDate;

                public function __construct($mode, $trip, $rangeDate)
                {
                    $this->trip = $trip;
                    $this->rangeDate = $rangeDate;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->trip->sum(function ($item) {
                        return $item->total ?? 0;
                    });

                    $data = $this->trip->map(function ($item) {
                        $kendaraan = Kendaraan::find($item->id_kendaraan);
                        $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                        return [
                            'tanggal' => $item->tanggal,
                            'kota' => $item->kota,
                            'nama' => $item->nama,
                            'ket' => $item->ket,
                            'uraian' => $item->uraian,
                            'nopol' => $kendaraan->nopol ?? '-',
                            'merk' => $kendaraan->merk ?? '-',
                            'qty' => $item->qty ?? '-',
                            'unit' => $item->unit ?? '-',
                            'km_awal' => $item->km_awal ? $item->km_awal : '-',
                            'km_isi_seb' => $item->km_isi_seb ? $item->km_isi_seb : '-',
                            'km_isi_sek' => $item->km_isi_sek ? $item->km_isi_sek : '-',
                            'km_akhir' => $item->km_akhir ? $item->km_akhir : '-',
                            'km_ltr' => $item->km_ltr ? $item->km_ltr : '-',
                            'harga' => $harga ? $harga : '-',
                            'total' => $total
                        ];
                    });

                    // Menambahkan baris total keseluruhan
                    $data->push([
                        'tanggal' => 'Total Keseluruhan',
                        'kota' => '',
                        'nama' => '',
                        'ket' => '',
                        'uraian' => '',
                        'nopol' => '',
                        'merk' => '',
                        'qty' => '',
                        'unit' => '',
                        'km_awal' => '',
                        'km_isi_seb' => '',
                        'km_isi_sek' => '',
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
                        ['Pengeluaran Trip ' . $this->rangeDate],
                        ['Tanggal',
                        'Kota',
                        'Nama',
                        'Keterangan',
                        'Uraian',
                        'Nopol / Kode Unit',
                        'Merk',
                        'Jumlah',
                        'Satuan',
                        'KM Awal',
                        'KM Pengisian (Sebelumnya)',
                        'KM Pengisian (Saat ini)',
                        'KM Akhir',
                        'KM/Liter',
                        'Harga',
                        'Total Harga',]
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Header
                    $sheet->mergeCells("A1:P1");
                    $sheet->getStyle('A1:P1')->getFont()->setBold(true);

                    $sheet->getStyle('A2:P2')->getFont()->setBold(true);
                    $sheet->getStyle('A:P')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran Trip');

                    // Mengatur kolom
                    $sheet->getColumnDimension('K')->setWidth(15);
                    $sheet->getStyle('K2:K2')->getAlignment()->setWrapText(true);

                    $sheet->getColumnDimension('L')->setWidth(15);
                    $sheet->getStyle('L2:L2')->getAlignment()->setWrapText(true);
                    
                    $sheet->getColumnDimension('E')->setWidth(50);
                    $sheet->getStyle('E3:E' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                    // Menyesuaikan lebar kolom lainnya
                    foreach (range('A', 'P') as $column) {
                        if (!in_array($column, ['E', 'K', 'L'])) {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
                        }
                        $sheet->getStyle($column . '2:' . $column . '2') ->getAlignment()->setHorizontal('center')->setVertical('center');
                        $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())
                              ->getAlignment()
                              ->setHorizontal('center')
                              ->setVertical('center');
                    }

                    // Menyempurnakan styling baris total
                    $totalRowIndex = $sheet->getHighestRow();
                    $sheet->mergeCells("A$totalRowIndex:O$totalRowIndex");
                    $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("P$totalRowIndex")->getFont()->setBold(true);
                    $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getAlignment()->setHorizontal('center');
                }
            }
        ];
    }
}
