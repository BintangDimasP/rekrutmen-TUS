<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tidak Dapat Mereset Password — Rekrutmen Telkom University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif-italic { font-family: 'Playfair Display', serif; font-style: italic; }
        .blob { position: absolute; border-radius: 50%; background: rgba(0, 0, 0, 0.05); z-index: 0; pointer-events: none; }
        .blob-1 { width: 700px; height: 700px; top: -300px; right: -250px; }
        .blob-2 { width: 500px; height: 500px; bottom: -200px; left: -150px; }
        .blob-3 { width: 300px; height: 300px; bottom: 10%; right: -50px; background: rgba(0,0,0,0.03); }
    </style>
</head>
<body class="bg-white text-gray-900">
@include('partials.loading-screen')
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- LEFT --}}
        <div class="w-full lg:w-[45%] bg-gradient-to-br from-[#b91c1c] to-[#8b1515] text-white flex flex-col items-center justify-center p-12 lg:p-16 relative overflow-hidden min-h-[40vh] lg:min-h-screen">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>

            <a href="{{ url('/') }}" class="absolute top-8 left-8 flex items-center text-white/80 hover:text-white transition gap-2 z-10 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Beranda
            </a>

            <div class="z-10 flex flex-col items-center max-w-[360px] text-center w-full">
                <div class="mb-14 flex items-center justify-center gap-2.5">
                    <img src="{{ asset('storage/images/logo2.png') }}" alt="Telkom University Logo" class="w-full h-10 object-contain">
                </div>

                <div class="w-20 h-20 rounded-full border border-white/30 flex items-center justify-center mb-8 bg-white/5 backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold mb-1 tracking-tight">Akses</h1>
                <h2 class="text-[2.2rem] font-serif-italic mb-6 text-white font-medium italic">Dibatasi</h2>

                <p class="text-white/85 text-[15px] mb-2 leading-relaxed font-light px-2">
                    Fitur reset password mandiri hanya tersedia untuk akun pelamar.
                </p>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white relative">
            <div class="w-full max-w-[420px]">

                {{-- Icon --}}
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-[#b91c1c]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Mohon Maaf</h3>

                <p class="text-gray-600 text-center text-[15px] leading-relaxed mb-8">
                    Aktivitas ini tidak dapat dilakukan untuk akun Anda. Silakan menghubungi <span class="font-semibold text-gray-800">pihak SDM</span> untuk melakukan perubahan password.
                </p>

                {{-- Action --}}
                <a href="{{ route('login') }}"
                   class="w-full flex items-center justify-center gap-2 bg-[#b91c1c] hover:bg-red-800 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-red-200/50 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Login
                </a>
            </div>
        </div>

    </div>
</body>
</html>
