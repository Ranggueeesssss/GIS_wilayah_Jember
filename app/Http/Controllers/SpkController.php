<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Services\SawService;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    /**
     * Mempersiapkan data mentah dari database untuk diproses SawService.
     * Method ini dipakai bersama oleh semua skenario SPK.
     *
     * @return array
     */
    private function getRawData(): array
    {
        return Kecamatan::with('statistik')
            ->get()
            ->map(function ($kec) {
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
            })
            ->toArray();
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SKENARIO 1: Analisis Potensi & Dinamika Demografi
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Halaman hasil SPK 1 — Analisis Potensi & Dinamika Demografi.
     * Menampilkan ranking kecamatan berdasarkan jumlah penduduk dan laju pertumbuhan.
     */
    public function spk1(Request $request)
    {
        $rawData  = $this->getRawData();
        $hasil    = SawService::hitung(SawService::SCENARIO_1, $rawData);
        $scenario = SawService::getScenario(SawService::SCENARIO_1);
        $validasi = SawService::validasiBobot(SawService::SCENARIO_1);

        // Filter tampilan berdasarkan kategori (opsional)
        $filterKategori = $request->query('kategori', 'semua');
        $hasilFiltered  = $hasil['hasil'];

        if ($filterKategori !== 'semua') {
            $hasilFiltered = array_filter(
                $hasil['hasil'],
                fn($row) => strtolower($row['kategori']) === strtolower($filterKategori)
            );
        }

        return view('spk.spk1', [
            'hasil'          => $hasil,
            'hasilFiltered'  => array_values($hasilFiltered),
            'scenario'       => $scenario,
            'validasi'       => $validasi,
            'filterKategori' => $filterKategori,
            'scenarioKey'    => SawService::SCENARIO_1,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SKENARIO 2: Prioritas Beban Pelayanan Administrasi Wilayah
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Halaman hasil SPK 2 — Prioritas Beban Pelayanan Administrasi Wilayah.
     * Menampilkan ranking kecamatan berdasarkan jumlah desa, penduduk, dan laju pertumbuhan.
     */
    public function spk2(Request $request)
    {
        $rawData  = $this->getRawData();
        $hasil    = SawService::hitung(SawService::SCENARIO_2, $rawData);
        $scenario = SawService::getScenario(SawService::SCENARIO_2);
        $validasi = SawService::validasiBobot(SawService::SCENARIO_2);

        $filterKategori = $request->query('kategori', 'semua');
        $hasilFiltered  = $hasil['hasil'];

        if ($filterKategori !== 'semua') {
            $hasilFiltered = array_filter(
                $hasil['hasil'],
                fn($row) => strtolower($row['kategori']) === strtolower($filterKategori)
            );
        }

        return view('spk.spk2', [
            'hasil'          => $hasil,
            'hasilFiltered'  => array_values($hasilFiltered),
            'scenario'       => $scenario,
            'validasi'       => $validasi,
            'filterKategori' => $filterKategori,
            'scenarioKey'    => SawService::SCENARIO_2,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  API Endpoint — Data SPK untuk Leaflet.js (dipakai di Tahap 4 - Peta)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * API JSON endpoint yang mengembalikan hasil perhitungan SPK.
     * Digunakan oleh JavaScript Leaflet.js untuk mewarnai peta choropleth.
     *
     * @param  string  $scenario  'spk1' atau 'spk2'
     */
    public function api(string $scenario)
    {
        $allowed = [SawService::SCENARIO_1, SawService::SCENARIO_2];

        if (!in_array($scenario, $allowed)) {
            return response()->json(['error' => 'Skenario tidak valid.'], 400);
        }

        $rawData = $this->getRawData();
        $hasil   = SawService::hitung($scenario, $rawData);

        return response()->json([
            'scenario_key'  => $scenario,
            'scenario_nama' => $hasil['scenario']['nama'],
            'jumlah_data'   => $hasil['jumlah_data'],
            'data'          => SawService::toApiFormat($hasil),
        ]);
    }
}
