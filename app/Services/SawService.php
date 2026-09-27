<?php

namespace App\Services;

/**
 * SawService — Kalkulator Metode Simple Additive Weighting (SAW)
 *
 * Metode SAW bekerja dalam 4 langkah utama:
 *   1. Membangun Matriks Keputusan (Decision Matrix)
 *   2. Normalisasi nilai setiap kriteria
 *   3. Pembobotan nilai yang dinormalisasi
 *   4. Penjumlahan skor akhir dan ranking
 *
 * Sifat Kriteria:
 *   - BENEFIT: Semakin BESAR nilai, semakin baik (dibagi nilai MAX)
 *   - COST   : Semakin KECIL nilai, semakin baik (dibagi nilai dengan nilai itu / MIN)
 *
 * @package App\Services
 */
class SawService
{
    /**
     * Skenario SPK yang tersedia.
     * Diambil dari konstanta agar mudah dirujuk.
     */
    const SCENARIO_1 = 'spk1';
    const SCENARIO_2 = 'spk2';

    /**
     * Definisi lengkap semua skenario SPK yang tersedia.
     *
     * Struktur setiap skenario:
     *  - 'nama'       : Judul tampilan skenario
     *  - 'deskripsi'  : Penjelasan tujuan pengambilan keputusan
     *  - 'tujuan'     : Rangkuman singkat rekomendasi yang dihasilkan
     *  - 'kriteria'   : Daftar kriteria dengan kunci = nama field di data
     *      - 'label'  : Nama tampilan kriteria
     *      - 'sifat'  : 'benefit' atau 'cost'
     *      - 'bobot'  : Bobot kepentingan (total bobot tiap skenario harus = 1.00)
     *      - 'satuan' : Satuan tampilan
     *
     * @return array<string, array>
     */
    public static function getScenarios(): array
    {
        return [
            self::SCENARIO_1 => [
                'nama'      => 'Analisis Potensi & Dinamika Demografi',
                'deskripsi' => 'Menentukan peringkat kecamatan berdasarkan potensi demografis '
                             . 'dan pertumbuhan penduduk yang dapat menunjukkan daya tarik '
                             . 'investasi, kebutuhan layanan publik, dan potensi pasar.',
                'tujuan'    => 'Mengidentifikasi kecamatan dengan POTENSI PASAR & DEMOGRAFI TERTINGGI',
                'icon'      => 'fa-users',
                'warna'     => 'blue',
                'kriteria'  => [
                    'jumlah_penduduk' => [
                        'label'  => 'Jumlah Penduduk',
                        'sifat'  => 'benefit',
                        'bobot'  => 0.60,
                        'satuan' => 'Jiwa',
                        'alasan' => 'Semakin besar jumlah penduduk, semakin besar basis pasar dan potensi ekonomi.',
                    ],
                    'laju_pertumbuhan' => [
                        'label'  => 'Laju Pertumbuhan Penduduk',
                        'sifat'  => 'benefit',
                        'bobot'  => 0.40,
                        'satuan' => '%/Tahun',
                        'alasan' => 'Laju pertumbuhan positif mengindikasikan kecamatan yang dinamis dan berkembang pesat.',
                    ],
                ],
            ],

            self::SCENARIO_2 => [
                'nama'      => 'Prioritas Beban Pelayanan Administrasi Wilayah',
                'deskripsi' => 'Menentukan peringkat kecamatan berdasarkan kompleksitas dan '
                             . 'beban kerja birokrasi, untuk keperluan alokasi sumber daya '
                             . 'aparatur, anggaran operasional kantor kecamatan, dan '
                             . 'penyediaan fasilitas layanan terpadu.',
                'tujuan'    => 'Mengidentifikasi kecamatan dengan BEBAN ADMINISTRASI & PELAYANAN PUBLIK TERTINGGI',
                'icon'      => 'fa-landmark-dome',
                'warna'     => 'amber',
                'kriteria'  => [
                    'jumlah_desa' => [
                        'label'  => 'Jumlah Desa/Kelurahan',
                        'sifat'  => 'benefit',
                        'bobot'  => 0.45,
                        'satuan' => 'Desa',
                        'alasan' => 'Semakin banyak desa, semakin luas rentang koordinasi dan pengawasan camat.',
                    ],
                    'jumlah_penduduk' => [
                        'label'  => 'Jumlah Penduduk',
                        'sifat'  => 'benefit',
                        'bobot'  => 0.35,
                        'satuan' => 'Jiwa',
                        'alasan' => 'Semakin banyak warga yang harus dilayani (KTP, izin, bansos, dll).',
                    ],
                    'laju_pertumbuhan' => [
                        'label'  => 'Laju Pertumbuhan Penduduk',
                        'sifat'  => 'benefit',
                        'bobot'  => 0.20,
                        'satuan' => '%/Tahun',
                        'alasan' => 'Kecamatan yang tumbuh cepat akan mengalami eskalasi kebutuhan layanan publik ke depannya.',
                    ],
                ],
            ],
        ];
    }

