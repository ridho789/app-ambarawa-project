<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;
    protected $table = 'tbl_proyek';
    protected $primaryKey = 'id_proyek';

    protected $fillable = [
        'nama',
        'subproyek'
    ];
}
