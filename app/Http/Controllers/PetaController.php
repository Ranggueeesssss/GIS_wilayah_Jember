<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    /**
     * Menampilkan halaman peta spasial (Web GIS) Kabupaten Jember.
     * Inisialisasi basemap Leaflet.js dengan koordinat pusat Jember.
     */
    public function index()
    {
        $totalKecamatan = Kecamatan::count();

        // Koordinat titik tengah geografis Kabupaten Jember
        $centerCoords = [
            'lat' => -8.17211,
            'lng' => 113.70011,
            'zoom' => 10,
        ];

        return view('peta.index', [
            'totalKecamatan' => $totalKecamatan,
            'centerCoords'   => $centerCoords,
        ]);
    }
}
