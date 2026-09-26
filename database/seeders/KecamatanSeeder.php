<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\DataStatistik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sumber: BPS Kabupaten Jember 2024 / 2025
     * Tabel 3.1.1 (Penduduk & Laju Pertumbuhan) & Tabel 2.1.1 (Jumlah Desa/Kelurahan)
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Kencong',
                'lat' => -8.285510,
                'lng' => 113.358112,
                'penduduk' => 71155,
                'laju' => -0.42,
                'desa' => 5,
            ],
            [
                'nama' => 'Gumuk Mas',
                'lat' => -8.333965,
                'lng' => 113.413900,
                'penduduk' => 90255,
                'laju' => 0.01,
                'desa' => 8,
            ],
            [
                'nama' => 'Puger',
                'lat' => -8.328785,
                'lng' => 113.477199,
                'penduduk' => 126660,
                'laju' => 0.19,
                'desa' => 12,
            ],
            [
                'nama' => 'Wuluhan',
                'lat' => -8.351245,
                'lng' => 113.542359,
                'penduduk' => 129414,
                'laju' => 0.56,
                'desa' => 7,
            ],
            [
                'nama' => 'Ambulu',
                'lat' => -8.379440,
                'lng' => 113.612737,
                'penduduk' => 121482,
                'laju' => 0.64,
                'desa' => 7,
            ],
            [
                'nama' => 'Tempurejo',
                'lat' => -8.419075,
                'lng' => 113.752272,
                'penduduk' => 82126,
                'laju' => 0.38,
                'desa' => 8,
            ],
            [
                'nama' => 'Silo',
                'lat' => -8.258970,
                'lng' => 113.867990,
                'penduduk' => 112043,
                'laju' => 0.43,
                'desa' => 9,
            ],
            [
                'nama' => 'Mayang',
                'lat' => -8.206520,
                'lng' => 113.811729,
                'penduduk' => 52840,
                'laju' => 0.56,
                'desa' => 7,
            ],
            [
                'nama' => 'Mumbulsari',
                'lat' => -8.255755,
                'lng' => 113.742095,
                'penduduk' => 70473,
                'laju' => 0.53,
                'desa' => 7,
            ],
            [
                'nama' => 'Jenggawah',
                'lat' => -8.288960,
                'lng' => 113.631917,
                'penduduk' => 91828,
                'laju' => 0.58,
                'desa' => 8,
            ],
            [
                'nama' => 'Ajung',
                'lat' => -8.239380,
                'lng' => 113.660642,
                'penduduk' => 86033,
                'laju' => 0.62,
                'desa' => 7,
            ],
            [
                'nama' => 'Rambipuji',
                'lat' => -8.230265,
                'lng' => 113.598181,
                'penduduk' => 88684,
                'laju' => 0.15,
                'desa' => 8,
            ],
            [
                'nama' => 'Balung',
                'lat' => -8.273950,
                'lng' => 113.519597,
                'penduduk' => 84749,
                'laju' => 0.42,
                'desa' => 8,
            ],
            [
                'nama' => 'Umbulsari',
                'lat' => -8.243335,
                'lng' => 113.416520,
                'penduduk' => 79411,
                'laju' => -0.27,
                'desa' => 10,
            ],
            [
                'nama' => 'Semboro',
                'lat' => -8.179055,
                'lng' => 113.432464,
                'penduduk' => 50011,
                'laju' => -0.21,
                'desa' => 6,
            ],
            [
                'nama' => 'Jombang',
                'lat' => -8.224315,
                'lng' => 113.355439,
                'penduduk' => 56241,
                'laju' => -0.35,
                'desa' => 6,
            ],
            [
                'nama' => 'Sumberbaru',
                'lat' => -8.091140,
                'lng' => 113.410436,
                'penduduk' => 116359,
                'laju' => -0.33,
                'desa' => 10,
            ],
            [
                'nama' => 'Tanggul',
                'lat' => -8.100540,
                'lng' => 113.501462,
                'penduduk' => 94169,
                'laju' => -0.24,
                'desa' => 8,
            ],
            [
                'nama' => 'Bangsalsari',
                'lat' => -8.117990,
                'lng' => 113.565351,
                'penduduk' => 128748,
                'laju' => 0.46,
                'desa' => 11,
            ],
            [
                'nama' => 'Panti',
                'lat' => -8.076495,
                'lng' => 113.618488,
                'penduduk' => 67654,
                'laju' => 0.52,
                'desa' => 7,
            ],
            [
                'nama' => 'Sukorambi',
                'lat' => -8.129035,
                'lng' => 113.662086,
                'penduduk' => 42929,
                'laju' => 0.69,
                'desa' => 5,
            ],
            [
                'nama' => 'Arjasa',
                'lat' => -8.101945,
                'lng' => 113.734209,
                'penduduk' => 43286,
                'laju' => 0.77,
                'desa' => 6,
            ],
            [
                'nama' => 'Pakusari',
                'lat' => -8.154100,
                'lng' => 113.774940,
                'penduduk' => 47131,
                'laju' => 0.74,
                'desa' => 7,
            ],
            [
                'nama' => 'Kalisat',
                'lat' => -8.124300,
                'lng' => 113.807580,
                'penduduk' => 80671,
                'laju' => 0.28,
                'desa' => 12,
            ],
            [
                'nama' => 'Ledokombo',
                'lat' => -8.137980,
                'lng' => 113.941442,
                'penduduk' => 70559,
                'laju' => 0.36,
                'desa' => 10,
            ],
            [
                'nama' => 'Sumberjambe',
                'lat' => -8.069785,
                'lng' => 113.932088,
                'penduduk' => 65112,
                'laju' => 0.67,
                'desa' => 9,
            ],
            [
                'nama' => 'Sukowono',
                'lat' => -8.058165,
                'lng' => 113.823701,
                'penduduk' => 62498,
                'laju' => 0.59,
                'desa' => 12,
            ],
            [
                'nama' => 'Jelbuk',
                'lat' => -8.046210,
                'lng' => 113.707806,
                'penduduk' => 33938,
                'laju' => 0.86,
                'desa' => 6,
            ],
            [
                'nama' => 'Kaliwates',
                'lat' => -8.173680,
                'lng' => 113.688720,
                'penduduk' => 127701,
                'laju' => 0.60,
                'desa' => 7,
            ],
            [
                'nama' => 'Sumbersari',
                'lat' => -8.173635,
                'lng' => 113.728461,
                'penduduk' => 137792,
                'laju' => 0.92,
                'desa' => 7,
            ],
            [
                'nama' => 'Patrang',
                'lat' => -8.126835,
                'lng' => 113.700827,
                'penduduk' => 103922,
                'laju' => 0.51,
                'desa' => 8,
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                $kecamatan = Kecamatan::create([
                    'nama' => $item['nama'],
                    'latitude' => $item['lat'],
                    'longitude' => $item['lng'],
                ]);

                DataStatistik::create([
                    'kecamatan_id' => $kecamatan->id,
                    'jumlah_penduduk' => $item['penduduk'],
                    'laju_pertumbuhan' => $item['laju'],
                    'jumlah_desa' => $item['desa'],
                    'tahun' => 2024,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
