<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'kecamatans';

    protected $fillable = [
        'nama',
        'latitude',
        'longitude',
        'warna_polygon',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Relasi ke data statistik tahun terbaru (default 2024).
     */
    public function statistik(): HasOne
    {
        return $this->hasOne(DataStatistik::class, 'kecamatan_id')->latestOfMany();
    }

    /**
     * Semua riwayat data statistik kecamatan.
     */
    public function semuaStatistik(): HasMany
    {
        return $this->hasMany(DataStatistik::class, 'kecamatan_id');
    }
}
