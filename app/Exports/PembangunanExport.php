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

class PembangunanExport implements WithMultipleSheets
{
    use Exportable;

    protected $mode;
    protected $pengurugan;
    protected $nama;

    public function __construct($mode, $pengurugan, $nama)
    {
        $this->mode = $mode;
        $this->pengurugan = $pengurugan;
        $this->nama = $nama;
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
                $sheets[] = new class($period, $data, $this->nama) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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

                                $data->push([
                                    'ket' => '-',
                                    'tanggal' => $item->tanggal,
                                    'nama' => $item->nama,
                                    'ukuran' => $item->ukuran,
                                    'deskripsi' => $item->deskripsi,
                                    'jumlah' => $item->jumlah,
                                    'satuan' => $item->satuan,
                                    'harga' => $harga,
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
                            'Keterangan',
                            'Tanggal',
                            'Nama',
                            'Ukuran',
                            'Deskripsi',
                            'Jumlah',
                            'Satuan',
                            'Harga',
                            'Total Harga'
                        ];
                    }

                    public function styles(Worksheet $sheet)
                    {
                        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
                        $sheet->getStyle('A:I')->getAlignment()->setHorizontal('center');
                        $sheet->setTitle($this->nama . ' Periode ' . Carbon::createFromFormat('Y-m', $this->period)->format('M-Y'));
                    }
                };
            }

            return $sheets;
        }

        // Handle mode lain jika ada
        return [
            new class('All Data', $this->pengurugan, $this->nama) implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
            {
                protected $pengurugan;
                protected $nama;

                public function __construct($mode, $pengurugan, $nama)
                {
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

                            $data->push([
                                'ket' => '-',
                                'tanggal' => $item->tanggal,
                                'nama' => $item->nama,
                                'ukuran' => $item->ukuran,
                                'deskripsi' => $item->deskripsi,
                                'jumlah' => $item->jumlah,
                                'satuan' => $item->satuan,
                                'harga' => $harga,
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
                        'Keterangan',
                        'Tanggal',
                        'Nama',
                        'Ukuran',
                        'Deskripsi',
                        'Jumlah',
                        'Satuan',
                        'Harga',
                        'Total Harga'
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->getStyle('A1:I1')->getFont()->setBold(true);
                    $sheet->getStyle('A:I')->getAlignment()->setHorizontal('center');
                    $sheet->setTitle('Pengeluaran ' . $this->nama);
                }
            }
        ];
    }
}