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

        /* ─── NAVBAR ─── */
        #navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            transition: background-color 0.35s ease, box-shadow 0.35s ease;
            background: transparent;
        }
        #navbar.scrolled {
            background: #b91c1c; /* red-700 */
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
            background: #b91c1c;
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
            background: #b91c1c;
            padding: 0.5rem 1.3rem;
            border-radius: 6px;
            transition: background 0.3s, box-shadow 0.3s;
        }
        #navbar.scrolled .btn-daftar { background: #fff; color: #b91c1c; }
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
        }
        .hero-title {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.2;
            color: #111;
            margin-bottom: 0.4rem;
        }
        .hero-title-red { color: #b91c1c; }
        .hero-desc {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.7;
            margin-bottom: 1.8rem;
            max-width: 420px;
        }
        .hero-actions { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
        .btn-hero-primary {
            display: inline-block;
            text-decoration: none;
            background: #b91c1c;
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
        .section-title-red { color: #b91c1c; }
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
        .panduan-card:hover, .panduan-card.active {
            background: #b91c1c;
            border-color: #b91c1c;
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
        .panduan-card:hover .panduan-num,
        .panduan-card.active .panduan-num {
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
        .panduan-card:hover .panduan-card-title,
        .panduan-card.active .panduan-card-title { color: #fff; }

        .panduan-divider {
            height: 2px;
            width: 32px;
            background: #e5e7eb;
            margin-bottom: 0.75rem;
            border-radius: 2px;
            transition: background 0.28s;
        }
        .panduan-card:hover .panduan-divider,
        .panduan-card.active .panduan-divider { background: rgba(255,255,255,0.5); }

        .panduan-card-desc {
            font-size: 0.82rem;
            color: #888;
            line-height: 1.6;
            transition: color 0.28s;
        }
        .panduan-card:hover .panduan-card-desc,
        .panduan-card.active .panduan-card-desc { color: rgba(255,255,255,0.85); }

        /* ─── SECTION LOWONGAN ─── */
        #lowongan {
            padding: 80px 2rem;
            background: #fff;
        }
        .lowongan-header {
            max-width: 1200px;
            margin: 0 auto 2rem;
        }
        .lowongan-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #b91c1c;
        }
        .lowongan-subtitle {
            font-size: 0.9rem;
            color: #777;
            margin-top: 0.2rem;
        }

        .lowongan-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.2rem;
            margin-bottom: 2rem;
        }
        .lowongan-card {
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.3rem 1.3rem 1.1rem;
            background: #fff;
            transition: box-shadow 0.25s, transform 0.2s;
        }
        .lowongan-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.10);
            transform: translateY(-3px);
        }
        .lowongan-card-header {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1rem;
        }
        .lowongan-logo {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #e5e7eb;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .lowongan-logo img { width: 100%; height: 100%; object-fit: cover; }
        .lowongan-logo-placeholder {
            width: 24px;
            height: 24px;
            background: #d1d5db;
            border-radius: 50%;
        }
        .lowongan-nama {
            font-size: 0.92rem;
            font-weight: 700;
            color: #111;
        }
        .lowongan-prodi {
            font-size: 0.78rem;
            color: #888;
        }

        .lowongan-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .lowongan-info-item label {
            display: block;
            font-size: 0.7rem;
            color: #aaa;
            margin-bottom: 0.15rem;
        }
        .lowongan-info-item span {
            font-size: 0.8rem;
            font-weight: 600;
            color: #333;
        }

        .lowongan-card-footer {
            border-top: 1px solid #f0f0f0;
            padding-top: 0.9rem;
        }
        .btn-detail {
            display: block;
            text-decoration: none;
            text-align: center;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            padding: 0.45rem 1rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #333;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
            width: 100%;
        }
        .btn-detail:hover { border-color: #b91c1c; color: #b91c1c; background: #fff5f5; }

        .lowongan-pagination {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .pag-btn {
            width: 34px;
            height: 34px;
            border: 1.5px solid #d1d5db;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.9rem;
            color: #555;
            transition: border-color 0.2s, color 0.2s;
        }
        .pag-btn:hover { border-color: #b91c1c; color: #b91c1c; }
        .btn-selengkapnya {
            display: inline-block;
            text-decoration: none;
            border: 1.5px solid #d1d5db;
            border-radius: 20px;
            padding: 0.4rem 1.5rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #333;
            background: #fff;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-selengkapnya:hover { border-color: #b91c1c; color: #b91c1c; }

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
            .hero-inner { grid-template-columns: 1fr; padding-top: 120px; }
            .hero-image-wrap { justify-content: center; }
            .panduan-grid { grid-template-columns: repeat(2, 1fr); }
            .lowongan-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .panduan-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 2rem; }
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
                        <strong>11+</strong> Total Pendaftar
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= PANDUAN ========================= --}}
    <section id="panduan">
        <div class="section-header">
            <h2 class="section-title">
                <span class="section-title-red">4 Langkah Mudah</span>
            </h2>
            <p class="section-subtitle">Menjadi Bagian Dari Kami</p>
        </div>

        <div class="panduan-grid">
            {{-- Card 1 --}}
            <div class="panduan-card" id="panduan-card-1">
                <div class="panduan-num">1</div>
                <div class="panduan-card-title">Buat Akun</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Daftarkan diri Anda dengan membuat akun baru menggunakan email aktif Anda.</p>
            </div>

            {{-- Card 2 — ditampilkan dalam state hover/aktif --}}
            <div class="panduan-card active" id="panduan-card-2">
                <div class="panduan-num">2</div>
                <div class="panduan-card-title">Daftar & Isi Data Pribadi</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Lengkapi profil dan data pribadi Anda sesuai dengan dokumen yang dimiliki.</p>
            </div>

            {{-- Card 3 --}}
            <div class="panduan-card" id="panduan-card-3">
                <div class="panduan-num">3</div>
                <div class="panduan-card-title">Pilih Posisi Lowongan</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Cari dan pilih posisi dosen yang sesuai dengan bidang keahlian Anda.</p>
            </div>

            {{-- Card 4 --}}
            <div class="panduan-card" id="panduan-card-4">
                <div class="panduan-num">4</div>
                <div class="panduan-card-title">Ajukan Lamaran</div>
                <div class="panduan-divider"></div>
                <p class="panduan-card-desc">Unggah dokumen pendukung dan kirimkan lamaran Anda untuk diproses.</p>
            </div>
        </div>
    </section>

    {{-- ========================= LOWONGAN ========================= --}}
    <section id="lowongan">
        <div class="lowongan-header">
            <h2 class="lowongan-title">Posisi Lowongan Dosen</h2>
            <p class="lowongan-subtitle">Temukan posisi yang sesuai dengan keahlian Anda</p>
        </div>

        <div class="lowongan-grid" id="lowongan-grid">
            @php
                // Safe placeholder data — ganti dengan query database setelah model Lowongan dibuat
                $lowongans = [
                    ['judul' => 'Dosen Teknik Informatika', 'prodi' => 'Teknik Informatika', 'kuota' => 3, 'sisa_kuota' => 2, 'pendidikan_min' => 'S3'],
                    ['judul' => 'Dosen Sistem Informasi', 'prodi' => 'Sistem Informasi', 'kuota' => 2, 'sisa_kuota' => 1, 'pendidikan_min' => 'S3'],
                    ['judul' => 'Dosen Teknik Elektro', 'prodi' => 'Teknik Elektro', 'kuota' => 4, 'sisa_kuota' => 3, 'pendidikan_min' => 'S3'],
                ];
            @endphp

            @foreach($lowongans as $lowongan)
            <div class="lowongan-card">
                <div class="lowongan-card-header">
                    <div class="lowongan-logo">
                        <div class="lowongan-logo-placeholder"></div>
                    </div>
                    <div>
                        <div class="lowongan-nama">{{ $lowongan['judul'] }}</div>
                        <div class="lowongan-prodi">{{ $lowongan['prodi'] }}</div>
                    </div>
                </div>

                <div class="lowongan-info">
                    <div class="lowongan-info-item">
                        <label>Kuota</label>
                        <span>{{ $lowongan['kuota'] }}</span>
                    </div>
                    <div class="lowongan-info-item">
                        <label>Sisa Kuota</label>
                        <span>{{ $lowongan['sisa_kuota'] }}</span>
                    </div>
                    <div class="lowongan-info-item">
                        <label>Pendidikan Min.</label>
                        <span>{{ $lowongan['pendidikan_min'] }}</span>
                    </div>
                </div>

                <div class="lowongan-card-footer">
                    <a href="#" class="btn-detail">Detail</a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="lowongan-pagination">
            <button class="pag-btn" id="pag-prev" aria-label="Sebelumnya">&#8249;</button>
            <a href="#" class="btn-selengkapnya">Selengkapnya</a>
            <button class="pag-btn" id="pag-next" aria-label="Berikutnya">&#8250;</button>
        </div>
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

        // ─── Panduan cards: click to activate ───
        document.querySelectorAll('.panduan-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.panduan-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
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
