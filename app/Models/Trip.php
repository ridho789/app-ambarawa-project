<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $table = 'tbl_trips';
    protected $primaryKey = 'id_trip';

    protected $fillable = [
        'id_kendaraan',
        'tanggal',
        'kota',
        'ket',
        'uraian',
        'qty',
        'unit',
        'km_awal',
        'km_isi_seb',
        'km_isi_sek',
        'km_akhir',
        'km_ltr',
        'harga',
        'total'
    ];
}
