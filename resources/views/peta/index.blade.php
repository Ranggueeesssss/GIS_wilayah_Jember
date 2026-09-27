@extends('layouts.app')

@section('title', 'Peta Spasial Web GIS & Choropleth SPK - Kabupaten Jember')

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

        /* Styling popup leaflet agar modern & selaras dengan UI */
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
            padding: 2px;
            border: 1px solid #e2e8f0;
        }
        .leaflet-popup-content {
            margin: 12px 14px;
            line-height: 1.5;
        }
        .leaflet-popup-tip {
            border: 1px solid #e2e8f0;
        }

        /* Tooltip nama kecamatan yang melayang saat kursor hover */
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

        /* Kontainer Legenda Peta (Floating Control) */
        .choropleth-legend {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
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
                <span class="text-slate-800 font-semibold">Peta Spasial & Choropleth</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-base shrink-0 shadow-sm shadow-emerald-500/30">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                Visualisasi Tematik & Choropleth SPK
            </h1>
            <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                Pemetaan tematik 31 kecamatan di Kabupaten Jember dengan gradasi warna berbasis hasil perangkingan metode SAW (Simple Additive Weighting).
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
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <!-- Toolbar Kontrol Visualisasi Tematik & Mode Peta -->
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/70 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Pilihan Mode Visualisasi Peta (Tabs) -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-palette text-emerald-600"></i> Mode Tampilan:
                </span>

                <!-- Mode 1: Standar Wilayah -->
                <button type="button" data-mode="standar" 
                        class="btn-mode px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all border flex items-center gap-1.5
                               bg-white text-slate-700 border-slate-200 hover:bg-slate-50 shadow-2xs active-mode">
                    <i class="fa-solid fa-layer-group text-slate-500"></i>
                    <span>Batas Wilayah Netral</span>
                </button>

                <!-- Mode 2: SPK 1 Choropleth -->
                <button type="button" data-mode="spk1" 
                        class="btn-mode px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all border flex items-center gap-1.5
                               bg-white text-slate-700 border-slate-200 hover:bg-blue-50 hover:text-blue-700 shadow-2xs">
                    <i class="fa-solid fa-users text-blue-600"></i>
                    <span>Choropleth SPK 1 (Potensi Demografi)</span>
                </button>

                <!-- Mode 3: SPK 2 Choropleth -->
                <button type="button" data-mode="spk2" 
                        class="btn-mode px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all border flex items-center gap-1.5
                               bg-white text-slate-700 border-slate-200 hover:bg-amber-50 hover:text-amber-700 shadow-2xs">
                    <i class="fa-solid fa-landmark-dome text-amber-600"></i>
                    <span>Choropleth SPK 2 (Beban Administrasi)</span>
                </button>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="flex items-center gap-2 self-end lg:self-auto">
                <!-- Fit Bounds GeoJSON Button -->
                <button id="btnFitBounds" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all"
                        title="Sesuaikan tampilan penuh ke batas 31 kecamatan">
                    <i class="fa-solid fa-expand text-emerald-600"></i>
                    <span>Fokus Wilayah</span>
                </button>

                <!-- Reset Center Button -->
                <button id="btnResetView" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all"
                        title="Pusatkan kembali ke Alun-Alun Jember">
                    <i class="fa-solid fa-crosshairs text-emerald-600"></i>
                    <span>Pusat Jember</span>
                </button>
            </div>
        </div>

        <!-- Info Banner Mode Aktif -->
        <div id="modeBanner" class="px-5 py-2.5 bg-slate-100/70 border-b border-slate-100 text-xs text-slate-600 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i id="modeBannerIcon" class="fa-solid fa-circle-info text-emerald-600"></i>
                <span id="modeBannerText">Menampilkan batas wilayah administratif 31 kecamatan Kabupaten Jember dalam warna netral.</span>
            </div>
            <div class="hidden sm:block text-slate-400 font-mono text-[11px]">
                Metode SAW • 31 Kecamatan
            </div>
        </div>

        <!-- Canvas Peta Leaflet -->
        <div id="map" class="relative"></div>

        <!-- Footer Map Status -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-mouse-pointer text-emerald-600"></i>
                <span>Arahkan kursor untuk melihat tooltip ranking/skor, atau klik poligon untuk menampilkan popup detail lengkap.</span>
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

            // Mode visualisasi saat ini: 'standar' | 'spk1' | 'spk2'
            let currentMode = 'standar';
            let geojsonLayer = null;
            let legendControl = null;

            // 1. Inisialisasi Peta Leaflet
            const map = L.map('map', {
                center: [defaultLat, defaultLng],
                zoom: defaultZoom,
                zoomControl: true,
                scrollWheelZoom: true
            });

            // 2. Tile Layer OpenStreetMap
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 3. Kontrol Skala Metrik
            L.control.scale({
                imperial: false,
                metric: true,
                position: 'bottomleft'
            }).addTo(map);

            // 4. Marker Pusat Kabupaten Jember
            const centerMarker = L.marker([defaultLat, defaultLng])
                .addTo(map)
                .bindPopup(`
                    <div class="text-slate-800">
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
                    case 'Sangat Tinggi': return '#e11d48'; // Rose-600 (Paling Prioritas)
                    case 'Tinggi':        return '#f97316'; // Orange-500
                    case 'Sedang':        return '#eab308'; // Yellow-500
                    case 'Rendah':        return '#0284c7'; // Sky-600
                    case 'Sangat Rendah': return '#64748b'; // Slate-500
                    default:              return '#10b981'; // Default Emerald
                }
            }

            // Fungsi Penentuan Gaya Poligon Dinamis Berdasarkan Mode
            function getFeatureStyle(feature) {
                const nama = feature.properties.nama;

                if (currentMode === 'spk1') {
                    const spk = spk1Data[nama];
                    const color = spk ? getCategoryColor(spk.kategori) : '#94a3b8';
                    return {
                        color: color,
                        weight: 2,
                        opacity: 0.95,
                        fillColor: color,
                        fillOpacity: 0.55
                    };
                } else if (currentMode === 'spk2') {
                    const spk = spk2Data[nama];
                    const color = spk ? getCategoryColor(spk.kategori) : '#94a3b8';
                    return {
                        color: color,
                        weight: 2,
                        opacity: 0.95,
                        fillColor: color,
                        fillOpacity: 0.55
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

            // Interaksi untuk setiap Feature Poligon
            function onEachFeature(feature, layer) {
                const props = feature.properties;
                const namaKecamatan = props.nama || 'Kecamatan';
                const kodeBPS = props.kode || '-';
                const kecamatanId = kecamatanIdMap[namaKecamatan] || null;

                // Bind Tooltip & Popup secara dinamis
                updateLayerPopupAndTooltip(layer, feature);

                // Event Listener Hover & Mouseout
                layer.on({
                    mouseover: function (e) {
                        const targetLayer = e.target;
                        targetLayer.setStyle({
                            weight: 3.5,
                            opacity: 1,
                            fillOpacity: 0.75
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

            // Fungsi Update Konten Popup & Tooltip Sesuai Mode Aktif
            function updateLayerPopupAndTooltip(layer, feature) {
                const nama = feature.properties.nama;
                const kodeBPS = feature.properties.kode || '-';
                const kecamatanId = kecamatanIdMap[nama] || null;

                const spk1 = spk1Data[nama] || null;
                const spk2 = spk2Data[nama] || null;

                // Tooltip text
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

                // Konten Popup
                let popupHtml = '';

                if (currentMode === 'spk1' && spk1) {
                    const badgeColor = getCategoryColor(spk1.kategori);
                    popupHtml = `
                        <div class="text-slate-800 min-w-[240px]">
                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2 mb-2">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold text-[10px] rounded-md border border-blue-100">
                                    SPK 1: Potensi Demografi
                                </span>
                                <span class="font-bold text-xs" style="color: ${badgeColor};">
                                    Peringkat #${spk1.ranking}
                                </span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mb-1 flex items-center justify-between">
                                <span>${nama}</span>
                                <span class="text-sm font-black text-blue-600">${spk1.skor_persen}%</span>
                            </h3>
                            <div class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold text-white mb-2" style="background-color: ${badgeColor};">
                                Kategori: ${spk1.kategori}
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 text-[11px] bg-slate-50 rounded-lg p-2 border border-slate-100 my-2">
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase">Penduduk</span>
                                    <strong class="text-slate-700">${Number(spk1.rincian_bobot.jumlah_penduduk.nilai_asli).toLocaleString('id-ID')}</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase">Laju Tumbuh</span>
                                    <strong class="${spk1.rincian_bobot.laju_pertumbuhan.nilai_asli >= 0 ? 'text-emerald-600' : 'text-rose-600'}">
                                        ${spk1.rincian_bobot.laju_pertumbuhan.nilai_asli}%
                                    </strong>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <a href="/spk/spk1" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                    Lihat Matriks SPK 1 &rarr;
                                </a>
                                ${kecamatanId ? `<a href="/kecamatan/${kecamatanId}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Detail &rarr;</a>` : ''}
                            </div>
                        </div>
                    `;
                } else if (currentMode === 'spk2' && spk2) {
                    const badgeColor = getCategoryColor(spk2.kategori);
                    popupHtml = `
                        <div class="text-slate-800 min-w-[240px]">
                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2 mb-2">
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold text-[10px] rounded-md border border-amber-100">
                                    SPK 2: Beban Administrasi
                                </span>
                                <span class="font-bold text-xs" style="color: ${badgeColor};">
                                    Peringkat #${spk2.ranking}
                                </span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mb-1 flex items-center justify-between">
                                <span>${nama}</span>
                                <span class="text-sm font-black text-amber-600">${spk2.skor_persen}%</span>
                            </h3>
                            <div class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold text-white mb-2" style="background-color: ${badgeColor};">
                                Kategori: ${spk2.kategori}
                            </div>
                            <div class="grid grid-cols-3 gap-1 text-[11px] bg-slate-50 rounded-lg p-2 border border-slate-100 my-2 text-center">
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase">Desa</span>
                                    <strong class="text-slate-700">${spk2.rincian_bobot.jumlah_desa.nilai_asli}</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase">Penduduk</span>
                                    <strong class="text-slate-700">${Math.round(spk2.rincian_bobot.jumlah_penduduk.nilai_asli / 1000)}k</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px] uppercase">Laju</span>
                                    <strong class="${spk2.rincian_bobot.laju_pertumbuhan.nilai_asli >= 0 ? 'text-emerald-600' : 'text-rose-600'}">
                                        ${spk2.rincian_bobot.laju_pertumbuhan.nilai_asli}%
                                    </strong>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <a href="/spk/spk2" class="text-xs font-semibold text-amber-600 hover:text-amber-800">
                                    Lihat Matriks SPK 2 &rarr;
                                </a>
                                ${kecamatanId ? `<a href="/kecamatan/${kecamatanId}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Detail &rarr;</a>` : ''}
                            </div>
                        </div>
                    `;
                } else {
                    // Mode Standar
                    popupHtml = `
                        <div class="text-slate-800 min-w-[210px]">
                            <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2 mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kecamatan</span>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold rounded-md border border-emerald-100">
                                    Kode: ${kodeBPS}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-emerald-600"></i>
                                ${nama}
                            </h3>
                            <p class="text-xs text-slate-500">
                                Bagian dari 31 Wilayah Administratif Kabupaten Jember
                            </p>
                            ${kecamatanId ? `
                                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <a href="/kecamatan/${kecamatanId}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-all">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span>Lihat Data Detail</span>
                                    </a>
                                    <span class="text-[10px] text-slate-400">ID: #${kecamatanId}</span>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }

                layer.bindPopup(popupHtml);
            }

            // 6. Kontrol Legenda Peta (Choropleth Legend)
            function updateMapLegend() {
                if (legendControl) {
                    map.removeControl(legendControl);
                    legendControl = null;
                }

                if (currentMode === 'standar') {
                    return; // Pada mode standar tidak memerlukan legenda choropleth
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
                    text.textContent = '31 Wilayah & Data SPK Siap';
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

            // 8. Event Handler Perpindahan Mode Tampilan Tematik (Choropleth Switcher)
            const modeButtons = document.querySelectorAll('.btn-mode');
            const modeBannerText = document.getElementById('modeBannerText');
            const modeBannerIcon = document.getElementById('modeBannerIcon');

            modeButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const newMode = this.getAttribute('data-mode');
                    if (newMode === currentMode) return;

                    currentMode = newMode;

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
                    } else if (currentMode === 'spk2') {
                        this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        this.classList.add('bg-amber-600', 'text-white', 'border-transparent', 'active-mode');
                        modeBannerText.textContent = 'Peta Tematik SPK 2 (Beban Administrasi Wilayah): Gradasi warna menunjukkan prioritas beban birokrasi & kebutuhan pelayanan publik.';
                        modeBannerIcon.className = 'fa-solid fa-landmark-dome text-amber-600';
                    } else {
                        this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        this.classList.add('bg-emerald-600', 'text-white', 'border-transparent', 'active-mode');
                        modeBannerText.textContent = 'Menampilkan batas wilayah administratif 31 kecamatan Kabupaten Jember dalam warna netral.';
                        modeBannerIcon.className = 'fa-solid fa-circle-info text-emerald-600';
                    }

                    // Re-style layer GeoJSON dengan palet warna mode yang baru
                    if (geojsonLayer) {
                        geojsonLayer.setStyle(getFeatureStyle);

                        // Update Tooltip dan Popup untuk setiap layer
                        geojsonLayer.eachLayer(function (layer) {
                            if (layer.feature) {
                                updateLayerPopupAndTooltip(layer, layer.feature);
                            }
                        });
                    }

                    // Tampilkan / Sembunyikan Legenda Tematik
                    updateMapLegend();
                });
            });

            // 9. Tombol Fokus Seluruh Wilayah (Fit Bounds)
            document.getElementById('btnFitBounds').addEventListener('click', function () {
                if (geojsonLayer) {
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [25, 25],
                        animate: true
                    });
                }
            });

            // 10. Tombol Reset View ke Pusat Jember
            document.getElementById('btnResetView').addEventListener('click', function () {
                map.setView([defaultLat, defaultLng], defaultZoom, { animate: true });
                centerMarker.openPopup();
            });

            // 11. Penunjuk Koordinat Mouse Real-time
            const coordsDisplay = document.getElementById('mouseCoords');
            map.on('mousemove', function (e) {
                coordsDisplay.textContent = `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
            });
        });
    </script>
@endpush