    /**
     * Menjalankan perhitungan SPK dengan Metode SAW secara penuh.
     *
     * @param  string  $scenarioKey  Kunci skenario (self::SCENARIO_1 atau self::SCENARIO_2)
     * @param  array   $rawData      Array data mentah, setiap elemen adalah array asosiatif:
     *                               [
     *                                 'kecamatan_id' => int,
     *                                 'nama'         => string,
     *                                 'latitude'     => float,
     *                                 'longitude'    => float,
     *                                 'jumlah_penduduk'  => int|float,
     *                                 'laju_pertumbuhan' => float,
     *                                 'jumlah_desa'      => int,
     *                               ]
     * @return array  Hasil perhitungan lengkap (meta + matriks + hasil ranking)
     */
    public static function hitung(string $scenarioKey, array $rawData): array
    {
        $scenarios = self::getScenarios();

        if (!isset($scenarios[$scenarioKey])) {
            throw new \InvalidArgumentException("Skenario SPK '{$scenarioKey}' tidak ditemukan.");
        }

        if (empty($rawData)) {
            return self::emptyResult($scenarios[$scenarioKey]);
        }

        $scenario = $scenarios[$scenarioKey];
        $kriteria  = $scenario['kriteria'];

        // ─────────────────────────────────────────────────────────────────────
        // LANGKAH 1: Bangun Matriks Keputusan
        //   Petik hanya nilai kolom yang menjadi kriteria dari data mentah.
        // ─────────────────────────────────────────────────────────────────────
        $matrix = [];
        foreach ($rawData as $row) {
            $nilaiKriteria = [];
            foreach ($kriteria as $fieldKey => $krit) {
                $nilaiKriteria[$fieldKey] = (float) ($row[$fieldKey] ?? 0);
            }
            $matrix[] = [
                'kecamatan_id' => $row['kecamatan_id'],
                'nama'         => $row['nama'],
                'latitude'     => $row['latitude'] ?? 0,
                'longitude'    => $row['longitude'] ?? 0,
                'nilai'        => $nilaiKriteria,
            ];
        }

        // ─────────────────────────────────────────────────────────────────────
        // LANGKAH 2: Temukan nilai MAX dan MIN per kriteria
        //   Diperlukan sebagai pembagi pada proses normalisasi.
        // ─────────────────────────────────────────────────────────────────────
        $stats = [];
        foreach ($kriteria as $fieldKey => $krit) {
            $allValues = array_column(array_map(fn($r) => $r['nilai'], $matrix), $fieldKey);

            $max = max($allValues);
            $min = min($allValues);

            // Tangani kasus laju pertumbuhan yang bisa negatif.
            // Untuk normalisasi benefit dengan nilai bisa negatif,
            // kita geser semua nilai agar minimum menjadi 0 sebelum normalisasi.
            $positiveShift = ($min < 0) ? abs($min) : 0;

            $stats[$fieldKey] = [
                'max'           => $max,
                'min'           => $min,
                'positive_shift' => $positiveShift,
                'adjusted_max'  => $max + $positiveShift,
                'adjusted_min'  => ($min + $positiveShift),
            ];
        }

        // ─────────────────────────────────────────────────────────────────────
        // LANGKAH 3: Normalisasi Matriks Keputusan
        //   Rumus BENEFIT: r_ij = x_ij / max(x_ij)  → range [0, 1]
        //   Rumus COST   : r_ij = min(x_ij) / x_ij  → range [0, 1]
        //   Khusus BENEFIT dengan nilai negatif: geser semua nilai dulu.
        // ─────────────────────────────────────────────────────────────────────
        foreach ($matrix as &$row) {
            $row['normalisasi'] = [];
            foreach ($kriteria as $fieldKey => $krit) {
                $nilai        = $row['nilai'][$fieldKey];
                $st           = $stats[$fieldKey];
                $sifat        = $krit['sifat'];

                if ($sifat === 'benefit') {
                    $adjustedNilai = $nilai + $st['positive_shift'];
                    $adjustedMax   = $st['adjusted_max'];

                    // Hindari pembagian dengan nol
                    $normalized = ($adjustedMax > 0)
                        ? round($adjustedNilai / $adjustedMax, 8)
                        : 0;
                } else {
                    // COST: semakin kecil nilainya, semakin baik
                    $adjustedMin = $st['adjusted_min'];
                    $adjustedNilai = $nilai + $st['positive_shift'];

                    $normalized = ($adjustedNilai > 0)
                        ? round($adjustedMin / $adjustedNilai, 8)
                        : 0;
                }

                $row['normalisasi'][$fieldKey] = $normalized;
            }
        }
        unset($row);

        // ─────────────────────────────────────────────────────────────────────
        // LANGKAH 4: Hitung Skor Akhir (V_i) = Σ (W_j × r_ij)
        //   Bobot (W_j) dikali nilai normalisasi (r_ij), lalu dijumlahkan.
        // ─────────────────────────────────────────────────────────────────────
        foreach ($matrix as &$row) {
            $skorAkhir = 0;
            $rincianBobot = [];

            foreach ($kriteria as $fieldKey => $krit) {
                $bobot          = $krit['bobot'];
                $normalValue    = $row['normalisasi'][$fieldKey];
                $kontribusi     = round($bobot * $normalValue, 8);
                $skorAkhir     += $kontribusi;

                $rincianBobot[$fieldKey] = [
                    'nilai_asli'    => $row['nilai'][$fieldKey],
                    'normalisasi'   => $normalValue,
                    'bobot'         => $bobot,
                    'kontribusi'    => $kontribusi,
                ];
            }

            $row['skor']          = round($skorAkhir, 6);
            $row['skor_persen']   = round($skorAkhir * 100, 2);
            $row['rincian_bobot'] = $rincianBobot;
        }
        unset($row);

        // ─────────────────────────────────────────────────────────────────────
        // LANGKAH 5: Ranking — Urutkan berdasarkan skor tertinggi
        // ─────────────────────────────────────────────────────────────────────
        usort($matrix, fn($a, $b) => $b['skor'] <=> $a['skor']);

        $ranking = 1;
        foreach ($matrix as &$row) {
            $row['ranking'] = $ranking++;

            // Tentukan label kategori berdasarkan ranking
            $total = count($matrix);
            if ($row['ranking'] <= ceil($total * 0.2)) {
                $row['kategori']       = 'Sangat Tinggi';
                $row['warna_kategori'] = 'rose';
            } elseif ($row['ranking'] <= ceil($total * 0.4)) {
                $row['kategori']       = 'Tinggi';
                $row['warna_kategori'] = 'orange';
            } elseif ($row['ranking'] <= ceil($total * 0.6)) {
                $row['kategori']       = 'Sedang';
                $row['warna_kategori'] = 'yellow';
            } elseif ($row['ranking'] <= ceil($total * 0.8)) {
                $row['kategori']       = 'Rendah';
                $row['warna_kategori'] = 'sky';
            } else {
                $row['kategori']       = 'Sangat Rendah';
                $row['warna_kategori'] = 'slate';
            }
        }
        unset($row);

        // ─────────────────────────────────────────────────────────────────────
        // HASIL AKHIR: Kemas semua informasi ke satu output terstruktur
        // ─────────────────────────────────────────────────────────────────────
        return [
            'scenario_key' => $scenarioKey,
            'scenario'     => $scenario,
            'stats'        => $stats,
            'jumlah_data'  => count($rawData),
            'hasil'        => $matrix,
            'peringkat_1'  => $matrix[0] ?? null,
            'skor_tertinggi' => isset($matrix[0]) ? $matrix[0]['skor'] : 0,
            'skor_terendah'  => isset($matrix[count($matrix) - 1])
                                 ? $matrix[count($matrix) - 1]['skor'] : 0,
        ];
    }

