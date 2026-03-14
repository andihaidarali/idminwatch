<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisTambangRef extends Model
{
    protected $table = 'jenis_tambang_refs';

    protected $fillable = [
        'nama',
        'nama_en',
    ];

    public function wilayahTambang(): HasMany
    {
        return $this->hasMany(WilayahTambang::class, 'jenis_tambang', 'nama');
    }
}
