<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Services\SawService;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    /**
     * Menampilkan halaman peta spasial (Web GIS) Kabupaten Jember.
     * Mengintegrasikan basemap Leaflet.js, Layer GeoJSON batas wilayah,
     * Visualisasi Tematik Choropleth, Popup Interaktif, dan Kontrol Peta Lanjutan.
     */
    public function index()
    {
        $totalKecamatan = Kecamatan::count();

        // Daftar kecamatan terurut untuk kontrol pencarian / quick jump
        $kecamatanList = Kecamatan::orderBy('nama')->get(['id', 'nama', 'latitude', 'longitude']);

        // Peta ID kecamatan untuk menghubungkan GeoJSON ke rute detail kecamatan
        $kecamatanMap = Kecamatan::pluck('id', 'nama');

        // Ambil data mentah kecamatan dan statistik untuk perhitungan SPK
        $rawData = Kecamatan::with('statistik')->get()->map(function ($kec) {
            $stat = $kec->statistik;
            return [
                'kecamatan_id'     => $kec->id,
                'nama'             => $kec->nama,
                'latitude'         => (float) $kec->latitude,
                'longitude'        => (float) $kec->longitude,
                'jumlah_penduduk'  => $stat ? (int)   $stat->jumlah_penduduk  : 0,
                'laju_pertumbuhan' => $stat ? (float) $stat->laju_pertumbuhan : 0,
                'jumlah_desa'      => $stat ? (int)   $stat->jumlah_desa      : 0,
            ];
        })->toArray();

        // Eksekusi kalkulasi SAW untuk SPK 1 dan SPK 2
        $spk1Result = SawService::hitung(SawService::SCENARIO_1, $rawData);
        $spk2Result = SawService::hitung(SawService::SCENARIO_2, $rawData);

        // Petakan hasil SPK dengan key = Nama Kecamatan untuk akses instan di GeoJSON layer
        $spk1ByName = [];
        foreach ($spk1Result['hasil'] as $item) {
            $spk1ByName[$item['nama']] = $item;
        }

        $spk2ByName = [];
        foreach ($spk2Result['hasil'] as $item) {
            $spk2ByName[$item['nama']] = $item;
        }

        // Koordinat titik tengah geografis Kabupaten Jember
        $centerCoords = [
            'lat' => -8.17211,
            'lng' => 113.70011,
            'zoom' => 10,
        ];

        return view('peta.index', [
            'totalKecamatan' => $totalKecamatan,
            'kecamatanList'  => $kecamatanList,
            'kecamatanMap'   => $kecamatanMap,
            'spk1ByName'     => $spk1ByName,
            'spk2ByName'     => $spk2ByName,
            'scenario1'      => $spk1Result['scenario'],
            'scenario2'      => $spk2Result['scenario'],
            'centerCoords'   => $centerCoords,
        ]);
    }
}
