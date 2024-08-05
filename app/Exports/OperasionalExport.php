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

class OperasionalExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $tagihan;
    protected $infoTagihan;
    protected $metode_pembelian;
    protected $rangeDate;

    public function __construct($mode, $tagihan, $infoTagihan, $metode_pembelian, $rangeDate)
    {
        $this->mode = $mode;
        $this->tagihan = $tagihan;
        $this->infoTagihan = $infoTagihan;
        $this->metode_pembelian = $metode_pembelian;
        $this->rangeDate = $rangeDate;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            $groupedData = $this->tagihan->groupBy(function ($item) {
                $date = Carbon::parse($item->tanggal);
                return $date->format('Y-m');
            });

            $sheets = [];
            foreach ($groupedData as $period => $data) {
                $sheets[] = new class($period, $data, $this->infoTagihan, $this->metode_pembelian) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
                {
                    protected $period;
                    protected $tagihan;
                    protected $infoTagihan;
                    protected $metode_pembelian;

                    public function __construct($period, $tagihan, $infoTagihan, $metode_pembelian)
                    {
                        $this->period = $period;
                        $this->tagihan = $tagihan;
                        $this->infoTagihan = $infoTagihan;
                        $this->metode_pembelian = $metode_pembelian;
                    }

                    public function collection()
                    {
                        $totalKeseluruhan = $this->tagihan->sum(function ($item) {
                            return $item->total ?? 0;
                        });

                        $data = $this->tagihan->map(function ($item) {
                            $harga_toko = 'Rp ' . number_format($item->harga_toko ?? 0, 0, ',', '.');
                            $harga_onl = 'Rp ' . number_format($item->harga_onl ?? 0, 0, ',', '.');
                            $diskon = 'Rp ' . number_format($item->diskon ?? 0, 0, ',', '.');
                            $ongkir = 'Rp ' . number_format($item->ongkir ?? 0, 0, ',', '.');
                            $asuransi = 'Rp ' . number_format($item->asuransi ?? 0, 0, ',', '.');
                            $p_member = 'Rp ' . number_format($item->p_member ?? 0, 0, ',', '.');
                            $b_proteksi = 'Rp ' . number_format($item->b_proteksi ?? 0, 0, ',', '.');
                            $b_aplikasi = 'Rp ' . number_format($item->b_aplikasi ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                            if ($this->metode_pembelian == 'online') {
                                return [
                                    'tanggal' => $item->tanggal,
                                    'uraian' => $item->uraian,
                                    'deskripsi' => $item->deskripsi,
                                    'nama' => $item->nama,
                                    'qty' => $item->qty,
                                    'unit' => $item->unit,
                                    'diskon' => $diskon,
                                    'harga_onl' => $harga_onl,
                                    'ongkir' => $ongkir,
                                    'asuransi' => $asuransi,
                                    'b_proteksi' => $b_proteksi,
                                    'p_member' => $p_member,
                                    'b_aplikasi' => $b_aplikasi,
                                    'toko' => $item->toko,
                                    'total' => $total
                                ];
                            } 
                            
                            if ($this->metode_pembelian == 'offline') {
                                return [
                                    'tanggal' => $item->tanggal,
                                    'uraian' => $item->uraian,
                                    'deskripsi' => $item->deskripsi,
                                    'nama' => $item->nama,
                                    'qty' => $item->qty,
                                    'unit' => $item->unit,
                                    'harga_toko' => $harga_toko,
                                    'toko' => $item->toko,
                                    'total' => $total
                                ];
                            }

                            if ($this->metode_pembelian == 'online_dan_offline') {
                                return [
                                    'tanggal' => $item->tanggal,
                                    'uraian' => $item->uraian,
                                    'deskripsi' => $item->deskripsi,
                                    'nama' => $item->nama,
                                    'qty' => $item->qty,
                                    'unit' => $item->unit,
                                    'harga_toko' => $harga_toko,
                                    'harga_onl' => $harga_onl,
                                    'diskon' => $diskon,
                                    'ongkir' => $ongkir,
                                    'asuransi' => $asuransi,
                                    'b_proteksi' => $b_proteksi,
                                    'p_member' => $p_member,
                                    'b_aplikasi' => $b_aplikasi,
                                    'toko' => $item->toko,
                                    'total' => $total
                                ];

                            } 
                        });

                        if ($this->metode_pembelian == 'online') {
                            $data->push([
                                'tanggal' => 'Total Keseluruhan',
                                'uraian' => '',
                                'deskripsi' => '',
                                'nama' => '',
                                'qty' => '',
                                'unit' => '',
                                'diskon' => '',
                                'harga_onl' => '',
                                'ongkir' => '',
                                'asuransi' => '',
                                'b_proteksi' => '',
                                'p_member' => '',
                                'b_aplikasi' => '',
                                'toko' => '',
                                'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                            ]);
                        } 
                        
                        if ($this->metode_pembelian == 'offline') {
                            $data->push([
                                'tanggal' => 'Total Keseluruhan',
                                'uraian' => '',
                                'deskripsi' => '',
                                'nama' => '',
                                'qty' => '',
                                'unit' => '',
                                'harga_toko' => '',
                                'toko' => '',
                                'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                            ]);
                        }

                        if ($this->metode_pembelian == 'online_dan_offline') {
                            $data->push([
                                'tanggal' => 'Total Keseluruhan',
                                'uraian' => '',
                                'deskripsi' => '',
                                'nama' => '',
                                'qty' => '',
                                'unit' => '',
                                'harga_toko' => '',
                                'harga_onl' => '',
                                'diskon' => '',
                                'ongkir' => '',
                                'asuransi' => '',
                                'b_proteksi' => '',
                                'p_member' => '',
                                'b_aplikasi' => '',
                                'toko' => '',
                                'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                            ]);
                        } 

                        return $data;
                    }

                    public function headings(): array
                    {
                        if ($this->metode_pembelian == 'online') {
                            return [
                                ['Pengeluaran ' . $this->infoTagihan . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                                ['Tanggal',
                                'Uraian',
                                'Deskripsi',
                                'Nama Barang',
                                'Jumlah',
                                'Satuan',
                                'Diskon',
                                'Harga Online',
                                'Ongkir',
                                'Asuransi',
                                'Biaya Proteksi',
                                'Potongan Member',
                                'Biaya Aplikasi',
                                'Toko',
                                'Total',]
                            ];
                        } 
                        
                        if ($this->metode_pembelian == 'offline') {
                            return [
                                ['Pengeluaran ' . $this->infoTagihan . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                                ['Tanggal',
                                'Uraian',
                                'Deskripsi',
                                'Nama Barang',
                                'Jumlah',
                                'Satuan',
                                'Harga',
                                'Toko',
                                'Total',]
                            ];
                        }

                        if ($this->metode_pembelian == 'online_dan_offline') {
                            return [
                                ['Pengeluaran ' . $this->infoTagihan . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                                ['Tanggal',
                                'Uraian',
                                'Deskripsi',
                                'Nama Barang',
                                'Jumlah',
                                'Satuan',
                                'Harga Toko',
                                'Harga Online',
                                'Diskon',
                                'Ongkir',
                                'Asuransi',
                                'Biaya Proteksi',
                                'Potongan Member',
                                'Biaya Aplikasi',
                                'Toko',
                                'Total',]
                            ];
                        } 
                    }

                    public function styles(Worksheet $sheet)
                    {
                        if ($this->metode_pembelian == 'online') {
                            // Header
                            $sheet->mergeCells("A1:O1");
                            $sheet->getStyle('A1:O1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:O2')->getFont()->setBold(true);
                            $sheet->getStyle('A:O')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Online ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:N$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:N$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("O$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getAlignment()->setHorizontal('center');
                        } 
                        
                        if ($this->metode_pembelian == 'offline') {
                            // Header
                            $sheet->mergeCells("A1:I1");
                            $sheet->getStyle('A1:I1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:I2')->getFont()->setBold(true);
                            $sheet->getStyle('A:I')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:H$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:H$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("I$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:I$totalRowIndex")->getAlignment()->setHorizontal('center');
                        }

                        if ($this->metode_pembelian == 'online_dan_offline') {
                            // Header
                            $sheet->mergeCells("A1:P1");
                            $sheet->getStyle('A1:P1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:P2')->getFont()->setBold(true);
                            $sheet->getStyle('A:P')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Online dan Offline ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:O$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("P$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getAlignment()->setHorizontal('center');
                        }
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->tagihan, $this->infoTagihan, $this->metode_pembelian, $this->rangeDate) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $tagihan;
                protected $infoTagihan;
                protected $metode_pembelian;
                protected $rangeDate;

                public function __construct($mode, $tagihan, $infoTagihan, $metode_pembelian, $rangeDate)
                {
                    $this->tagihan = $tagihan;
                    $this->infoTagihan = $infoTagihan;
                    $this->metode_pembelian = $metode_pembelian;
                    $this->rangeDate = $rangeDate;
                }

                public function collection()
                {
                    $totalKeseluruhan = $this->tagihan->sum(function ($item) {
                        return $item->total ?? 0;
                    });

                    $data = $this->tagihan->map(function ($item) {
                        $harga_toko = 'Rp ' . number_format($item->harga_toko ?? 0, 0, ',', '.');
                        $harga_onl = 'Rp ' . number_format($item->harga_onl ?? 0, 0, ',', '.');
                        $diskon = 'Rp ' . number_format($item->diskon ?? 0, 0, ',', '.');
                        $ongkir = 'Rp ' . number_format($item->ongkir ?? 0, 0, ',', '.');
                        $asuransi = 'Rp ' . number_format($item->asuransi ?? 0, 0, ',', '.');
                        $p_member = 'Rp ' . number_format($item->p_member ?? 0, 0, ',', '.');
                        $b_proteksi = 'Rp ' . number_format($item->b_proteksi ?? 0, 0, ',', '.');
                        $b_aplikasi = 'Rp ' . number_format($item->b_aplikasi ?? 0, 0, ',', '.');
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                        if ($this->metode_pembelian == 'online') {
                            return [
                                'tanggal' => $item->tanggal,
                                'uraian' => $item->uraian,
                                'deskripsi' => $item->deskripsi,
                                'nama' => $item->nama,
                                'qty' => $item->qty,
                                'unit' => $item->unit,
                                'diskon' => $diskon,
                                'harga_onl' => $harga_onl,
                                'ongkir' => $ongkir,
                                'asuransi' => $asuransi,
                                'b_proteksi' => $b_proteksi,
                                'p_member' => $p_member,
                                'b_aplikasi' => $b_aplikasi,
                                'toko' => $item->toko,
                                'total' => $total
                            ];
                        } 
                        
                        if ($this->metode_pembelian == 'offline') {
                            return [
                                'tanggal' => $item->tanggal,
                                'uraian' => $item->uraian,
                                'deskripsi' => $item->deskripsi,
                                'nama' => $item->nama,
                                'qty' => $item->qty,
                                'unit' => $item->unit,
                                'harga_toko' => $harga_toko,
                                'toko' => $item->toko,
                                'total' => $total
                            ];
                        }

                        if ($this->metode_pembelian == 'online_dan_offline') {
                            return [
                                'tanggal' => $item->tanggal,
                                'uraian' => $item->uraian,
                                'deskripsi' => $item->deskripsi,
                                'nama' => $item->nama,
                                'qty' => $item->qty,
                                'unit' => $item->unit,
                                'harga_toko' => $harga_toko,
                                'harga_onl' => $harga_onl,
                                'diskon' => $diskon,
                                'ongkir' => $ongkir,
                                'asuransi' => $asuransi,
                                'b_proteksi' => $b_proteksi,
                                'p_member' => $p_member,
                                'b_aplikasi' => $b_aplikasi,
                                'toko' => $item->toko,
                                'total' => $total
                            ];

                        } 
                    });

                    // Menambahkan baris total keseluruhan
                    if ($this->metode_pembelian == 'online') {
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'uraian' => '',
                            'deskripsi' => '',
                            'nama' => '',
                            'qty' => '',
                            'unit' => '',
                            'diskon' => '',
                            'harga_onl' => '',
                            'ongkir' => '',
                            'asuransi' => '',
                            'b_proteksi' => '',
                            'p_member' => '',
                            'b_aplikasi' => '',
                            'toko' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);
                    } 
                    
                    if ($this->metode_pembelian == 'offline') {
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'uraian' => '',
                            'deskripsi' => '',
                            'nama' => '',
                            'qty' => '',
                            'unit' => '',
                            'harga_toko' => '',
                            'toko' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);
                    }

                    if ($this->metode_pembelian == 'online_dan_offline') {
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'uraian' => '',
                            'deskripsi' => '',
                            'nama' => '',
                            'qty' => '',
                            'unit' => '',
                            'harga_toko' => '',
                            'harga_onl' => '',
                            'diskon' => '',
                            'ongkir' => '',
                            'asuransi' => '',
                            'b_proteksi' => '',
                            'p_member' => '',
                            'b_aplikasi' => '',
                            'toko' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);
                    } 

                    return $data;
                }

                public function headings(): array
                {
                    if ($this->metode_pembelian == 'online') {
                        return [
                            ['Pengeluaran ' . $this->infoTagihan . ' ' . $this->rangeDate],
                            ['Tanggal',
                            'Uraian',
                            'Deskripsi',
                            'Nama Barang',
                            'Jumlah',
                            'Satuan',
                            'Diskon',
                            'Harga Online',
                            'Ongkir',
                            'Asuransi',
                            'Biaya Proteksi',
                            'Potongan Member',
                            'Biaya Aplikasi',
                            'Toko',
                            'Total',]
                        ];
                    } 
                    
                    if ($this->metode_pembelian == 'offline') {
                        return [
                            ['Pengeluaran ' . $this->infoTagihan . ' ' . $this->rangeDate],
                            ['Tanggal',
                            'Uraian',
                            'Deskripsi',
                            'Nama Barang',
                            'Jumlah',
                            'Satuan',
                            'Harga',
                            'Toko',
                            'Total',]
                        ];
                    }

                    if ($this->metode_pembelian == 'online_dan_offline') {
                        return [
                            ['Pengeluaran ' . $this->infoTagihan . ' ' . $this->rangeDate],
                            ['Tanggal',
                            'Uraian',
                            'Deskripsi',
                            'Nama Barang',
                            'Jumlah',
                            'Satuan',
                            'Harga Toko',
                            'Harga Online',
                            'Diskon',
                            'Ongkir',
                            'Asuransi',
                            'Biaya Proteksi',
                            'Potongan Member',
                            'Biaya Aplikasi',
                            'Toko',
                            'Total',]
                        ];
                    } 
                }

                public function styles(Worksheet $sheet)
                {
                    if ($this->metode_pembelian == 'online') {
                        // Header
                        $sheet->mergeCells("A1:O1");
                        $sheet->getStyle('A1:O1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:O2')->getFont()->setBold(true);
                        $sheet->getStyle('A:O')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan . ' Online');

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:N$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:N$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("O$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getAlignment()->setHorizontal('center');
                    } 
                    
                    if ($this->metode_pembelian == 'offline') {
                        // Header
                        $sheet->mergeCells("A1:I1");
                        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:I2')->getFont()->setBold(true);
                        $sheet->getStyle('A:I')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan);

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:H$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:H$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("I$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:I$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }

                    if ($this->metode_pembelian == 'online_dan_offline') {
                        // Header
                        $sheet->mergeCells("A1:P1");
                        $sheet->getStyle('A1:P1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:P2')->getFont()->setBold(true);
                        $sheet->getStyle('A:P')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan . ' Online dan Offline');

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:O$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:O$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("P$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }
                }
            }
        ];
    }
}
