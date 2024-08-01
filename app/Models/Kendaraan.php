<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;
    protected $table = 'tbl_kendaraan';
    protected $primaryKey = 'id_kendaraan';

    protected $fillable = [
        'nopol',
        'merk'
    ];
}