    /**
     * Mendapatkan konfigurasi satu skenario berdasarkan kunci.
     *
     * @param  string  $scenarioKey
     * @return array
     */
    public static function getScenario(string $scenarioKey): array
    {
        $scenarios = self::getScenarios();

        if (!isset($scenarios[$scenarioKey])) {
            throw new \InvalidArgumentException("Skenario '{$scenarioKey}' tidak ditemukan.");
        }

        return $scenarios[$scenarioKey];
    }

    /**
     * Mengembalikan validasi apakah jumlah total bobot dalam satu skenario = 1.00.
     * Berguna untuk verifikasi konsistensi konfigurasi bobot.
     *
     * @param  string  $scenarioKey
     * @return array  ['valid' => bool, 'total_bobot' => float, 'selisih' => float]
     */
    public static function validasiBobot(string $scenarioKey): array
    {
        $scenario    = self::getScenario($scenarioKey);
        $totalBobot  = array_sum(array_column($scenario['kriteria'], 'bobot'));
        $selisih     = abs(1.0 - $totalBobot);
        $valid       = $selisih < 0.0001; // toleransi floating point

        return [
            'valid'       => $valid,
            'total_bobot' => round($totalBobot, 4),
            'selisih'     => round($selisih, 6),
        ];
    }

    /**
     * Helper: Mengonversi output hasil SPK menjadi format array sederhana
     * yang siap dikonsumsi oleh API JSON (untuk dipakai peta Leaflet nantinya).
     *
     * @param  array  $hasilSpk  Output dari self::hitung()
     * @return array
     */
    public static function toApiFormat(array $hasilSpk): array
    {
        return array_map(function ($row) {
            return [
                'kecamatan_id' => $row['kecamatan_id'],
                'nama'         => $row['nama'],
                'latitude'     => $row['latitude'],
                'longitude'    => $row['longitude'],
                'skor'         => $row['skor'],
                'skor_persen'  => $row['skor_persen'],
                'ranking'      => $row['ranking'],
                'kategori'     => $row['kategori'],
                'warna_kategori' => $row['warna_kategori'],
            ];
        }, $hasilSpk['hasil']);
    }

    /**
     * Helper: Mengembalikan struktur kosong jika data input tidak tersedia.
     *
     * @param  array  $scenario
     * @return array
     */
    private static function emptyResult(array $scenario): array
    {
        return [
            'scenario'       => $scenario,
            'stats'          => [],
            'jumlah_data'    => 0,
            'hasil'          => [],
            'peringkat_1'    => null,
            'skor_tertinggi' => 0,
            'skor_terendah'  => 0,
        ];
    }
}
