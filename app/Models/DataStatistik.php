<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataStatistik extends Model
{
    use HasFactory;

    protected $table = 'data_statistiks';

    protected $fillable = [
        'kecamatan_id',
        'jumlah_penduduk',
        'laju_pertumbuhan',
        'jumlah_desa',
        'tahun',
    ];

    protected $casts = [
        'jumlah_penduduk' => 'integer',
        'laju_pertumbuhan' => 'float',
        'jumlah_desa' => 'integer',
        'tahun' => 'integer',
    ];

    /**
     * Relasi balik ke model Kecamatan.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
}
