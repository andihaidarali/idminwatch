<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KawasanHutan extends Model
{
    protected $table = 'kawasan_hutan';

    protected $fillable = [
        'nama',
        'fungsi',
        'sumber_data',
        'geom',
    ];
}
