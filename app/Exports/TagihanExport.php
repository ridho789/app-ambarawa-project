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

class TagihanExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $tagihan;
    protected $infoTagihan;

    public function __construct($mode, $tagihan, $infoTagihan)
    {
        $this->mode = $mode;
        $this->tagihan = $tagihan;
        $this->infoTagihan = $infoTagihan;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            $groupedData = $this->tagihan->groupBy(function ($item) {
                $date = Carbon::parse($item->tgl_order);
                return $date->format('Y-m');
            });

            $sortedData = $groupedData->map(function ($items) {
                return $items->sortBy('lokasi');
            });

            $sheets = [];
            foreach ($sortedData as $period => $data) {
                $sheets[] = new class($period, $data, $this->infoTagihan) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
                {
                    protected $period;
                    protected $tagihan;
                    protected $infoTagihan;

                    public function __construct($period, $tagihan, $infoTagihan)
                    {
                        $this->period = $period;
                        $this->tagihan = $tagihan;
                        $this->infoTagihan = $infoTagihan;
                    }

                    public function collection()
                    {
                        $totalKeseluruhan = $this->tagihan->sum(function ($item) {
                            return $item->total ?? 0;
                        });

                        $data = $this->tagihan->map(function ($item) {
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                            return [
                                'lokasi' => $item->lokasi,
                                'pemesan' => $item->pemesan,
                                'tgl_order' => $item->tgl_order,
                                'tgl_invoice' => $item->tgl_invoice,
                                'no_inventaris' => $item->no_inventaris,
                                'nama' => $item->nama,
                                'kategori' => $item->kategori,
                                'dipakai_untuk' => $item->dipakai_untuk,
                                'masa_pakai' => $item->masa_pakai,
                                'jml' => $item->jml,
                                'unit' => $item->unit,
                                'harga' => $harga,
                                'toko' => $item->toko,
                                'total' => $total,
                            ];
                        });

                        // Menambahkan baris total keseluruhan
                        $data->push([
                            'lokasi' => 'Total Keseluruhan',
                            'pemesan' => '',
                            'tgl_order' => '',
                            'tgl_invoice' => '',
                            'no_inventaris' => '',
                            'nama' => '',
                            'kategori' => '',
                            'dipakai_untuk' => '',
                            'masa_pakai' => '',
                            'jml' => '',
                            'unit' => '',
                            'harga' => '',
                            'toko' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            'Lokasi',
                            'Pemesan',
                            'Tgl. Order',
                            'Tgl. Invoice',
                            'No. Inventaris',
                            'Nama Barang',
                            'Kategori',
                            'Keperluan',
                            'Masa Pakai',
                            'Jumlah',
                            'Satuan',
                            'Harga',
                            'Toko',
                            'Total'
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
                        $sheet->getStyle('A:N')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan . ' Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

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
            new class('All Data', $this->tagihan, $this->infoTagihan) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $tagihan;
                protected $infoTagihan;

                public function __construct($mode, $tagihan, $infoTagihan)
                {
                    $this->tagihan = $tagihan;
                    $this->infoTagihan = $infoTagihan;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->tagihan->sum(function ($item) {
                        return $item->total ?? 0;
                    });

                    $data = $this->tagihan->map(function ($item) {
                        $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                        return [
                            'lokasi' => $item->lokasi,
                            'pemesan' => $item->pemesan,
                            'tgl_order' => $item->tgl_order,
                            'tgl_invoice' => $item->tgl_invoice,
                            'no_inventaris' => $item->no_inventaris,
                            'nama' => $item->nama,
                            'kategori' => $item->kategori,
                            'dipakai_untuk' => $item->dipakai_untuk,
                            'masa_pakai' => $item->masa_pakai,
                            'jml' => $item->jml,
                            'unit' => $item->unit,
                            'harga' => $harga,
                            'toko' => $item->toko,
                            'total' => $total,
                        ];
                    });

                    // Menambahkan baris total keseluruhan
                    $data->push([
                        'lokasi' => 'Total Keseluruhan',
                        'pemesan' => '',
                        'tgl_order' => '',
                        'tgl_invoice' => '',
                        'no_inventaris' => '',
                        'nama' => '',
                        'kategori' => '',
                        'dipakai_untuk' => '',
                        'masa_pakai' => '',
                        'jml' => '',
                        'unit' => '',
                        'harga' => '',
                        'toko' => '',
                        'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                    ]);

                    return $data;
                }

                public function headings(): array
                {
                    return [
                        'Lokasi',
                        'Pemesan',
                        'Tgl. Order',
                        'Tgl. Invoice',
                        'No. Inventaris',
                        'Nama Barang',
                        'Kategori',
                        'Keperluan',
                        'Masa Pakai',
                        'Jumlah',
                        'Satuan',
                        'Harga',
                        'Toko',
                        'Total'
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->getStyle('A1:N1')->getFont()->setBold(true);
                    $sheet->getStyle('A:N')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran ' . $this->infoTagihan);

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
