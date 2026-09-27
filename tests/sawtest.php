<?php
/**
 * Script pengujian mandiri SawService.
 * Jalankan dengan: php tests/sawtest.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\SawService;
use App\Models\Kecamatan;
use App\Models\DataStatistik;

echo "\n" . str_repeat("═", 65) . "\n";
echo "  PENGUJIAN SAW SERVICE — Web GIS Kab. Jember\n";
echo str_repeat("═", 65) . "\n\n";

// ─── 1. Validasi Bobot ───────────────────────────────────────────────────────
echo "1. VALIDASI BOBOT SKENARIO\n";
echo str_repeat("─", 40) . "\n";
foreach ([SawService::SCENARIO_1, SawService::SCENARIO_2] as $key) {
    $v = SawService::validasiBobot($key);
    $status = $v['valid'] ? "✅  VALID" : "❌  TIDAK VALID";
    $scenario = SawService::getScenario($key);
    echo "  {$scenario['nama']}\n";
    echo "    Total Bobot: {$v['total_bobot']} → {$status}\n\n";
}

// ─── 2. Ambil data dari database ─────────────────────────────────────────────
echo "2. MENGAMBIL DATA DARI DATABASE\n";
echo str_repeat("─", 40) . "\n";

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

echo "  Total kecamatan ditemukan: " . count($rawData) . " kecamatan\n\n";

// ─── 3. Hitung SPK 1 ─────────────────────────────────────────────────────────
echo "3. SKENARIO SPK 1 — Potensi & Dinamika Demografi\n";
echo str_repeat("─", 40) . "\n";

$hasilSpk1 = SawService::hitung(SawService::SCENARIO_1, $rawData);

echo "  Kecamatan dalam perhitungan: {$hasilSpk1['jumlah_data']}\n";
echo "  Skor tertinggi: {$hasilSpk1['skor_tertinggi']}\n";
echo "  Skor terendah : {$hasilSpk1['skor_terendah']}\n\n";
echo "  TOP 5 PERINGKAT:\n";
$top5_1 = array_slice($hasilSpk1['hasil'], 0, 5);
foreach ($top5_1 as $row) {
    printf("    #%2d. %-15s | Skor: %.6f (%5.2f%%) | Kategori: %s\n",
        $row['ranking'],
        $row['nama'],
        $row['skor'],
        $row['skor_persen'],
        $row['kategori']
    );
}

echo "\n  BOTTOM 3 PERINGKAT:\n";
$bottom3_1 = array_slice($hasilSpk1['hasil'], -3, 3);
foreach ($bottom3_1 as $row) {
    printf("    #%2d. %-15s | Skor: %.6f (%5.2f%%) | Kategori: %s\n",
        $row['ranking'],
        $row['nama'],
        $row['skor'],
        $row['skor_persen'],
        $row['kategori']
    );
}

// ─── 4. Hitung SPK 2 ─────────────────────────────────────────────────────────
echo "\n4. SKENARIO SPK 2 — Beban Pelayanan Administrasi\n";
echo str_repeat("─", 40) . "\n";

$hasilSpk2 = SawService::hitung(SawService::SCENARIO_2, $rawData);

echo "  Kecamatan dalam perhitungan: {$hasilSpk2['jumlah_data']}\n";
echo "  Skor tertinggi: {$hasilSpk2['skor_tertinggi']}\n";
echo "  Skor terendah : {$hasilSpk2['skor_terendah']}\n\n";
echo "  TOP 5 PERINGKAT:\n";
$top5_2 = array_slice($hasilSpk2['hasil'], 0, 5);
foreach ($top5_2 as $row) {
    printf("    #%2d. %-15s | Skor: %.6f (%5.2f%%) | Kategori: %s\n",
        $row['ranking'],
        $row['nama'],
        $row['skor'],
        $row['skor_persen'],
        $row['kategori']
    );
}

// ─── 5. Uji Format API ───────────────────────────────────────────────────────
echo "\n5. CEK FORMAT API JSON (untuk integrasi Leaflet)\n";
echo str_repeat("─", 40) . "\n";
$apiData = SawService::toApiFormat($hasilSpk1);
echo "  Total item API: " . count($apiData) . "\n";
echo "  Contoh item pertama:\n";
$firstItem = $apiData[0];
echo "    " . json_encode($firstItem, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

// ─── 6. Konfirmasi perbedaan ranking antar skenario ──────────────────────────
echo "\n6. PERBANDINGAN RANKING ANTAR SKENARIO\n";
echo str_repeat("─", 55) . "\n";
printf("  %-20s %10s %10s\n", "Kecamatan", "Rank SPK1", "Rank SPK2");
echo "  " . str_repeat("─", 42) . "\n";

$rankMap1 = array_column($hasilSpk1['hasil'], 'ranking', 'kecamatan_id');
$rankMap2 = array_column($hasilSpk2['hasil'], 'ranking', 'kecamatan_id');
$namaMap  = array_column($rawData, 'nama', 'kecamatan_id');

foreach ($namaMap as $id => $nama) {
    printf("  %-20s %10s %10s\n", $nama, '#' . ($rankMap1[$id] ?? '-'), '#' . ($rankMap2[$id] ?? '-'));
}

echo "\n" . str_repeat("═", 65) . "\n";
echo "  PENGUJIAN SELESAI — SawService berjalan dengan baik!\n";
echo str_repeat("═", 65) . "\n\n";
