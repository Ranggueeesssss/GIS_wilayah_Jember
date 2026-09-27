@extends('layouts.app')

@section('title', 'Peta Spasial Web GIS & Analisis SPK - Kabupaten Jember')

@push('styles')
    <!-- Leaflet CSS 1.9.4 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />

    <style>
        #map {
            height: 640px;
            width: 100%;
            z-index: 10;
        }

        /* Saat fullscreen diaktifkan */
        #mapCard:fullscreen {
            border-radius: 0;
            border: none;
            width: 100vw;
            height: 100vh;
        }
        #mapCard:fullscreen #map {
            height: calc(100vh - 125px);
        }

        /* Styling popup leaflet agar modern & rapi */
        .leaflet-popup-content-wrapper {
            border-radius: 18px;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.16), 0 8px 12px -6px rgba(0, 0, 0, 0.08);
            padding: 2px;
            border: 1px solid #e2e8f0;
        }
        .leaflet-popup-content {
            margin: 10px 12px;
            line-height: 1.5;
        }
        .leaflet-popup-tip {
            border: 1px solid #e2e8f0;
        }

        /* Tooltip nama kecamatan saat hover */
        .kecamatan-tooltip {
            background-color: rgba(15, 23, 42, 0.92);
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 11px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(4px);
        }
        .kecamatan-tooltip::before {
            border-top-color: rgba(15, 23, 42, 0.92);
        }

        /* Label Centroid Nama Kecamatan yang menempel di peta */
        .district-centroid-label {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.5);
            border-radius: 6px;
            padding: 1px 6px;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
            pointer-events: none;
            backdrop-filter: blur(2px);
        }

        /* Kontainer Legenda Peta (Floating Control) */
        .choropleth-legend {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            line-height: 1.5;
            font-size: 11px;
            color: #334155;
            max-width: 250px;
        }

        .legend-color-box {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Mini Tabs di dalam Popup */
        .popup-tab-btn.active {
            color: #059669;
            border-bottom: 2px solid #059669;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('kecamatan.index') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Peta Spasial & Analisis SPK</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-base shrink-0 shadow-sm shadow-emerald-500/30">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                Peta Spasial Web GIS & Analisis SPK
            </h1>
            <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                Eksplorasi spasial interaktif 31 kecamatan di Kabupaten Jember dengan fitur Choropleth SAW, navigasi pencarian cepat, dan popup informasi komprehensif.
            </p>
        </div>

        <!-- Indikator Status & Navigasi Cepat -->
        <div class="flex flex-wrap items-center gap-2.5">
            <div id="layerStatusBadge" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-semibold shadow-2xs transition-all">
                <i id="layerStatusIcon" class="fa-solid fa-spinner fa-spin text-emerald-600"></i>
                <span id="layerStatusText">Memuat Data Spasial...</span>
            </div>
            <a href="{{ route('spk.spk1') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition-all">
                <i class="fa-solid fa-chart-column"></i>
                <span>Tabel SPK 1</span>
            </a>
            <a href="{{ route('spk.spk2') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition-all">
                <i class="fa-solid fa-chart-column"></i>
                <span>Tabel SPK 2</span>
            </a>
        </div>
    </div>

    <!-- Map Container Card -->
    <div id="mapCard" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all">
        
        <!-- Toolbar Baris 1: Mode Choropleth & Navigasi Utama -->
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            
            <!-- Pilihan Mode Visualisasi Peta (Tabs) -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-palette text-emerald-600"></i> Visualisasi:
                </span>

                <!-- Mode 1: Standar Wilayah -->
                <button type="button" data-mode="standar" 
                        class="btn-mode px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all border flex items-center gap-1.5
                               bg-emerald-600 text-white border-transparent shadow-2xs active-mode">
                    <i class="fa-solid fa-layer-group text-white"></i>
                    <span>Batas Netral</span>
                </button>

                <!-- Mode 2: SPK 1 Choropleth -->
                <button type="button" data-mode="spk1" 
                        class="btn-mode px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all border flex items-center gap-1.5
                               bg-white text-slate-700 border-slate-200 hover:bg-blue-50 hover:text-blue-700 shadow-2xs">
                    <i class="fa-solid fa-users text-blue-600"></i>
                    <span>Choropleth SPK 1 (Demografi)</span>
                </button>

                <!-- Mode 3: SPK 2 Choropleth -->
                <button type="button" data-mode="spk2" 
                        class="btn-mode px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all border flex items-center gap-1.5
                               bg-white text-slate-700 border-slate-200 hover:bg-amber-50 hover:text-amber-700 shadow-2xs">
                    <i class="fa-solid fa-landmark-dome text-amber-600"></i>
                    <span>Choropleth SPK 2 (Administrasi)</span>
                </button>
            </div>

            <!-- Toolbar Kanan: Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto">
                
                <!-- Tombol Fullscreen -->
                <button id="btnFullscreen" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all"
                        title="Perbesar Peta ke Layar Penuh">
                    <i id="fullscreenIcon" class="fa-solid fa-expand text-slate-600"></i>
                    <span id="fullscreenText">Layar Penuh</span>
                </button>

                <!-- Fit Bounds GeoJSON Button -->
                <button id="btnFitBounds" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all"
                        title="Sesuaikan tampilan penuh ke batas 31 kecamatan">
                    <i class="fa-solid fa-compress text-emerald-600"></i>
                    <span>Fokus Jember</span>
                </button>

                <!-- Reset Center Button -->
                <button id="btnResetView" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all"
                        title="Pusatkan kembali ke Alun-Alun Jember">
                    <i class="fa-solid fa-crosshairs text-emerald-600"></i>
                    <span>Pusat</span>
                </button>
            </div>
        </div>

        <!-- Toolbar Baris 2: Kontrol Lanjutan (Pencarian Cepat, Filter Kategori, Pilihan Basemap, Label Centroid) -->
        <div class="px-5 py-2.5 border-b border-slate-100 bg-white flex flex-wrap items-center justify-between gap-3 text-xs">
            
            <!-- Pencarian Cepat Wilayah (Quick Jump) -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold text-slate-700 flex items-center gap-1">
                    <i class="fa-solid fa-magnifying-glass-location text-emerald-600"></i> Cari Wilayah:
                </span>
                <select id="selectQuickJump" 
                        class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:outline-emerald-500 focus:bg-white text-xs min-w-[170px]">
                    <option value="">-- Pilih Kecamatan (31) --</option>
                    @foreach ($kecamatanList as $kec)
                        <option value="{{ $kec->nama }}">{{ $kec->nama }}</option>
                    @endforeach
                </select>

                <!-- Filter Kategori (hanya saat mode SPK aktif) -->
                <div id="filterCategoryContainer" class="hidden items-center gap-2 ml-1">
                    <span class="text-slate-300">|</span>
                    <span class="font-bold text-slate-700 flex items-center gap-1">
                        <i class="fa-solid fa-filter text-blue-600"></i> Kategori:
                    </span>
                    <select id="selectCategoryFilter" 
                            class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:outline-blue-500 focus:bg-white text-xs">
                        <option value="all">Semua Kategori</option>
                        <option value="Sangat Tinggi">Sangat Tinggi</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Rendah">Rendah</option>
                        <option value="Sangat Rendah">Sangat Rendah</option>
                    </select>
                </div>
            </div>

            <!-- Kontrol Layer Tambahan (Basemap Selector & Label Centroid) -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Pilihan Basemap Tile Layer -->
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-500 font-semibold"><i class="fa-solid fa-map"></i> Basemap:</span>
                    <select id="selectBasemap" 
                            class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:outline-emerald-500 text-xs">
                        <option value="osm">OpenStreetMap</option>
                        <option value="positron">CartoDB Positron (Terang)</option>
                        <option value="satellite">Esri World Imagery (Satelit)</option>
                    </select>
                </div>

                <span class="text-slate-200">|</span>

                <!-- Checkbox Label Nama Centroid -->
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none text-slate-700 font-medium">
                    <input type="checkbox" id="toggleCentroidLabels" 
                           class="w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <span>Label Nama Peta</span>
                </label>
            </div>
        </div>

        <!-- Info Banner Mode Aktif -->
        <div id="modeBanner" class="px-5 py-2 bg-slate-100/70 border-b border-slate-100 text-xs text-slate-600 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i id="modeBannerIcon" class="fa-solid fa-circle-info text-emerald-600"></i>
                <span id="modeBannerText">Menampilkan batas wilayah administratif 31 kecamatan Kabupaten Jember dalam warna netral.</span>
            </div>
            <div class="hidden sm:block text-slate-400 font-mono text-[11px]">
                31 Wilayah Terdaftar • BPS 2024
            </div>
        </div>

        <!-- Canvas Peta Leaflet -->
        <div id="map" class="relative"></div>

        <!-- Footer Map Status -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>Gunakan dropdown "Cari Wilayah" untuk langsung terbang ke kecamatan, atau klik poligon untuk membuka popup komprehensif.</span>
            </div>
            <div class="font-mono text-[11px] text-slate-400">
                Koordinat Cursor: <span id="mouseCoords" class="text-slate-600 font-semibold">-8.1721, 113.7001</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <!-- Leaflet JS 1.9.4 -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data konfigurasi dari Controller
            const defaultLat = {{ $centerCoords['lat'] }};
            const defaultLng = {{ $centerCoords['lng'] }};
            const defaultZoom = {{ $centerCoords['zoom'] }};
            const kecamatanIdMap = @json($kecamatanMap);
            const spk1Data = @json($spk1ByName);
            const spk2Data = @json($spk2ByName);

            // Variabel Status Peta
            let currentMode = 'standar';
            let currentCategoryFilter = 'all';
            let geojsonLayer = null;
            let legendControl = null;
            let centroidLabelLayer = L.layerGroup();
            let districtLayers = {}; // Simpan referensi layer berdasarkan nama kecamatan

            // 1. Inisialisasi Peta Leaflet
            const map = L.map('map', {
                center: [defaultLat, defaultLng],
                zoom: defaultZoom,
                zoomControl: true,
                scrollWheelZoom: true
            });

            // 2. Daftar Basemap Tile Layer
            const basemaps = {
                osm: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
                }),
                positron: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20,
                    attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
                }),
                satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
                })
            };

            // Tambahkan default basemap (OSM)
            let activeBasemap = basemaps.osm.addTo(map);

            // Ganti Basemap saat selector berubah
            document.getElementById('selectBasemap').addEventListener('change', function (e) {
                map.removeLayer(activeBasemap);
                activeBasemap = basemaps[e.target.value] || basemaps.osm;
                activeBasemap.addTo(map);
            });

            // 3. Kontrol Skala Metrik
            L.control.scale({
                imperial: false,
                metric: true,
                position: 'bottomleft'
            }).addTo(map);

            // 4. Marker Penanda Pusat Kabupaten Jember
            const centerMarker = L.marker([defaultLat, defaultLng])
                .addTo(map)
                .bindPopup(`
                    <div class="text-slate-800 p-1">
                        <div class="flex items-center gap-2 text-emerald-600 font-bold text-sm mb-1">
                            <i class="fa-solid fa-location-dot"></i>
                            Pusat Kabupaten Jember
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Alun-Alun / Pusat Pemerintahan Kab. Jember<br>
                            <span class="font-mono text-[11px] text-slate-500">Lat: ${defaultLat}, Lng: ${defaultLng}</span>
                        </p>
                    </div>
                `);

            // 5. Palet Warna Kategori Choropleth SPK (5 Tingkat)
            function getCategoryColor(kategori) {
                switch (kategori) {
                    case 'Sangat Tinggi': return '#e11d48'; // Rose-600 (Prioritas Tertinggi)
                    case 'Tinggi':        return '#f97316'; // Orange-500
                    case 'Sedang':        return '#eab308'; // Yellow-500
                    case 'Rendah':        return '#0284c7'; // Sky-600
                    case 'Sangat Rendah': return '#64748b'; // Slate-500
                    default:              return '#10b981'; // Default Emerald
                }
            }

            // Fungsi Penentuan Gaya Poligon Dinamis (Mendukung Filter Kategori)
            function getFeatureStyle(feature) {
                const nama = feature.properties.nama;
                const spk1 = spk1Data[nama];
                const spk2 = spk2Data[nama];

                if (currentMode === 'spk1') {
                    const kategori = spk1 ? spk1.kategori : '';
                    const isFiltered = currentCategoryFilter !== 'all' && kategori !== currentCategoryFilter;
                    const baseColor = spk1 ? getCategoryColor(kategori) : '#94a3b8';

                    if (isFiltered) {
                        return {
                            color: '#cbd5e1',
                            weight: 1,
                            opacity: 0.4,
                            fillColor: '#f1f5f9',
                            fillOpacity: 0.1
                        };
                    }

                    return {
                        color: baseColor,
                        weight: 2,
                        opacity: 0.95,
                        fillColor: baseColor,
                        fillOpacity: 0.58
                    };
                } else if (currentMode === 'spk2') {
                    const kategori = spk2 ? spk2.kategori : '';
                    const isFiltered = currentCategoryFilter !== 'all' && kategori !== currentCategoryFilter;
                    const baseColor = spk2 ? getCategoryColor(kategori) : '#94a3b8';

                    if (isFiltered) {
                        return {
                            color: '#cbd5e1',
                            weight: 1,
                            opacity: 0.4,
                            fillColor: '#f1f5f9',
                            fillOpacity: 0.1
                        };
                    }

                    return {
                        color: baseColor,
                        weight: 2,
                        opacity: 0.95,
                        fillColor: baseColor,
                        fillOpacity: 0.58
                    };
                } else {
                    // Mode Standar Batas Wilayah
                    return {
                        color: '#059669',
                        weight: 1.5,
                        opacity: 0.85,
                        fillColor: '#10b981',
                        fillOpacity: 0.20,
                        dashArray: '3, 4'
                    };
                }
            }

            // Fungsi Membangun Popup Interaktif Komprehensif (3-Tab & Detail)
            function createComprehensivePopupHtml(nama, kodeBPS, kecamatanId, spk1, spk2) {
                const safeName = nama.replace(/['"]/g, '');

                return `
                    <div class="text-slate-800 w-[285px] max-w-full text-xs">
                        <!-- Header Popup -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                            <span class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-map-pin text-emerald-600"></i> ${safeName}
                            </span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-mono text-[10px] font-bold rounded">
                                ${kodeBPS}
                            </span>
                        </div>

                        <!-- Perbandingan SPK 1 vs SPK 2 Card -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <!-- SPK 1 Mini Card -->
                            <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-2 text-center">
                                <span class="text-[9px] uppercase font-bold text-blue-700 block">SPK 1 Demografi</span>
                                <div class="text-lg font-black text-blue-900 my-0.5 leading-none">
                                    #${spk1 ? spk1.ranking : '-'}
                                </div>
                                <span class="text-[10px] text-blue-800 font-semibold block">${spk1 ? spk1.skor_persen + '%' : '-'}</span>
                                <span class="inline-block mt-1 px-1.5 py-0.2 text-[8px] font-bold rounded-full text-white" 
                                      style="background-color: ${spk1 ? getCategoryColor(spk1.kategori) : '#94a3b8'}">
                                    ${spk1 ? spk1.kategori : '-'}
                                </span>
                            </div>

                            <!-- SPK 2 Mini Card -->
                            <div class="bg-amber-50/70 border border-amber-100 rounded-xl p-2 text-center">
                                <span class="text-[9px] uppercase font-bold text-amber-700 block">SPK 2 Administrasi</span>
                                <div class="text-lg font-black text-amber-900 my-0.5 leading-none">
                                    #${spk2 ? spk2.ranking : '-'}
                                </div>
                                <span class="text-[10px] text-amber-800 font-semibold block">${spk2 ? spk2.skor_persen + '%' : '-'}</span>
                                <span class="inline-block mt-1 px-1.5 py-0.2 text-[8px] font-bold rounded-full text-white" 
                                      style="background-color: ${spk2 ? getCategoryColor(spk2.kategori) : '#94a3b8'}">
                                    ${spk2 ? spk2.kategori : '-'}
                                </span>
                            </div>
                        </div>

                        <!-- Ringkasan Statistik BPS -->
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 mb-3">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1.5">
                                <i class="fa-solid fa-chart-simple text-slate-500 mr-1"></i> Data Statistik BPS 2024
                            </span>
                            <div class="grid grid-cols-3 gap-1.5 text-center text-[11px]">
                                <div>
                                    <span class="text-slate-400 block text-[9px]">Penduduk</span>
                                    <strong class="text-slate-800">${spk1 ? Number(spk1.rincian_bobot.jumlah_penduduk.nilai_asli).toLocaleString('id-ID') : '-'}</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px]">Laju</span>
                                    <strong class="${spk1 && spk1.rincian_bobot.laju_pertumbuhan.nilai_asli >= 0 ? 'text-emerald-600' : 'text-rose-600'}">
                                        ${spk1 ? spk1.rincian_bobot.laju_pertumbuhan.nilai_asli + '%' : '-'}
                                    </strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px]">Desa</span>
                                    <strong class="text-slate-800">${spk2 ? spk2.rincian_bobot.jumlah_desa.nilai_asli : '-'}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Aksi Tombol Navigasi -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-1.5">
                            <button type="button" onclick="window.zoomToDistrict('${safeName}')" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-all">
                                <i class="fa-solid fa-magnifying-glass-plus text-slate-500"></i> Zoom
                            </button>
                            ${kecamatanId ? `
                                <a href="/kecamatan/${kecamatanId}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-semibold transition-all shadow-2xs">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Lihat Detail
                                </a>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            // Fungsi Global: Zoom ke poligon kecamatan dari dalam popup
            window.zoomToDistrict = function (nama) {
                const layer = districtLayers[nama];
                if (layer) {
                    map.flyToBounds(layer.getBounds(), {
                        maxZoom: 13,
                        duration: 1.2
                    });
                }
            };

            // Interaksi untuk setiap Feature Poligon
            function onEachFeature(feature, layer) {
                const props = feature.properties;
                const nama = props.nama || 'Kecamatan';
                const kodeBPS = props.kode || '-';
                const kecamatanId = kecamatanIdMap[nama] || null;

                // Simpan referensi layer
                districtLayers[nama] = layer;

                const spk1 = spk1Data[nama] || null;
                const spk2 = spk2Data[nama] || null;

                // Tooltip dinamis saat hover
                updateLayerTooltip(layer, nama, spk1, spk2);

                // Pasang Popup Komprehensif
                const popupContent = createComprehensivePopupHtml(nama, kodeBPS, kecamatanId, spk1, spk2);
                layer.bindPopup(popupContent, { maxWidth: 320 });

                // Tambahkan Label Centroid jika koordinat label tersedia
                if (props.label && Array.isArray(props.label) && props.label.length === 2) {
                    const labelIcon = L.divIcon({
                        className: 'district-centroid-label',
                        html: nama,
                        iconSize: [80, 20],
                        iconAnchor: [40, 10]
                    });
                    L.marker(props.label, { icon: labelIcon }).addTo(centroidLabelLayer);
                }

                // Hover Effects
                layer.on({
                    mouseover: function (e) {
                        const targetLayer = e.target;
                        targetLayer.setStyle({
                            weight: 3.5,
                            opacity: 1,
                            fillOpacity: 0.8
                        });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            targetLayer.bringToFront();
                        }
                    },
                    mouseout: function (e) {
                        if (geojsonLayer) {
                            geojsonLayer.resetStyle(e.target);
                        }
                    }
                });
            }

            // Update Tooltip dinamis sesuai mode aktif
            function updateLayerTooltip(layer, nama, spk1, spk2) {
                let tooltipText = nama;
                if (currentMode === 'spk1' && spk1) {
                    tooltipText = `#${spk1.ranking} ${nama} (${spk1.skor_persen}% - ${spk1.kategori})`;
                } else if (currentMode === 'spk2' && spk2) {
                    tooltipText = `#${spk2.ranking} ${nama} (${spk2.skor_persen}% - ${spk2.kategori})`;
                }

                layer.bindTooltip(tooltipText, {
                    permanent: false,
                    direction: 'center',
                    className: 'kecamatan-tooltip'
                });
            }

            // 6. Kontrol Legenda Peta (Choropleth Legend)
            function updateMapLegend() {
                if (legendControl) {
                    map.removeControl(legendControl);
                    legendControl = null;
                }

                if (currentMode === 'standar') {
                    return; // Sembunyikan legenda pada mode standar
                }

                legendControl = L.control({ position: 'bottomright' });
                legendControl.onAdd = function () {
                    const div = L.DomUtil.create('div', 'choropleth-legend');
                    const isSpk1 = currentMode === 'spk1';
                    const title = isSpk1 ? 'Legenda SPK 1 — Demografi' : 'Legenda SPK 2 — Administrasi';

                    const categories = [
                        { name: 'Sangat Tinggi', color: '#e11d48', desc: 'Rank 1 - 7' },
                        { name: 'Tinggi',        color: '#f97316', desc: 'Rank 8 - 13' },
                        { name: 'Sedang',        color: '#eab308', desc: 'Rank 14 - 19' },
                        { name: 'Rendah',        color: '#0284c7', desc: 'Rank 20 - 25' },
                        { name: 'Sangat Rendah', color: '#64748b', desc: 'Rank 26 - 31' },
                    ];

                    let html = `
                        <div class="font-bold text-slate-800 text-xs mb-2 pb-1.5 border-b border-slate-200/80 flex items-center justify-between">
                            <span>${title}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 font-bold">SAW</span>
                        </div>
                        <div class="space-y-1">
                    `;

                    categories.forEach(item => {
                        html += `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="legend-color-box" style="background-color: ${item.color};"></span>
                                    <span class="font-semibold text-slate-700">${item.name}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono">${item.desc}</span>
                            </div>
                        `;
                    });

                    html += `
                        </div>
                        <div class="mt-2 pt-1.5 border-t border-slate-100 text-[10px] text-slate-400 text-right">
                            31 Wilayah Terklasifikasi
                        </div>
                    `;

                    div.innerHTML = html;
                    return div;
                };

                legendControl.addTo(map);
            }

            // 7. Muat File GeoJSON
            fetch('/geojson/jember_kecamatan.geojson')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Gagal memuat GeoJSON (HTTP ${response.status})`);
                    }
                    return response.json();
                })
                .then(geojsonData => {
                    geojsonLayer = L.geoJSON(geojsonData, {
                        style: getFeatureStyle,
                        onEachFeature: onEachFeature
                    }).addTo(map);

                    // FitBounds ke wilayah Kabupaten Jember
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [25, 25],
                        animate: true
                    });

                    // Update Badge Status
                    const badge = document.getElementById('layerStatusBadge');
                    const icon = document.getElementById('layerStatusIcon');
                    const text = document.getElementById('layerStatusText');

                    badge.className = 'inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-semibold shadow-2xs transition-all';
                    icon.className = 'fa-solid fa-circle-check text-emerald-600';
                    text.textContent = '31 Wilayah & Data Lengkap';
                })
                .catch(error => {
                    console.error('Error saat memuat layer GeoJSON:', error);
                    const badge = document.getElementById('layerStatusBadge');
                    const icon = document.getElementById('layerStatusIcon');
                    const text = document.getElementById('layerStatusText');

                    badge.className = 'inline-flex items-center gap-2 px-3.5 py-2 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl text-xs font-semibold shadow-2xs';
                    icon.className = 'fa-solid fa-circle-exclamation text-rose-600';
                    text.textContent = 'Gagal memuat layer spasial';
                });

            // 8. Event Handler Switch Mode Tampilan Tematik
            const modeButtons = document.querySelectorAll('.btn-mode');
            const modeBannerText = document.getElementById('modeBannerText');
            const modeBannerIcon = document.getElementById('modeBannerIcon');
            const filterCategoryContainer = document.getElementById('filterCategoryContainer');

            modeButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const newMode = this.getAttribute('data-mode');
                    if (newMode === currentMode) return;

                    currentMode = newMode;
                    currentCategoryFilter = 'all';
                    document.getElementById('selectCategoryFilter').value = 'all';

                    // Update Style Tombol Aktif
                    modeButtons.forEach(b => {
                        b.classList.remove('bg-emerald-600', 'bg-blue-600', 'bg-amber-600', 'text-white', 'border-transparent', 'active-mode');
                        b.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
                    });

                    if (currentMode === 'spk1') {
                        this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        this.classList.add('bg-blue-600', 'text-white', 'border-transparent', 'active-mode');
                        modeBannerText.textContent = 'Peta Tematik SPK 1 (Potensi Demografi): Gradasi warna menunjukkan perankingan potensi pasar dan dinamika pertumbuhan penduduk.';
                        modeBannerIcon.className = 'fa-solid fa-users text-blue-600';
                        filterCategoryContainer.classList.remove('hidden');
                        filterCategoryContainer.classList.add('inline-flex');
                    } else if (currentMode === 'spk2') {
                        this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        this.classList.add('bg-amber-600', 'text-white', 'border-transparent', 'active-mode');
                        modeBannerText.textContent = 'Peta Tematik SPK 2 (Beban Administrasi Wilayah): Gradasi warna menunjukkan prioritas beban birokrasi & alokasi pelayanan publik.';
                        modeBannerIcon.className = 'fa-solid fa-landmark-dome text-amber-600';
                        filterCategoryContainer.classList.remove('hidden');
                        filterCategoryContainer.classList.add('inline-flex');
                    } else {
                        this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        this.classList.add('bg-emerald-600', 'text-white', 'border-transparent', 'active-mode');
                        modeBannerText.textContent = 'Menampilkan batas wilayah administratif 31 kecamatan Kabupaten Jember dalam warna netral.';
                        modeBannerIcon.className = 'fa-solid fa-circle-info text-emerald-600';
                        filterCategoryContainer.classList.remove('inline-flex');
                        filterCategoryContainer.classList.add('hidden');
                    }

                    // Re-style layer GeoJSON
                    if (geojsonLayer) {
                        geojsonLayer.setStyle(getFeatureStyle);

                        // Update Tooltip untuk setiap layer
                        geojsonLayer.eachLayer(function (layer) {
                            if (layer.feature) {
                                const nama = layer.feature.properties.nama;
                                const spk1 = spk1Data[nama] || null;
                                const spk2 = spk2Data[nama] || null;
                                updateLayerTooltip(layer, nama, spk1, spk2);
                            }
                        });
                    }

                    // Tampilkan / Sembunyikan Legenda Tematik
                    updateMapLegend();
                });
            });

            // 9. Event Handler Filter Kategori SPK di Peta
            document.getElementById('selectCategoryFilter').addEventListener('change', function (e) {
                currentCategoryFilter = e.target.value;
                if (geojsonLayer) {
                    geojsonLayer.setStyle(getFeatureStyle);
                }
            });

            // 10. Pencarian Cepat / Quick Jump ke Wilayah Tertentu
            document.getElementById('selectQuickJump').addEventListener('change', function (e) {
                const targetName = e.target.value;
                if (!targetName) return;

                const layer = districtLayers[targetName];
                if (layer) {
                    map.flyToBounds(layer.getBounds(), {
                        maxZoom: 13,
                        duration: 1.4
                    });
                    setTimeout(() => {
                        layer.openPopup();
                    }, 1400);
                }
            });

            // 11. Toggle Label Nama Centroid di Peta
            document.getElementById('toggleCentroidLabels').addEventListener('change', function (e) {
                if (e.target.checked) {
                    centroidLabelLayer.addTo(map);
                } else {
                    map.removeLayer(centroidLabelLayer);
                }
            });

            // 12. Tombol Layar Penuh (Fullscreen)
            const btnFullscreen = document.getElementById('btnFullscreen');
            const mapCard = document.getElementById('mapCard');
            const fullscreenIcon = document.getElementById('fullscreenIcon');
            const fullscreenText = document.getElementById('fullscreenText');

            btnFullscreen.addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    if (mapCard.requestFullscreen) {
                        mapCard.requestFullscreen();
                    } else if (mapCard.webkitRequestFullscreen) {
                        mapCard.webkitRequestFullscreen();
                    }
                    fullscreenIcon.className = 'fa-solid fa-compress text-slate-600';
                    fullscreenText.textContent = 'Keluar Penuh';
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                    fullscreenIcon.className = 'fa-solid fa-expand text-slate-600';
                    fullscreenText.textContent = 'Layar Penuh';
                }
            });

            document.addEventListener('fullscreenchange', function () {
                setTimeout(() => { map.invalidateSize(); }, 250);
            });

            // 13. Tombol Fokus Seluruh Wilayah (Fit Bounds)
            document.getElementById('btnFitBounds').addEventListener('click', function () {
                if (geojsonLayer) {
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [25, 25],
                        animate: true
                    });
                }
            });

            // 14. Tombol Reset View ke Pusat Jember
            document.getElementById('btnResetView').addEventListener('click', function () {
                map.setView([defaultLat, defaultLng], defaultZoom, { animate: true });
                centerMarker.openPopup();
            });

            // 15. Penunjuk Koordinat Mouse Real-time
            const coordsDisplay = document.getElementById('mouseCoords');
            map.on('mousemove', function (e) {
                coordsDisplay.textContent = `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
            });
        });
    </script>
@endpush
