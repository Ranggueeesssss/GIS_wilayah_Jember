@extends('layouts.app')

@section('title', 'SPK 2 - Prioritas Beban Pelayanan Administrasi Wilayah - GIS Jember')

@push('styles')
<style>
    /* Styling Badge Kategori yang Bersih & Natural */
    .badge-sangat-tinggi { background:#fef3c7; color:#92400e; border: 1px solid #fde68a; }
    .badge-tinggi        { background:#ffedd5; color:#9a3412; border: 1px solid #fed7aa; }
    .badge-sedang        { background:#d1fae5; color:#065f46; border: 1px solid #a7f3d0; }
    .badge-rendah        { background:#e0f2fe; color:#075985; border: 1px solid #bae6fd; }
    .badge-sangat-rendah { background:#f1f5f9; color:#475569; border: 1px solid #e2e8f0; }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- ─── Breadcrumb & Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-slate-500 mb-1.5">
                <a href="{{ route('kecamatan.index') }}" class="hover:text-amber-600 transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Analisis SPK 2</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center text-white text-base shrink-0 shadow-sm shadow-amber-500/30">
                    <i class="fa-solid fa-landmark-dome"></i>
                </span>
                {{ $scenario['nama'] }}
            </h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                {{ $scenario['deskripsi'] }}
            </p>
        </div>

        {{-- Navigasi ke SPK 1 --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('spk.spk1') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-all whitespace-nowrap">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <i class="fa-solid fa-users"></i>
                <span>Beralih ke SPK 1</span>
            </a>
        </div>
    </div>

    {{-- ─── Banner Ringkasan Tujuan SPK ────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-gradient-to-r from-amber-600 to-orange-500 p-4 sm:p-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-100">Tujuan Analisis</p>
                <p class="font-bold text-sm sm:text-base mt-0.5">{{ $scenario['tujuan'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2 self-start sm:self-auto shrink-0">
            <i class="fa-solid fa-sliders text-amber-100"></i>
            <div class="text-xs">
                <span class="block text-slate-100">Kriteria Penilaian</span>
                <strong class="text-white">3 Kriteria (Benefit)</strong>
            </div>
        </div>
    </div>

    {{-- ─── DUA KOLOM GRAFIK (Pengganti Kriteria Statis & Podium) ─────────────── --}}
    @php
        // Ambil Top 7 kecamatan SPK 2 untuk grafik batang
        $topKecamatan = array_slice($hasil['hasil'], 0, 7);
        $chartLabels = array_map(fn($r) => $r['nama'], $topKecamatan);
        $chartScores = array_map(fn($r) => $r['skor_persen'], $topKecamatan);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Kolom Kiri (1/3): Grafik Donat Proporsi Bobot 3 Kriteria -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-amber-600"></i>
                            Bobot Kriteria Penilaian
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Proporsi penentu skor SAW Skenario 2</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md border border-amber-100">
                        100%
                    </span>
                </div>

                <!-- Canvas Chart Donat -->
                <div class="relative h-44 my-4 flex items-center justify-center">
                    <canvas id="chartBobot2"></canvas>
                </div>
            </div>

            <!-- Keterangan 3 Bobot Kriteria -->
            <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                        <span class="font-medium">Jumlah Desa/Kelurahan</span>
                    </div>
                    <strong class="text-amber-700 font-bold">45%</strong>
                </div>
                <div class="flex items-center justify-between text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                        <span class="font-medium">Jumlah Penduduk</span>
                    </div>
                    <strong class="text-blue-700 font-bold">35%</strong>
                </div>
                <div class="flex items-center justify-between text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
                        <span class="font-medium">Laju Pertumbuhan</span>
                    </div>
                    <strong class="text-purple-700 font-bold">20%</strong>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (2/3): Grafik Batang Skor Peringkat Teratas -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chart-simple text-amber-600"></i>
                            Peringkat Beban Tertinggi (Top Kecamatan)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Perbandingan skor prioritas alokasi pelayanan administrasi</p>
                    </div>
                    <div class="text-right text-xs">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Peringkat 1</span>
                        <strong class="text-amber-700 font-bold">{{ $topKecamatan[0]['nama'] }} ({{ $topKecamatan[0]['skor_persen'] }}%)</strong>
                    </div>
                </div>

                <!-- Canvas Chart Batang Horizontal -->
                <div class="relative h-52 my-3">
                    <canvas id="chartTopRanking2"></canvas>
                </div>
            </div>

            <!-- Footer Grafik -->
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Skala persentase skor SAW (0 - 100%)</span>
                <span>Fokus: Kompleksitas beban birokrasi kewilayahan</span>
            </div>
        </div>

    </div>

    {{-- ─── Panel Perbandingan Dinamis: SPK 1 vs SPK 2 ─────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-slate-100 gap-2 mb-4">
            <div>
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right-arrow-left text-amber-600"></i>
                    Perbandingan Pergeseran Ranking: SPK 1 vs SPK 2
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Bukti bahwa variasi kriteria (penambahan jumlah desa) menghasilkan rekomendasi prioritas yang berbeda secara objektif.
                </p>
            </div>
            <a href="{{ route('spk.spk1') }}" 
               class="text-xs text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1 self-start sm:self-auto">
                <span>Tabel SPK 1</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-xs">
            <!-- Kolom Naik Drastis -->
            <div class="bg-emerald-50/60 rounded-xl p-3.5 border border-emerald-100">
                <span class="font-bold text-emerald-800 flex items-center gap-1.5 mb-2">
                    <i class="fa-solid fa-arrow-trend-up text-emerald-600"></i> Naik Signifikan di SPK 2
                </span>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-emerald-100/80">
                        <span class="font-semibold">Bangsalsari (11 desa)</span>
                        <span class="font-mono text-xs">
                            <span class="text-slate-400">#5</span> &rarr; <strong class="text-emerald-700">#1</strong>
                        </span>
                    </li>
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-emerald-100/80">
                        <span class="font-semibold">Puger (12 desa)</span>
                        <span class="font-mono text-xs">
                            <span class="text-slate-400">#7</span> &rarr; <strong class="text-emerald-700">#2</strong>
                        </span>
                    </li>
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-emerald-100/80">
                        <span class="font-semibold">Sukowono (12 desa)</span>
                        <span class="font-mono text-xs">
                            <span class="text-slate-400">#16</span> &rarr; <strong class="text-emerald-700">#4</strong>
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Kolom Turun -->
            <div class="bg-rose-50/60 rounded-xl p-3.5 border border-rose-100">
                <span class="font-bold text-rose-800 flex items-center gap-1.5 mb-2">
                    <i class="fa-solid fa-arrow-trend-down text-rose-600"></i> Turun di SPK 2
                </span>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-rose-100/80">
                        <span class="font-semibold">Kaliwates (7 kelurahan)</span>
                        <span class="font-mono text-xs">
                            <span class="text-slate-400">#2</span> &rarr; <strong class="text-rose-700">#7</strong>
                        </span>
                    </li>
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-rose-100/80">
                        <span class="font-semibold">Wuluhan (7 desa)</span>
                        <span class="font-mono text-xs">
                            <span class="text-slate-400">#3</span> &rarr; <strong class="text-rose-700">#8</strong>
                        </span>
                    </li>
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-rose-100/80">
                        <span class="font-semibold">Ambulu (7 desa)</span>
                        <span class="font-mono text-xs">
                            <span class="text-slate-400">#4</span> &rarr; <strong class="text-rose-700">#9</strong>
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Kolom Konsisten -->
            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200/80">
                <span class="font-bold text-slate-800 flex items-center gap-1.5 mb-2">
                    <i class="fa-solid fa-scale-balanced text-slate-600"></i> Konsisten di Kedua SPK
                </span>
                <ul class="space-y-2 text-slate-700">
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/60">
                        <span class="font-semibold">Sumbersari</span>
                        <span class="font-mono text-xs text-blue-700 font-bold">#1 / #3</span>
                    </li>
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/60">
                        <span class="font-semibold">Silo</span>
                        <span class="font-mono text-xs text-slate-700 font-bold">#6 / #6</span>
                    </li>
                    <li class="flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/60">
                        <span class="font-semibold">Rambipuji</span>
                        <span class="font-mono text-xs text-slate-700 font-bold">#18 / #18</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ─── Tabel Lengkap Hasil SAW SPK 2 (31 Kecamatan) ───────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">

        {{-- Toolbar Filter Kategori --}}
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-amber-600"></i>
                    Matriks & Perankingan 31 Kecamatan (SPK 2)
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Nilai kriteria desa, penduduk, laju pertumbuhan, dan skor preferensi akhir</p>
            </div>

            {{-- Filter Kategori --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs text-slate-400 font-medium mr-1">Kategori:</span>
                @foreach (['semua' => 'Semua', 'Sangat Tinggi' => 'Sangat Tinggi', 'Tinggi' => 'Tinggi', 'Sedang' => 'Sedang', 'Rendah' => 'Rendah', 'Sangat Rendah' => 'Sangat Rendah'] as $val => $label)
                    <a href="{{ route('spk.spk2', ['kategori' => $val]) }}"
                       class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition-all
                              {{ $filterKategori === $val
                                 ? 'bg-amber-500 text-white border-amber-500 shadow-2xs'
                                 : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700">
                <thead class="text-xs uppercase font-semibold text-slate-500 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-center w-14">Rank</th>
                        <th class="px-4 py-3">Kecamatan</th>
                        <th class="px-4 py-3 text-center">Desa</th>
                        <th class="px-4 py-3 text-center font-mono">Norm. (r1)</th>
                        <th class="px-4 py-3 text-right">Penduduk (Jiwa)</th>
                        <th class="px-4 py-3 text-center font-mono">Norm. (r2)</th>
                        <th class="px-4 py-3 text-right">Laju Tumbuh</th>
                        <th class="px-4 py-3 text-center font-mono">Norm. (r3)</th>
                        <th class="px-4 py-3 text-right font-bold text-amber-700">Skor Akhir (V)</th>
                        <th class="px-4 py-3 text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($hasilFiltered as $row)
                        @php
                            $badgeMap = [
                                'Sangat Tinggi' => 'badge-sangat-tinggi',
                                'Tinggi'        => 'badge-tinggi',
                                'Sedang'        => 'badge-sedang',
                                'Rendah'        => 'badge-rendah',
                                'Sangat Rendah' => 'badge-sangat-rendah',
                            ];
                            $badgeKelas = $badgeMap[$row['kategori']] ?? 'badge-sangat-rendah';
                            $rb = $row['rincian_bobot'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            {{-- Ranking --}}
                            <td class="px-4 py-3 text-center font-semibold">
                                @if ($row['ranking'] === 1)
                                    <span class="inline-flex w-6 h-6 rounded-full bg-amber-500 text-white items-center justify-center text-xs font-bold">1</span>
                                @elseif ($row['ranking'] <= 3)
                                    <span class="inline-flex w-6 h-6 rounded-full bg-slate-800 text-white items-center justify-center text-xs font-bold">{{ $row['ranking'] }}</span>
                                @else
                                    <span class="text-slate-400 text-xs font-mono font-medium">{{ $row['ranking'] }}</span>
                                @endif
                            </td>

                            {{-- Nama Kecamatan --}}
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                <a href="{{ route('kecamatan.show', $row['kecamatan_id']) }}"
                                   class="hover:text-amber-600 transition-colors">
                                    {{ $row['nama'] }}
                                </a>
                            </td>

                            {{-- Jumlah Desa --}}
                            <td class="px-4 py-3 text-center font-medium">
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-800 rounded font-semibold text-xs border border-amber-100">
                                    {{ $rb['jumlah_desa']['nilai_asli'] }}
                                </span>
                            </td>

                            {{-- Normalisasi Desa --}}
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded border border-slate-200/60">
                                    {{ number_format($rb['jumlah_desa']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Jumlah Penduduk --}}
                            <td class="px-4 py-3 text-right font-medium">
                                {{ number_format($rb['jumlah_penduduk']['nilai_asli'], 0, ',', '.') }}
                            </td>

                            {{-- Normalisasi Penduduk --}}
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded border border-slate-200/60">
                                    {{ number_format($rb['jumlah_penduduk']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Laju Pertumbuhan --}}
                            <td class="px-4 py-3 text-right font-medium {{ $rb['laju_pertumbuhan']['nilai_asli'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $rb['laju_pertumbuhan']['nilai_asli'] > 0 ? '+' : '' }}{{ number_format($rb['laju_pertumbuhan']['nilai_asli'], 2) }}%
                            </td>

                            {{-- Normalisasi Laju --}}
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded border border-slate-200/60">
                                    {{ number_format($rb['laju_pertumbuhan']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Skor Akhir --}}
                            <td class="px-4 py-3 text-right">
                                <div class="font-bold text-amber-700 text-sm">
                                    {{ $row['skor_persen'] }}%
                                </div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    {{ number_format($row['skor'], 4) }}
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeKelas }}">
                                    {{ $row['kategori'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-slate-400 text-xs">
                                Tidak ada data dalam kategori yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Tabel --}}
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>
                Menampilkan <strong>{{ count($hasilFiltered) }}</strong> dari <strong>{{ $hasil['jumlah_data'] }}</strong> kecamatan
            </span>
            <div class="flex items-center gap-4 text-slate-400 font-mono text-[11px]">
                <span>Nilai Tertinggi: <strong class="text-slate-700">{{ number_format($hasil['skor_tertinggi'], 4) }}</strong></span>
                <span>Nilai Terendah: <strong class="text-slate-700">{{ number_format($hasil['skor_terendah'], 4) }}</strong></span>
            </div>
        </div>
    </div>

    {{-- ─── Panel Rumus & Metodologi SAW Skenario 2 ─────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5">
        <button onclick="document.getElementById('panel-rumus2').classList.toggle('hidden')"
                class="w-full flex items-center justify-between text-sm font-bold text-slate-700">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-amber-500"></i>
                Detail Rumus Perhitungan Metode SAW — Skenario 2
            </span>
            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
        </button>
        <div id="panel-rumus2" class="hidden mt-4 pt-3 border-t border-slate-100 space-y-4 text-xs text-slate-600">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                    <h4 class="font-bold text-slate-800 mb-1.5">1. Normalisasi Matriks (Benefit)</h4>
                    <p class="font-mono bg-white rounded p-2 text-center text-amber-900 border border-slate-200">
                        r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>ij</sub>)
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1.5">
                        Nilai desa, penduduk, dan laju pertumbuhan dinormalisasi ke skala 0 - 1.
                    </p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                    <h4 class="font-bold text-slate-800 mb-1.5">2. Penjumlahan Preferensi Terbobot (V<sub>i</sub>)</h4>
                    <p class="font-mono bg-white rounded p-2 text-center text-amber-900 border border-slate-200">
                        V<sub>i</sub> = (0.45 × r<sub>i1</sub>) + (0.35 × r<sub>i2</sub>) + (0.20 × r<sub>i3</sub>)
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1.5">
                        Bobot: 45% Jumlah Desa + 35% Jumlah Penduduk + 20% Laju Pertumbuhan.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data Grafik dari Backend
        const topLabels = @json($chartLabels);
        const topScores = @json($chartScores);

        // 1. Inisialisasi Grafik Donat (Bobot 3 Kriteria)
        const ctxBobot2 = document.getElementById('chartBobot2').getContext('2d');
        new Chart(ctxBobot2, {
            type: 'doughnut',
            data: {
                labels: ['Jumlah Desa/Kelurahan', 'Jumlah Penduduk', 'Laju Pertumbuhan'],
                datasets: [{
                    data: [45, 35, 20],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#a855f7'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ` ${context.label}: ${context.raw}% (Benefit)`;
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });

        // 2. Inisialisasi Grafik Batang Horizontal (Top Kecamatan SPK 2)
        const ctxTop2 = document.getElementById('chartTopRanking2').getContext('2d');
        new Chart(ctxTop2, {
            type: 'bar',
            data: {
                labels: topLabels,
                datasets: [{
                    label: 'Skor Akhir (%)',
                    data: topScores,
                    backgroundColor: [
                        '#d97706', // Rank 1 (Amber Tua)
                        '#f59e0b', // Rank 2
                        '#fbbf24', // Rank 3
                        '#fcd34d', // Rank 4
                        '#fde68a', // Rank 5
                        '#fde68a', // Rank 6
                        '#fef3c7'  // Rank 7
                    ],
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 18
                }]
            },
            options: {
                indexAxis: 'y', // Tampilan Bar Horizontal
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: function (val) {
                                return val + '%';
                            },
                            font: {
                                size: 10
                            },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '600'
                            },
                            color: '#334155'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return ` Skor SAW: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
