<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\StokBarang;
use App\Models\BarangMasuk;
use App\Models\Satuan;

class StokImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    private $logErrors = [];

    public function collection(Collection $collection)
    {
        $rowNumber = 0;
        $currentRow = 0;
        foreach ($collection as $row) {
            $currentRow++;
            if ($rowNumber >= 3 && $row[1]) {
                // Satuan
                $IdUnit = null;
                if ($row[1] && $row[6]) {
                    $checkUnit = Satuan::where('nama', strtoupper($row[6]))->first();
                    if (empty($checkUnit)) {
                        $checkUnit = Satuan::create(['nama' => strtoupper($row[6])]);
                    }
                    
                    // IdUnit
                    $IdUnit = $checkUnit->id_satuan;
                }

                $dataStokBarang = [
                    'nama' => strtoupper($row[1]) ?? '-',
                    'jumlah' => $row[2] ?? '-',
                    'kategori' => strtoupper($row[3]) ?? '-',
                    'merk' => strtoupper($row[4]) ?? '-',
                    'type' => strtoupper($row[5]) ?? '-',
                    'id_satuan' => $IdUnit,
                    'no_rak' => $row[7] ?? '-',
                    'keterangan' => ucfirst($row[8]) ?? '-'
                ];

                $exitingStokBarang = StokBarang::where('nama', strtoupper($row[1]))->where('jumlah', $row[2])->where('kategori', strtoupper($row[3]))
                ->where('merk', strtoupper($row[4]))->where('type', strtoupper($row[5]))->where('id_satuan', $IdUnit)
                ->where('no_rak', $row[7])->where('keterangan', ucfirst($row[8]))->first();
                
                if ($exitingStokBarang) {
                    $errorMessage = 'Error importing data: The data in the row ' . $currentRow . ' already exists in the system ';
                    $this->logErrors[] = $errorMessage;

                } else {
                    // Create stok barang
                    StokBarang::create($dataStokBarang);
                }
            }

            // Next row
            $rowNumber++;
        }
    }

    public function getLogErrors()
    {
        return $this->logErrors;
    }
}
