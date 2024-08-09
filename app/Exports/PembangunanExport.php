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
use App\Models\Proyek;
use App\Models\Satuan;

class PembangunanExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $pengurugan;
    protected $nama;
    protected $rangeDate;

    public function __construct($mode, $pengurugan, $nama, $rangeDate)
    {
        $this->mode = $mode;
        $this->pengurugan = $pengurugan;
        $this->nama = $nama;
        $this->rangeDate = $rangeDate;
    }

    public function sheets(): array
    {
        if ($this->mode == 'all_data') {
            // Kelompokkan data berdasarkan tahun dan bulan
            $groupedData = $this->pengurugan->groupBy(function ($item) {
                $date = Carbon::parse($item->tanggal);
                return $date->format('Y-m');
            });

            $sheets = [];
            foreach ($groupedData as $period => $data) {
                $sheets[] = new class($period, $data, $this->nama) implements FromCollection, WithHeadings, WithStyles
                {
                    protected $period;
                    protected $pengurugan;
                    protected $nama;

                    public function __construct($period, $pengurugan, $nama)
                    {
                        $this->period = $period;
                        $this->pengurugan = $pengurugan;
                        $this->nama = $nama;
                    }

                    public function collection()
                    {
                        // Mengelompokkan data berdasarkan id_proyek
                        $groupedData = $this->pengurugan->groupBy('id_proyek');
                        $data = collect();

                        foreach ($groupedData as $idProyek => $items) {
                            if ($idProyek) {
                                $namaProyek = $this->getNamaProyek($idProyek);

                                // Menambahkan baris nama proyek jika id_proyek ada
                                $data->push([
                                    'ket' => 'Proyek: ' . $namaProyek,
                                    'tanggal' => '',
                                    'nama' => '',
                                    'ukuran' => '',
                                    'deskripsi' => '',
                                    'jumlah' => '',
                                    'satuan' => '',
                                    'harga' => '',
                                    'toko' => '',
                                    'tot_harga' => '',
                                ]);
                            }

                            // Menghitung total keseluruhan per id_proyek
                            $totalKeseluruhan = $items->sum(function ($item) {
                                return $item->tot_harga ?? 0;
                            });

                            // Menambahkan baris data per id_proyek
                            foreach ($items as $item) {
                                $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                                $tot_harga = 'Rp ' . number_format($item->tot_harga ?? 0, 0, ',', '.');
                                $satuan = Satuan::find($item->id_satuan);

                                $data->push([
                                    'ket' => '-',
                                    'tanggal' => $item->tanggal,
                                    'nama' => $item->nama,
                                    'ukuran' => $item->ukuran,
                                    'deskripsi' => $item->deskripsi,
                                    'jumlah' => $item->jumlah,
                                    'satuan' => $satuan->nama,
                                    'harga' => $harga,
                                    'toko' => $item->toko,
                                    'tot_harga' => $tot_harga,
                                ]);
                            }

                            // Menambahkan baris total keseluruhan per id_proyek
                            $data->push([
                                'ket' => 'Total Keseluruhan',
                                'tanggal' => '',
                                'nama' => '',
                                'ukuran' => '',
                                'deskripsi' => '',
                                'jumlah' => '',
                                'satuan' => '',
                                'harga' => '',
                                'toko' => '',
                                'tot_harga' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                            ]);

                            // Tambahkan baris kosong untuk pemisah antar proyek
                            $data->push([
                                'ket' => '',
                                'tanggal' => '',
                                'nama' => '',
                                'ukuran' => '',
                                'deskripsi' => '',
                                'jumlah' => '',
                                'satuan' => '',
                                'harga' => '',
                                'toko' => '',
                                'tot_harga' => '',
                            ]);
                        }

                        return $data;
                    }

                    private function getNamaProyek($idProyek)
                    {
                        return Proyek::find($idProyek)->nama;
                    }

                    public function headings(): array
                    {
                        return [
                            ['Pengeluaran ' . $this->nama . ' ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y')],
                            ['Keterangan',
                            'Tanggal',
                            'Nama',
                            'Ukuran',
                            'Deskripsi',
                            'Jumlah',
                            'Satuan',
                            'Harga',
                            'Toko',
                            'Total Harga']
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        // Header
                        $sheet->mergeCells("A1:J1");
                        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

                        // Mengatur kolom E
                        $sheet->getColumnDimension('E')->setWidth(50);
                        $sheet->getStyle('E4:E' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                        // Menyesuaikan lebar kolom lainnya
                        foreach (range('A', 'J') as $column) {
                            if ($column !== 'E') {
                                $sheet->getColumnDimension($column)->setAutoSize(true);
                                $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                            }
                        }

                        $sheet->getStyle('A2:J2')->getFont()->setBold(true);
                        $sheet->getStyle('A:J')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->nama . ' Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->pengurugan, $this->nama, $this->rangeDate) implements FromCollection, WithHeadings, WithStyles
            {
                protected $pengurugan;
                protected $nama;
                protected $rangeDate;

                public function __construct($mode, $pengurugan, $nama, $rangeDate)
                {
                    $this->pengurugan = $pengurugan;
                    $this->nama = $nama;
                    $this->rangeDate = $rangeDate;
                }

                public function collection()
                {
                    // Mengelompokkan data berdasarkan id_proyek
                    $groupedData = $this->pengurugan->groupBy('id_proyek');
                    $data = collect();

                    foreach ($groupedData as $idProyek => $items) {
                        if ($idProyek) {
                            $namaProyek = $this->getNamaProyek($idProyek);

                            // Menambahkan baris nama proyek jika id_proyek ada
                            $data->push([
                                'ket' => 'Proyek: ' . $namaProyek,
                                'tanggal' => '',
                                'nama' => '',
                                'ukuran' => '',
                                'deskripsi' => '',
                                'jumlah' => '',
                                'satuan' => '',
                                'harga' => '',
                                'toko' => '',
                                'tot_harga' => '',
                            ]);
                        }

                        // Menghitung total keseluruhan per id_proyek
                        $totalKeseluruhan = $items->sum(function ($item) {
                            return $item->tot_harga ?? 0;
                        });

                        // Menambahkan baris data per id_proyek
                        foreach ($items as $item) {
                            $harga = 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.');
                            $tot_harga = 'Rp ' . number_format($item->tot_harga ?? 0, 0, ',', '.');
                            $satuan = Satuan::find($item->id_satuan);

                            $data->push([
                                'ket' => '-',
                                'tanggal' => $item->tanggal,
                                'nama' => $item->nama,
                                'ukuran' => $item->ukuran,
                                'deskripsi' => $item->deskripsi,
                                'jumlah' => $item->jumlah,
                                'satuan' => $satuan->nama,
                                'harga' => $harga,
                                'toko' => $item->toko,
                                'tot_harga' => $tot_harga,
                            ]);
                        }

                        // Menambahkan baris total keseluruhan per id_proyek
                        $data->push([
                            'ket' => 'Total Keseluruhan',
                            'tanggal' => '',
                            'nama' => '',
                            'ukuran' => '',
                            'deskripsi' => '',
                            'jumlah' => '',
                            'satuan' => '',
                            'harga' => '',
                            'toko' => '',
                            'tot_harga' => 'Rp ' . number_format($totalKeseluruhan, 0, ',', '.'),
                        ]);

                        // Tambahkan baris kosong untuk pemisah antar proyek
                        $data->push([
                            'ket' => '',
                            'tanggal' => '',
                            'nama' => '',
                            'ukuran' => '',
                            'deskripsi' => '',
                            'jumlah' => '',
                            'satuan' => '',
                            'harga' => '',
                            'toko' => '',
                            'tot_harga' => '',
                        ]);
                    }

                    return $data;
                }

                private function getNamaProyek($idProyek)
                {
                    return Proyek::find($idProyek)->nama;
                }

                public function headings(): array
                {
                    return [
                        ['Pengeluaran ' . $this->nama . ' ' . $this->rangeDate],
                        ['Keterangan',
                        'Tanggal',
                        'Nama',
                        'Ukuran',
                        'Deskripsi',
                        'Jumlah',
                        'Satuan',
                        'Harga',
                        'Toko',
                        'Total Harga']
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Header
                    $sheet->mergeCells("A1:J1");
                    $sheet->getStyle('A1:J1')->getFont()->setBold(true);

                    // Mengatur kolom E
                    $sheet->getColumnDimension('E')->setWidth(50);
                    $sheet->getStyle('E4:E' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                    // Menyesuaikan lebar kolom lainnya
                    foreach (range('A', 'J') as $column) {
                        if ($column !== 'E') {
                            $sheet->getColumnDimension($column)->setAutoSize(true);
                            $sheet->getStyle($column . '3:' . $column . $sheet->getHighestRow())->getAlignment()->setHorizontal('center')->setVertical('center');
                        }
                    }

                    $sheet->getStyle('A2:J2')->getFont()->setBold(true);
                    $sheet->getStyle('A:J')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran ' . $this->nama);
                }
            }
        ];
    }
}
