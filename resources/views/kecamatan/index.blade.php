@extends('layouts.app')

@section('title', 'Data Statistik Wilayah - GIS Jember')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Manajemen Data Statistik Wilayah
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola data profil keruangan dan indikator statistik 31 kecamatan di Kabupaten Jember.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('kecamatan.create') }}" 
               class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm shadow-emerald-600/30 transition-all hover:shadow-md hover:-translate-y-0.5">
                <i class="fa-solid fa-plus-circle mr-2 text-base"></i>
                <span>Tambah Kecamatan</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Kecamatan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 text-xl font-bold">
                <i class="fa-solid fa-map-marked-alt"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kecamatan</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $summary['total_kecamatan'] ?? 31 }}</h3>
                <span class="text-[11px] text-slate-500">Kabupaten Jember</span>
            </div>
        </div>

        <!-- Card 2: Total Penduduk -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xl font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Penduduk</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($summary['total_penduduk'] ?? 0, 0, ',', '.') }}</h3>
                <span class="text-[11px] text-emerald-600 font-medium">Jiwa (Tahun 2024)</span>
            </div>
        </div>

        <!-- Card 3: Total Desa/Kelurahan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-xl font-bold">
                <i class="fa-solid fa-building-flag"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Desa/Kelurahan</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-0.5">{{ $summary['total_desa'] ?? 0 }}</h3>
                <span class="text-[11px] text-slate-500">Desa & Kelurahan</span>
            </div>
        </div>

        <!-- Card 4: Rata-rata Pertumbuhan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 text-xl font-bold">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Laju Pertumbuhan</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-0.5">+{{ $summary['avg_laju'] ?? 0.57 }}%</h3>
                <span class="text-[11px] text-purple-600 font-medium">Rata-rata/Tahun (2020-2024)</span>
            </div>
        </div>
    </div>

    <!-- Filter, Search (Find), and Sort Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('kecamatan.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <!-- Search Input (Find) -->
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari nama kecamatan..." 
                       class="w-full pl-10 pr-10 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                @if (!empty($search))
                    <a href="{{ route('kecamatan.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                    </a>
                @endif
            </div>

            <!-- Sort By Controls -->
            <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
                <label for="sort_by" class="text-xs font-semibold text-slate-500 hidden sm:inline-block">Urutkan:</label>
                <select name="sort_by" id="sort_by" onchange="this.form.submit()" 
                        class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
                    <option value="nama" {{ $sortBy == 'nama' ? 'selected' : '' }}>Nama Kecamatan</option>
                    <option value="jumlah_penduduk" {{ $sortBy == 'jumlah_penduduk' ? 'selected' : '' }}>Jumlah Penduduk</option>
                    <option value="laju_pertumbuhan" {{ $sortBy == 'laju_pertumbuhan' ? 'selected' : '' }}>Laju Pertumbuhan</option>
                    <option value="jumlah_desa" {{ $sortBy == 'jumlah_desa' ? 'selected' : '' }}>Jumlah Desa</option>
                </select>

                <select name="sort_order" onchange="this.form.submit()" 
                        class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-700">
                    <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Menaik (A-Z / Rendah)</option>
                    <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Menurun (Z-A / Tinggi)</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4 w-12 text-center">No</th>
                        
                        <!-- Header Sortable: Nama -->
                        <th class="px-5 py-4">
                            <a href="{{ route('kecamatan.index', ['sort_by' => 'nama', 'sort_order' => ($sortBy == 'nama' && $sortOrder == 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" 
                               class="group inline-flex items-center space-x-1.5 hover:text-slate-900 transition-colors">
                                <span>Kecamatan</span>
                                <i class="fa-solid fa-sort text-xs {{ $sortBy == 'nama' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-500' }}"></i>
                            </a>
                        </th>

                        <!-- Header: Koordinat Pusat -->
                        <th class="px-5 py-4">Koordinat Pusat (Lat, Lng)</th>

                        <!-- Header Sortable: Jumlah Penduduk -->
                        <th class="px-5 py-4">
                            <a href="{{ route('kecamatan.index', ['sort_by' => 'jumlah_penduduk', 'sort_order' => ($sortBy == 'jumlah_penduduk' && $sortOrder == 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" 
                               class="group inline-flex items-center space-x-1.5 hover:text-slate-900 transition-colors">
                                <span>Jumlah Penduduk</span>
                                <i class="fa-solid fa-sort text-xs {{ $sortBy == 'jumlah_penduduk' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-500' }}"></i>
                            </a>
                        </th>

                        <!-- Header Sortable: Laju Pertumbuhan -->
                        <th class="px-5 py-4">
                            <a href="{{ route('kecamatan.index', ['sort_by' => 'laju_pertumbuhan', 'sort_order' => ($sortBy == 'laju_pertumbuhan' && $sortOrder == 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" 
                               class="group inline-flex items-center space-x-1.5 hover:text-slate-900 transition-colors">
                                <span>Laju Pertumbuhan</span>
                                <i class="fa-solid fa-sort text-xs {{ $sortBy == 'laju_pertumbuhan' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-500' }}"></i>
                            </a>
                        </th>

                        <!-- Header Sortable: Jumlah Desa -->
                        <th class="px-5 py-4">
                            <a href="{{ route('kecamatan.index', ['sort_by' => 'jumlah_desa', 'sort_order' => ($sortBy == 'jumlah_desa' && $sortOrder == 'asc') ? 'desc' : 'asc', 'search' => $search]) }}" 
                               class="group inline-flex items-center space-x-1.5 hover:text-slate-900 transition-colors">
                                <span>Jumlah Desa</span>
                                <i class="fa-solid fa-sort text-xs {{ $sortBy == 'jumlah_desa' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-500' }}"></i>
                            </a>
                        </th>

                        <th class="px-5 py-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kecamatans as $index => $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-4 text-center font-medium text-slate-400 text-xs">
                                {{ $kecamatans->firstItem() + $index }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100/60 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('kecamatan.show', $item->id) }}" class="font-bold text-slate-900 hover:text-emerald-600 transition-colors">
                                            {{ $item->nama }}
                                        </a>
                                        <p class="text-[11px] text-slate-400">ID Wilayah: #{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono bg-slate-100 text-slate-600 border border-slate-200/60">
                                    <i class="fa-solid fa-location-crosshairs mr-1.5 text-[10px] text-slate-400"></i>
                                    {{ number_format($item->latitude, 4) }}, {{ number_format($item->longitude, 4) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800">
                                @if ($item->statistik)
                                    <span>{{ number_format($item->statistik->jumlah_penduduk, 0, ',', '.') }}</span>
                                    <span class="text-xs font-normal text-slate-400 ml-1">jiwa</span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada data</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($item->statistik)
                                    @php $laju = $item->statistik->laju_pertumbuhan; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $laju >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        <i class="fa-solid {{ $laju >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1 text-[10px]"></i>
                                        {{ $laju > 0 ? '+' : '' }}{{ number_format($laju, 2) }}%
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700">
                                @if ($item->statistik)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200/60 text-xs font-semibold">
                                        <i class="fa-solid fa-house-chimney mr-1.5 text-[10px] text-amber-600"></i>
                                        {{ $item->statistik->jumlah_desa }} Desa
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- View Detail -->
                                    <a href="{{ route('kecamatan.show', $item->id) }}" 
                                       title="Lihat Detail" 
                                       class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    <!-- Edit Data -->
                                    <a href="{{ route('kecamatan.edit', $item->id) }}" 
                                       title="Edit Data" 
                                       class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('kecamatan.destroy', $item->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Kecamatan {{ $item->nama }}? Semua data statistik terkait juga akan dihapus.');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Kecamatan" 
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300"></i>
                                    <p class="text-base font-semibold text-slate-600">Tidak ada data kecamatan ditemukan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba kata kunci pencarian lain atau klik tombol tambah data.</p>
                                    <a href="{{ route('kecamatan.index') }}" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                                        Reset Pencarian
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if ($kecamatans->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-700">{{ $kecamatans->firstItem() }}</span> sampai 
                    <span class="font-semibold text-slate-700">{{ $kecamatans->lastItem() }}</span> dari 
                    <span class="font-semibold text-slate-700">{{ $kecamatans->total() }}</span> kecamatan
                </span>
                <div>
                    {{ $kecamatans->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
