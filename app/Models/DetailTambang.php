<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailTambang extends Model
{
    protected $table = 'detail_tambang';

    protected $fillable = [
        'nama_perusahaan',
        'profil_singkat',
        'profil_singkat_en',
    ];

    /**
     * Wilayah tambang yang dimiliki perusahaan ini.
     */
    public function wilayahTambang(): HasMany
    {
        return $this->hasMany(WilayahTambang::class);
    }
}
