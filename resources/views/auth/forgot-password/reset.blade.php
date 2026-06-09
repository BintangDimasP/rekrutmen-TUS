<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — Rekrutmen Telkom University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif-italic { font-family: 'Playfair Display', serif; font-style: italic; }
        .blob { position: absolute; border-radius: 50%; background: rgba(0,0,0,0.05); z-index: 0; pointer-events: none; }
        .blob-1 { width: 700px; height: 700px; top: -300px; right: -250px; }
        .blob-2 { width: 500px; height: 500px; bottom: -200px; left: -150px; }
        .blob-3 { width: 300px; height: 300px; bottom: 10%; right: -50px; background: rgba(0,0,0,0.03); }

        /* Toast slide-in dari kanan atas */
        #toast-wrap {
            position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999;
            display: flex; flex-direction: column; gap: 0.5rem;
            width: 340px; pointer-events: none;
        }
        .fp-toast {
            pointer-events: all;
            display: flex; align-items: flex-start; gap: 10px;
            background: #fff; border-radius: 10px; padding: 14px 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            border-left: 4px solid #ef4444;
            animation: fp-in .3s ease forwards;
        }
        .fp-toast.success { border-left-color: #22c55e; }
        .fp-toast.removing { animation: fp-out .3s ease forwards; }
        @keyframes fp-in  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
        @keyframes fp-out { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(40px); } }
        .fp-toast-msg { font-size: 13px; color: #374151; line-height: 1.4; flex: 1; }
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

            <div class="z-10 flex flex-col items-center max-w-[360px] text-center w-full">
                <div class="mb-14">
                    <img src="{{ asset('storage/images/logo2.png') }}" alt="Telkom University Logo" class="h-10 object-contain">
                </div>
                <div class="w-20 h-20 rounded-full border border-white/30 flex items-center justify-center mb-8 bg-white/5 backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-1 tracking-tight">Buat</h1>
                <h2 class="text-[2.2rem] font-serif-italic mb-6 font-medium italic">Password Baru</h2>
                <p class="text-white/85 text-[15px] leading-relaxed font-light px-2">
                    Kode terverifikasi. Silakan tentukan password baru untuk akun Anda.
                </p>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white">
            <div class="w-full max-w-[420px]">

                {{-- Stepper --}}
                <div class="flex items-center gap-2 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500">Email</span>
                    </div>
                    <div class="flex-1 h-[2px] bg-green-500"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500">OTP</span>
                    </div>
                    <div class="flex-1 h-[2px] bg-[#b91c1c]"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-[#b91c1c] text-white flex items-center justify-center text-xs font-bold">3</div>
                        <span class="text-xs font-semibold text-gray-900">Reset</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Password Baru</h2>
                    <p class="text-gray-500 text-[15px]">Akun: <strong class="text-gray-900">{{ $email }}</strong></p>
                </div>

                <form action="{{ route('password.otp.reset') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="password" class="block text-[11px] font-bold text-gray-600 tracking-wider uppercase mb-2">Password Baru</label>
                        <div class="relative">
                            <input id="password" type="password" name="password"
                                   value="{{ old('password') }}"
                                   placeholder="Minimal 8 karakter" required
                                   class="w-full px-4 py-3.5 rounded-lg border border-gray-200 focus:border-[#b91c1c] focus:ring-1 focus:ring-[#b91c1c] outline-none transition-colors pr-12 text-sm placeholder-gray-400 bg-gray-50/50 focus:bg-white">
                            <button type="button" onclick="toggleVis('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-bold text-gray-600 tracking-wider uppercase mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   value="{{ old('password_confirmation') }}"
                                   placeholder="Ulangi password baru" required
                                   class="w-full px-4 py-3.5 rounded-lg border border-gray-200 focus:border-[#b91c1c] focus:ring-1 focus:ring-[#b91c1c] outline-none transition-colors pr-12 text-sm placeholder-gray-400 bg-gray-50/50 focus:bg-white">
                            <button type="button" onclick="toggleVis('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#b91c1c] hover:bg-[#991b1b] text-white font-bold py-3.5 px-4 rounded-lg transition-colors shadow-lg shadow-red-700/20 mt-2">
                        Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleVis(id) {
            const el = document.getElementById(id);
            el.type = el.type === 'password' ? 'text' : 'password';
        }

        function showToast(msg, type = 'error') {
            const wrap = document.getElementById('toast-wrap');
            const t = document.createElement('div');
            t.className = 'fp-toast' + (type === 'success' ? ' success' : '');
            t.innerHTML = `<span class="fp-toast-msg">${msg}</span>`;
            wrap.appendChild(t);
            setTimeout(() => { t.classList.add('removing'); setTimeout(() => t.remove(), 300); }, 4000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            @foreach ($errors->all() as $error)
                showToast('{{ addslashes($error) }}', 'error');
            @endforeach
        });
    </script>
</body>
</html>
