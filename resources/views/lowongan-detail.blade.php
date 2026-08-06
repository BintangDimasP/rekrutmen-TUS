<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Detail Lowongan: {{ $lowongan->nama_posisi }} — Telkom University</title>
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
        /* StyleLightt */
        html { color-scheme: light; }

        /* Navbar custom (always white since we don't have hero image here) */
        #navbar { background: #fff; box-shadow: 0 2px 16px rgba(0,0,0,0.05); }
        #navbar .nav-link { color: #111; }
        #navbar .nav-link:hover { color: #8b1515; }
        #navbar .btn-masuk { color: #111; }
        #navbar .btn-masuk:hover { background: rgba(0,0,0,0.05); }
        #navbar .btn-daftar { background: #8b1515; color: #fff; }
        #navbar .btn-daftar:hover { background: #991b1b; }

        .card-header-red {
            background: linear-gradient(135deg, #8b1515 0%, #6b0f0f 100%);
            position: relative; overflow: hidden;
        }
        .card-header-red::before {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.05); border-radius: 50%;
            pointer-events: none;
        }
        .card-header-red::after {
            content: ''; position: absolute; bottom: -80px; right: 60px;
            width: 250px; height: 250px;
            background: rgba(255,255,255,0.04); border-radius: 50%;
            pointer-events: none;
        }

        .info-row-grid {
            display: grid; grid-template-columns: repeat(4,1fr);
            border-radius: 14px; overflow: hidden;
            border: 1px solid #ebebeb;
        }
        .info-row-cell { padding: 14px 16px; background: #fafafa; border-right: 1px solid #ebebeb; }
        .info-row-cell:last-child { border-right: none; }
        @media(max-width:640px) {
            .info-row-grid { grid-template-columns: repeat(2,1fr); }
            .info-row-cell:nth-child(2) { border-right: none; }
            .info-row-cell:nth-child(3),
            .info-row-cell:nth-child(4) { border-top: 1px solid #ebebeb; }
        }

        .desc-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 11px 14px;
            background: #fafafa; border-radius: 10px;
            border-left: 3px solid #8b1515;
            font-size: 13.5px; color: #444; line-height: 1.6;
        }
        .desc-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: #8b1515; flex-shrink: 0; margin-top: 8px;
        }
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

    <!-- Main Content -->
    <main class="flex-grow pt-[100px] pb-16 px-5 sm:px-8">
        <div class="max-w-[1200px] mx-auto">
            
            <div class="bg-white rounded-[22px] border border-gray-200 overflow-hidden shadow-sm">
                {{-- RED HEADER --}}
                <div class="card-header-red px-8 py-7 flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-[24px] font-bold text-white leading-snug relative z-10">{{ $lowongan->nama_posisi }}</h1>
                        <p class="text-white/80 text-[14px] mt-2 relative z-10">
                            {{ $lowongan->prodi->nama ?? ($lowongan->kategori === 'Tenaga Kependidikan' ? 'Tenaga Kependidikan' : 'Semua Program Studi') }} — Telkom University Surabaya
                        </p>
                    </div>
                    <div class="relative z-10 shrink-0">
                        @if($lowongan->prodi && $lowongan->prodi->logo)
                            <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}"
                                class="w-16 h-16 rounded-full object-cover bg-white border-2 border-white/30 shadow-md shrink-0" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px; max-width: 64px; max-height: 64px; object-fit: cover;">
                        @else
                            <div class="w-16 h-16 rounded-full bg-white border-2 border-white/30 flex items-center justify-center shadow-md p-1.5 shrink-0" style="width: 64px; height: 64px; min-width: 64px; min-height: 64px;">
                                <img src="{{ asset('images/logo-icon.png') }}" alt="Telkom University" class="w-full h-full object-contain">
                            </div>
                        @endif
                    </div>
                </div>

                {{-- BODY: 2 COLUMNS --}}
                <div class="flex flex-col lg:flex-row px-8 py-8 gap-10 items-start">
                    
                    {{-- KIRI: Sidebar Dokumen (Dari Deskripsi Database) --}}
                    <div class="w-full lg:w-1/2 shrink-0">
                        <div class="max-h-[420px] overflow-y-auto pr-4 pb-2 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                            @if($lowongan->deskripsi)
                            @php
                                $lines = explode("\n", $lowongan->deskripsi);
                                $sections = [];
                                $currentSection = ['title' => null, 'items' => []];

                                foreach ($lines as $line) {
                                    $trimmed = trim($line);
                                    if ($trimmed === '') continue;

                                    if (str_starts_with($trimmed, '-')) {
                                        $currentSection['items'][] = ltrim($trimmed, '- ');
                                    } elseif (str_ends_with($trimmed, ':')) {
                                        if ($currentSection['title'] !== null || !empty($currentSection['items'])) {
                                            $sections[] = $currentSection;
                                        }
                                        $currentSection = ['title' => rtrim($trimmed, ' :') . ' :'];
                                    } elseif (!empty($currentSection['items'])) {
                                        $lastIdx = count($currentSection['items']) - 1;
                                        $currentSection['items'][$lastIdx] .= ' ' . $trimmed;
                                    } else {
                                        if ($currentSection['title'] !== null || !empty($currentSection['items'])) {
                                            $sections[] = $currentSection;
                                        }
                                        $currentSection = ['title' => $trimmed, 'items' => []];
                                    }
                                }
                                if ($currentSection['title'] !== null || !empty($currentSection['items'])) {
                                    $sections[] = $currentSection;
                                }
                            @endphp

                            <div class="space-y-8">
                                @foreach($sections as $i => $section)
                                    <div>
                                        @if($section['title'])
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-1 h-5 rounded-full bg-[#8b1515] flex-shrink-0"></div>
                                            <span class="text-[12px] font-bold text-[#8b1515] uppercase tracking-wider">{{ $section['title'] }}</span>
                                        </div>
                                        @endif
                                        
                                        @if(!empty($section['items']))
                                        <div class="flex flex-col gap-3">
                                            @foreach($section['items'] as $item)
                                            <div class="desc-item">
                                                <div class="desc-dot"></div>
                                                <span>{{ $item }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada dokumen persyaratan yang ditentukan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- KANAN: Detail Lowongan --}}
                    <div class="w-full lg:w-1/2 flex-1 min-w-0 space-y-8 lg:border-l lg:border-gray-100 lg:pl-10">
                            
                        {{-- PILLS INFO --}}
                        <div class="flex flex-wrap gap-2.5">
                            <span class="px-2.5 py-1.5 rounded-md text-[12px] font-semibold text-gray-700 bg-gray-100 border border-gray-200/80 shadow-sm">{{ $lowongan->jenjang_minimal }}</span>
                            @if($lowongan->kategori !== 'Tenaga Kependidikan')
                            <span class="px-2.5 py-1.5 rounded-md text-[12px] font-semibold text-gray-700 bg-gray-100 border border-gray-200/80 shadow-sm">IPK ≥ {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                            @endif
                            <span class="px-2.5 py-1.5 rounded-md text-[12px] font-semibold text-gray-700 bg-gray-100 border border-gray-200/80 shadow-sm">Full-Time</span>
                            <span class="px-2.5 py-1.5 rounded-md text-[12px] font-semibold text-gray-700 bg-gray-100 border border-gray-200/80 shadow-sm">{{ $lowongan->kuota }} Kuota</span>
                            <span class="px-2.5 py-1.5 rounded-md text-[12px] font-semibold text-red-700 bg-red-50 border border-red-100 shadow-sm">
                                Tutup {{ $lowongan->tanggal_tutup->format('d M Y') }}
                            </span>
                        </div>

                        {{-- KUALIFIKASI KHUSUS --}}
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1 h-5 rounded-full bg-[#8b1515] flex-shrink-0"></div>
                                <span class="text-[12px] font-bold text-[#8b1515] uppercase tracking-wider">KUALIFIKASI KHUSUS</span>
                            </div>
                            <div class="grid grid-cols-1 @if($lowongan->kategori !== 'Tenaga Kependidikan') xl:grid-cols-2 @endif gap-4">
                                @if($lowongan->kategori !== 'Tenaga Kependidikan')
                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Prodi Linear / Prioritas</div>
                                    @if($lowongan->prodi_prioritas)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(array_filter(array_map('trim', explode(',', $lowongan->prodi_prioritas))) as $pp)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-white border border-gray-200 text-[13px] font-medium text-gray-700">{{ $pp }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-[14px] text-gray-500 leading-relaxed">-</div>
                                    @endif
                                </div>
                                @endif
                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Skill Utama</div>
                                    @if($lowongan->skill_dibutuhkan)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(array_filter(array_map('trim', explode(',', $lowongan->skill_dibutuhkan))) as $sk)
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-white border border-gray-200 text-[13px] font-medium text-gray-700">{{ $sk }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-[14px] text-gray-500 leading-relaxed">-</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- TOMBOL LAMAR / LOGIN --}}
                        @if($lowongan->tanggal_tutup->endOfDay()->isPast())
                            <div class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center">
                                <div class="text-lg font-bold text-[#8b1515] mb-1">Pendaftaran Ditutup</div>
                                <p class="text-sm text-red-500">Batas waktu pendaftaran untuk posisi ini telah berakhir.</p>
                            </div>
                        @else
                            <div class="text-center pt-2">
                                <p class="text-sm text-gray-500 mb-5">Jadi Bagian Dari Telkom University, Sekarang!</p>
                                @auth
                                    @if(auth()->user()->role === 'pelamar')
                                        <a href="{{ route('pelamar.lowongan.show', $lowongan) }}" class="inline-block px-10 py-3.5 bg-[#8b1515] text-white text-[15px] font-bold rounded-xl shadow-md hover:bg-[#7a1212] hover:-translate-y-0.5 transition-all">
                                            Ajukan Lamaran
                                        </a>
                                    @else
                                        <a href="{{ url('/dashboard') }}" class="inline-block px-10 py-3.5 bg-[#8b1515] text-white text-[15px] font-bold rounded-xl shadow-md hover:bg-[#7a1212] hover:-translate-y-0.5 transition-all">
                                            Ajukan Lamaran
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="inline-block px-10 py-3.5 bg-[#8b1515] text-white text-[15px] font-bold rounded-xl shadow-md hover:bg-[#7a1212] hover:-translate-y-0.5 transition-all">
                                        Ajukan Lamaran
                                    </a>
                                  
                                @endauth
                            </div>
                        @endif

                    </div>
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
</body>
</html>
