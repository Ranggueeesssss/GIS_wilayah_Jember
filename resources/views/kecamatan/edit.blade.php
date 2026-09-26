@extends('layouts.app')

@section('title', 'Edit Data Kecamatan ' . $kecamatan->nama . ' - GIS Jember')

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
            <span class="text-slate-800 font-semibold">Edit {{ $kecamatan->nama }}</span>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Edit Data Kecamatan: {{ $kecamatan->nama }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi spasial atau angka statistik BPS wilayah ini.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('kecamatan.update', $kecamatan->id) }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Identitas Keruangan & Geografis -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center">
                    <i class="fa-solid fa-compass mr-2 text-amber-600"></i>
                    1. Identitas & Koordinat Spasial
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kecamatan -->
                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Nama Kecamatan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama" 
                               id="nama" 
                               value="{{ old('nama', $kecamatan->nama) }}" 
                               required
                               placeholder="Nama Kecamatan" 
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>

                    <!-- Latitude -->
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Latitude (Garis Lintang) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               step="0.000001" 
                               name="latitude" 
                               id="latitude" 
                               value="{{ old('latitude', $kecamatan->latitude) }}" 
                               required
                               class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>

                    <!-- Longitude -->
                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Longitude (Garis Bujur) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               step="0.000001" 
                               name="longitude" 
                               id="longitude" 
                               value="{{ old('longitude', $kecamatan->longitude) }}" 
                               required
                               class="w-full px-4 py-2.5 text-sm font-mono bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>

                    <!-- Warna Poligon -->
                    <div>
                        <label for="warna_polygon" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Warna Poligon Kustom
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color" 
                                   name="warna_polygon" 
                                   id="warna_polygon" 
                                   value="{{ old('warna_polygon', $kecamatan->warna_polygon ?? '#10b981') }}" 
                                   class="w-12 h-10 rounded-lg border border-slate-200 p-0.5 cursor-pointer bg-white">
                            <span class="text-xs text-slate-500">Warna representasi di peta.</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Section 2: Indikator Statistik BPS -->
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center">
                    <i class="fa-solid fa-chart-pie mr-2 text-amber-600"></i>
                    2. Indikator Statistik (BPS)
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jumlah Penduduk -->
                    <div>
                        <label for="jumlah_penduduk" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Jumlah Penduduk (Jiwa)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="jumlah_penduduk" 
                                   id="jumlah_penduduk" 
                                   value="{{ old('jumlah_penduduk', $kecamatan->statistik->jumlah_penduduk ?? '') }}" 
                                   min="0"
                                   placeholder="Contoh: 125000" 
                                   class="w-full pl-4 pr-14 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 pointer-events-none">
                                Jiwa
                            </span>
                        </div>
                    </div>

                    <!-- Laju Pertumbuhan -->
                    <div>
                        <label for="laju_pertumbuhan" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Laju Pertumbuhan Penduduk (%/Tahun)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   step="0.01" 
                                   name="laju_pertumbuhan" 
                                   id="laju_pertumbuhan" 
                                   value="{{ old('laju_pertumbuhan', $kecamatan->statistik->laju_pertumbuhan ?? '') }}" 
                                   class="w-full pl-4 pr-10 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 pointer-events-none">
                                %
                            </span>
                        </div>
                    </div>

                    <!-- Jumlah Desa / Kelurahan -->
                    <div>
                        <label for="jumlah_desa" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Jumlah Desa / Kelurahan
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="jumlah_desa" 
                                   id="jumlah_desa" 
                                   value="{{ old('jumlah_desa', $kecamatan->statistik->jumlah_desa ?? '') }}" 
                                   min="0"
                                   class="w-full pl-4 pr-16 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-slate-400 pointer-events-none">
                                Desa
                            </span>
                        </div>
                    </div>

                    <!-- Tahun Data -->
                    <div>
                        <label for="tahun" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Tahun Rujukan Data
                        </label>
                        <input type="number" 
                               name="tahun" 
                               id="tahun" 
                               value="{{ old('tahun', $kecamatan->statistik->tahun ?? 2024) }}" 
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('kecamatan.index') }}" 
                   class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-sm shadow-amber-600/30 transition-all hover:shadow-md hover:-translate-y-0.5 flex items-center">
                    <i class="fa-solid fa-check mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
