<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Cari Lowongan — Telkom University</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        #navbar { transition: background-color 0.35s ease, box-shadow 0.35s ease; }
        #navbar.scrolled { background: #8b1515; box-shadow: 0 2px 16px rgba(0,0,0,0.15); }
        #navbar.scrolled .nav-link { color: #fff; }
        #navbar.scrolled .btn-masuk { color: #fff; }
        #navbar.scrolled .btn-masuk:hover { background: rgba(255,255,255,0.15); }
        #navbar.scrolled .btn-daftar { background: #fff; color: #8b1515; }
        #navbar.scrolled .btn-daftar:hover { background: #f3f4f6; }
        #navbar.scrolled .logo-normal { opacity: 0; }
        #navbar.scrolled .logo-scrolled { opacity: 1; }
        #navbar .mobile-menu { background: #fff; }
        html { color-scheme: light; }
        #navbar { background: #fff; box-shadow: 0 2px 16px rgba(0,0,0,0.05); }
        #navbar .nav-link { color: #111; }
        #navbar .nav-link:hover { color: #8b1515; }
        #navbar .btn-masuk { color: #111; }
        #navbar .btn-masuk:hover { background: rgba(0,0,0,0.05); }
        #navbar .btn-daftar { background: #8b1515; color: #fff; }
        #navbar .btn-daftar:hover { background: #991b1b; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    @include('partials.loading-screen')
    
    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-transparent" x-data="{ mobileOpen: false }">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 h-[68px] flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                <div class="relative w-[100px] sm:w-[120px] h-14 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('images/logo1.png') }}" alt="Telkom University Logo" class="logo-normal w-full h-8 object-contain transition-opacity duration-300 opacity-100">
                    <img src="{{ asset('images/logo2.png') }}" alt="Telkom University Logo" class="logo-scrolled w-full h-8 object-contain transition-opacity duration-300 opacity-0 absolute">
                </div>
            </a>
            <!-- Desktopnav -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ url('/') }}" class="nav-link text-sm font-medium text-gray-900 no-underline transition-colors duration-300 hover:opacity-80">Beranda</a>
            </div>
            <!-- desktoptombolauth -->
            <div class="hidden md:flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-masuk text-sm font-medium text-gray-900 no-underline px-4 py-2 rounded-md transition-all duration-300 hover:bg-black/5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk text-sm font-medium text-gray-900 no-underline px-4 py-2 rounded-md transition-all duration-300 hover:bg-black/5">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-daftar text-sm font-semibold text-white no-underline bg-[#8b1515] px-5 py-2 rounded-md transition-all duration-300 hover:bg-[#991b1b]">Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
            <!-- mobiletombolauth -->
            <div class="flex md:flex items-center gap-2 md:hidden">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-masuk text-xs font-medium text-gray-900 no-underline px-3 py-1.5 rounded-md transition-all duration-300 hover:bg-black/5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk text-xs font-medium text-gray-900 no-underline px-3 py-1.5 rounded-md transition-all duration-300 hover:bg-black/5">Masuk</a>
                    @endauth
                @endif
                <button @click="mobileOpen = !mobileOpen" type="button" class="btn-masuk w-9 h-9 flex items-center justify-center rounded-md transition-all duration-300 hover:bg-black/5 focus:outline-none">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <!-- mobiledropdown -->
        <div x-show="mobileOpen" style="display:none;" class="md:hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="max-w-[1200px] mx-auto px-5 py-4 flex flex-col gap-1">
                <a href="{{ url('/') }}" class="text-sm font-medium text-gray-800 no-underline px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Beranda</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-[100px] pb-16 px-5 sm:px-8">
        <div class="max-w-5xl mx-auto space-y-8" x-data="lowonganApp()">
       
            {{-- Filter Chips Bar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    {{-- Filters on Left --}}
                    <div class="flex items-center gap-3 flex-wrap flex-1">

                    {{-- Kategori Chip --}}
                    <div class="relative" @click.outside="kategoriOpen = false">
                        <button type="button" @click="kategoriOpen = !kategoriOpen"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                                :class="kategoriFilter !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                            Kategori
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="kategoriOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="kategoriOpen" x-transition class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                            <div class="p-3 space-y-1">
                                <button type="button" @click="kategoriFilter = kategoriFilter === 'Dosen' ? '' : 'Dosen'; if(kategoriFilter !== 'Dosen') prodiFilter = []; kategoriOpen = false"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left" :class="kategoriFilter === 'Dosen' ? 'bg-gray-50' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="kategoriFilter === 'Dosen' ? 'border-[#8b1515] bg-[#8b1515]' : 'border-gray-300'">
                                        <svg x-show="kategoriFilter === 'Dosen'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">Dosen</span>
                                </button>
                                <button type="button" @click="kategoriFilter = kategoriFilter === 'Tenaga Kependidikan' ? '' : 'Tenaga Kependidikan'; if(kategoriFilter !== 'Dosen') prodiFilter = []; kategoriOpen = false"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left" :class="kategoriFilter === 'Tenaga Kependidikan' ? 'bg-gray-50' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="kategoriFilter === 'Tenaga Kependidikan' ? 'border-blue-500 bg-blue-500' : 'border-gray-300'">
                                        <svg x-show="kategoriFilter === 'Tenaga Kependidikan'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">Tenaga Kependidikan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Prodi Chip — hanya saat filter Dosen aktif --}}
                    <div class="relative" x-show="kategoriFilter === 'Dosen'" x-transition @click.outside="prodiOpen = false">
                        <button type="button" @click="prodiOpen = !prodiOpen"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                                :class="prodiFilter.length > 0 ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                            Prodi
                            <span x-show="prodiFilter.length > 0" x-text="prodiFilter.length" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center"></span>
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="prodiOpen" x-transition
                             class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p>
                            </div>
                            <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                                @foreach($prodis as $prodi)
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox" value="{{ $prodi->nama }}" x-model="prodiFilter"
                                           class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                    <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Jenjang Chip --}}
                    <div class="relative" @click.outside="jenjangOpen = false">
                        <button type="button" @click="jenjangOpen = !jenjangOpen"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                                :class="jenjangFilter.length > 0 ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                            Jenjang
                            <span x-show="jenjangFilter.length > 0" x-text="jenjangFilter.length" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center"></span>
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="jenjangOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="jenjangOpen" x-transition
                             class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Jenjang</p>
                            </div>
                            <div class="p-3 space-y-1">
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" value="S1" x-model="jenjangFilter" class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                    <span class="text-sm font-medium text-gray-700">S1</span>
                                </label>
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" value="S2" x-model="jenjangFilter" class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                    <span class="text-sm font-medium text-gray-700">S2</span>
                                </label>
                                <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" value="S3" x-model="jenjangFilter" class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                    <span class="text-sm font-medium text-gray-700">S3</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Active filter tags --}}
                    <template x-for="p in prodiFilter" :key="'p-'+p">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                            <span x-text="p"></span>
                            <button type="button" @click="prodiFilter = prodiFilter.filter(v => v !== p)" class="ml-0.5 hover:text-gray-900">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    </template>
                    <template x-for="j in jenjangFilter" :key="'j-'+j">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                            <span x-text="j"></span>
                            <button type="button" @click="jenjangFilter = jenjangFilter.filter(v => v !== j)" class="ml-0.5 hover:text-gray-900">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </span>
                    </template>

                    {{-- Clear All --}}
                    <button x-show="hasFilters" x-transition type="button" @click="clearAll()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear Filters
                    </button>
                    </div>

                    {{-- Search on Right (Always Visible) --}}
                    <div class="relative flex items-center w-full md:w-72 shrink-0">
                        <div class="absolute left-0 z-10 w-9 h-9 flex items-center justify-center text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Cari lowongan"
                               @keydown.escape="search = ''"
                               class="w-full pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                        <button type="button" x-show="search !== ''" @click="search = ''"
                                class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors" style="display:none;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lowongan Cards Grid (Available) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($availableLowongans as $lowongan)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col p-6 relative"
                         x-show="isCardVisible({{ $lowongan->id }}, '{{ $lowongan->prodi->nama ?? '' }}', '{{ $lowongan->jenjang_minimal }}', '{{ addslashes($lowongan->nama_posisi) }}', '{{ $lowongan->kategori }}')"
                         x-transition>

                        {{-- Header: Title & Logo --}}
                        <div class="flex items-start justify-between gap-4 mb-5">
                            <div class="flex-1 pt-1">
                                <h3 class="text-base font-bold text-gray-800 leading-tight">{{ $lowongan->nama_posisi }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $lowongan->prodi->nama ?? ($lowongan->kategori === 'Tenaga Kependidikan' ? 'Tenaga Kependidikan' : 'Semua Prodi') }}</p>
                            </div>
                            @if($lowongan->prodi && $lowongan->prodi->logo)
                                <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}"
                                    class="w-14 h-14 rounded-full object-cover bg-white border border-gray-100 flex-shrink-0" style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; max-width: 56px; max-height: 56px; object-fit: cover;">
                            @else
                                <div class="w-14 h-14 rounded-full bg-white border border-gray-200 flex-shrink-0 flex items-center justify-center p-1.5 shadow-sm" style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                                    <img src="{{ asset('images/logo-icon.png') }}" alt="Telkom University" class="w-full h-full object-contain">
                                </div>
                            @endif
                        </div>

                        {{-- Info Rows --}}
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-3 text-black font-medium text-sm">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                <span>Surabaya</span>
                            </div>
                            <div class="flex items-center gap-3 text-gray-400 text-xs font-medium">
                                <svg class="w-6 h-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $lowongan->tanggal_tutup->format('j F Y') }} . {{ $lowongan->kuota }} Kuota</span>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-3 mb-8">
                            <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">{{ $lowongan->jenjang_minimal }}</span>
                            @if($lowongan->kategori !== 'Tenaga Kependidikan')
                            <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">> {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                            @endif
                            <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">Full-Time</span>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-3 mt-auto">
                            <a href="{{ route('landing.lowongan.show', $lowongan) }}"
                                class="flex-1 py-2.5 bg-[#8b1515] text-white text-center text-sm font-bold rounded-xl shadow-sm hover:bg-red-900 transition-colors duration-300">
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white border border-gray-100 rounded-3xl p-16 text-center">
                        <h2 class="text-xl font-bold text-gray-800">Tidak ada lowongan tersedia</h2>
                        <p class="text-sm text-gray-500 mt-2">Coba sesuaikan filter Anda.</p>
                    </div>
                @endforelse

                {{-- Frontend Empty State --}}
                <div class="col-span-full bg-white border border-gray-100 rounded-3xl p-16 text-center"
                     x-show="visibleCount === 0 && {{ $availableLowongans->count() }} > 0" x-cloak>
                    <h2 class="text-xl font-bold text-gray-800">Tidak ada lowongan yang cocok</h2>
                    <p class="text-sm text-gray-500 mt-2">Coba sesuaikan filter Anda atau klik "Clear Filters".</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
   <footer class="bg-[#1a1a1a] text-gray-400 text-sm">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <img src="{{ asset('images/logo2.png') }}" alt="Telkom University" class="h-12 object-contain mb-4">
            </div>

            <div>
                <h4 class="text-base font-bold text-white mb-4">Kontak Kami</h4>
                <p class="text-sm font-semibold text-[#8b1515] mb-1">Untuk Perusahaan</p>
                <p class="text-xs text-gray-400 leading-relaxed mb-3">Admin 082118362845 Chat Only (For Company)</p>
                <p class="text-sm font-semibold text-[#8b1515] mb-1">Untuk Pelamar</p>
                <p class="text-xs text-gray-400 leading-relaxed">Admin 081298678038 Chat Only (For Jobseeker)</p>
            </div>

            <div>
                <h4 class="text-base font-bold text-white mb-4">Lokasi</h4>
                <p class="text-sm font-semibold text-[#8b1515] mb-1">Gedung Bangkit (Rektorat)</p>
                <p class="text-xs text-gray-400 leading-relaxed">Gedung Bangkit Lantai 3, Jl. Telekomunikasi Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat 40257</p>
            </div>

            <div>
                <h4 class="text-base font-bold text-white mb-4">Fitur</h4>
                <a href="#lowongan" class="block text-xs text-gray-400 no-underline mb-2 hover:text-white transition-colors">Lowongan</a>
                <a href="#panduan" class="block text-xs text-gray-400 no-underline mb-2 hover:text-white transition-colors">Panduan</a>
            </div>
        </div>

        <div class="bg-[#8b1515] px-5 sm:px-8 py-4 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 flex-wrap">
            <div class="text-xs  text-white text-center sm:text-right">
                &copy; {{ date('Y') }} Telkom University Surabaya.
            </div>
        </div>
    </footer>


    <script>
        const navbar = document.getElementById('navbar');
        const scrollThreshold = 60;
        function onScroll() {
            if (window.scrollY > scrollThreshold) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    </script>

    @php
        $lowonganData = $availableLowongans->map(function($l) {
            return ['id' => $l->id, 'prodi' => $l->prodi->nama ?? '', 'jenjang' => $l->jenjang_minimal, 'nama' => $l->nama_posisi, 'kategori' => $l->kategori];
        });
    @endphp
    <script>
        window._lowonganData = @json($lowonganData);
        document.addEventListener('alpine:init', () => {
            Alpine.data('lowonganApp', () => ({
                prodiFilter: [],
                jenjangFilter: [],
                kategoriFilter: '',
                search: '',
                prodiOpen: false,
                jenjangOpen: false,
                kategoriOpen: false,

                get hasFilters() { return this.prodiFilter.length > 0 || this.jenjangFilter.length > 0 || this.search !== '' || this.kategoriFilter !== ''; },

                get visibleCount() {
                    const all = window._lowonganData;
                    return all.filter(l => this.isCardVisibleRaw(l.id, l.prodi, l.jenjang, l.nama, l.kategori)).length;
                },

                clearAll() { this.prodiFilter = []; this.jenjangFilter = []; this.search = ''; this.kategoriFilter = ''; },

                isCardVisible(id, prodi, jenjang, nama, kategori) {
                    return this.isCardVisibleRaw(id, prodi, jenjang, nama, kategori);
                },

                isCardVisibleRaw(id, prodi, jenjang, nama, kategori) {
                    if (this.kategoriFilter !== '' && kategori !== this.kategoriFilter) return false;
                    if (this.prodiFilter.length > 0 && !this.prodiFilter.includes(prodi)) return false;
                    if (this.jenjangFilter.length > 0 && !this.jenjangFilter.includes(jenjang)) return false;
                    if (this.search !== '' && !nama.toLowerCase().includes(this.search.toLowerCase())) return false;
                    return true;
                }
            }));
        });
    </script>
</body>
</html>
