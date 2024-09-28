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
use App\Models\Satuan;
use App\Models\Kendaraan;
use App\Models\StokBarang;

class BarangKeluarExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $barangKeluar;
    protected $rangeDate;

    public function __construct($mode, $barangKeluar, $rangeDate)
    {
        $this->mode = $mode;
        $this->barangKeluar = $barangKeluar;
        $this->rangeDate = $rangeDate;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            // Kelompokkan data berdasarkan tahun dan bulan
            $groupedData = $this->barangKeluar->groupBy(function ($item) {
                $date = Carbon::parse($item->tanggal);
                return $date->format('Y-m');
            });

            $sheets = [];
            foreach ($groupedData as $period => $data) {
                $sheets[] = new class($period, $data) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
                {
                    protected $period;
                    protected $barangKeluar;

                    public function __construct($period, $barangKeluar)
                    {
                        $this->period = $period;
                        $this->barangKeluar = $barangKeluar;
                    }

                    public function collection()
                    {
                        $data = $this->barangKeluar->map(function ($item) {
                            $stokBarang = StokBarang::find($item->id_stok_barang);
                            $kendaraan = Kendaraan::find($item->id_kendaraan);
                            $satuan = Satuan::find($stokBarang->id_satuan);

                            $dataKendaraan = '-';
                            if ($kendaraan) {
                                $dataKendaraan = $kendaraan->nopol . ' ' . $kendaraan->merk;
                            }

                            return [
                                'tanggal_keluar' => $item->tanggal_keluar,
                                'pengguna' => $item->pengguna,
                                'barang' => $stokBarang->nama . ' ' . $stokBarang->merk,
                                'jumlah' => $item->jumlah,
                                // 'sisa_stok' => $item->sisa_stok ?? '-',
                                'satuan' => $satuan->nama ?? '-',
                                'kendaraan' => $dataKendaraan,
                                'lokasi' => $item->lokasi ?? '-',
                                'ket' => $item->ket ?? '-',
                            ];
                        });

                        return $data;
                    }

                    public function headings(): array
                    {
                        return [
                            ['List Data Barang Keluar ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                            ['Tanggal Pengambilan', 'Pengguna', 'Barang', 'Jumlah', 'Satuan', 'Kendaraan', 'Lokasi', 'Keterangan']
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        // Header
                        $sheet->mergeCells("A1:H1");
                        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

                        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
                        $sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle('Barang Keluar Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->barangKeluar, $this->rangeDate) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $barangKeluar;
                protected $rangeDate;

                public function __construct($mode, $barangKeluar, $rangeDate)
                {
                    $this->barangKeluar = $barangKeluar;
                    $this->rangeDate = $rangeDate;
                }

                public function collection()
                {
                    $data = $this->barangKeluar->map(function ($item) {
                        $stokBarang = StokBarang::find($item->id_stok_barang);
                        $kendaraan = Kendaraan::find($item->id_kendaraan);
                        $satuan = Satuan::find($stokBarang->id_satuan);

                        $dataKendaraan = '-';
                        if ($kendaraan) {
                            $dataKendaraan = $kendaraan->nopol . ' ' . $kendaraan->merk;
                        }

                        return [
                            'tanggal_keluar' => $item->tanggal_keluar,
                            'pengguna' => $item->pengguna,
                            'barang' => $stokBarang->nama . ' ' . $stokBarang->merk,
                            'jumlah' => $item->jumlah,
                            // 'sisa_stok' => $item->sisa_stok,
                            'satuan' => $satuan->nama ?? '-',
                            'kendaraan' => $dataKendaraan,
                            'lokasi' => $item->lokasi ?? '-',
                            'ket' => $item->ket ?? '-',
                        ];
                    });

                    return $data;
                }

                public function headings(): array
                {
                    return [
                        ['List Data Barang Keluar ' . $this->rangeDate],
                        ['Tanggal Pengambilan', 'Pengguna', 'Barang', 'Jumlah', 'Satuan', 'Kendaraan', 'Lokasi', 'Keterangan']
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Header
                    $sheet->mergeCells("A1:H1");
                    $sheet->getStyle('A1:H1')->getFont()->setBold(true);

                    $sheet->getStyle('A2:H2')->getFont()->setBold(true);
                    $sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Data Barang Keluar');
                }
            }
        ];
    }
}
