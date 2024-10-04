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
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Toko;

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
                $sheets[] = new class($period, $data, $this->infoTagihan, $this->metode_pembelian) implements FromCollection, WithHeadings, WithStyles
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

                        $data = $this->tagihan->flatMap(function ($item) {
                            // Format nilai uang
                            $diskon = 'Rp ' . number_format($item->diskon ?? 0, 0, ',', '.');
                            $ongkir = 'Rp ' . number_format($item->ongkir ?? 0, 0, ',', '.');
                            $asuransi = 'Rp ' . number_format($item->asuransi ?? 0, 0, ',', '.');
                            $p_member = 'Rp ' . number_format($item->p_member ?? 0, 0, ',', '.');
                            $b_proteksi = 'Rp ' . number_format($item->b_proteksi ?? 0, 0, ',', '.');
                            $b_aplikasi = 'Rp ' . number_format($item->b_aplikasi ?? 0, 0, ',', '.');
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');
                        
                            // Ambil barang
                            $barang = Barang::where('id_relasi', $item->id_operasional)->get();

                            // Data toko
                            $toko = Toko::find($item->id_toko);

                            if ($barang->isNotEmpty()) {
                                // Jika ada barang, map untuk setiap item
                                return $barang->map(function ($data) use ($item, $toko, $diskon, $ongkir, $asuransi, $p_member, $b_proteksi, $b_aplikasi, $total) {
                                    $harga = 'Rp ' . number_format($data->harga ?? 0, 0, ',', '.');
                                    $satuan = Satuan::find($data->id_satuan);
                        
                                    if ($this->metode_pembelian == 'online') {
                                        return [
                                            'tanggal' => $item->tanggal,
                                            'uraian' => $item->uraian,
                                            'deskripsi' => $item->deskripsi,
                                            'lokasi' => $item->lokasi ?? '-',
                                            'nama' => $item->nama,
                                            'barang' => $data->nama,
                                            'qty' => $data->jumlah,
                                            'unit' => $satuan->nama,
                                            'harga' => $harga,
                                            'diskon' => $diskon,
                                            'ongkir' => $ongkir,
                                            'asuransi' => $asuransi,
                                            'b_proteksi' => $b_proteksi,
                                            'p_member' => $p_member,
                                            'b_aplikasi' => $b_aplikasi,
                                            'toko' => $toko->nama ?? '-',
                                            'total' => $total
                                        ];
                                    }
                        
                                    if ($this->metode_pembelian == 'offline') {
                                        return [
                                            'tanggal' => $item->tanggal,
                                            'uraian' => $item->uraian,
                                            'deskripsi' => $item->deskripsi,
                                            'lokasi' => $item->lokasi ?? '-',
                                            'nama' => $item->nama,
                                            'barang' => $data->nama,
                                            'qty' => $data->jumlah,
                                            'unit' => $satuan->nama,
                                            'harga' => $harga,
                                            'toko' => $toko->nama ?? '-',
                                            'total' => $total
                                        ];
                                    }
                        
                                    if ($this->metode_pembelian == 'online_dan_offline') {
                                        return [
                                            'tanggal' => $item->tanggal,
                                            'uraian' => $item->uraian,
                                            'deskripsi' => $item->deskripsi,
                                            'lokasi' => $item->lokasi ?? '-',
                                            'nama' => $item->nama,
                                            'barang' => $data->nama,
                                            'qty' => $data->jumlah,
                                            'unit' => $satuan->nama,
                                            'harga' => $harga,
                                            'diskon' => $diskon,
                                            'ongkir' => $ongkir,
                                            'asuransi' => $asuransi,
                                            'b_proteksi' => $b_proteksi,
                                            'p_member' => $p_member,
                                            'b_aplikasi' => $b_aplikasi,
                                            'toko' => $toko->nama ?? '-',
                                            'total' => $total
                                        ];
                                    }
                                });
                            }
                        
                            // Jika tidak ada barang
                            if ($this->metode_pembelian == 'online') {
                                return [
                                    [
                                        'tanggal' => $item->tanggal,
                                        'uraian' => $item->uraian,
                                        'deskripsi' => $item->deskripsi,
                                        'lokasi' => $item->lokasi ?? '-',
                                        'nama' => $item->nama,
                                        'barang' => '-',
                                        'qty' => '-',
                                        'unit' => '-',
                                        'harga' => '-',
                                        'diskon' => $diskon,
                                        'ongkir' => $ongkir,
                                        'asuransi' => $asuransi,
                                        'b_proteksi' => $b_proteksi,
                                        'p_member' => $p_member,
                                        'b_aplikasi' => $b_aplikasi,
                                        'toko' => $toko->nama ?? '-',
                                        'total' => $total
                                    ]
                                ];
                            }
                        
                            if ($this->metode_pembelian == 'offline') {
                                return [
                                    [
                                        'tanggal' => $item->tanggal,
                                        'uraian' => $item->uraian,
                                        'deskripsi' => $item->deskripsi,
                                        'lokasi' => $item->lokasi ?? '-',
                                        'nama' => $item->nama,
                                        'barang' => '-',
                                        'qty' => '-',
                                        'unit' => '-',
                                        'harga' => '-',
                                        'toko' => $toko->nama ?? '-',
                                        'total' => $total
                                    ]
                                ];
                            }
                        
                            if ($this->metode_pembelian == 'online_dan_offline') {
                                return [
                                    [
                                        'tanggal' => $item->tanggal,
                                        'uraian' => $item->uraian,
                                        'deskripsi' => $item->deskripsi,
                                        'lokasi' => $item->lokasi ?? '-',
                                        'nama' => $item->nama,
                                        'barang' => '-',
                                        'qty' => '-',
                                        'unit' => '-',
                                        'harga' => '-',
                                        'diskon' => $diskon,
                                        'ongkir' => $ongkir,
                                        'asuransi' => $asuransi,
                                        'b_proteksi' => $b_proteksi,
                                        'p_member' => $p_member,
                                        'b_aplikasi' => $b_aplikasi,
                                        'toko' => $toko->nama ?? '-',
                                        'total' => $total
                                    ]
                                ];
                            }
                        });

                        if ($this->metode_pembelian == 'online') {
                            $data->push([
                                'tanggal' => 'Total Keseluruhan',
                                'uraian' => '',
                                'deskripsi' => '',
                                'lokasi' => '',
                                'nama' => '',
                                'barang' => '',
                                'qty' => '',
                                'unit' => '',
                                'harga' => '',
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
                        
                        if ($this->metode_pembelian == 'offline') {
                            $data->push([
                                'tanggal' => 'Total Keseluruhan',
                                'uraian' => '',
                                'deskripsi' => '',
                                'lokasi' => '',
                                'nama' => '',
                                'barang' => '',
                                'qty' => '',
                                'unit' => '',
                                'harga' => '',
                                'toko' => '',
                                'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                            ]);
                        }

                        if ($this->metode_pembelian == 'online_dan_offline') {
                            $data->push([
                                'tanggal' => 'Total Keseluruhan',
                                'uraian' => '',
                                'deskripsi' => '',
                                'lokasi' => '',
                                'nama' => '',
                                'barang' => '',
                                'qty' => '',
                                'unit' => '',
                                'harga' => '',
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
                                'Lokasi',
                                'Pemesan',
                                'Nama Barang',
                                'Jumlah',
                                'Satuan',
                                'Harga',
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
                        
                        if ($this->metode_pembelian == 'offline') {
                            return [
                                ['Pengeluaran ' . $this->infoTagihan . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                                ['Tanggal',
                                'Uraian',
                                'Deskripsi',
                                'Lokasi',
                                'Pemesan',
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
                                'Lokasi',
                                'Pemesan',
                                'Nama Barang',
                                'Jumlah',
                                'Satuan',
                                'Harga',
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
                            $sheet->mergeCells("A1:Q1");
                            $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
                            $sheet->getStyle('A:Q')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Online ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Mengatur kolom C
                            $sheet->getColumnDimension('C')->setWidth(50);
                            $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                            // Menyesuaikan lebar kolom lainnya
                            foreach (range('A', 'Q') as $column) {
                                if ($column !== 'C') {
                                    $sheet->getColumnDimension($column)->setAutoSize(true);
                                    $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                                }
                            }

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:P$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("Q$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:Q$totalRowIndex")->getAlignment()->setHorizontal('center');
                        } 
                        
                        if ($this->metode_pembelian == 'offline') {
                            // Header
                            $sheet->mergeCells("A1:K1");
                            $sheet->getStyle('A1:K1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:K2')->getFont()->setBold(true);
                            $sheet->getStyle('A:K')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Mengatur kolom C
                            $sheet->getColumnDimension('C')->setWidth(50);
                            $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                            // Menyesuaikan lebar kolom lainnya
                            foreach (range('A', 'K') as $column) {
                                if ($column !== 'C') {
                                    $sheet->getColumnDimension($column)->setAutoSize(true);
                                    $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                                }
                            }

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:J$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:J$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("K$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:K$totalRowIndex")->getAlignment()->setHorizontal('center');
                        }

                        if ($this->metode_pembelian == 'online_dan_offline') {
                            // Header
                            $sheet->mergeCells("A1:Q1");
                            $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
                            $sheet->getStyle('A:Q')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Online dan Offline ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Mengatur kolom C
                            $sheet->getColumnDimension('C')->setWidth(50);
                            $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                            // Menyesuaikan lebar kolom lainnya
                            foreach (range('A', 'Q') as $column) {
                                if ($column !== 'C') {
                                    $sheet->getColumnDimension($column)->setAutoSize(true);
                                    $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                                }
                            }

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:P$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("Q$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:Q$totalRowIndex")->getAlignment()->setHorizontal('center');
                        }

                        // Merge cells based on tanggal
                        $this->mergeCellsByDate($sheet, $this->metode_pembelian);
                    }

                    private function mergeCellsByDate(Worksheet $sheet, $metode_pembelian)
                    {
                        $highestRow = $sheet->getHighestRow();
                        $dateColumn = 'A'; // Kolom tanggal
                        $columnsToMerge = ['A', 'B', 'C', 'D', 'E']; // Kolom yang ingin digabungkan

                        // Tambahkan kolom I dan J jika metode pembelian adalah 'offline'
                        if ($metode_pembelian == 'offline') {
                            $columnsToMerge = array_merge($columnsToMerge, ['J', 'K']);
                        }

                        if ($metode_pembelian == 'online' || $metode_pembelian == 'online_dan_offline') {
                            $columnsToMerge = array_merge($columnsToMerge, ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q']);
                        }

                        $startRow = 2; // Assuming header starts at row 2

                        $currentDate = '';
                        $currentUraian = '';
                        $currentDeskripsi = '';
                        $currentLokasi = '';
                        $currentPemesan = '';
                        $currentToko = '';
                        $startMergeRow = $startRow;

                        for ($row = $startRow; $row <= $highestRow; $row++) {
                            $cellDateValue = $sheet->getCell("$dateColumn$row")->getValue();
                            $cellUraianValue = $sheet->getCell("B$row")->getValue();
                            $cellDeskripsiValue = $sheet->getCell("C$row")->getValue();
                            $cellLokasiValue = $sheet->getCell("D$row")->getValue();
                            $cellPemesanValue = $sheet->getCell("E$row")->getValue();
                            $cellTokoValue = $metode_pembelian == 'offline' ? $sheet->getCell("I$row")->getValue() : '';

                            // Check if the date or other columns change
                            if ($cellDateValue != $currentDate || 
                                $cellUraianValue != $currentUraian || 
                                $cellDeskripsiValue != $currentDeskripsi || 
                                $cellLokasiValue != $currentLokasi || 
                                $cellPemesanValue != $currentPemesan || 
                                ($metode_pembelian == 'offline' && $cellTokoValue != $currentToko)) {

                                // Merge cells for the previous group
                                if ($row - 1 > $startMergeRow) {
                                    foreach ($columnsToMerge as $column) {
                                        $sheet->mergeCells("$column$startMergeRow:$column" . ($row - 1));
                                        $sheet->getStyle("$column$startMergeRow:$column" . ($row - 1))
                                            ->getAlignment()->setHorizontal('center')->setVertical('center');
                                    }
                                }

                                // Update current values and start new merge range
                                $currentDate = $cellDateValue;
                                $currentUraian = $cellUraianValue;
                                $currentDeskripsi = $cellDeskripsiValue;
                                $currentLokasi = $cellLokasiValue;
                                $currentPemesan = $cellPemesanValue;
                                $currentToko = $cellTokoValue;
                                $startMergeRow = $row;
                            }
                        }

                        // Merge the last group if necessary
                        if ($highestRow > $startMergeRow) {
                            foreach ($columnsToMerge as $column) {
                                $sheet->mergeCells("$column$startMergeRow:$column$highestRow");
                                $sheet->getStyle("$column$startMergeRow:$column$highestRow")
                                    ->getAlignment()->setHorizontal('center')->setVertical('center');
                            }
                        }
                    }

                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->tagihan, $this->infoTagihan, $this->metode_pembelian, $this->rangeDate) implements FromCollection, WithHeadings, WithStyles
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

                    $data = $this->tagihan->flatMap(function ($item) {
                        // Format nilai uang
                        $diskon = 'Rp ' . number_format($item->diskon ?? 0, 0, ',', '.');
                        $ongkir = 'Rp ' . number_format($item->ongkir ?? 0, 0, ',', '.');
                        $asuransi = 'Rp ' . number_format($item->asuransi ?? 0, 0, ',', '.');
                        $p_member = 'Rp ' . number_format($item->p_member ?? 0, 0, ',', '.');
                        $b_proteksi = 'Rp ' . number_format($item->b_proteksi ?? 0, 0, ',', '.');
                        $b_aplikasi = 'Rp ' . number_format($item->b_aplikasi ?? 0, 0, ',', '.');
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');
                    
                        // Ambil barang
                        $barang = Barang::where('id_relasi', $item->id_operasional)->get();

                        // Data toko
                        $toko = Toko::find($item->id_toko);
                    
                        if ($barang->isNotEmpty()) {
                            // Jika ada barang, map untuk setiap item
                            return $barang->map(function ($data) use ($item, $toko, $diskon, $ongkir, $asuransi, $p_member, $b_proteksi, $b_aplikasi, $total) {
                                $harga = 'Rp ' . number_format($data->harga ?? 0, 0, ',', '.');
                                $satuan = Satuan::find($data->id_satuan);
                    
                                if ($this->metode_pembelian == 'online') {
                                    return [
                                        'tanggal' => $item->tanggal,
                                        'uraian' => $item->uraian,
                                        'deskripsi' => $item->deskripsi,
                                        'lokasi' => $item->lokasi ?? '-',
                                        'nama' => $item->nama,
                                        'barang' => $data->nama,
                                        'qty' => $data->jumlah,
                                        'unit' => $satuan->nama,
                                        'harga' => $harga,
                                        'diskon' => $diskon,
                                        'ongkir' => $ongkir,
                                        'asuransi' => $asuransi,
                                        'b_proteksi' => $b_proteksi,
                                        'p_member' => $p_member,
                                        'b_aplikasi' => $b_aplikasi,
                                        'toko' => $toko->nama ?? '-',
                                        'total' => $total
                                    ];
                                }
                    
                                if ($this->metode_pembelian == 'offline') {
                                    return [
                                        'tanggal' => $item->tanggal,
                                        'uraian' => $item->uraian,
                                        'deskripsi' => $item->deskripsi,
                                        'lokasi' => $item->lokasi ?? '-',
                                        'nama' => $item->nama,
                                        'barang' => $data->nama,
                                        'qty' => $data->jumlah,
                                        'unit' => $satuan->nama,
                                        'harga' => $harga,
                                        'toko' => $toko->nama ?? '-',
                                        'total' => $total
                                    ];
                                }
                    
                                if ($this->metode_pembelian == 'online_dan_offline') {
                                    return [
                                        'tanggal' => $item->tanggal,
                                        'uraian' => $item->uraian,
                                        'deskripsi' => $item->deskripsi,
                                        'lokasi' => $item->lokasi ?? '-',
                                        'nama' => $item->nama,
                                        'barang' => $data->nama,
                                        'qty' => $data->jumlah,
                                        'unit' => $satuan->nama,
                                        'harga' => $harga,
                                        'diskon' => $diskon,
                                        'ongkir' => $ongkir,
                                        'asuransi' => $asuransi,
                                        'b_proteksi' => $b_proteksi,
                                        'p_member' => $p_member,
                                        'b_aplikasi' => $b_aplikasi,
                                        'toko' => $toko->nama ?? '-',
                                        'total' => $total
                                    ];
                                }
                            });
                        }
                    
                        // Jika tidak ada barang
                        if ($this->metode_pembelian == 'online') {
                            return [
                                [
                                    'tanggal' => $item->tanggal,
                                    'uraian' => $item->uraian,
                                    'deskripsi' => $item->deskripsi,
                                    'lokasi' => $item->lokasi ?? '-',
                                    'nama' => $item->nama,
                                    'barang' => '-',
                                    'qty' => '-',
                                    'unit' => '-',
                                    'harga' => '-',
                                    'diskon' => $diskon,
                                    'ongkir' => $ongkir,
                                    'asuransi' => $asuransi,
                                    'b_proteksi' => $b_proteksi,
                                    'p_member' => $p_member,
                                    'b_aplikasi' => $b_aplikasi,
                                    'toko' => $toko->nama ?? '-',
                                    'total' => $total
                                ]
                            ];
                        }
                    
                        if ($this->metode_pembelian == 'offline') {
                            return [
                                [
                                    'tanggal' => $item->tanggal,
                                    'uraian' => $item->uraian,
                                    'deskripsi' => $item->deskripsi,
                                    'lokasi' => $item->lokasi ?? '-',
                                    'nama' => $item->nama,
                                    'barang' => '-',
                                    'qty' => '-',
                                    'unit' => '-',
                                    'harga' => '-',
                                    'toko' => $toko->nama ?? '-',
                                    'total' => $total
                                ]
                            ];
                        }
                    
                        if ($this->metode_pembelian == 'online_dan_offline') {
                            return [
                                [
                                    'tanggal' => $item->tanggal,
                                    'uraian' => $item->uraian,
                                    'deskripsi' => $item->deskripsi,
                                    'lokasi' => $item->lokasi ?? '-',
                                    'nama' => $item->nama,
                                    'barang' => '-',
                                    'qty' => '-',
                                    'unit' => '-',
                                    'harga' => '-',
                                    'diskon' => $diskon,
                                    'ongkir' => $ongkir,
                                    'asuransi' => $asuransi,
                                    'b_proteksi' => $b_proteksi,
                                    'p_member' => $p_member,
                                    'b_aplikasi' => $b_aplikasi,
                                    'toko' => $toko->nama ?? '-',
                                    'total' => $total
                                ]
                            ];
                        }
                    });

                    if ($this->metode_pembelian == 'online') {
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'uraian' => '',
                            'deskripsi' => '',
                            'lokasi' => '',
                            'nama' => '',
                            'barang' => '',
                            'qty' => '',
                            'unit' => '',
                            'harga' => '',
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
                    
                    if ($this->metode_pembelian == 'offline') {
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'uraian' => '',
                            'deskripsi' => '',
                            'lokasi' => '',
                            'nama' => '',
                            'barang' => '',
                            'qty' => '',
                            'unit' => '',
                            'harga' => '',
                            'toko' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);
                    }

                    if ($this->metode_pembelian == 'online_dan_offline') {
                        $data->push([
                            'tanggal' => 'Total Keseluruhan',
                            'uraian' => '',
                            'deskripsi' => '',
                            'lokasi' => '',
                            'nama' => '',
                            'barang' => '',
                            'qty' => '',
                            'unit' => '',
                            'harga' => '',
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
                            'Lokasi',
                            'Pemesan',
                            'Nama Barang',
                            'Jumlah',
                            'Satuan',
                            'Harga',
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
                    
                    if ($this->metode_pembelian == 'offline') {
                        return [
                            ['Pengeluaran ' . $this->infoTagihan . ' ' . $this->rangeDate],
                            ['Tanggal',
                            'Uraian',
                            'Deskripsi',
                            'Lokasi',
                            'Pemesan',
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
                            'Lokasi',
                            'Pemesan',
                            'Nama Barang',
                            'Jumlah',
                            'Satuan',
                            'Harga',
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
                        $sheet->mergeCells("A1:Q1");
                        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
                        $sheet->getStyle('A:Q')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan . ' Online');

                        // Mengatur kolom C
                        $sheet->getColumnDimension('C')->setWidth(50);
                        $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                        // Menyesuaikan lebar kolom lainnya
                        foreach (range('A', 'Q') as $column) {
                            if ($column !== 'C') {
                                $sheet->getColumnDimension($column)->setAutoSize(true);
                                $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                            }
                        }

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:P$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("Q$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:Q$totalRowIndex")->getAlignment()->setHorizontal('center');
                    } 
                    
                    if ($this->metode_pembelian == 'offline') {
                        // Header
                        $sheet->mergeCells("A1:K1");
                        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:K2')->getFont()->setBold(true);
                        $sheet->getStyle('A:K')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan);

                        // Mengatur kolom C
                        $sheet->getColumnDimension('C')->setWidth(50);
                        $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                        // Menyesuaikan lebar kolom lainnya
                        foreach (range('A', 'K') as $column) {
                            if ($column !== 'C') {
                                $sheet->getColumnDimension($column)->setAutoSize(true);
                                $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                            }
                        }

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:J$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:J$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("K$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:K$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }

                    if ($this->metode_pembelian == 'online_dan_offline') {
                        // Header
                        $sheet->mergeCells("A1:Q1");
                        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
                        $sheet->getStyle('A:Q')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan . ' Online dan Offline');

                        // Mengatur kolom C
                        $sheet->getColumnDimension('C')->setWidth(50);
                        $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                        // Menyesuaikan lebar kolom lainnya
                        foreach (range('A', 'Q') as $column) {
                            if ($column !== 'C') {
                                $sheet->getColumnDimension($column)->setAutoSize(true);
                                $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                            }
                        }

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:P$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:P$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("Q$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:Q$totalRowIndex")->getAlignment()->setHorizontal('center');
                    }

                    // Merge cells based on tanggal
                    $this->mergeCellsByDate($sheet, $this->metode_pembelian);
                }

                private function mergeCellsByDate(Worksheet $sheet, $metode_pembelian)
                {
                    $highestRow = $sheet->getHighestRow();
                    $dateColumn = 'A'; // Kolom tanggal
                    $columnsToMerge = ['A', 'B', 'C', 'D', 'E']; // Kolom yang ingin digabungkan

                    // Tambahkan kolom I dan J jika metode pembelian adalah 'offline'
                    if ($metode_pembelian == 'offline') {
                        $columnsToMerge = array_merge($columnsToMerge, ['J', 'K']);
                    }

                    if ($metode_pembelian == 'online' || $metode_pembelian == 'online_dan_offline') {
                        $columnsToMerge = array_merge($columnsToMerge, ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q']);
                    }

                    $startRow = 2; // Assuming header starts at row 2

                    $currentDate = '';
                    $currentUraian = '';
                    $currentDeskripsi = '';
                    $currentLokasi = '';
                    $currentPemesan = '';
                    $currentToko = '';
                    $startMergeRow = $startRow;

                    for ($row = $startRow; $row <= $highestRow; $row++) {
                        $cellDateValue = $sheet->getCell("$dateColumn$row")->getValue();
                        $cellUraianValue = $sheet->getCell("B$row")->getValue();
                        $cellDeskripsiValue = $sheet->getCell("C$row")->getValue();
                        $cellLokasiValue = $sheet->getCell("D$row")->getValue();
                        $cellPemesanValue = $sheet->getCell("E$row")->getValue();
                        $cellTokoValue = $metode_pembelian == 'offline' ? $sheet->getCell("I$row")->getValue() : '';

                        // Check if the date or other columns change
                        if ($cellDateValue != $currentDate || 
                            $cellUraianValue != $currentUraian || 
                            $cellDeskripsiValue != $currentDeskripsi || 
                            $cellLokasiValue != $currentLokasi || 
                            $cellPemesanValue != $currentPemesan || 
                            ($metode_pembelian == 'offline' && $cellTokoValue != $currentToko)) {

                            // Merge cells for the previous group
                            if ($row - 1 > $startMergeRow) {
                                foreach ($columnsToMerge as $column) {
                                    $sheet->mergeCells("$column$startMergeRow:$column" . ($row - 1));
                                    $sheet->getStyle("$column$startMergeRow:$column" . ($row - 1))
                                        ->getAlignment()->setHorizontal('center')->setVertical('center');
                                }
                            }

                            // Update current values and start new merge range
                            $currentDate = $cellDateValue;
                            $currentUraian = $cellUraianValue;
                            $currentDeskripsi = $cellDeskripsiValue;
                            $currentLokasi = $cellLokasiValue;
                            $currentPemesan = $cellPemesanValue;
                            $currentToko = $cellTokoValue;
                            $startMergeRow = $row;
                        }
                    }

                    // Merge the last group if necessary
                    if ($highestRow > $startMergeRow) {
                        foreach ($columnsToMerge as $column) {
                            $sheet->mergeCells("$column$startMergeRow:$column$highestRow");
                            $sheet->getStyle("$column$startMergeRow:$column$highestRow")
                                ->getAlignment()->setHorizontal('center')->setVertical('center');
                        }
                    }
                }
            }
        ];
    }
}
