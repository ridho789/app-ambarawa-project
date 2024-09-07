<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Sembako;
use App\Models\Satuan;

class SembakoImport implements ToCollection
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
            if ($rowNumber >= 2 && strtolower($row[0]) != 'total') {
                // Satuan
                $IdUnit = null;
                if ($row[1] && $row[4]) {
                    $checkUnit = Satuan::where('nama', strtoupper($row[4]))->first();
                    if (empty($checkUnit)) {
                        $checkUnit = Satuan::create(['nama' => strtoupper($row[4])]);
                    }
                    
                    // IdUnit
                    $IdUnit = $checkUnit->id_satuan;
                }

                $numericHarga = preg_replace("/[^0-9]/", "", explode(",", $row[5])[0]);
                $totalValue = 0;

                if ($row[3] && $row[5]) {
                    $totalValue = $row[3] * $numericHarga;

                } else {
                    $totalValue = preg_replace("/[^0-9]/", "", explode(",", $row[6])[0]);
                }

                $numericTotal = preg_replace("/[^0-9]/", "", explode(",", $totalValue)[0]);

                $dataSembako = [
                    'tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]),
                    'nama' => strtoupper($row[2]),
                    'qty' => $row[3],
                    'id_satuan' => $IdUnit,
                    'harga' => $numericHarga,
                    'total' => $numericTotal,
                ];

                $exitingSembako = Sembako::where('tanggal', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]))
                ->where('nama', strtoupper($row[2]))->where('qty', $row[3])->where('id_satuan', $IdUnit)->where('harga', $numericHarga)->where('total', $numericTotal)->first();
                
                if ($exitingSembako) {
                    $errorMessage = 'Error importing data: The data in the row ' . $currentRow . ' already exists in the system ';
                    $this->logErrors[] = $errorMessage;

                } else {
                    // Create sembako
                    Sembako::create($dataSembako);
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
