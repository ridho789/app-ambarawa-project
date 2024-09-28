<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Satuan;

class StokBarangExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected $StokBarang;

    public function __construct($StokBarang)
    {
        $this->StokBarang = $StokBarang;
    }

    public function collection()
    {
        $data = $this->StokBarang->map(function ($item) {
            $satuan = Satuan::find($item->id_satuan);

            return [
                'nama' => $item->nama ?? '-',
                'merk' => $item->merk ?? '-',
                'type' => $item->type ?? '-',
                'kategori' => $item->kategori ?? '-',
                'jumlah' => $item->jumlah ?? '-',
                'satuan' => $satuan->nama ?? '-',
                'no_rak' => $item->no_rak ?? '-',
                'keterangan' => $item->keterangan ?? '-',
            ];
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            ['List Data Stok Barang'],
            ['Nama Barang', 'Merk', 'Type', 'Kategori', 'Jumlah Stok', 'Satuan', 'No. Rak', 'Keterangan']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header
        $sheet->mergeCells("A1:H1");
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
        $sheet->setTitle('Data Stok Barang');
    }
}
