@extends('layouts.app')

@section('title', 'SPK 1 - Analisis Potensi & Dinamika Demografi - GIS Jember')

@push('styles')
<style>
    .skor-bar { transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); }

    .badge-sangat-tinggi { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
    .badge-tinggi        { background:#ffedd5; color:#9a3412; border-color:#fed7aa; }
    .badge-sedang        { background:#fef9c3; color:#854d0e; border-color:#fef08a; }
    .badge-rendah        { background:#e0f2fe; color:#075985; border-color:#bae6fd; }
    .badge-sangat-rendah { background:#f1f5f9; color:#475569; border-color:#e2e8f0; }

    .row-sangat-tinggi { background: linear-gradient(90deg, #fff1f2 0%, transparent 60%); }
    .row-tinggi        { background: linear-gradient(90deg, #fff7ed 0%, transparent 60%); }
    .row-sedang        { background: linear-gradient(90deg, #fefce8 0%, transparent 60%); }
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
            <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                {{ $scenario['deskripsi'] }}
            </p>
        </div>

        {{-- Tombol navigasi ke SPK 2 --}}
        <a href="{{ route('spk.spk2') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition-all whitespace-nowrap">
            <i class="fa-solid fa-landmark-dome"></i>
            <span>Lihat SPK 2 — Administrasi</span>
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </a>
    </div>

    {{-- ─── Banner Tujuan SPK ────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-gradient-to-r from-blue-700 to-blue-500 p-5 text-white flex flex-col sm:flex-row sm:items-center gap-4 shadow-md shadow-blue-600/20">
        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
            <i class="fa-solid fa-bullseye"></i>
        </div>
        <div class="flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider opacity-80">Tujuan Pengambilan Keputusan</p>
            <p class="font-bold mt-0.5 text-base">{{ $scenario['tujuan'] }}</p>
        </div>
        <div class="flex items-center gap-3 text-sm bg-white/15 rounded-xl px-4 py-2.5">
            <i class="fa-solid fa-chart-simple text-lg"></i>
            <div>
                <p class="font-bold text-xl leading-none">{{ $hasil['jumlah_data'] }}</p>
                <p class="opacity-80 text-xs">Kecamatan dianalisis</p>
            </div>
        </div>
    </div>

    {{-- ─── Kartu Kriteria & Bobot ──────────────────────────────────────────────── --}}
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-scale-balanced text-blue-500"></i>
            Kriteria & Bobot Kepentingan (Total: {{ $validasi['total_bobot'] * 100 }}%)
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($scenario['kriteria'] as $key => $krit)
                @php
                    $persen = $krit['bobot'] * 100;
                    $colors = ['jumlah_penduduk' => 'blue', 'laju_pertumbuhan' => 'purple'];
                    $c = $colors[$key] ?? 'slate';
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kriteria ({{ strtoupper($krit['sifat']) }})</p>
                            <h3 class="font-bold text-slate-900 mt-0.5">{{ $krit['label'] }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $krit['alasan'] }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-xl bg-{{ $c }}-100 text-{{ $c }}-700 flex flex-col items-center justify-center shrink-0 ml-3 font-bold shadow-sm">
                            <span class="text-lg leading-none">{{ $persen }}%</span>
                            <span class="text-[10px] font-medium opacity-70">bobot</span>
                        </div>
                    </div>
                    {{-- Progress bar bobot --}}
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="skor-bar h-full bg-{{ $c }}-500 rounded-full" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Top 3 Podium ────────────────────────────────────────────────────────── --}}
    @php
        $top3 = array_slice($hasil['hasil'], 0, 3);
    @endphp
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-trophy text-amber-500"></i>
            Peringkat Teratas
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach ($top3 as $i => $podium)
                @php
                    $medals = ['🥇', '🥈', '🥉'];
                    $gradients = [
                        'from-amber-500 to-yellow-400',
                        'from-slate-400 to-slate-300',
                        'from-orange-500 to-amber-400',
                    ];
                    $bg = [
                        'bg-gradient-to-br from-amber-50 to-yellow-50 border-amber-200',
                        'bg-gradient-to-br from-slate-50 to-zinc-50 border-slate-200',
                        'bg-gradient-to-br from-orange-50 to-amber-50 border-orange-200',
                    ];
                @endphp
                <div class="rounded-2xl border p-5 {{ $bg[$i] }} shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-3xl">{{ $medals[$i] }}</span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/70 text-slate-600 border border-slate-200/60">
                            #{{ $podium['ranking'] }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $podium['nama'] }}</h3>
                    <div class="mt-3 space-y-1">
                        <div class="text-3xl font-black text-slate-900">
                            {{ $podium['skor_persen'] }}%
                        </div>
                        <p class="text-xs text-slate-500">Skor SAW: {{ number_format($podium['skor'], 6) }}</p>
                    </div>
                    {{-- Mini bar skor --}}
                    <div class="mt-3 h-2 bg-black/10 rounded-full overflow-hidden">
                        <div class="skor-bar h-full bg-gradient-to-r {{ $gradients[$i] }} rounded-full"
                             style="width: {{ $podium['skor_persen'] }}%"></div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                        <div>
                            <span class="opacity-60">Penduduk</span>
                            <p class="font-bold">{{ number_format($podium['rincian_bobot']['jumlah_penduduk']['nilai_asli'], 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="opacity-60">Laju</span>
                            <p class="font-bold {{ $podium['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $podium['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] > 0 ? '+' : '' }}{{ number_format($podium['rincian_bobot']['laju_pertumbuhan']['nilai_asli'], 2) }}%
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Filter Kategori & Tabel Lengkap ────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

        {{-- Toolbar filter --}}
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-ol text-blue-500"></i>
                Tabel Ranking Lengkap — 31 Kecamatan
            </h2>

            {{-- Filter berdasarkan kategori --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-slate-500 font-semibold">Filter:</span>
                @foreach (['semua' => 'Semua', 'Sangat Tinggi' => 'Sangat Tinggi', 'Tinggi' => 'Tinggi', 'Sedang' => 'Sedang', 'Rendah' => 'Rendah', 'Sangat Rendah' => 'Sangat Rendah'] as $val => $label)
                    <a href="{{ route('spk.spk1', ['kategori' => $val]) }}"
                       class="px-3 py-1 text-xs font-semibold rounded-full border transition-all
                              {{ $filterKategori === $val
                                 ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                 : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tabel Ranking --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700">
                <thead class="text-xs uppercase font-semibold text-slate-400 bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-center w-16">Rank</th>
                        <th class="px-5 py-3">Kecamatan</th>
                        <th class="px-5 py-3 text-right">Penduduk (Jiwa)</th>
                        <th class="px-5 py-3 text-center">Normalisasi</th>
                        <th class="px-5 py-3 text-right">Laju (%)</th>
                        <th class="px-5 py-3 text-center">Normalisasi</th>
                        <th class="px-5 py-3 text-right font-bold text-blue-600">Skor Akhir</th>
                        <th class="px-5 py-3 text-center">Visualisasi</th>
                        <th class="px-5 py-3 text-center">Kategori</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($hasilFiltered as $row)
                        @php
                            $kelasRow = 'row-' . strtolower(str_replace(' ', '-', $row['kategori']));
                        @endphp
                        <tr class="{{ $kelasRow }} hover:brightness-95 transition-all">
                            {{-- Ranking --}}
                            <td class="px-5 py-3 text-center">
                                @if ($row['ranking'] <= 3)
                                    <span class="text-lg">{{ ['🥇','🥈','🥉'][$row['ranking']-1] }}</span>
                                @else
                                    <span class="inline-flex w-7 h-7 rounded-full bg-slate-100 items-center justify-center text-xs font-bold text-slate-600">
                                        {{ $row['ranking'] }}
                                    </span>
                                @endif
                            </td>

                            {{-- Nama Kecamatan --}}
                            <td class="px-5 py-3 font-semibold text-slate-900">
                                <a href="{{ route('kecamatan.show', $row['kecamatan_id']) }}"
                                   class="hover:text-blue-600 transition-colors">
                                    {{ $row['nama'] }}
                                </a>
                            </td>

                            {{-- Jumlah Penduduk --}}
                            <td class="px-5 py-3 text-right font-medium">
                                {{ number_format($row['rincian_bobot']['jumlah_penduduk']['nilai_asli'], 0, ',', '.') }}
                            </td>

                            {{-- Normalisasi Penduduk --}}
                            <td class="px-5 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md border border-blue-100">
                                    {{ number_format($row['rincian_bobot']['jumlah_penduduk']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Laju Pertumbuhan --}}
                            <td class="px-5 py-3 text-right font-medium {{ $row['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $row['rincian_bobot']['laju_pertumbuhan']['nilai_asli'] > 0 ? '+' : '' }}{{ number_format($row['rincian_bobot']['laju_pertumbuhan']['nilai_asli'], 2) }}%
                            </td>

                            {{-- Normalisasi Laju --}}
                            <td class="px-5 py-3 text-center text-xs font-mono">
                                <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md border border-purple-100">
                                    {{ number_format($row['rincian_bobot']['laju_pertumbuhan']['normalisasi'], 4) }}
                                </span>
                            </td>

                            {{-- Skor Akhir --}}
                            <td class="px-5 py-3 text-right">
                                <div class="font-black text-blue-700 text-base">
                                    {{ $row['skor_persen'] }}%
                                </div>
                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                    {{ number_format($row['skor'], 6) }}
                                </div>
                            </td>

                            {{-- Visualisasi Bar --}}
                            <td class="px-5 py-3 w-32">
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="skor-bar h-full bg-blue-500 rounded-full"
                                         style="width: {{ $row['skor_persen'] }}%">
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-0.5 block text-right">{{ $row['skor_persen'] }}%</span>
                            </td>

                            {{-- Kategori Badge --}}
                            <td class="px-5 py-3 text-center">
                                @php
                                    $kelasMap = [
                                        'Sangat Tinggi' => 'badge-sangat-tinggi',
                                        'Tinggi'        => 'badge-tinggi',
                                        'Sedang'        => 'badge-sedang',
                                        'Rendah'        => 'badge-rendah',
                                        'Sangat Rendah' => 'badge-sangat-rendah',
                                    ];
                                    $badgeKelas = $kelasMap[$row['kategori']] ?? 'badge-sangat-rendah';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeKelas }}">
                                    {{ $row['kategori'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-filter-circle-xmark text-3xl mb-2 block text-slate-300"></i>
                                Tidak ada kecamatan dalam kategori yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer info --}}
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <span>
                Menampilkan <strong>{{ count($hasilFiltered) }}</strong> dari
                <strong>{{ $hasil['jumlah_data'] }}</strong> kecamatan
                @if ($filterKategori !== 'semua')
                    | Filter aktif: <span class="font-semibold text-blue-600">{{ $filterKategori }}</span>
                @endif
            </span>
            <span>
                Metode: <strong>Simple Additive Weighting (SAW)</strong> •
                Skor tertinggi: <strong>{{ number_format($hasil['skor_tertinggi'], 4) }}</strong> •
                Skor terendah: <strong>{{ number_format($hasil['skor_terendah'], 4) }}</strong>
            </span>
        </div>
    </div>

    {{-- ─── Rumus SAW Accordion ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <button onclick="document.getElementById('panel-rumus').classList.toggle('hidden')"
                class="w-full flex items-center justify-between text-sm font-bold text-slate-700">
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-square-root-variable text-blue-500"></i>
                Detail Rumus Perhitungan Metode SAW
            </span>
            <i class="fa-solid fa-chevron-down text-slate-400"></i>
        </button>
        <div id="panel-rumus" class="hidden mt-4 space-y-4 text-sm text-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2">① Normalisasi (Benefit)</h4>
                    <p class="font-mono bg-white rounded-lg p-3 text-center text-blue-900 border border-blue-100">
                        r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>ij</sub>)
                    </p>
                    <p class="text-xs text-blue-700 mt-2">Nilai dibagi dengan nilai TERBESAR pada kolom yang sama.</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                    <h4 class="font-bold text-purple-800 mb-2">② Skor Akhir (V<sub>i</sub>)</h4>
                    <p class="font-mono bg-white rounded-lg p-3 text-center text-purple-900 border border-purple-100">
                        V<sub>i</sub> = Σ (W<sub>j</sub> × r<sub>ij</sub>)
                    </p>
                    <p class="text-xs text-purple-700 mt-2">Jumlahkan hasil perkalian bobot (W) dengan nilai normalisasi (r) tiap kriteria.</p>
                </div>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-xs">
                <h4 class="font-bold text-slate-700 mb-2">③ Rincian Bobot Skenario ini:</h4>
                <ul class="space-y-1 font-mono text-slate-600">
                    @foreach ($scenario['kriteria'] as $key => $krit)
                        <li>
                            <span class="text-blue-600">W_{{ $loop->iteration }}</span> ({{ $krit['label'] }}) =
                            <strong>{{ $krit['bobot'] }}</strong> ({{ $krit['bobot'] * 100 }}%)
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Animasi bar skor saat halaman dimuat
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
