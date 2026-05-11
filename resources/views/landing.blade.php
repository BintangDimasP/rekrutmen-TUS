<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekrutmen Dosen — Telkom University</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; color: #1a1a1a; background: #fff; }

        /* ─── SCROLL ANIMATIONS ─── */
        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInFromBottom {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .animate-on-scroll.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        .animate-on-scroll.slide-left {
            transform: translateX(-60px);
        }

        .animate-on-scroll.slide-left.in-view {
            transform: translateX(0);
        }

        .animate-on-scroll.slide-right {
            transform: translateX(60px);
        }

        .animate-on-scroll.slide-right.in-view {
            transform: translateX(0);
        }

        .animate-on-scroll.slide-bottom {
            transform: translateY(40px);
        }

        .animate-on-scroll.slide-bottom.in-view {
            transform: translateY(0);
        }

        .animate-on-scroll.fade-scale {
            transform: scale(0.92) translateY(20px);
        }

        .animate-on-scroll.fade-scale.in-view {
            transform: scale(1) translateY(0);
        }

        /* ─── NAVBAR ─── */
        #navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            transition: background-color 0.35s ease, box-shadow 0.35s ease;
            background: transparent;
        }
        #navbar.scrolled {
            background: #8b1515; /* red-700 */
            box-shadow: 0 2px 16px rgba(0,0,0,0.15);
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 36px;
            height: 36px;
            background: #8b1515;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.3s;
        }
        #navbar.scrolled .nav-logo-icon {
            background: rgba(255,255,255,0.2);
        }
        .nav-logo-icon svg { width: 22px; height: 22px; fill: #fff; }
        .nav-logo-text { font-weight: 700; font-size: 0.95rem; line-height: 1.2; color: #1a1a1a; transition: color 0.3s; }
        .nav-logo-text span { display: block; font-weight: 400; font-size: 0.75rem; color: #555; transition: color 0.3s; }
        #navbar.scrolled .nav-logo-text { color: #fff; }
        #navbar.scrolled .nav-logo-text span { color: rgba(255,255,255,0.8); }

        .nav-links { display: flex; align-items: center; gap: 2rem; }
        .nav-links a {
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1a1a1a;
            transition: color 0.3s;
        }
        #navbar.scrolled .nav-links a { color: #fff; }
        .nav-links a:hover { opacity: 0.8; }

        .nav-actions { display: flex; align-items: center; gap: 0.75rem; }
        .btn-masuk {
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            color: #1a1a1a;
            padding: 0.45rem 1.1rem;
            border-radius: 6px;
            transition: color 0.3s, background 0.3s;
        }
        #navbar.scrolled .btn-masuk { color: #fff; }
        .btn-masuk:hover { background: rgba(0,0,0,0.06); }
        #navbar.scrolled .btn-masuk:hover { background: rgba(255,255,255,0.15); }

        .btn-daftar {
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            background: #8b1515;
            padding: 0.5rem 1.3rem;
            border-radius: 6px;
            transition: background 0.3s, box-shadow 0.3s;
        }
        #navbar.scrolled .btn-daftar { background: #fff; color: #8b1515; }
        .btn-daftar:hover { background: #991b1b; }
        #navbar.scrolled .btn-daftar:hover { background: #f3f4f6; }

        /* ─── HERO ─── */
        #hero {
            min-height: 100vh;
            /* Gunakan gambar yang dikirim sebagai background */
            background: #fff url('{{ asset('images/hero-bg.png') }}') no-repeat center right;
            background-size: cover;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 2rem 60px;
            width: 100%;
        }
        .hero-content { 
            position: relative;
            z-index: 2; 
            max-width: 50%;
            animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .hero-title {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.2;
            color: #111;
            margin-bottom: 0.4rem;
            animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s forwards;
            opacity: 0;
        }
        .hero-title-red { color: #8b1515; }
        .hero-desc {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.7;
            margin-bottom: 1.8rem;
            max-width: 420px;
            animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s forwards;
            opacity: 0;
        }
        .hero-actions { 
            display: flex; 
            align-items: center; 
            gap: 1.5rem; 
            flex-wrap: wrap;
            animation: slideInFromLeft 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s forwards;
            opacity: 0;
        }
        .btn-hero-primary {
            display: inline-block;
            text-decoration: none;
            background: #8b1515;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.7rem 1.8rem;
            border-radius: 6px;
            transition: background 0.25s, transform 0.2s;
        }
        .btn-hero-primary:hover { background: #991b1b; transform: translateY(-1px); }
        .hero-stat {
            font-size: 0.82rem;
            color: #888;
            font-weight: 500;
        }
        .hero-stat strong { color: #111; font-weight: 700; }

        /* ─── SECTION PANDUAN ─── */
        #panduan {
            background: #f9fafb;
            padding: 80px 2rem;
        }
        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .section-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: #111;
        }
        .section-title-red { color: #8b1515; }
        .section-subtitle {
            font-size: 1rem;
            color: #555;
            margin-top: 0.3rem;
        }

        .panduan-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.2rem;
        }
        .panduan-card {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.6rem 1.4rem 1.4rem;
            cursor: pointer;
            transition: all 0.28s ease;
            position: relative;
        }
        .panduan-card:hover {
            background: #8b1515;
            border-color: #8b1515;
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(185,28,28,0.25);
        }
        .panduan-num {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 1rem;
            transition: background 0.28s, color 0.28s;
        }
        .panduan-card:hover .panduan-num {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }
        .panduan-card-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.5rem;
            transition: color 0.28s;
        }
        .panduan-card:hover .panduan-card-title { color: #fff; }

        .panduan-divider {
            height: 2px;
            width: 32px;
            background: #e5e7eb;
            margin-bottom: 0.75rem;
            border-radius: 2px;
            transition: background 0.28s;
        }
        .panduan-card:hover .panduan-divider { background: rgba(255,255,255,0.5); }

        .panduan-card-desc {
            font-size: 0.82rem;
            color: #888;
            line-height: 1.6;
            transition: color 0.28s;
        }
        .panduan-card:hover .panduan-card-desc { color: rgba(255,255,255,0.85); }

        /* ─── SECTION LOWONGAN ─── */
        #lowongan {
            padding: 80px 2rem;
            background: #fff;
        }
        .lowongan-header {
            max-width: 1200px;
            margin: 0 auto 2.5rem;
            text-align: center;
        }
        .lowongan-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.3rem;
        }
        .lowongan-title-red { color: #8b1515; }
        .lowongan-subtitle {
            font-size: 1rem;
            color: #555;
        }

        .lowongan-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .lowongan-card {
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        .lowongan-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8b1515, #dc2626);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .lowongan-card:hover {
            border-color: #8b1515;
            box-shadow: 0 12px 32px rgba(185,28,28,0.15);
            transform: translateY(-4px);
        }
        .lowongan-card:hover::before {
            transform: scaleX(1);
        }
        .lowongan-card-header {
            display: flex;
            align-items: start;
            gap: 1rem;
            margin-bottom: 1.2rem;
        }
        .lowongan-logo-wrap {
            flex-shrink: 0;
        }
        .lowongan-logo {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .lowongan-logo img { 
            width: 100%; 
            height: 100%; 
            object-fit: contain;
            padding: 4px;
        }
        .lowongan-logo-placeholder {
            font-size: 1.5rem;
            font-weight: 700;
            color: #9ca3af;
        }
        .lowongan-info-wrap {
            flex: 1;
            min-width: 0;
        }
        .lowongan-nama {
            font-size: 1rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }
        .lowongan-prodi {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        .lowongan-meta {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.2rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 10px;
        }
        .lowongan-meta-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.85rem;
        }
        .lowongan-meta-row svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: #6b7280;
        }
        .lowongan-meta-row span {
            color: #374151;
            font-weight: 500;
        }

        .lowongan-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.2rem;
        }
        .lowongan-badge {
            padding: 0.4rem 0.8rem;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
        }

        .lowongan-card-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }
        .btn-detail {
            display: block;
            text-decoration: none;
            text-align: center;
            background: #fff;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            transition: all 0.25s ease;
            width: 100%;
        }
        .btn-detail:hover { 
            border-color: #8b1515; 
            color: #8b1515; 
            background: #fef2f2;
        }

        .lowongan-footer-actions {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-selengkapnya {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            border: 1.5px solid #8b1515;
            border-radius: 8px;
            padding: 0.7rem 2rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #8b1515;
            background: #fff;
            transition: all 0.25s ease;
        }
        .btn-selengkapnya:hover { 
            background: #8b1515;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(185,28,28,0.25);
        }
        .btn-selengkapnya svg {
            width: 18px;
            height: 18px;
        }

        /* ─── FOOTER ─── */
        #footer {
            background: #111;
            color: #ccc;
            text-align: center;
            padding: 2rem;
            font-size: 0.82rem;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .hero-content { max-width: 100%; }
            .hero-title { font-size: 2.2rem; }
            .panduan-grid { grid-template-columns: repeat(2, 1fr); }
            .lowongan-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .panduan-grid { grid-template-columns: 1fr; }
            .lowongan-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 1.8rem; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

    {{-- ========================= NAVBAR ========================= --}}
    <nav id="navbar">
        <div class="nav-inner">
            <a href="{{ url('/') }}" class="nav-logo">
                <div class="nav-logo-icon">
                    {{-- Placeholder logo icon --}}
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="nav-logo-text">
                    Telkom
                    <span>University</span>
                </div>
            </a>

            <div class="nav-links">
                <a href="#hero">Beranda</a>
                <a href="#panduan">Panduan</a>
                <a href="#lowongan">Lowongan</a>
            </div>

            <div class="nav-actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-masuk">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-daftar" id="btn-daftar-nav">Daftar</a>
                        @endif
                    @endauth
                @else
                    <a href="#" class="btn-masuk">Masuk</a>
                    <a href="#" class="btn-daftar">Daftar</a>
                @endif
            </div>
        </div>
    </nav>

    {{-- ========================= HERO ========================= --}}
    <section id="hero">
        <div class="hero-inner">
            <div class="hero-content">
                <h1 class="hero-title">
                    Wujudkan Karier<br>
                    Impianmu Bersama<br>
                    <span class="hero-title-red">Telkom University</span>
                </h1>
                <p class="hero-desc">
                    Kami membuka kesempatan emas bagi akademisi terbaik Indonesia untuk berkontribusi sebagai tenaga pendidik profesional di kampus kami.
                </p>
                <div class="hero-actions">
                    <a href="#lowongan" class="btn-hero-primary">Selengkapnya</a>
                    <span class="hero-stat">
                        <strong>{{ $totalPendaftar }}+</strong> Total Pendaftar
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= PANDUAN ========================= --}}
    <section id="panduan">
        <div class="section-header animate-on-scroll slide-bottom">
            <h2 class="section-title">
                <span class="section-title-red">4 Langkah Mudah</span>
            </h2>
            <p class="section-subtitle">Menjadi Bagian Dari Kami</p>
        </div>

        <div class="panduan-grid">
            {{-- Card 1 --}}
            <div class="panduan-card animate-on-scroll fade-scale" style="transition-delay: 0.1s">
                <div class="panduan-num">1</div>
                <div class="panduan-card-title">Buat Akun</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Daftarkan diri Anda dengan membuat akun baru menggunakan email aktif Anda.</p>
            </div>

            {{-- Card 2 --}}
            <div class="panduan-card animate-on-scroll fade-scale" style="transition-delay: 0.2s">
                <div class="panduan-num">2</div>
                <div class="panduan-card-title">Daftar & Isi Data Pribadi</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Lengkapi profil dan data pribadi Anda sesuai dengan dokumen yang dimiliki.</p>
            </div>

            {{-- Card 3 --}}
            <div class="panduan-card animate-on-scroll fade-scale" style="transition-delay: 0.3s">
                <div class="panduan-num">3</div>
                <div class="panduan-card-title">Pilih Posisi Lowongan</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Cari dan pilih posisi dosen yang sesuai dengan bidang keahlian Anda.</p>
            </div>

            {{-- Card 4 --}}
            <div class="panduan-card animate-on-scroll fade-scale" style="transition-delay: 0.4s">
                <div class="panduan-num">4</div>
                <div class="panduan-card-title">Ajukan Lamaran</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Unggah dokumen pendukung dan kirimkan lamaran Anda untuk diproses.</p>
            </div>
        </div>
    </section>

    {{-- ========================= LOWONGAN ========================= --}}
    <section id="lowongan">
        <div class="lowongan-header animate-on-scroll slide-bottom">
            <h2 class="lowongan-title">
                <span class="lowongan-title-red">Posisi Lowongan Dosen</span>
            </h2>
            <p class="lowongan-subtitle">Temukan posisi yang sesuai dengan keahlian Anda</p>
        </div>

        <div class="lowongan-grid">
            @forelse($lowongans as $index => $lowongan)
            <div class="lowongan-card animate-on-scroll fade-scale" style="transition-delay: {{ ($index * 0.1) + 0.1 }}s">
                <div class="lowongan-card-header">
                    <div class="lowongan-logo-wrap">
                        <div class="lowongan-logo">
                            @if($lowongan->prodi && $lowongan->prodi->logo)
                                <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}">
                            @else
                                <div class="lowongan-logo-placeholder">
                                    {{ substr($lowongan->nama_posisi, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="lowongan-info-wrap">
                        <div class="lowongan-nama">{{ $lowongan->nama_posisi }}</div>
                        <div class="lowongan-prodi">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</div>
                    </div>
                </div>

                <div class="lowongan-meta">
                    <div class="lowongan-meta-row">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        <span>Surabaya</span>
                    </div>
                    <div class="lowongan-meta-row">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $lowongan->tanggal_tutup->format('j F Y') }}</span>
                    </div>
                </div>

                <div class="lowongan-badges">
                    <span class="lowongan-badge">{{ $lowongan->jenjang_minimal }}</span>
                    <span class="lowongan-badge">IPK > {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                    <span class="lowongan-badge">{{ $lowongan->kuota }} Kuota</span>
                    <span class="lowongan-badge">Full-Time</span>
                </div>

                <div class="lowongan-card-footer">
                    @auth
                        @if(auth()->user()->role === 'pelamar')
                            <a href="{{ route('pelamar.lowongan.show', $lowongan) }}" class="btn-detail">Lihat Detail</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-detail">Lihat Detail</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-detail">Lihat Detail</a>
                    @endauth
                </div>
            </div>
            @empty
            <div class="col-span-full" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <p style="color: #9ca3af; font-size: 1rem;">Belum ada lowongan tersedia saat ini.</p>
            </div>
            @endforelse
        </div>

        {{-- Footer Actions --}}
        @if($lowongans->count() > 0)
        <div class="lowongan-footer-actions animate-on-scroll slide-bottom">
            @auth
                @if(auth()->user()->role === 'pelamar')
                    <a href="{{ route('pelamar.lowongan.index') }}" class="btn-selengkapnya">
                        Lihat Semua Lowongan
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-selengkapnya">
                        Lihat Semua Lowongan
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-selengkapnya">
                    Lihat Semua Lowongan
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @endauth
        </div>
        @endif
    </section>

    {{-- ========================= FOOTER ========================= --}}
    <footer id="footer">
        <p>&copy; {{ date('Y') }} Telkom University — Sistem Rekrutmen Dosen. All rights reserved.</p>
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

        // Observe semua elemen dengan class animate-on-scroll
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
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
</body>
</html>
