@extends('layouts.app')

@section('title', 'SPK 1 - Analisis Potensi & Dinamika Demografi - GIS Jember')

@push('styles')
<style>
    /* Styling Badge Kategori yang Bersih & Natural */
    .badge-sangat-tinggi { background:#fee2e2; color:#991b1b; border: 1px solid #fecaca; }
    .badge-tinggi        { background:#ffedd5; color:#9a3412; border: 1px solid #fed7aa; }
    .badge-sedang        { background:#fef9c3; color:#854d0e; border: 1px solid #fef08a; }
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
                <a href="{{ route('kecamatan.index') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Analisis SPK 1</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white text-base shrink-0 shadow-sm shadow-blue-500/30">
                    <i class="fa-solid fa-users"></i>
                </span>
                {{ $scenario['nama'] }}
            </h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                {{ $scenario['deskripsi'] }}
            </p>
        </div>

        {{-- Navigasi ke SPK 2 --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('spk.spk2') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition-all whitespace-nowrap">
                <i class="fa-solid fa-landmark-dome"></i>
                <span>Beralih ke SPK 2</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    {{-- ─── Banner Ringkasan Tujuan SPK ────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-600 p-4 sm:p-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-200">Tujuan Analisis</p>
                <p class="font-bold text-sm sm:text-base mt-0.5">{{ $scenario['tujuan'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2 self-start sm:self-auto shrink-0">
            <i class="fa-solid fa-calculator text-blue-200"></i>
            <div class="text-xs">
                <span class="block text-slate-200">Metode Penilaian</span>
                <strong class="text-white">Simple Additive Weighting</strong>
            </div>
        </div>
    </div>

    {{-- ─── DUA KOLOM GRAFIK (Pengganti Kriteria Statis & Podium) ─────────────── --}}
    @php
        // Ambil Top 7 untuk divisualisasikan pada grafik batang
        $topKecamatan = array_slice($hasil['hasil'], 0, 7);
        $chartLabels = array_map(fn($r) => $r['nama'], $topKecamatan);
        $chartScores = array_map(fn($r) => $r['skor_persen'], $topKecamatan);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Kolom Kiri (1/3): Grafik Donat Proporsi Bobot Kriteria -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-blue-600"></i>
                            Bobot Kriteria Penilaian
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Proporsi penentu skor SAW</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md border border-blue-100">
                        100%
                    </span>
                </div>

                <!-- Canvas Chart Donat -->
                <div class="relative h-44 my-4 flex items-center justify-center">
                    <canvas id="chartBobot"></canvas>
                </div>
            </div>

            <!-- Keterangan Bobot Kriteria -->
            <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shrink-0"></span>
                        <span class="font-medium">Jumlah Penduduk (Benefit)</span>
                    </div>
                    <strong class="text-blue-700 font-bold">60%</strong>
                </div>
                <div class="flex items-center justify-between text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                        <span class="font-medium">Laju Pertumbuhan (Benefit)</span>
                    </div>
                    <strong class="text-indigo-700 font-bold">40%</strong>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (2/3): Grafik Batang Skor Peringkat Teratas -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-chart-simple text-blue-600"></i>
                            Peringkat Skor Tertinggi (Top Kecamatan)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Perbandingan nilai preferensi akhir (V_i) kecamatan teratas</p>
                    </div>
                    <div class="text-right text-xs">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Peringkat 1</span>
                        <strong class="text-blue-700 font-bold">{{ $topKecamatan[0]['nama'] }} ({{ $topKecamatan[0]['skor_persen'] }}%)</strong>
                    </div>
                </div>

                <!-- Canvas Chart Batang Horizontal -->
                <div class="relative h-52 my-3">
                    <canvas id="chartTopRanking"></canvas>
                </div>
            </div>

            <!-- Footer Grafik -->
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Skala persentase skor SAW (0 - 100%)</span>
                <span>Sumber data: BPS Kab. Jember 2024</span>
            </div>
        </div>

    </div>

    {{-- ─── Tabel Lengkap Hasil SAW (31 Kecamatan) ─────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">

        {{-- Toolbar Filter Kategori --}}
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-blue-600"></i>
                    Matriks & Perankingan 31 Kecamatan
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Data nilai asli, nilai normalisasi, dan skor akhir preferensi</p>
            </div>

            {{-- Filter Kategori --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs text-slate-400 font-medium mr-1">Kategori:</span>
                @foreach (['semua' => 'Semua', 'Sangat Tinggi' => 'Sangat Tinggi', 'Tinggi' => 'Tinggi', 'Sedang' => 'Sedang', 'Rendah' => 'Rendah', 'Sangat Rendah' => 'Sangat Rendah'] as $val => $label)
                    <a href="{{ route('spk.spk1', ['kategori' => $val]) }}"
                       class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition-all
                              {{ $filterKategori === $val
                                 ? 'bg-blue-600 text-white border-blue-600 shadow-2xs'
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
                        <th class="px-4 py-3 text-right">Penduduk (Jiwa)</th>
                        <th class="px-4 py-3 text-center font-mono">Norm. (r1)</th>
                        <th class="px-4 py-3 text-right">Laju Tumbuh</th>
                        <th class="px-4 py-3 text-center font-mono">Norm. (r2)</th>
                        <th class="px-4 py-3 text-right font-bold text-blue-700">Skor Akhir (V)</th>
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
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            {{-- Ranking --}}
                            <td class="px-4 py-3 text-center font-semibold">
                                @if ($row['ranking'] === 1)
                                    <span class="inline-flex w-6 h-6 rounded-full bg-blue-600 text-white items-center justify-center text-xs font-bold">1</span>
                                @elseif ($row['ranking'] <= 3)
                                    <span class="inline-flex w-6 h-6 rounded-full bg-slate-800 text-white items-center justify-center text-xs font-bold">{{ $row['ranking'] }}</span>
                                @else
                                    <span class="text-slate-400 text-xs font-mono font-medium">{{ $row['ranking'] }}</span>
                                @endif
                            </td>

                            {{-- Nama Kecamatan --}}
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                <a href="{{ route('kecamatan.show', $row['kecamatan_id']) }}"
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $row['nama'] }}
                                </a>
                            </td>

                            {{-- Jumlah Penduduk (Nilai Asli) --}}
                            <td class="px-4 py-3 text-right font-medium">
                                {{ number_format($row['rincian_bobot']['jumlah_penduduk']['nilai_asli'], 0, ',', '.') }}
                            </td>

                            {{-- Normalisasi Penduduk --}}
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded border border-slate-200/60">
                                    {{ number_format($row['rincian_bobot']['jumlah_penduduk']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Laju Pertumbuhan (Nilai Asli) --}}
                            <td class="px-4 py-3 text-right font-medium {{ $row['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $row['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] > 0 ? '+' : '' }}{{ number_format($row['rincian_bobot']['laju_pertumbuhan']['nilai_asli'], 2) }}%
                            </td>

                            {{-- Normalisasi Laju --}}
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded border border-slate-200/60">
                                    {{ number_format($row['rincian_bobot']['laju_pertumbuhan']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Skor Akhir --}}
                            <td class="px-4 py-3 text-right">
                                <div class="font-bold text-blue-700 text-sm">
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
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400 text-xs">
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

    {{-- ─── Panel Rumus & Metodologi SAW ───────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5">
        <button onclick="document.getElementById('panel-rumus').classList.toggle('hidden')"
                class="w-full flex items-center justify-between text-sm font-bold text-slate-700">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-blue-500"></i>
                Penjelasan Formula Matematis Simple Additive Weighting (SAW)
            </span>
            <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
        </button>
        <div id="panel-rumus" class="hidden mt-4 pt-3 border-t border-slate-100 space-y-4 text-xs text-slate-600">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                    <h4 class="font-bold text-slate-800 mb-1.5">1. Rumus Normalisasi Kriteria (Benefit)</h4>
                    <p class="font-mono bg-white rounded p-2 text-center text-blue-900 border border-slate-200">
                        r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>ij</sub>)
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1.5">
                        Nilai tiap kecamatan dibagi dengan nilai tertinggi pada kriteria tersebut.
                    </p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                    <h4 class="font-bold text-slate-800 mb-1.5">2. Rumus Skor Akhir Preferensi (V<sub>i</sub>)</h4>
                    <p class="font-mono bg-white rounded p-2 text-center text-blue-900 border border-slate-200">
                        V<sub>i</sub> = Σ (W<sub>j</sub> × r<sub>ij</sub>)
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1.5">
                        Penjumlahan hasil kali bobot kriteria ($W_j$) dengan nilai normalisasi ($r_{ij}$).
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

        // 1. Inisialisasi Grafik Donat (Bobot Kriteria)
        const ctxBobot = document.getElementById('chartBobot').getContext('2d');
        new Chart(ctxBobot, {
            type: 'doughnut',
            data: {
                labels: ['Jumlah Penduduk', 'Laju Pertumbuhan'],
                datasets: [{
                    data: [60, 40],
                    backgroundColor: ['#2563eb', '#6366f1'],
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

        // 2. Inisialisasi Grafik Batang Horizontal (Top Kecamatan)
        const ctxTop = document.getElementById('chartTopRanking').getContext('2d');
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topLabels,
                datasets: [{
                    label: 'Skor Akhir (%)',
                    data: topScores,
                    backgroundColor: [
                        '#1d4ed8', // Rank 1 (Biru Tua Kuat)
                        '#2563eb', // Rank 2
                        '#3b82f6', // Rank 3
                        '#60a5fa', // Rank 4
                        '#93c5fd', // Rank 5
                        '#93c5fd', // Rank 6
                        '#bfdbfe'  // Rank 7
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
