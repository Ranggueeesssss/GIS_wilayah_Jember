@extends('layouts.app')

@section('title', 'Peta Spasial Web GIS - Kabupaten Jember')

@push('styles')
    <!-- Leaflet CSS 1.9.4 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />

    <style>
        #map {
            height: 620px;
            width: 100%;
            z-index: 10;
        }

        /* Styling popup leaflet agar modern & selaras dengan UI */
        .leaflet-popup-content-wrapper {
            border-radius: 14px;
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
            background-color: rgba(15, 23, 42, 0.88);
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .kecamatan-tooltip::before {
            border-top-color: rgba(15, 23, 42, 0.88);
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
                <span class="text-slate-800 font-semibold">Peta Spasial</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white text-base shrink-0 shadow-sm shadow-emerald-500/30">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                Peta Spasial Web GIS Kabupaten Jember
            </h1>
            <p class="text-sm text-slate-500 mt-1.5">
                Visualisasi batas wilayah administratif 31 kecamatan di Kabupaten Jember menggunakan layer GeoJSON.
            </p>
        </div>

        <!-- Indikator Status GeoJSON Layer -->
        <div class="flex items-center gap-2">
            <div id="layerStatusBadge" class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold shadow-2xs transition-all">
                <i id="layerStatusIcon" class="fa-solid fa-spinner fa-spin text-amber-600"></i>
                <span id="layerStatusText">Memuat Batas Wilayah GeoJSON...</span>
            </div>
        </div>
    </div>

    <!-- Map Container Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Map Toolbar -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                <span class="font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i> Basemap:
                </span>
                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg font-semibold text-slate-700 shadow-2xs">
                    OpenStreetMap Standard
                </span>

                <span class="text-slate-300">|</span>

                <!-- Switch Toggle Layer GeoJSON -->
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="toggleGeojsonLayer" checked 
                           class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <span class="font-semibold text-slate-700">Tampilkan Batas Wilayah (GeoJSON)</span>
                </label>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="flex items-center gap-2">
                <!-- Fit Bounds GeoJSON Button -->
                <button id="btnFitBounds" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all"
                        title="Sesuaikan tampilan penuh ke batas 31 kecamatan">
                    <i class="fa-solid fa-expand text-emerald-600"></i>
                    <span>Fokus Seluruh Wilayah</span>
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

        <!-- Canvas Peta Leaflet -->
        <div id="map" class="relative"></div>

        <!-- Footer Map Status -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-draw-polygon text-emerald-600"></i>
                <span>Arahkan kursor atau klik pada salah satu kecamatan untuk melihat nama wilayah dan batas polygon.</span>
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
            // Data peta dari controller
            const defaultLat = {{ $centerCoords['lat'] }};
            const defaultLng = {{ $centerCoords['lng'] }};
            const defaultZoom = {{ $centerCoords['zoom'] }};
            const kecamatanIdMap = @json($kecamatanMap);

            // 1. Inisialisasi Peta Leaflet
            const map = L.map('map', {
                center: [defaultLat, defaultLng],
                zoom: defaultZoom,
                zoomControl: true,
                scrollWheelZoom: true
            });

            // 2. Tambahkan Basemap OpenStreetMap
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 3. Tambahkan Kontrol Skala Metrik
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

            // 5. Variabel Layer GeoJSON
            let geojsonLayer = null;

            // Gaya Poligon Default (Hijau Emerald Elegan)
            const defaultPolygonStyle = {
                color: '#059669',       // Warna garis batas (border)
                weight: 1.5,            // Ketebalan garis
                opacity: 0.9,           // Opasitas garis
                fillColor: '#10b981',   // Warna isi poligon
                fillOpacity: 0.22,      // Transparansi isi poligon
                dashArray: '3, 4'       // Garis putus-putus halus
            };

            // Gaya Poligon Saat Di-hover (Sorot)
            const highlightPolygonStyle = {
                color: '#047857',
                weight: 3,
                opacity: 1,
                fillColor: '#34d399',
                fillOpacity: 0.45,
                dashArray: ''
            };

            // Interaksi untuk setiap Feature Poligon Kecamatan
            function onEachFeature(feature, layer) {
                const props = feature.properties;
                const namaKecamatan = props.nama || 'Kecamatan';
                const kodeBPS = props.kode || '-';
                const kecamatanId = kecamatanIdMap[namaKecamatan] || null;

                // Tooltip Nama Kecamatan saat kursor diarahkan
                layer.bindTooltip(namaKecamatan, {
                    permanent: false,
                    direction: 'center',
                    className: 'kecamatan-tooltip'
                });

                // Popup Detail saat poligon diklik
                let detailButtonHtml = '';
                if (kecamatanId) {
                    detailButtonHtml = `
                        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                            <a href="/kecamatan/${kecamatanId}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition-all">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                <span>Lihat Data Detail</span>
                            </a>
                            <span class="text-[10px] text-slate-400">ID: #${kecamatanId}</span>
                        </div>
                    `;
                }

                layer.bindPopup(`
                    <div class="text-slate-800 min-w-[210px]">
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2 mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kecamatan</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold rounded-md border border-emerald-100">
                                Kode: ${kodeBPS}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-emerald-600"></i>
                            ${namaKecamatan}
                        </h3>
                        <p class="text-xs text-slate-500">
                            Bagian dari 31 Wilayah Administratif Kabupaten Jember
                        </p>
                        ${detailButtonHtml}
                    </div>
                `);

                // Event Listener Hover dan Mouseout
                layer.on({
                    mouseover: function (e) {
                        const targetLayer = e.target;
                        targetLayer.setStyle(highlightPolygonStyle);
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

            // 6. Ambil File GeoJSON & Integrasikan ke Peta
            fetch('/geojson/jember_kecamatan.geojson')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Gagal memuat GeoJSON (HTTP ${response.status})`);
                    }
                    return response.json();
                })
                .then(geojsonData => {
                    geojsonLayer = L.geoJSON(geojsonData, {
                        style: defaultPolygonStyle,
                        onEachFeature: onEachFeature
                    }).addTo(map);

                    // Pasang batas pandang otomatis agar 31 kecamatan muat sempurna di layar
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [25, 25],
                        animate: true
                    });

                    // Update Badge Status di Header
                    const badge = document.getElementById('layerStatusBadge');
                    const icon = document.getElementById('layerStatusIcon');
                    const text = document.getElementById('layerStatusText');

                    badge.className = 'inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-semibold shadow-2xs transition-all';
                    icon.className = 'fa-solid fa-circle-check text-emerald-600';
                    text.textContent = '31 Batas Kecamatan Dimuat';
                })
                .catch(error => {
                    console.error('Error saat memuat layer GeoJSON:', error);
                    const badge = document.getElementById('layerStatusBadge');
                    const icon = document.getElementById('layerStatusIcon');
                    const text = document.getElementById('layerStatusText');

                    badge.className = 'inline-flex items-center gap-2 px-3.5 py-2 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl text-xs font-semibold shadow-2xs';
                    icon.className = 'fa-solid fa-circle-exclamation text-rose-600';
                    text.textContent = 'Gagal memuat layer GeoJSON';
                });

            // 7. Toggle Switch Layer Batas Wilayah GeoJSON
            document.getElementById('toggleGeojsonLayer').addEventListener('change', function (e) {
                if (!geojsonLayer) return;
                if (e.target.checked) {
                    map.addLayer(geojsonLayer);
                } else {
                    map.removeLayer(geojsonLayer);
                }
            });

            // 8. Tombol Fokus Seluruh Wilayah (Fit Bounds)
            document.getElementById('btnFitBounds').addEventListener('click', function () {
                if (geojsonLayer) {
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [25, 25],
                        animate: true
                    });
                }
            });

            // 9. Tombol Reset View ke Pusat Jember
            document.getElementById('btnResetView').addEventListener('click', function () {
                map.setView([defaultLat, defaultLng], defaultZoom, { animate: true });
                centerMarker.openPopup();
            });

            // 10. Penunjuk Koordinat Mouse Real-time
            const coordsDisplay = document.getElementById('mouseCoords');
            map.on('mousemove', function (e) {
                coordsDisplay.textContent = `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
            });
        });
    </script>
@endpush
