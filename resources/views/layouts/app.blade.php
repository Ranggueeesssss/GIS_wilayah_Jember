<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Web GIS Kabupaten Jember')</title>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (via CDN with custom config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        },
                        slateDark: '#0f172a',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col text-slate-800 antialiased bg-slate-50/60">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo & Title -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('kecamatan.index') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-map-location-dot text-lg"></i>
                        </div>
                        <div>
                            <span class="text-lg font-bold bg-gradient-to-r from-slate-900 to-slate-700 bg-clip-text text-transparent">
                                GIS Jember
                            </span>
                            <span class="hidden sm:inline-block ml-1.5 px-2 py-0.5 text-xs font-semibold bg-emerald-100/70 text-emerald-700 rounded-full border border-emerald-200/60">
                                BPS 2024
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center space-x-1 sm:space-x-2">
                    <a href="{{ route('kecamatan.index') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-all {{ request()->routeIs('kecamatan.*') ? 'bg-emerald-50 text-emerald-700 shadow-sm border border-emerald-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fa-solid fa-table-list mr-1.5"></i>
                        <span>Data Wilayah</span>
                    </a>

                    {{-- Dropdown SPK --}}
                    <div class="relative group hidden md:block">
                        <button class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-all flex items-center gap-1.5
                                       {{ request()->routeIs('spk.*') ? 'bg-blue-50 text-blue-700 shadow-sm border border-blue-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-brain text-sm"></i>
                            <span>Analisis SPK</span>
                            <i class="fa-solid fa-chevron-down text-[10px] opacity-60 group-hover:rotate-180 transition-transform duration-200"></i>
                        </button>
                        {{-- Dropdown Menu --}}
                        <div class="absolute top-full left-0 mt-1 w-72 bg-white rounded-xl border border-slate-200 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 p-2">
                            <a href="{{ route('spk.spk1') }}"
                               class="flex items-start gap-3 p-3 rounded-lg hover:bg-blue-50 transition-colors {{ request()->routeIs('spk.spk1') ? 'bg-blue-50' : '' }}">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-users text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">SPK 1 — Potensi Demografi</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Ranking potensi pasar & pertumbuhan penduduk</p>
                                </div>
                            </a>
                            <a href="{{ route('spk.spk2') }}"
                               class="flex items-start gap-3 p-3 rounded-lg hover:bg-amber-50 transition-colors {{ request()->routeIs('spk.spk2') ? 'bg-amber-50' : '' }}">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-landmark-dome text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">SPK 2 — Beban Administrasi</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Prioritas alokasi sumber daya & layanan publik</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Placeholder Peta (aktif di Tahap 4) --}}
                    <span class="px-3.5 py-2 text-sm font-medium text-slate-400 cursor-not-allowed hidden md:inline-flex items-center" title="Akan aktif di Tahap 4">
                        <i class="fa-solid fa-map mr-1.5 opacity-60"></i>
                        <span>Peta Spasial</span>
                    </span>

                    <a href="{{ route('kecamatan.create') }}" 
                       class="ml-1 inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm shadow-emerald-600/30 transition-all hover:shadow-md hover:-translate-y-0.5">
                        <i class="fa-solid fa-plus mr-1.5"></i>
                        <span>Tambah Data</span>

                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">

        <!-- Flash Message Notification -->
        @if (session('success'))
            <div id="flash-message" class="mb-6 flex items-center p-4 bg-emerald-50 border border-emerald-200 rounded-xl shadow-sm text-emerald-800 transition-all duration-300">
                <div class="w-8 h-8 rounded-lg bg-emerald-600/10 flex items-center justify-center mr-3 text-emerald-600 shrink-0">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div class="text-sm font-medium flex-1">
                    {{ session('success') }}
                </div>
                <button type="button" onclick="document.getElementById('flash-message').remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl shadow-sm text-rose-800">
                <div class="flex items-center mb-2">
                    <i class="fa-solid fa-triangle-exclamation mr-2 text-rose-600"></i>
                    <span class="text-sm font-semibold">Terdapat kesalahan pengisian data:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 space-y-2 sm:space-y-0">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-slate-700">Web GIS Kabupaten Jember</span>
                <span>•</span>
                <span>Sumber Data: BPS Kabupaten Jember 2024</span>
            </div>
            <div>
                <span>Framework Laravel & Leaflet.js</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
