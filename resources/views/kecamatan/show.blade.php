@extends('layouts.app')

@section('title', 'Detail Kecamatan ' . $kecamatan->nama . ' - GIS Jember')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-slate-500">
            <a href="{{ route('kecamatan.index') }}" class="hover:text-emerald-600 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1.5"></i>
                <span>Kembali ke Daftar Wilayah</span>
            </a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">{{ $kecamatan->nama }}</span>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('kecamatan.edit', $kecamatan->id) }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition-all">
                <i class="fa-solid fa-pen-to-square mr-2 text-amber-500"></i>
                <span>Edit Data</span>
            </a>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-100 gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center font-bold text-2xl shadow-md shadow-emerald-500/20">
                    {{ substr($kecamatan->nama, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold text-slate-900">Kecamatan {{ $kecamatan->nama }}</h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                            Kab. Jember
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        ID Entitas Wilayah: #{{ str_pad($kecamatan->id, 2, '0', STR_PAD_LEFT) }} • Terdaftar pada sistem GIS
                    </p>
                </div>
            </div>

            <!-- Coordinate Pill -->
            <div class="inline-flex items-center px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-700">
                <i class="fa-solid fa-location-dot text-emerald-600 mr-2 text-sm"></i>
                <span>Lat: {{ $kecamatan->latitude }} | Lng: {{ $kecamatan->longitude }}</span>
            </div>
        </div>

        <!-- 4 Metric Cards for this Kecamatan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <!-- Jumlah Penduduk -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Penduduk</p>
                <h4 class="text-xl font-bold text-slate-900 mt-1">
                    {{ number_format($kecamatan->statistik->jumlah_penduduk ?? 0, 0, ',', '.') }}
                </h4>
                <span class="text-[11px] text-slate-500">Jiwa (BPS 2024)</span>
            </div>

            <!-- Laju Pertumbuhan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pertumbuhan / Thn</p>
                @php $laju = $kecamatan->statistik->laju_pertumbuhan ?? 0; @endphp
                <h4 class="text-xl font-bold {{ $laju >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                    {{ $laju > 0 ? '+' : '' }}{{ number_format($laju, 2) }}%
                </h4>
                <span class="text-[11px] text-slate-500">Periode 2020-2024</span>
            </div>

            <!-- Jumlah Desa / Kelurahan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Wilayah Administrasi</p>
                <h4 class="text-xl font-bold text-amber-700 mt-1">
                    {{ $kecamatan->statistik->jumlah_desa ?? 0 }} Desa
                </h4>
                <span class="text-[11px] text-slate-500">Desa & Kelurahan</span>
            </div>

            <!-- Rata-rata Penduduk per Desa -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Rata-rata per Desa</p>
                @php 
                    $pddk = $kecamatan->statistik->jumlah_penduduk ?? 0;
                    $desa = $kecamatan->statistik->jumlah_desa ?? 1;
                    $avgPerDesa = $desa > 0 ? round($pddk / $desa) : 0;
                @endphp
                <h4 class="text-xl font-bold text-indigo-700 mt-1">
                    {{ number_format($avgPerDesa, 0, ',', '.') }}
                </h4>
                <span class="text-[11px] text-slate-500">Jiwa / Desa</span>
            </div>
        </div>

        <!-- Google Maps Link / External Map Preview -->
        <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                <i class="fa-solid fa-circle-info mr-1 text-slate-400"></i>
                Visualisasi poligon batas kecamatan ini akan tampil di modul peta Leaflet pada Tahap 4.
            </div>

            <a href="https://www.google.com/maps?q={{ $kecamatan->latitude }},{{ $kecamatan->longitude }}" 
               target="_blank" 
               class="inline-flex items-center px-4 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1.5"></i>
                <span>Cek di Google Maps</span>
            </a>
        </div>
    </div>

</div>
@endsection
