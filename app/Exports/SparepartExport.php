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
use App\Models\Satuan;

class SparepartExport implements WithMultipleSheets
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
                $date = Carbon::parse($item->tgl_order);
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
                            $kendaraan = Kendaraan::find($item->id_kendaraan);
                            $satuan = Satuan::find($item->id_satuan);
                            $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                            if ($this->metode_pembelian == 'online') {
                                $harga_online = 'Rp ' . number_format($item->harga_online ?? 0, 0, ',', '.');
                                $ongkir = 'Rp ' . number_format($item->ongkir ?? 0, 0, ',', '.');
                                $diskon_ongkir = 'Rp ' . number_format($item->diskon_ongkir ?? 0, 0, ',', '.');
                                $asuransi = 'Rp ' . number_format($item->asuransi ?? 0, 0, ',', '.');
                                $b_proteksi = 'Rp ' . number_format($item->b_proteksi ?? 0, 0, ',', '.');
                                $b_jasa_aplikasi = 'Rp ' . number_format($item->b_jasa_aplikasi ?? 0, 0, ',', '.');

                                return [
                                    'lokasi' => $item->lokasi,
                                    'nopol' => $kendaraan->nopol ?? '-',
                                    'merk' => $kendaraan->merk ?? '-',
                                    'pemesan' => $item->pemesan,
                                    'tgl_order' => $item->tgl_order,
                                    'tgl_invoice' => $item->tgl_invoice,
                                    'no_inventaris' => $item->no_inventaris,
                                    'nama' => $item->nama,
                                    'kategori' => $item->kategori,
                                    'dipakai_untuk' => $item->dipakai_untuk,
                                    'masa_pakai' => $item->masa_pakai,
                                    'jml' => $item->jml ? $item->jml : '-',
                                    'unit' => $satuan->nama ?? '-',
                                    'harga_online' => $harga_online ? $harga_online : '-',
                                    'ongkir' => $ongkir ? $ongkir : '-',
                                    'diskon_ongkir' => $diskon_ongkir ? $diskon_ongkir : '-',
                                    'asuransi' => $asuransi ? $asuransi : '-',
                                    'b_proteksi' => $b_proteksi ? $b_proteksi : '-',
                                    'b_jasa_aplikasi' => $b_jasa_aplikasi ? $b_jasa_aplikasi : '-',
                                    'toko' => $item->toko,
                                    'total' => $total
                                ];

                            } else {
                                $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');

                                return [
                                    'lokasi' => $item->lokasi,
                                    'nopol' => $kendaraan->nopol ?? '-',
                                    'merk' => $kendaraan->merk ?? '-',
                                    'pemesan' => $item->pemesan,
                                    'tgl_order' => $item->tgl_order,
                                    'tgl_invoice' => $item->tgl_invoice,
                                    'no_inventaris' => $item->no_inventaris,
                                    'nama' => $item->nama,
                                    'kategori' => $item->kategori,
                                    'dipakai_untuk' => $item->dipakai_untuk,
                                    'masa_pakai' => $item->masa_pakai,
                                    'jml' => $item->jml ? $item->jml : '-',
                                    'unit' => $satuan->nama ?? '-',
                                    'harga' => $harga ? $harga : '-',
                                    'toko' => $item->toko,
                                    'total' => $total
                                ];
                            }
                        });

                        // Menambahkan baris total keseluruhan
                        if ($this->metode_pembelian == 'online') {
                            $data->push([
                                'lokasi' => 'Total Keseluruhan',
                                'nopol' => '',
                                'merk' => '',
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
                                'harga_online' => '',
                                'ongkir' => '',
                                'diskon_ongkir' => '',
                                'asuransi' => '',
                                'b_proteksi' => '',
                                'b_jasa_aplikasi' => '',
                                'toko' => '',
                                'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                            ]);

                        } else {
                            $data->push([
                                'lokasi' => 'Total Keseluruhan',
                                'nopol' => '',
                                'merk' => '',
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
                        }

                        return $data;
                    }

                    public function headings(): array
                    {
                        if ($this->metode_pembelian == 'online') {
                            return [
                                ['Pengeluaran ' . $this->infoTagihan . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                                ['Lokasi',
                                'Nopol / Kode Unit',
                                'Merk',
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
                                'Ongkir',
                                'Diskon Ongkir',
                                'Asuransi',
                                'Biaya Proteksi',
                                'Biaya Jasa Aplikasi',
                                'Toko',
                                'Total']
                            ];

                        } else {
                            return [
                                ['Pengeluaran ' . $this->infoTagihan . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                                ['Lokasi',
                                'Nopol / Kode Unit',
                                'Merk',
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
                                'Total']
                            ];
                        }
                    }

                    public function styles(Worksheet $sheet)
                    {
                        if ($this->metode_pembelian == 'online') {
                            // Header
                            $sheet->mergeCells("A1:U1");
                            $sheet->getStyle('A1:U1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:U2')->getFont()->setBold(true);
                            $sheet->getStyle('A:U')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Online ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

                            // Menyempurnakan styling baris total
                            $totalRowIndex = $sheet->getHighestRow();
                            $sheet->mergeCells("A$totalRowIndex:T$totalRowIndex");
                            $sheet->getStyle("A$totalRowIndex:T$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("U$totalRowIndex")->getFont()->setBold(true);
                            $sheet->getStyle("A$totalRowIndex:U$totalRowIndex")->getAlignment()->setHorizontal('center');

                        } else {
                            // Header
                            $sheet->mergeCells("A1:P1");
                            $sheet->getStyle('A1:P1')->getFont()->setBold(true);

                            $sheet->getStyle('A2:P2')->getFont()->setBold(true);
                            $sheet->getStyle('A:P')->getAlignment()->setHorizontal('center');
                            $sheet->setTitle($this->infoTagihan . ' Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));

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
                        $kendaraan = Kendaraan::find($item->id_kendaraan);
                        $satuan = Satuan::find($item->id_satuan);
                        $total = 'Rp ' . number_format($item->total ?? 0, 0, ',', '.');

                        if ($this->metode_pembelian == 'online') {
                            $harga_online = 'Rp ' . number_format($item->harga_online ?? 0, 0, ',', '.');
                            $ongkir = 'Rp ' . number_format($item->ongkir ?? 0, 0, ',', '.');
                            $diskon_ongkir = 'Rp ' . number_format($item->diskon_ongkir ?? 0, 0, ',', '.');
                            $asuransi = 'Rp ' . number_format($item->asuransi ?? 0, 0, ',', '.');
                            $b_proteksi = 'Rp ' . number_format($item->b_proteksi ?? 0, 0, ',', '.');
                            $b_jasa_aplikasi = 'Rp ' . number_format($item->b_jasa_aplikasi ?? 0, 0, ',', '.');

                            return [
                                'lokasi' => $item->lokasi,
                                'nopol' => $kendaraan->nopol ?? '-',
                                'merk' => $kendaraan->merk ?? '-',
                                'pemesan' => $item->pemesan,
                                'tgl_order' => $item->tgl_order,
                                'tgl_invoice' => $item->tgl_invoice,
                                'no_inventaris' => $item->no_inventaris,
                                'nama' => $item->nama,
                                'kategori' => $item->kategori,
                                'dipakai_untuk' => $item->dipakai_untuk,
                                'masa_pakai' => $item->masa_pakai,
                                'jml' => $item->jml ? $item->jml : '-',
                                'unit' => $satuan->nama ?? '-',
                                'harga_online' => $harga_online ? $harga_online : '-',
                                'ongkir' => $ongkir ? $ongkir : '-',
                                'diskon_ongkir' => $diskon_ongkir ? $diskon_ongkir : '-',
                                'asuransi' => $asuransi ? $asuransi : '-',
                                'b_proteksi' => $b_proteksi ? $b_proteksi : '-',
                                'b_jasa_aplikasi' => $b_jasa_aplikasi ? $b_jasa_aplikasi : '-',
                                'toko' => $item->toko,
                                'total' => $total
                            ];

                        } else {
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');

                            return [
                                'lokasi' => $item->lokasi,
                                'nopol' => $kendaraan->nopol ?? '-',
                                'merk' => $kendaraan->merk ?? '-',
                                'pemesan' => $item->pemesan,
                                'tgl_order' => $item->tgl_order,
                                'tgl_invoice' => $item->tgl_invoice,
                                'no_inventaris' => $item->no_inventaris,
                                'nama' => $item->nama,
                                'kategori' => $item->kategori,
                                'dipakai_untuk' => $item->dipakai_untuk,
                                'masa_pakai' => $item->masa_pakai,
                                'jml' => $item->jml ? $item->jml : '-',
                                'unit' => $satuan->nama ?? '-',
                                'harga' => $harga ? $harga : '-',
                                'toko' => $item->toko,
                                'total' => $total
                            ];
                        }
                    });

                    // Menambahkan baris total keseluruhan
                    if ($this->metode_pembelian == 'online') {
                        $data->push([
                            'lokasi' => 'Total Keseluruhan',
                            'nopol' => '',
                            'merk' => '',
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
                            'harga_online' => '',
                            'ongkir' => '',
                            'diskon_ongkir' => '',
                            'asuransi' => '',
                            'b_proteksi' => '',
                            'b_jasa_aplikasi' => '',
                            'toko' => '',
                            'total' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);

                    } else {
                        $data->push([
                            'lokasi' => 'Total Keseluruhan',
                            'nopol' => '',
                            'merk' => '',
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
                    }

                    return $data;
                }

                public function headings(): array
                {
                    if ($this->metode_pembelian == 'online') {
                        return [
                            ['Pengeluaran ' . $this->infoTagihan . ' ' . $this->rangeDate],
                            ['Lokasi',
                            'Nopol / Kode Unit',
                            'Merk',
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
                            'Ongkir',
                            'Diskon Ongkir',
                            'Asuransi',
                            'Biaya Proteksi',
                            'Biaya Jasa Aplikasi',
                            'Toko',
                            'Total']
                        ];

                    } else {
                        return [
                            ['Pengeluaran ' . $this->infoTagihan . ' ' . $this->rangeDate],
                            ['Lokasi',
                            'Nopol / Kode Unit',
                            'Merk',
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
                            'Total']
                        ];
                    }
                }

                public function styles(Worksheet $sheet)
                {
                    if ($this->metode_pembelian == 'online') {
                        // Header
                        $sheet->mergeCells("A1:U1");
                        $sheet->getStyle('A1:U1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:U2')->getFont()->setBold(true);
                        $sheet->getStyle('A:U')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan . ' Online');

                        // Menyempurnakan styling baris total
                        $totalRowIndex = $sheet->getHighestRow();
                        $sheet->mergeCells("A$totalRowIndex:T$totalRowIndex");
                        $sheet->getStyle("A$totalRowIndex:T$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("U$totalRowIndex")->getFont()->setBold(true);
                        $sheet->getStyle("A$totalRowIndex:U$totalRowIndex")->getAlignment()->setHorizontal('center');

                    } else {
                        // Header
                        $sheet->mergeCells("A1:P1");
                        $sheet->getStyle('A1:P1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:P2')->getFont()->setBold(true);
                        $sheet->getStyle('A:P')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->infoTagihan);

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
