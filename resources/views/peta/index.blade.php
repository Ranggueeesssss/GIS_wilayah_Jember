@extends('layouts.app')

@section('title', 'Peta Spasial Web GIS - Kabupaten Jember')

@push('styles')
    <!-- Leaflet CSS 1.9.4 -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin="" />

    <style>
        #map {
            height: 600px;
            width: 100%;
            z-index: 10;
        }

        /* Styling popup leaflet agar serasi dengan UI */
        .leaflet-popup-content-wrapper {
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 4px;
            border: 1px solid #e2e8f0;
        }
        .leaflet-popup-content {
            margin: 12px 14px;
            line-height: 1.5;
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
                Visualisasi geografis wilayah Kabupaten Jember berbasis library Leaflet.js dan OpenStreetMap.
            </p>
        </div>

        <!-- Indikator Status Library -->
        <div class="inline-flex items-center gap-2.5 px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm text-xs font-medium text-slate-700">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Leaflet.js v1.9.4 Aktif</span>
            <span class="text-slate-300">•</span>
            <span class="text-slate-500">Pusat: -8.1721, 113.7001 (Zoom: 10)</span>
        </div>
    </div>

    <!-- Map Container Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Map Toolbar -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex items-center space-x-3 text-xs text-slate-600">
                <span class="font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-emerald-600"></i> Basemap:
                </span>
                <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg font-semibold text-slate-700 shadow-2xs">
                    OpenStreetMap Standard
                </span>
                <span class="text-slate-300">|</span>
                <span>Cakupan: <strong>{{ $totalKecamatan }} Kecamatan</strong></span>
            </div>

            <!-- Reset View Button -->
            <button id="btnResetView" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg shadow-2xs transition-all">
                <i class="fa-solid fa-crosshairs text-emerald-600"></i>
                <span>Pusatkan ke Jember</span>
            </button>
        </div>

        <!-- Canvas Peta Leaflet -->
        <div id="map" class="relative"></div>

        <!-- Footer Map Status -->
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/70 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-500"></i>
                <span>Integrasi Leaflet.js berhasil. Siap untuk penambahan layer GeoJSON batas wilayah di langkah berikutnya.</span>
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
            // Koordinat pusat Kabupaten Jember
            const defaultLat = {{ $centerCoords['lat'] }};
            const defaultLng = {{ $centerCoords['lng'] }};
            const defaultZoom = {{ $centerCoords['zoom'] }};

            // 1. Inisialisasi Peta
            const map = L.map('map', {
                center: [defaultLat, defaultLng],
                zoom: defaultZoom,
                zoomControl: true,
                scrollWheelZoom: true
            });

            // 2. Tambahkan Tile Layer (OpenStreetMap)
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 3. Tambahkan Kontrol Skala
            L.control.scale({
                imperial: false,
                metric: true,
                position: 'bottomleft'
            }).addTo(map);

            // 4. Marker Penanda Pusat Kabupaten Jember (Verifikasi Integrasi)
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
                        <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-check"></i> Leaflet.js terintegrasi sempurna
                        </div>
                    </div>
                `);

            // 5. Tombol Reset View
            document.getElementById('btnResetView').addEventListener('click', function () {
                map.setView([defaultLat, defaultLng], defaultZoom, { animate: true });
                centerMarker.openPopup();
            });

            // 6. Penunjuk Koordinat Mouse Real-time
            const coordsDisplay = document.getElementById('mouseCoords');
            map.on('mousemove', function (e) {
                coordsDisplay.textContent = `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
            });
        });
    </script>
@endpush
