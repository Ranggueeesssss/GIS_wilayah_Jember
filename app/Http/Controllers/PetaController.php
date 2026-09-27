<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    /**
     * Menampilkan halaman peta spasial (Web GIS) Kabupaten Jember.
     * Mengintegrasikan basemap Leaflet.js dan Layer GeoJSON batas wilayah 31 kecamatan.
     */
    public function index()
    {
        $totalKecamatan = Kecamatan::count();

        // Peta ID kecamatan untuk menghubungkan GeoJSON ke rute detail kecamatan
        $kecamatanMap = Kecamatan::pluck('id', 'nama');

        // Koordinat titik tengah geografis Kabupaten Jember
        $centerCoords = [
            'lat' => -8.17211,
            'lng' => 113.70011,
            'zoom' => 10,
        ];

        return view('peta.index', [
            'totalKecamatan' => $totalKecamatan,
            'kecamatanMap'   => $kecamatanMap,
            'centerCoords'   => $centerCoords,
        ]);
    }
}
