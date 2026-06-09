<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekrutmen Dosen — Telkom University</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet (peta + pin point) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ─── Animations (cannot be done purely in Tailwind) ─── */
        @keyframes slideInFromLeft {
            from { opacity: 0; transform: translateX(-60px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-hero { animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .animate-hero-1 { animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s forwards; opacity: 0; }
        .animate-hero-2 { animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s forwards; opacity: 0; }
        .animate-hero-3 { animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s forwards; opacity: 0; }

        /* Scroll reveal */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .animate-on-scroll.in-view { opacity: 1; transform: translateY(0); }
        .animate-on-scroll.slide-bottom { transform: translateY(40px); }
        .animate-on-scroll.slide-bottom.in-view { transform: translateY(0); }
        .animate-on-scroll.fade-scale { transform: scale(0.92) translateY(20px); }
        .animate-on-scroll.fade-scale.in-view { transform: scale(1) translateY(0); }

        /* ─── Navbar scroll state (JS toggled) ─── */
        #navbar { transition: background-color 0.35s ease, box-shadow 0.35s ease; }
        #navbar.scrolled { background: #8b1515; box-shadow: 0 2px 16px rgba(0,0,0,0.15); }
        #navbar.scrolled .nav-link { color: #fff; }
        #navbar.scrolled .btn-masuk { color: #fff; }
        #navbar.scrolled .btn-masuk:hover { background: rgba(255,255,255,0.15); }
        #navbar.scrolled .btn-daftar { background: #fff; color: #8b1515; }
        #navbar.scrolled .btn-daftar:hover { background: #f3f4f6; }
        #navbar.scrolled .logo-normal { opacity: 0; }
        #navbar.scrolled .logo-scrolled { opacity: 1; }
        /* Mobile menu background always white regardless of scroll */
        #navbar .mobile-menu { background: #fff; }

        /* ─── Panduan Card Hover Effects ─── */
        .panduan-card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }
        .panduan-card:hover {
            background: #8b1515;
            border-color: #8b1515;
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(0,0,0,0.18), 0 6px 16px rgba(0,0,0,0.10);
        }

        /* Angka "01"–"04": snap hilang saat hover */
        .panduan-num {
            transition: none;
        }
        .panduan-card:hover .panduan-num {
            opacity: 0;
            transform: translateY(-8px);
        }
        .panduan-num::after {
            transition: none;
        }
        .panduan-card:hover .panduan-num::after {
            opacity: 0;
        }

        /* Bubble angka besar: animasi pop dari bawah saat hover */
        .panduan-hover-num {
            transition:
                opacity 0.25s ease,
                transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            opacity: 0;
            transform: translateX(-50%) translateY(20px) scale(0.5);
        }
        .panduan-card:hover .panduan-hover-num {
            opacity: 1;
            transform: translateX(-50%) translateY(0) scale(1);
        }

        /* Judul & deskripsi: instant */
        .panduan-title { transition: none; }
        .panduan-desc  { transition: none; }
        .panduan-card:hover .panduan-title { color: #fff; }
        .panduan-card:hover .panduan-desc  { color: rgba(255,255,255,0.82); }

        /* Dissolve masuk saat scroll reveal */
        .panduan-num-dissolve {
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.6s ease, transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .panduan-card.in-view .panduan-num-dissolve {
            opacity: 1;
            transform: translateY(0);
        }
        .panduan-card:nth-child(1) .panduan-num-dissolve { transition-delay: 0.15s; }
        .panduan-card:nth-child(2) .panduan-num-dissolve { transition-delay: 0.28s; }
        .panduan-card:nth-child(3) .panduan-num-dissolve { transition-delay: 0.41s; }
        .panduan-card:nth-child(4) .panduan-num-dissolve { transition-delay: 0.54s; }

        /* ─── Lowongan Card ─── */
        .lowongan-card { transition: all 0.3s ease; }
        .lowongan-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: #8b1515;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .lowongan-card:hover { 
           
            transform: translateY(-10px); 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 16px rgba(0, 0, 0, 0.04);
        }
        .lowongan-card:hover::before { transform: scaleX(1); }
    </style>
</head>
<body class="bg-white text-gray-900">
@include('partials.loading-screen')
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-transparent" x-data="{ mobileOpen: false }">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 h-[68px] flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                <div class="relative w-[100px] sm:w-[120px] h-14 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('storage/images/logo1.png') }}" alt="Telkom University Logo" class="logo-normal w-full h-8 object-contain transition-opacity duration-300 opacity-100">
                    <img src="{{ asset('storage/images/logo2.png') }}" alt="Telkom University Logo" class="logo-scrolled w-full h-8 object-contain transition-opacity duration-300 opacity-0 absolute">
                </div>
            </a>

            {{-- Desktop nav links --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="#hero" class="nav-link text-sm font-medium text-gray-900 no-underline transition-colors duration-300 hover:opacity-80">Beranda</a>
                <a href="#panduan" class="nav-link text-sm font-medium text-gray-900 no-underline transition-colors duration-300 hover:opacity-80">Panduan</a>
                <a href="#lowongan" class="nav-link text-sm font-medium text-gray-900 no-underline transition-colors duration-300 hover:opacity-80">Lowongan</a>
                <a href="#lokasi" class="nav-link text-sm font-medium text-gray-900 no-underline transition-colors duration-300 hover:opacity-80">Lokasi</a>
            </div>

            {{-- Desktop auth buttons --}}
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
                @else
                    <a href="#" class="btn-masuk text-sm font-medium text-gray-900 no-underline px-4 py-2 rounded-md transition-all duration-300 hover:bg-black/5">Masuk</a>
                    <a href="#" class="btn-daftar text-sm font-semibold text-white no-underline bg-[#8b1515] px-5 py-2 rounded-md transition-all duration-300 hover:bg-[#991b1b]">Daftar</a>
                @endif
            </div>

            {{-- Mobile: auth + hamburger --}}
            <div class="flex md:hidden items-center gap-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-masuk text-xs font-medium text-gray-900 no-underline px-3 py-1.5 rounded-md transition-all duration-300 hover:bg-black/5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk text-xs font-medium text-gray-900 no-underline px-3 py-1.5 rounded-md transition-all duration-300 hover:bg-black/5">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-daftar text-xs font-semibold text-white no-underline bg-[#8b1515] px-3 py-1.5 rounded-md transition-all duration-300 hover:bg-[#991b1b]">Daftar</a>
                        @endif
                    @endauth
                @endif
                {{-- Hamburger button --}}
                <button @click="mobileOpen = !mobileOpen" type="button"
                        class="btn-masuk w-9 h-9 flex items-center justify-center rounded-md transition-all duration-300 hover:bg-black/5 focus:outline-none"
                        aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             style="display:none;"
             class="md:hidden bg-white border-t border-gray-100 shadow-lg">
            <div class="max-w-[1200px] mx-auto px-5 py-4 flex flex-col gap-1">
                <a href="#hero" @click="mobileOpen = false" class="text-sm font-medium text-gray-800 no-underline px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Beranda</a>
                <a href="#panduan" @click="mobileOpen = false" class="text-sm font-medium text-gray-800 no-underline px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Panduan</a>
                <a href="#lowongan" @click="mobileOpen = false" class="text-sm font-medium text-gray-800 no-underline px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Lowongan</a>
                <a href="#lokasi" @click="mobileOpen = false" class="text-sm font-medium text-gray-800 no-underline px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">Lokasi</a>
            </div>
        </div>
    </nav>

    {{-- ========================= HERO ========================= --}}
    {{-- Desktop: background image kanan. Mobile: layout 2 baris (teks atas, gambar bawah) --}}
    <section id="hero" class="relative overflow-hidden bg-white">

        {{-- ── DESKTOP layout (md ke atas): background image seperti semula ── --}}
        <div class="hidden md:flex items-center min-h-screen"
             style="background: #fff url('{{ asset('images/hero-bg.png') }}') no-repeat center right / cover;">
            <div class="max-w-[1200px] mx-auto px-8 pt-[100px] pb-[60px] w-full">
                <div class="relative z-10 max-w-[55%] animate-hero">
                    <h1 class="animate-hero-1 text-[2.6rem] font-extrabold leading-[1.2] text-gray-900 mb-1">
                        Wujudkan Karier<br>
                        Impianmu Bersama<br>
                        <span class="text-[#8b1515]">Telkom University</span>
                    </h1>
                    <p class="animate-hero-2 text-[0.95rem] text-gray-500 leading-relaxed mb-7 max-w-[420px]">
                        Kami membuka kesempatan emas bagi akademisi terbaik Indonesia untuk berkontribusi sebagai tenaga pendidik profesional di kampus kami.
                    </p>
                    <div class="animate-hero-3 flex items-center gap-6 flex-wrap">
                        <a href="#lowongan" class="inline-block no-underline bg-[#8b1515] text-white font-semibold text-sm px-7 py-3 rounded-md transition-all duration-200 hover:bg-[#991b1b] hover:-translate-y-0.5">Selengkapnya</a>
                        <span class="text-xs text-gray-400 font-medium">
                            <strong class="text-gray-900 font-bold">{{ $totalPendaftar }}+</strong> Total Pendaftar
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── MOBILE layout (di bawah md): teks di atas, gambar di bawah ── --}}
        <div class="md:hidden flex flex-col">
            {{-- Teks atas dengan background putih bersih --}}
            <div class="bg-white px-5 pt-[88px] pb-8 z-10 relative">
                <h1 class="text-[1.85rem] font-extrabold leading-[1.2] text-gray-900 mb-3">
                    Wujudkan Karier<br>
                    Impianmu Bersama<br>
                    <span class="text-[#8b1515]">Telkom University</span>
                </h1>
                <p class="text-[0.9rem] text-gray-500 leading-relaxed mb-6">
                    Kami membuka kesempatan emas bagi akademisi terbaik Indonesia untuk berkontribusi sebagai tenaga pendidik profesional di kampus kami.
                </p>
                <div class="flex items-center gap-5 flex-wrap">
                    <a href="#lowongan" class="inline-block no-underline bg-[#8b1515] text-white font-semibold text-sm px-6 py-3 rounded-md transition-all duration-200 hover:bg-[#991b1b]">Selengkapnya</a>
                    <span class="text-xs text-gray-400 font-medium">
                        <strong class="text-gray-900 font-bold">{{ $totalPendaftar }}+</strong> Total Pendaftar
                    </span>
                </div>
            </div>
            {{-- Gambar penuh di bawah --}}
            <div class="w-full h-[320px] overflow-hidden">
                <img src="{{ asset('images/hero-bg.png') }}"
                     alt="Hero"
                     class="w-full h-full object-cover object-top">
            </div>
        </div>

    </section>

    {{-- ========================= PANDUAN ========================= --}}
    <section id="panduan" class="bg-gray-50 py-12 sm:py-16 px-5 sm:px-8">
        <div class="max-w-[1200px] mx-auto text-center mb-16 animate-on-scroll slide-bottom">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                4 Langkah Mudah Menjadi <span class="text-[#8b1515]">Bagian Dari Kami</span>
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                Kami merancang proses rekrutmen yang transparan dan memudahkan Anda untuk bergabung sebagai tenaga pendidik profesional.
            </p>
        </div>

        <div class="max-w-[1200px] mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Card 1 --}}
            <div class="panduan-card relative overflow-visible mt-10 bg-white border-[1.5px] border-gray-200 rounded-2xl min-h-[320px] flex flex-col cursor-pointer animate-on-scroll fade-scale" style="transition-delay: 0.1s">
                <div class="panduan-hover-num absolute -top-10 left-1/2 w-20 h-20 rounded-full bg-[#8b1515] flex items-center justify-center text-2xl font-bold text-white border-4 border-white shadow-lg z-10">1</div>
                <div class="p-8 pt-8 pb-6 flex-1 flex flex-col">
                    <div class="panduan-num panduan-num-dissolve text-2xl font-bold text-[#8b1515] mb-1 w-fit relative after:content-[''] after:block after:w-full after:h-[3px] after:bg-[#8b1515] after:mt-1 after:rounded">01</div>
                    <div class="panduan-title text-base font-bold text-gray-900 mb-2 mt-3 leading-snug">Buat Akun</div>
                    <p class="panduan-desc text-sm text-gray-500 leading-relaxed flex-1">Daftarkan diri Anda dengan membuat akun baru menggunakan email aktif Anda.</p>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="panduan-card relative overflow-visible mt-10 bg-white border-[1.5px] border-gray-200 rounded-2xl min-h-[320px] flex flex-col cursor-pointer animate-on-scroll fade-scale" style="transition-delay: 0.2s">
                <div class="panduan-hover-num absolute -top-10 left-1/2 w-20 h-20 rounded-full bg-[#8b1515] flex items-center justify-center text-2xl font-bold text-white border-4 border-white shadow-lg z-10">2</div>
                <div class="p-8 pt-8 pb-6 flex-1 flex flex-col">
                    <div class="panduan-num panduan-num-dissolve text-2xl font-bold text-[#8b1515] mb-1 w-fit relative after:content-[''] after:block after:w-full after:h-[3px] after:bg-[#8b1515] after:mt-1 after:rounded">02</div>
                    <div class="panduan-title text-base font-bold text-gray-900 mb-2 mt-3 leading-snug">Daftar & Isi Data Pribadi</div>
                    <p class="panduan-desc text-sm text-gray-500 leading-relaxed flex-1">Lengkapi profil dan data pribadi Anda sesuai dengan dokumen yang dimiliki.</p>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="panduan-card relative overflow-visible mt-10 bg-white border-[1.5px] border-gray-200 rounded-2xl min-h-[320px] flex flex-col cursor-pointer animate-on-scroll fade-scale" style="transition-delay: 0.3s">
                <div class="panduan-hover-num absolute -top-10 left-1/2 w-20 h-20 rounded-full bg-[#8b1515] flex items-center justify-center text-2xl font-bold text-white border-4 border-white shadow-lg z-10">3</div>
                <div class="p-8 pt-8 pb-6 flex-1 flex flex-col">
                    <div class="panduan-num panduan-num-dissolve text-2xl font-bold text-[#8b1515] mb-1 w-fit relative after:content-[''] after:block after:w-full after:h-[3px] after:bg-[#8b1515] after:mt-1 after:rounded">03</div>
                    <div class="panduan-title text-base font-bold text-gray-900 mb-2 mt-3 leading-snug">Pilih Posisi Lowongan</div>
                    <p class="panduan-desc text-sm text-gray-500 leading-relaxed flex-1">Cari dan pilih posisi dosen yang sesuai dengan bidang keahlian Anda.</p>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="panduan-card relative overflow-visible mt-10 bg-white border-[1.5px] border-gray-200 rounded-2xl min-h-[320px] flex flex-col cursor-pointer animate-on-scroll fade-scale" style="transition-delay: 0.4s">
                <div class="panduan-hover-num absolute -top-10 left-1/2 w-20 h-20 rounded-full bg-[#8b1515] flex items-center justify-center text-2xl font-bold text-white border-4 border-white shadow-lg z-10">4</div>
                <div class="p-8 pt-8 pb-6 flex-1 flex flex-col">
                    <div class="panduan-num panduan-num-dissolve text-2xl font-bold text-[#8b1515] mb-1 w-fit relative after:content-[''] after:block after:w-full after:h-[3px] after:bg-[#8b1515] after:mt-1 after:rounded">04</div>
                    <div class="panduan-title text-base font-bold text-gray-900 mb-2 mt-3 leading-snug">Ajukan Lamaran</div>
                    <p class="panduan-desc text-sm text-gray-500 leading-relaxed flex-1">Unggah dokumen pendukung dan kirimkan lamaran Anda untuk diproses.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= LOWONGAN ========================= --}}
    <section id="lowongan" class="py-12 sm:py-16 px-5 sm:px-8 bg-white">
        <div class="max-w-[1200px] mx-auto text-center mb-16
         animate-on-scroll slide-bottom">
            
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                Posisi <span class="text-[#8b1515]">Lowongan Dosen</span> Terbaru
            </h2>
           
            <p class="text-gray-500 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                Temukan peluang untuk berkontribusi dalam mencetak generasi unggul melalui berbagai program studi kami yang inovatif.
            </p>
        </div>

        <div class="max-w-[1200px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @forelse($lowongans as $index => $lowongan)
            <div class="lowongan-card relative overflow-hidden bg-white border-[1.5px] border-gray-200 rounded-2xl p-6 flex flex-col animate-on-scroll fade-scale" style="transition-delay: {{ ($index * 0.1) + 0.1 }}s">
                <div class="flex items-start gap-4 mb-4">
                    <div class="shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                            @if($lowongan->prodi && $lowongan->prodi->logo)
                                <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}" class="w-full h-full object-contain p-1">
                            @else
                                <span class="text-xl font-bold text-gray-400">{{ substr($lowongan->nama_posisi, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-base font-bold text-gray-900 leading-snug mb-1">{{ $lowongan->nama_posisi }}</div>
                        <div class="text-xs text-gray-400 font-medium">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</div>
                    </div>
                </div>

                {{-- Meta: lokasi + deadline --}}
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        <span>Surabaya</span>
                    </div>
                    <span class="text-gray-200">|</span>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $lowongan->tanggal_tutup->format('j M Y') }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5 mb-5">
                    <span class="px-2.5 py-1 bg-gray-100 rounded-md text-[0.7rem] font-semibold text-gray-600">{{ $lowongan->jenjang_minimal }}</span>
                    <span class="px-2.5 py-1 bg-gray-100 rounded-md text-[0.7rem] font-semibold text-gray-600">IPK > {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                    <span class="px-2.5 py-1 bg-gray-100 rounded-md text-[0.7rem] font-semibold text-gray-600">{{ $lowongan->kuota }} Kuota</span>
                    <span class="px-2.5 py-1 bg-gray-100 rounded-md text-[0.7rem] font-semibold text-gray-600">Full-Time</span>
                </div>

                <div class="mt-auto pt-4 border-t border-gray-100">
                    @auth
                        @if(auth()->user()->role === 'pelamar')
                            <a href="{{ route('pelamar.lowongan.show', $lowongan) }}" class="inline-flex items-center justify-center gap-2 no-underline bg-white border-[1.5px] border-[#8b1515] rounded-lg py-2.5 px-4 text-sm font-semibold text-[#8b1515] transition-all duration-200 hover:bg-[#8b1515] hover:text-white hover:-translate-y-0.5 hover:shadow-lg w-full group">
                                Detail
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 no-underline bg-white border-[1.5px] border-[#8b1515] rounded-lg py-2.5 px-4 text-sm font-semibold text-[#8b1515] transition-all duration-200 hover:bg-[#8b1515] hover:text-white hover:-translate-y-0.5 hover:shadow-lg w-full">
                                Detail
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 no-underline bg-white border-[1.5px] border-[#8b1515] rounded-lg py-2.5 px-4 text-sm font-semibold text-[#8b1515] transition-all duration-200 hover:bg-[#8b1515] hover:text-white hover:-translate-y-0.5 hover:shadow-lg w-full">
                            Detail
                        </a>
                    @endauth
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-400 text-base">Belum ada lowongan tersedia saat ini.</p>
            </div>
            @endforelse
        </div>

        {{-- Footer Actions --}}
        @if($lowongans->count() > 0)
        <div class="max-w-[1200px] mx-auto flex items-center justify-center animate-on-scroll slide-bottom">
            @auth
                @if(auth()->user()->role === 'pelamar')
                    <a href="{{ route('pelamar.lowongan.index') }}" class="inline-flex items-center gap-2 no-underline rounded-lg px-8 py-3 text-sm font-semibold text-white bg-[#8b1515] transition-all duration-200 hover:bg-[#8b1515] hover:-translate-y-0.5 hover:shadow-lg">
                        Lihat Semua Lowongan
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 no-underline rounded-lg px-8 py-3 text-sm font-semibold text-white bg-[#8b1515] transition-all duration-200 hover:bg-[#8b1515] hover:-translate-y-0.5 hover:shadow-lg">
                        Lihat Semua Lowongan
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 no-underline rounded-lg px-8 py-3 text-sm font-semibold text-white bg-[#8b1515] transition-all duration-200 hover:bg-[#8b1515] hover:-translate-y-0.5 hover:shadow-lg">
                    Lihat Semua Lowongan
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @endauth
        </div>
        @endif
    </section>

    {{-- ========================= LOKASI ========================= --}}
    <section id="lokasi" class="bg-gray-50 py-12 sm:py-16 px-5 sm:px-8">
        <div class="max-w-[1200px] mx-auto">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:gap-16 mb-10 animate-on-scroll slide-bottom">
                <div class="md:w-[40%] shrink-0">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
                        Telkom University<br>
                        <span class="text-[#8b1515]">Surabaya</span>
                    </h2>
                </div>
                <div class="mt-5 md:mt-0 md:w-[60%]">
                    <p class="text-gray-500 text-base leading-relaxed">
                        Telkom University Surabaya merupakan kampus yang berdedikasi mencetak generasi unggul di bidang teknologi dan bisnis. Kami membuka kesempatan bagi akademisi terbaik untuk bergabung dan berkontribusi dalam ekosistem pendidikan yang inovatif dan kolaboratif.
                    </p>
                </div>
            </div>

            {{-- Peta interaktif dengan pin point (Leaflet + OpenStreetMap) --}}
            <div id="map-telu-surabaya" class="w-full rounded-2xl overflow-hidden border border-gray-200 shadow-sm animate-on-scroll fade-scale mb-10" style="height: 400px; z-index: 0;"></div>

            {{-- Info bar bawah --}}
            <div class="flex flex-col md:flex-row md:items-stretch md:gap-8 gap-6 animate-on-scroll slide-bottom">

                {{-- Foto gedung --}}
                <div class="shrink-0 w-full md:w-[200px] h-[130px] rounded-xl overflow-hidden shadow-sm">
                    {{-- Simpan foto gedung di: storage/app/public/images/telu-surabaya.jpg --}}
                    @if(file_exists(storage_path('app/public/images/telu-surabaya.jpg')))
                        <img src="{{ asset('storage/images/telu-surabaya.jpg') }}"
                             alt="Gedung Telkom University Surabaya"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#8b1515] to-[#5a0d0d] flex items-center justify-center">
                            <div class="text-white text-center px-3">
                                <svg class="w-8 h-8 mx-auto mb-1 opacity-80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
                                </svg>
                                <span class="text-[10px] font-bold tracking-wider">TEL-U SURABAYA</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Alamat --}}
                <div class="flex-1 min-w-0 md:border-l md:border-gray-200 md:pl-8">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-[#8b1515] shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span class="text-xs font-bold tracking-widest text-gray-900 uppercase">Alamat</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Jl. Ketintang No.156, Ketintang,<br>
                        Kec. Gayungan, Surabaya,<br>
                        Jawa Timur 60231
                    </p>
                </div>

                {{-- Kontak --}}
                <div class="flex-1 min-w-0 md:border-l md:border-gray-200 md:pl-8">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-[#8b1515] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                        </svg>
                        <span class="text-xs font-bold tracking-widest text-gray-900 uppercase">Informasi Kontak</span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-1">+62 31 8439 9000</p>
                    <p class="text-sm text-gray-600 leading-relaxed">rekrutmen@telkomuniversity.ac.id</p>
                </div>

                {{-- Tombol Get Directions --}}
                <div class="shrink-0 self-start md:self-center">
                    <a href="https://www.google.com/maps/search/?api=1&query=-7.314908,112.726939"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 no-underline bg-[#8b1515] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all duration-200 hover:bg-[#991b1b] hover:-translate-y-0.5 hover:shadow-lg whitespace-nowrap">
                        Lokasi
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================= FOOTER ========================= --}}
    <footer class="bg-[#1a1a1a] text-gray-400 text-sm">
        <div class="max-w-[1200px] mx-auto px-5 sm:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            {{-- Logo --}}
            <div>
                <img src="{{ asset('storage/images/logo2.png') }}" alt="Telkom University" class="h-12 object-contain mb-4">
            </div>

            {{-- Kontak Kami --}}
            <div>
                <h4 class="text-base font-bold text-white mb-4">Kontak Kami</h4>
                <p class="text-sm font-semibold text-[#8b1515] mb-1">Untuk Perusahaan</p>
                <p class="text-xs text-gray-400 leading-relaxed mb-3">Admin 082118362845 Chat Only (For Company)</p>
                <p class="text-sm font-semibold text-[#8b1515] mb-1">Untuk Pelamar</p>
                <p class="text-xs text-gray-400 leading-relaxed">Admin 081298678038 Chat Only (For Jobseeker)</p>
            </div>

            {{-- Lokasi --}}
            <div>
                <h4 class="text-base font-bold text-white mb-4">Lokasi</h4>
                <p class="text-sm font-semibold text-[#8b1515] mb-1">Gedung Bangkit (Rektorat)</p>
                <p class="text-xs text-gray-400 leading-relaxed">Gedung Bangkit Lantai 3, Jl. Telekomunikasi Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat 40257</p>
            </div>

            {{-- Fitur --}}
            <div>
                <h4 class="text-base font-bold text-white mb-4">Fitur</h4>
                <a href="#lowongan" class="block text-xs text-gray-400 no-underline mb-2 hover:text-white transition-colors">Lowongan</a>
                <a href="#panduan" class="block text-xs text-gray-400 no-underline mb-2 hover:text-white transition-colors">Panduan</a>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="bg-[#8b1515] px-5 sm:px-8 py-4 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 flex-wrap">
            <p class="text-white text-xs m-0">Don't forget to follow Tel-U Career's official social media:</p>
            <div class="flex items-center gap-4">
                <a href="#" aria-label="Instagram" class="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center text-white no-underline transition-all hover:bg-white/15 hover:border-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="#" aria-label="LinkedIn" class="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center text-white no-underline transition-all hover:bg-white/15 hover:border-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <a href="#" aria-label="X" class="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center text-white no-underline transition-all hover:bg-white/15 hover:border-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="#" aria-label="Facebook" class="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center text-white no-underline transition-all hover:bg-white/15 hover:border-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="#" aria-label="YouTube" class="w-8 h-8 rounded-full border border-white/40 flex items-center justify-center text-white no-underline transition-all hover:bg-white/15 hover:border-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
        // ─── Scroll handler: transparent → red navbar ───
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

        // ─── Intersection Observer untuk animasi scroll ───
        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                } else {
                    entry.target.classList.remove('in-view');
                }
            });
        }, observerOptions);

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
            // Observe panduan-card secara terpisah untuk dissolve angka
            document.querySelectorAll('.panduan-card').forEach(el => {
                observer.observe(el);
            });
        });

        // ─── Smooth scroll for anchor links ───
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href').slice(1);
                const target = document.getElementById(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>

    {{-- Leaflet untuk peta lokasi --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mapEl = document.getElementById('map-telu-surabaya');
            if (!mapEl || typeof L === 'undefined') return;

            // Koordinat Telkom University Surabaya — Jl. Ketintang No.156
            const lat = -7.314908;
            const lng = 112.726939;

            const map = L.map('map-telu-surabaya', {
                center: [lat, lng],
                zoom: 16,
                scrollWheelZoom: false,
            });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>'
            }).addTo(map);

            // Pin point custom warna merah Telkom
            const pinIcon = L.divIcon({
                className: 'telu-pin',
                html: `
                    <div style="position: relative; width: 36px; height: 48px;">
                        <svg viewBox="0 0 36 48" width="36" height="48" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 0C8.06 0 0 8.06 0 18c0 13.5 18 30 18 30s18-16.5 18-30C36 8.06 27.94 0 18 0z" fill="#8b1515"/>
                            <circle cx="18" cy="18" r="7" fill="#fff"/>
                            <circle cx="18" cy="18" r="3.5" fill="#8b1515"/>
                        </svg>
                    </div>
                `,
                iconSize: [36, 48],
                iconAnchor: [18, 48],
                popupAnchor: [0, -42],
            });

            L.marker([lat, lng], { icon: pinIcon })
                .addTo(map)
                .bindPopup(`
                    <div style="font-family: 'Inter', sans-serif; min-width: 200px;">
                        <div style="font-weight: 700; color: #8b1515; font-size: 13px; margin-bottom: 4px;">Telkom University Surabaya</div>
                        <div style="font-size: 12px; color: #4b5563; line-height: 1.5;">
                            Jl. Ketintang No.156, Ketintang,<br>
                            Kec. Gayungan, Surabaya,<br>
                            Jawa Timur 60231
                        </div>
                    </div>
                `)
                .openPopup();

            // Aktifkan scroll wheel zoom hanya saat user klik peta dulu
            map.on('focus', () => map.scrollWheelZoom.enable());
            map.on('blur', () => map.scrollWheelZoom.disable());
        });
    </script>
</body>
</html>
