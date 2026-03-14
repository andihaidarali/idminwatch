<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WilayahTambang extends Model
{
    protected $table = 'wilayah_tambang';

    protected $fillable = [
        'public_uid',
        'detail_tambang_id',
        'nama',
        'nomor_sk',
        'tanggal_berlaku',
        'tanggal_berakhir',
        'kegiatan',
        'kegiatan_en',
        'nama_provinsi',
        'nama_kabupaten',
        'lokasi',
        'jenis_izin',
        'jenis_tambang',
        'status',
        'luas_sk_ha',
        'luas_overlap',
        'dampak_sosial',
        'dampak_sosial_en',
        'dampak_ekonomi',
        'dampak_ekonomi_en',
        'dampak_lingkungan',
        'dampak_lingkungan_en',
        'dokumentasi',
        'geom',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
        'tanggal_berakhir' => 'date',
        'luas_sk_ha' => 'double',
        'luas_overlap' => 'double',
        'dokumentasi' => 'array',
    ];

    /**
     * Perusahaan pemilik wilayah tambang.
     */
    public function detailTambang(): BelongsTo
    {
        return $this->belongsTo(DetailTambang::class);
    }

    public function jenisTambangRef(): BelongsTo
    {
        return $this->belongsTo(JenisTambangRef::class, 'jenis_tambang', 'nama');
    }

    protected static function booted(): void
    {
        static::creating(function (WilayahTambang $wilayahTambang): void {
            if (!$wilayahTambang->public_uid) {
                $wilayahTambang->public_uid = (string) Str::lower((string) Str::ulid());
            }
        });
    }
}
