<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'tbl_activity';
    protected $primaryKey = 'id_activity';

    protected $fillable = [
        'id_relation',
        'description',
        'scope',
        'action',
        'user',
        'action_time',
    ];
}
