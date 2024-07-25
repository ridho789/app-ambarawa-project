<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sembako extends Model
{
    use HasFactory;
    protected $table = 'tbl_sembako';
    protected $primaryKey = 'id_sembako';

    protected $fillable = [
        'tanggal',
        'nama',
        'qty',
        'unit',
        'harga',
        'total'
    ];
}
