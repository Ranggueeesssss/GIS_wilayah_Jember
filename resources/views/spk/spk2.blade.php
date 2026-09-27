@extends('layouts.app')

@section('title', 'SPK 2 - Prioritas Beban Pelayanan Administrasi Wilayah - GIS Jember')

@push('styles')
<style>
    .skor-bar { transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); }

    .badge-sangat-tinggi { background:#fef3c7; color:#92400e; border-color:#fde68a; }
    .badge-tinggi        { background:#ffedd5; color:#9a3412; border-color:#fed7aa; }
    .badge-sedang        { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
    .badge-rendah        { background:#e0f2fe; color:#075985; border-color:#bae6fd; }
    .badge-sangat-rendah { background:#f1f5f9; color:#475569; border-color:#e2e8f0; }

    .row-sangat-tinggi { background: linear-gradient(90deg, #fffbeb 0%, transparent 60%); }
    .row-tinggi        { background: linear-gradient(90deg, #fff7ed 0%, transparent 60%); }
    .row-sedang        { background: linear-gradient(90deg, #f0fdf4 0%, transparent 60%); }
    .row-rendah        { background: linear-gradient(90deg, #f0f9ff 0%, transparent 60%); }
    .row-sangat-rendah { background: transparent; }
</style>
@endpush

@section('content')
<div class="space-y-7">

    {{-- ─── Breadcrumb & Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-slate-500 mb-2">
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
            <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                {{ $scenario['deskripsi'] }}
            </p>
        </div>

        {{-- Tombol navigasi ke SPK 1 --}}
        <a href="{{ route('spk.spk1') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-all whitespace-nowrap">
            <i class="fa-solid fa-chevron-left text-xs"></i>
            <i class="fa-solid fa-users"></i>
            <span>Lihat SPK 1 — Potensi Demografi</span>
        </a>
    </div>

    {{-- ─── Banner Tujuan SPK ────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-gradient-to-r from-amber-600 to-orange-500 p-5 text-white flex flex-col sm:flex-row sm:items-center gap-4 shadow-md shadow-amber-600/20">
        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
            <i class="fa-solid fa-bullseye"></i>
        </div>
        <div class="flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Tujuan Pengambilan Keputusan</p>
            <p class="font-bold mt-0.5 text-base">{{ $scenario['tujuan'] }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex items-center gap-3 text-sm bg-white/15 rounded-xl px-4 py-2.5">
                <i class="fa-solid fa-chart-simple text-lg"></i>
                <div>
                    <p class="font-bold text-xl leading-none">{{ $hasil['jumlah_data'] }}</p>
                    <p class="opacity-80 text-xs">Kecamatan dianalisis</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-sm bg-white/15 rounded-xl px-4 py-2.5">
                <i class="fa-solid fa-sliders text-lg"></i>
                <div>
                    <p class="font-bold text-xl leading-none">{{ count($scenario['kriteria']) }}</p>
                    <p class="opacity-80 text-xs">Kriteria digunakan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Kartu Kriteria & Bobot (3 Kriteria) ────────────────────────────────── --}}
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-scale-balanced text-amber-500"></i>
            Kriteria & Bobot Kepentingan (Total: {{ $validasi['total_bobot'] * 100 }}%)
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php
                $kriteriaColors = [
                    'jumlah_desa'      => ['color' => 'amber',  'icon' => 'fa-house-chimney'],
                    'jumlah_penduduk'  => ['color' => 'blue',   'icon' => 'fa-users'],
                    'laju_pertumbuhan' => ['color' => 'purple', 'icon' => 'fa-chart-line'],
                ];
            @endphp
            @foreach ($scenario['kriteria'] as $key => $krit)
                @php
                    $persen = $krit['bobot'] * 100;
                    $meta   = $kriteriaColors[$key] ?? ['color' => 'slate', 'icon' => 'fa-circle'];
                    $c      = $meta['color'];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-7 h-7 rounded-lg bg-{{ $c }}-100 text-{{ $c }}-600 flex items-center justify-center text-xs">
                                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                                </div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-{{ $c }}-50 text-{{ $c }}-700 border border-{{ $c }}-100">
                                    {{ strtoupper($krit['sifat']) }}
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $krit['label'] }}</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $krit['alasan'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-{{ $c }}-100 text-{{ $c }}-700 flex flex-col items-center justify-center shrink-0 ml-3 font-bold shadow-sm">
                            <span class="text-base leading-none">{{ $persen }}%</span>
                        </div>
                    </div>
                    {{-- Progress bar bobot --}}
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="skor-bar h-full bg-{{ $c }}-500 rounded-full" style="width: {{ $persen }}%"></div>
                    </div>
                    <p class="text-right text-[11px] text-slate-400 mt-1">Bobot: {{ $krit['bobot'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Top 3 Podium ────────────────────────────────────────────────────────── --}}
    @php $top3 = array_slice($hasil['hasil'], 0, 3); @endphp
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-trophy text-amber-500"></i>
            Kecamatan Prioritas Tertinggi — Beban Administrasi
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach ($top3 as $i => $podium)
                @php
                    $medals    = ['🥇','🥈','🥉'];
                    $gradients = ['from-amber-500 to-yellow-400','from-slate-400 to-slate-300','from-orange-500 to-amber-400'];
                    $bg        = [
                        'bg-gradient-to-br from-amber-50 to-yellow-50 border-amber-200',
                        'bg-gradient-to-br from-slate-50 to-zinc-50 border-slate-200',
                        'bg-gradient-to-br from-orange-50 to-amber-50 border-orange-200',
                    ];
                @endphp
                <div class="rounded-2xl border p-5 {{ $bg[$i] }} shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl">{{ $medals[$i] }}</span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/70 text-slate-600 border border-slate-200/60">
                            #{{ $podium['ranking'] }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $podium['nama'] }}</h3>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-slate-900">{{ $podium['skor_persen'] }}%</div>
                        <p class="text-xs text-slate-500 mt-0.5">Skor SAW: {{ number_format($podium['skor'], 6) }}</p>
                    </div>
                    <div class="mt-3 h-2 bg-black/10 rounded-full overflow-hidden">
                        <div class="skor-bar h-full bg-gradient-to-r {{ $gradients[$i] }} rounded-full"
                             style="width: {{ $podium['skor_persen'] }}%"></div>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-1.5 text-xs text-slate-600">
                        <div class="bg-white/60 rounded-lg p-1.5 text-center">
                            <span class="block text-[10px] opacity-60">Desa</span>
                            <span class="font-bold text-amber-700">{{ $podium['rincian_bobot']['jumlah_desa']['nilai_asli'] }}</span>
                        </div>
                        <div class="bg-white/60 rounded-lg p-1.5 text-center">
                            <span class="block text-[10px] opacity-60">Pddk (rb)</span>
                            <span class="font-bold text-blue-700">{{ round($podium['rincian_bobot']['jumlah_penduduk']['nilai_asli'] / 1000, 1) }}k</span>
                        </div>
                        <div class="bg-white/60 rounded-lg p-1.5 text-center">
                            <span class="block text-[10px] opacity-60">Laju</span>
                            <span class="font-bold {{ $podium['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $podium['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] > 0 ? '+' : '' }}{{ number_format($podium['rincian_bobot']['laju_pertumbuhan']['nilai_asli'], 2) }}%
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Perbandingan Peringkat dengan SPK 1 ────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-5 text-white">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-right-left text-amber-400"></i>
            <h2 class="text-sm font-bold uppercase tracking-wider">Perbandingan Menarik: SPK 1 vs SPK 2</h2>
        </div>
        <p class="text-xs text-slate-300 mb-4">
            Kecamatan dengan perubahan ranking signifikan antar skenario, membuktikan bahwa kriteria yang berbeda menghasilkan rekomendasi berbeda.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="bg-white/10 rounded-xl p-3.5">
                <p class="text-xs text-slate-300 mb-2 font-semibold">📈 Naik Drastis di SPK 2</p>
                <ul class="space-y-1.5">
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Bangsalsari</span>
                        <span class="text-xs">
                            <span class="text-slate-400">#5</span>
                            <i class="fa-solid fa-arrow-right mx-1 text-[10px] text-amber-400"></i>
                            <span class="text-amber-400 font-bold">#1</span>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Puger</span>
                        <span class="text-xs">
                            <span class="text-slate-400">#7</span>
                            <i class="fa-solid fa-arrow-right mx-1 text-[10px] text-amber-400"></i>
                            <span class="text-amber-400 font-bold">#2</span>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Sukowono</span>
                        <span class="text-xs">
                            <span class="text-slate-400">#16</span>
                            <i class="fa-solid fa-arrow-right mx-1 text-[10px] text-amber-400"></i>
                            <span class="text-amber-400 font-bold">#4</span>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="bg-white/10 rounded-xl p-3.5">
                <p class="text-xs text-slate-300 mb-2 font-semibold">📉 Turun di SPK 2</p>
                <ul class="space-y-1.5">
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Kaliwates</span>
                        <span class="text-xs">
                            <span class="text-blue-300">#2</span>
                            <i class="fa-solid fa-arrow-right mx-1 text-[10px] text-rose-400"></i>
                            <span class="text-rose-400 font-bold">#7</span>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Wuluhan</span>
                        <span class="text-xs">
                            <span class="text-blue-300">#3</span>
                            <i class="fa-solid fa-arrow-right mx-1 text-[10px] text-rose-400"></i>
                            <span class="text-rose-400 font-bold">#8</span>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Ambulu</span>
                        <span class="text-xs">
                            <span class="text-blue-300">#4</span>
                            <i class="fa-solid fa-arrow-right mx-1 text-[10px] text-rose-400"></i>
                            <span class="text-rose-400 font-bold">#9</span>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="bg-white/10 rounded-xl p-3.5">
                <p class="text-xs text-slate-300 mb-2 font-semibold">✅ Konsisten di Kedua SPK</p>
                <ul class="space-y-1.5">
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Sumbersari</span>
                        <span class="text-xs">
                            <span class="text-emerald-300 font-bold">#1 / #3</span>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Silo</span>
                        <span class="text-xs">
                            <span class="text-emerald-300 font-bold">#6 / #6</span>
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-white font-medium">Rambipuji</span>
                        <span class="text-xs">
                            <span class="text-emerald-300 font-bold">#18 / #18</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('spk.spk1') }}" class="inline-flex items-center gap-1.5 text-xs text-amber-400 hover:text-amber-300 font-semibold transition-colors">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Lihat Tabel Ranking SPK 1 untuk perbandingan lengkap
            </a>
        </div>
    </div>

    {{-- ─── Filter Kategori & Tabel Lengkap ────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-ol text-amber-500"></i>
                Tabel Ranking Lengkap — 31 Kecamatan
            </h2>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-slate-500 font-semibold">Filter:</span>
                @foreach (['semua' => 'Semua', 'Sangat Tinggi' => 'Sangat Tinggi', 'Tinggi' => 'Tinggi', 'Sedang' => 'Sedang', 'Rendah' => 'Rendah', 'Sangat Rendah' => 'Sangat Rendah'] as $val => $label)
                    <a href="{{ route('spk.spk2', ['kategori' => $val]) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full border transition-all
                              {{ $filterKategori === $val
                                 ? 'bg-amber-500 text-white border-amber-500 shadow-sm'
                                 : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700">
                <thead class="text-xs uppercase font-semibold text-slate-400 bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-center w-14">Rank</th>
                        <th class="px-4 py-3">Kecamatan</th>
                        {{-- Kolom Kriteria --}}
                        <th class="px-4 py-3 text-center text-amber-600">Desa</th>
                        <th class="px-4 py-3 text-center text-[10px]">Norm.</th>
                        <th class="px-4 py-3 text-right text-blue-600">Penduduk</th>
                        <th class="px-4 py-3 text-center text-[10px]">Norm.</th>
                        <th class="px-4 py-3 text-right text-purple-600">Laju%</th>
                        <th class="px-4 py-3 text-center text-[10px]">Norm.</th>
                        {{-- Skor --}}
                        <th class="px-4 py-3 text-right font-bold text-amber-600">Skor Akhir</th>
                        <th class="px-4 py-3 text-center w-28">Visual</th>
                        <th class="px-4 py-3 text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($hasilFiltered as $row)
                        @php
                            $kelasRow  = 'row-' . strtolower(str_replace(' ', '-', $row['kategori']));
                            $rb        = $row['rincian_bobot'];
                        @endphp
                        <tr class="{{ $kelasRow }} hover:brightness-95 transition-all">
                            {{-- Ranking --}}
                            <td class="px-4 py-3 text-center">
                                @if ($row['ranking'] <= 3)
                                    <span class="text-xl">{{ ['🥇','🥈','🥉'][$row['ranking']-1] }}</span>
                                @else
                                    <span class="inline-flex w-7 h-7 rounded-full bg-slate-100 items-center justify-center text-xs font-bold text-slate-600">
                                        {{ $row['ranking'] }}
                                    </span>
                                @endif
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                <a href="{{ route('kecamatan.show', $row['kecamatan_id']) }}"
                                   class="hover:text-amber-600 transition-colors">
                                    {{ $row['nama'] }}
                                </a>
                            </td>

                            {{-- Jumlah Desa --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 rounded-lg border border-amber-100 text-amber-800 font-bold text-xs">
                                    <i class="fa-solid fa-house-chimney text-[9px]"></i>
                                    {{ $rb['jumlah_desa']['nilai_asli'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 rounded-md border border-amber-100 text-[11px]">
                                    {{ number_format($rb['jumlah_desa']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Jumlah Penduduk --}}
                            <td class="px-4 py-3 text-right font-medium">
                                {{ number_format($rb['jumlah_penduduk']['nilai_asli'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded-md border border-blue-100 text-[11px]">
                                    {{ number_format($rb['jumlah_penduduk']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Laju Pertumbuhan --}}
                            <td class="px-4 py-3 text-right font-medium {{ $rb['laju_pertumbuhan']['nilai_asli'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $rb['laju_pertumbuhan']['nilai_asli'] > 0 ? '+' : '' }}{{ number_format($rb['laju_pertumbuhan']['nilai_asli'], 2) }}%
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-mono">
                                <span class="px-1.5 py-0.5 bg-purple-50 text-purple-700 rounded-md border border-purple-100 text-[11px]">
                                    {{ number_format($rb['laju_pertumbuhan']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Skor Akhir --}}
                            <td class="px-4 py-3 text-right">
                                <div class="font-black text-amber-700 text-base">{{ $row['skor_persen'] }}%</div>
                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ number_format($row['skor'], 6) }}</div>
                            </td>

                            {{-- Visual Bar --}}
                            <td class="px-4 py-3">
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="skor-bar h-full bg-amber-500 rounded-full"
                                         style="width: {{ $row['skor_persen'] }}%"></div>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-0.5 block text-right">{{ $row['skor_persen'] }}%</span>
                            </td>

                            {{-- Kategori Badge --}}
                            <td class="px-4 py-3 text-center">
                                @php
                                    $badgeMap = [
                                        'Sangat Tinggi' => 'badge-sangat-tinggi',
                                        'Tinggi'        => 'badge-tinggi',
                                        'Sedang'        => 'badge-sedang',
                                        'Rendah'        => 'badge-rendah',
                                        'Sangat Rendah' => 'badge-sangat-rendah',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeMap[$row['kategori']] ?? 'badge-sangat-rendah' }}">
                                    {{ $row['kategori'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-filter-circle-xmark text-3xl mb-2 block text-slate-300"></i>
                                Tidak ada kecamatan dalam kategori yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <span>
                Menampilkan <strong>{{ count($hasilFiltered) }}</strong> dari
                <strong>{{ $hasil['jumlah_data'] }}</strong> kecamatan
                @if ($filterKategori !== 'semua')
                    | Filter aktif: <span class="font-semibold text-amber-600">{{ $filterKategori }}</span>
                @endif
            </span>
            <span>
                Metode: <strong>Simple Additive Weighting (SAW)</strong> •
                Skor tertinggi: <strong>{{ number_format($hasil['skor_tertinggi'], 4) }}</strong> •
                Skor terendah: <strong>{{ number_format($hasil['skor_terendah'], 4) }}</strong>
            </span>
        </div>
    </div>

    {{-- ─── Rumus SAW & Detail Bobot SPK 2 ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <button onclick="document.getElementById('panel-rumus2').classList.toggle('hidden')"
                class="w-full flex items-center justify-between text-sm font-bold text-slate-700">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-square-root-variable text-amber-500"></i>
                Detail Rumus Perhitungan Metode SAW — Skenario 2
            </span>
            <i class="fa-solid fa-chevron-down text-slate-400"></i>
        </button>
        <div id="panel-rumus2" class="hidden mt-4 space-y-4 text-sm text-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                    <h4 class="font-bold text-amber-800 mb-2">① Normalisasi (Benefit)</h4>
                    <p class="font-mono bg-white rounded-lg p-3 text-center text-amber-900 border border-amber-100">
                        r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>ij</sub>)
                    </p>
                    <p class="text-xs text-amber-700 mt-2">
                        Nilai dibagi dengan nilai TERBESAR pada kolom yang sama.<br>
                        Untuk laju pertumbuhan yang negatif, nilai digeser (+|min|) sebelum normalisasi.
                    </p>
                </div>
                <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                    <h4 class="font-bold text-orange-800 mb-2">② Skor Akhir (V<sub>i</sub>)</h4>
                    <p class="font-mono bg-white rounded-lg p-3 text-center text-orange-900 border border-orange-100">
                        V<sub>i</sub> = Σ (W<sub>j</sub> × r<sub>ij</sub>)
                    </p>
                    <p class="text-xs text-orange-700 mt-2">Jumlahkan hasil perkalian bobot (W) dengan nilai normalisasi (r) untuk semua 3 kriteria.</p>
                </div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-xs">
                <h4 class="font-bold text-slate-700 mb-3">③ Rincian Bobot & Kontribusi Skenario 2:</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach ($scenario['kriteria'] as $key => $krit)
                        @php
                            $meta = $kriteriaColors[$key] ?? ['color' => 'slate', 'icon' => 'fa-circle'];
                            $c    = $meta['color'];
                        @endphp
                        <div class="bg-white rounded-lg p-3 border border-slate-200/60">
                            <div class="flex items-center gap-1.5 mb-2">
                                <i class="fa-solid {{ $meta['icon'] }} text-{{ $c }}-600 text-sm"></i>
                                <span class="font-semibold text-slate-800">{{ $krit['label'] }}</span>
                            </div>
                            <p class="text-2xl font-black text-{{ $c }}-600">{{ $krit['bobot'] * 100 }}%</p>
                            <p class="text-slate-500 mt-1">Bobot W = {{ $krit['bobot'] }}</p>
                            <div class="mt-2 h-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $c }}-400 rounded-full" style="width: {{ $krit['bobot'] * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bars = document.querySelectorAll('.skor-bar');
        bars.forEach(bar => {
            const targetWidth = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = targetWidth; }, 150);
        });
    });
</script>
@endpush
