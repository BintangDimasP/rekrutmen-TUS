<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi — Rekrutmen Telkom University</title>

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

        @keyframes toast-in { from { opacity:0; transform: translateY(-12px); } to { opacity:1; transform: translateY(0); } }
        .toast { animation: toast-in 0.3s ease forwards; }
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
                <div class="w-20 h-20 rounded-full border border-white/30 flex items-center justify-center mb-8 bg-white/5 backdrop-blur-sm shadow-sm">
                    <img src="{{ asset('images/logo3.png') }}" alt="Telkom University Logo" class="w-full h-10 object-contain">
                </div>

                <h1 class="text-3xl font-bold mb-1 tracking-tight">Lupa</h1>
                <h2 class="text-[2.2rem] font-serif-italic mb-6 text-white font-medium italic">Kata Sandi?</h2>

                <p class="text-white/85 text-[15px] mb-2 leading-relaxed font-light px-2">
                    Tenang, kami akan kirimkan kode verifikasi ke email Anda untuk mereset password.
                </p>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white relative">
            <div class="w-full max-w-[420px]">
                <a href="{{ route('login') }}" class="flex items-center text-gray-500 hover:text-gray-900 transition gap-2 mb-8 text-sm font-medium w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Login
                </a>

                {{-- Stepper --}}
                <div class="flex items-center gap-2 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-[#b91c1c] text-white flex items-center justify-center text-xs font-bold">1</div>
                        <span class="text-xs font-semibold text-gray-900">Email</span>
                    </div>
                    <div class="flex-1 h-[2px] bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold">2</div>
                        <span class="text-xs font-medium text-gray-400">OTP</span>
                    </div>
                    <div class="flex-1 h-[2px] bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold">3</div>
                        <span class="text-xs font-medium text-gray-400">Reset</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Masukkan Email</h2>
                    <p class="text-gray-500 text-[15px]">Kami akan mengirim kode OTP ke email yang terdaftar.</p>
                </div>

                {{-- Toast error --}}
                @if($errors->has('email'))
                    <div class="toast mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                <form action="{{ route('password.otp.send') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-[11px] font-bold text-gray-600 tracking-wider uppercase mb-2">Email</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                                   class="w-full px-4 py-3.5 rounded-lg border border-gray-200 focus:border-[#b91c1c] focus:ring-1 focus:ring-[#b91c1c] outline-none transition-colors pr-12 text-sm placeholder-gray-400 bg-gray-50/50 focus:bg-white">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#b91c1c] hover:bg-[#991b1b] text-white font-bold py-3.5 px-4 rounded-lg transition-colors shadow-lg shadow-red-700/20 mt-2">
                        Kirim Kode OTP
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    Ingat password Anda?
                    <a href="{{ route('login') }}" class="text-[#b91c1c] font-semibold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
