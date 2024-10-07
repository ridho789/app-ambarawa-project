<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\BBM;
use App\Models\Kendaraan;

class BBMImport implements ToCollection
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
            if ($rowNumber >= 4 && $row[0]) {

                // Check data kendaraan
                $idKendaraan = null;
                if ($row[2]) {
                    $checkKendaraan = Kendaraan::where('nopol', strtoupper($row[2]))->first();
                    if (empty($checkKendaraan)) {
                        if ($row[2] && $row[3] && $row[4]) {
                            $checkKendaraan = Kendaraan::create([
                                'nopol' => strtoupper($row[2]),
                                'merk' => strtoupper($row[3]),
                                'jns_bbm' => strtoupper($row[4])
                            ]);
                        }
                    }
                    
                    // idKendaraan
                    $idKendaraan = $checkKendaraan->id_kendaraan ?? 0;
                }

                // Pastikan nilai numerik, jika tidak berikan default nilai 0
                $km_awal = is_numeric($row[6]) ? (float) $row[6] : 0.0;
                $km_isi_seb = 0.0;
                $km_isi_sek = is_numeric($row[7]) ? (float) $row[7] : 0.0;
                $km_akhir = is_numeric($row[8]) ? (float) $row[8] : 0.0;

                // Check previous BBM record
                $checkExitingBBM = BBM::where('id_kendaraan', $idKendaraan)->orderBy('created_at', 'desc')->first();
                if ($checkExitingBBM) {
                    $km_isi_seb = is_numeric($checkExitingBBM->km_isi_sek) ? (float) $checkExitingBBM->km_isi_sek : 0.0;
                }

                // Hitung KM per liter
                $km_ltr = 0.0;
                if (is_numeric($km_isi_seb) && is_numeric($km_isi_sek) && is_numeric($row[5])) {
                    $liter = (float)$row[5];
                    if ($liter != 0 && $km_isi_seb != 0) {
                        $km_ltr = ($km_isi_sek - $km_isi_seb) / $liter;
                    }
                }

                // Hitung total KM
                $tot_km = 0.0;
                if (is_numeric($km_awal) && is_numeric($km_akhir)) {
                    $tot_km = $km_akhir - $km_awal;
                }

                // Hapus karakter non-numerik pada harga
                $numericHarga = isset($row[9]) ? (float) preg_replace("/[^0-9.]/", "", explode(",", $row[9])[0]) : 0.0;

                // Total Value
                $totalValue = 0.0;
                if (is_numeric($row[5]) && is_numeric($numericHarga)) {
                    $totalValue = (float)$row[5] * $numericHarga;
                } else {
                    $totalValue = isset($row[10]) ? (float) preg_replace("/[^0-9.]/", "", explode(",", $row[10])[0]) : 0.0;
                }

                // Pastikan tot_harga adalah numerik
                $numericTotHarga = is_numeric($totalValue) ? (float) $totalValue : 0.0;

                // Data BBM
                $dataBBM = [
                    'nama' => strtoupper($row[0]),
                    'tanggal' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[1]),
                    'id_kendaraan' => (int)$idKendaraan,
                    'liter' => is_numeric($row[5]) ? (float)$row[5] : 0.0,
                    'km_awal' => $km_awal,
                    'km_isi_seb' => $km_isi_seb,
                    'km_isi_sek' => $km_isi_sek,
                    'km_akhir' => $km_akhir,
                    'km_ltr' => $km_ltr,
                    'tot_km' => $tot_km,
                    'harga' => $numericHarga,
                    'tot_harga' => $numericTotHarga,
                    'ket' => $row[11] ?? '-'
                ];

                // Check if the BBM data already exists
                $exitingBBM = BBM::where('tanggal', $dataBBM['tanggal'])
                    ->where('nama', $dataBBM['nama'])
                    ->where('id_kendaraan', $dataBBM['id_kendaraan'])
                    ->where('liter', $dataBBM['liter'])
                    ->where('km_awal', $dataBBM['km_awal'])
                    ->where('km_isi_sek', $dataBBM['km_isi_sek'])
                    ->where('km_akhir', $dataBBM['km_akhir'])
                    ->where('harga', $dataBBM['harga'])
                    ->where('tot_harga', $dataBBM['tot_harga'])
                    ->where('ket', $dataBBM['ket'])
                    ->first();

                if ($exitingBBM) {
                    $errorMessage = 'Error importing data: The data in the row ' . $currentRow . ' already exists in the system ';
                    $this->logErrors[] = $errorMessage;
                } else {
                    // Create BBM record
                    BBM::create($dataBBM);
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
