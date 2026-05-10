<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Rekrutmen Telkom University</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif-italic { font-family: 'Playfair Display', serif; font-style: italic; }
        
        /* Custom blobs for left background */
        .blob {
            position: absolute;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05); /* very subtle dark red/black */
            z-index: 0;
            pointer-events: none;
        }
        .blob-1 { width: 700px; height: 700px; top: -300px; right: -250px; }
        .blob-2 { width: 500px; height: 500px; bottom: -200px; left: -150px; }
        .blob-3 { width: 300px; height: 300px; bottom: 10%; right: -50px; background: rgba(0,0,0,0.03); }
        
        /* For Tailwind customization specific to the design */
        .bg-red-700 { background-color: #b91c1c; }
        .text-red-700 { color: #b91c1c; }
        .focus\:border-red-700:focus { border-color: #b91c1c; }
        .focus\:ring-red-700:focus { --tw-ring-color: #b91c1c; }

        /* Toast */
        #toast-container {
            position: fixed; top: 1.25rem; right: 1.25rem;
            z-index: 9999; display: flex; flex-direction: column; gap: 0.625rem;
            width: 360px; pointer-events: none;
        }
        .toast {
            pointer-events: all;
            display: flex; position: relative; overflow: hidden;
            background: white; border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            animation: slideIn .3s ease forwards;
            padding: 1.25rem 1rem 1.25rem 4rem;
        }
        .toast::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 6px;
        }
        .toast.removing { animation: slideOut .3s ease forwards; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(60px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(60px); }
        }
        
        .toast-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        
        .toast-success::before { background: #22c55e; }
        .toast-success .toast-icon { background: #22c55e; }
        
        .toast-error::before { background: #ef4444; }
        .toast-error .toast-icon { background: #ef4444; }
        
        .toast-warning::before { background: #facc15; }
        .toast-warning .toast-icon { background: #facc15; }
        
        .toast-info::before { background: #3b82f6; }
        .toast-info .toast-icon { background: #3b82f6; }

        .toast-content { flex: 1; }
        .toast-title { font-weight: 700; color: #111827; font-size: 0.95rem; margin-bottom: 0.2rem; line-height: 1.2; }
        .toast-message { color: #6b7280; font-size: 0.8rem; line-height: 1.3; }
        
        .toast-close {
            position: absolute; right: 0.75rem; top: 0.75rem;
            cursor: pointer; opacity: 0.4;
            background: none; border: none; font-size: 1.2rem;
            line-height: 1; padding: 0; color: #1f2937; flex-shrink: 0;
            transition: opacity 0.2s;
        }
        .toast-close:hover { opacity: 1; }
    </style>
</head>
<body class="bg-white flex min-h-screen text-gray-900">

    {{-- ── Toast Container ── --}}
    <div id="toast-container"></div>

    {{-- LEFT SECTION --}}
    <div class="relative hidden lg:flex flex-col w-[45%] bg-[#b91c1c] text-white overflow-hidden p-10 justify-center items-center">
        <!-- Blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        
        <!-- Back Button -->
        <a href="{{ url('/') }}" class="absolute top-8 left-8 flex items-center text-white/80 hover:text-white transition gap-2 z-10 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Beranda
        </a>

        <!-- Content -->
        <div class="z-10 flex flex-col items-center max-w-[360px] text-center w-full">
            <!-- Logo area -->
            <div class="mb-14 flex items-center justify-center gap-2.5">
                <!-- White custom icon as logo -->
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="24" height="24" rx="6" fill="white"/>
                    <path d="M12 4L4 8L12 12L20 8L12 4Z" fill="#b91c1c"/>
                    <path d="M4 11V16L12 20L20 16V11L12 15L4 11Z" fill="#b91c1c"/>
                </svg>
                <div class="text-left leading-tight">
                    <div class="font-bold text-[1.4rem]">Telkom</div>
                    <div class="text-[0.7rem] tracking-widest font-medium uppercase mt-0.5">University</div>
                </div>
            </div>

            <!-- Focus Icon Circle -->
            <div class="w-20 h-20 rounded-full border border-white/30 flex items-center justify-center mb-8 bg-white/5 backdrop-blur-sm shadow-sm">
                <!-- Open book icon -->
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold mb-1 tracking-tight">Halo, Selamat</h1>
            <h2 class="text-[2.2rem] font-serif-italic mb-6 text-white font-medium italic">Datang!</h2>
            
            <p class="text-white/85 text-[15px] mb-8 leading-relaxed font-light px-2">
                Belum punya akun? Daftarkan diri Anda dan mulai proses lamaran sekarang.
            </p>

            <a href="{{ route('register') }}" class="border border-white hover:bg-white hover:text-[#b91c1c] text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 w-full sm:w-auto shadow-sm">
                Daftar Sekarang
            </a>
        </div>
    </div>

    {{-- RIGHT SECTION --}}
    <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white relative">
        <div class="w-full max-w-[420px]">
            <!-- Mobile Back Button -->
            <a href="{{ url('/') }}" class="lg:hidden flex items-center text-gray-500 hover:text-gray-900 transition gap-2 mb-8 text-sm font-medium w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Beranda
            </a>

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Masuk</h2>
                <p class="text-gray-500 text-[15px]">Masuk ke akun pelamar Anda</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="email" class="block text-[11px] font-bold text-gray-600 tracking-wider uppercase mb-2">Email</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" placeholder="nama@email.com" class="w-full px-4 py-3.5 rounded-lg border border-gray-200 focus:border-[#b91c1c] focus:ring-1 focus:ring-[#b91c1c] outline-none transition-colors pr-12 text-sm placeholder-gray-400 bg-gray-50/50 focus:bg-white">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <!-- Envelope icon -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-[11px] font-bold text-gray-600 tracking-wider uppercase mb-2">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" placeholder="••••••••" class="w-full px-4 py-3.5 rounded-lg border border-gray-200 focus:border-[#b91c1c] focus:ring-1 focus:ring-[#b91c1c] outline-none transition-colors pr-12 text-sm placeholder-gray-400 bg-gray-50/50 focus:bg-white">
                        <button type="button" id="toggle-password" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <!-- Eye icon -->
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm py-1">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-600 group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#b91c1c] focus:ring-[#b91c1c]">
                        <span class="group-hover:text-gray-800 transition-colors text-[14px]">Ingat saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-[#b91c1c] hover:underline font-medium text-[14px]">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="w-full bg-[#b91c1c] hover:bg-[#991b1b] text-white font-bold py-3.5 px-4 rounded-lg transition-colors shadow-lg shadow-red-700/20 mt-2">
                    Masuk
                </button>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-4 text-gray-400">atau masuk dengan</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <button type="button" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-gray-200 rounded-lg text-[14px] font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200 outline-none">
                        <!-- Google Logo SVG -->
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            <path d="M1 1h22v22H1z" fill="none"/>
                        </svg>
                        Google
                    </button>
                    <button type="button" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-gray-200 rounded-lg text-[14px] font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200 outline-none">
                        <!-- Facebook Logo SVG -->
                        <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Password Toggle ──────────────────────────────────
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
            }
        }

        // ── Toast System ─────────────────────────────────────
        function showToast(title, message, type = 'error', duration = 4000) {
            const container = document.getElementById('toast-container');
            const icons = {
                success: `<svg class="w-4 h-4 stroke-[3px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
                error:   `<svg class="w-4 h-4 stroke-[3px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
                info:    `<svg class="w-4 h-4 stroke-[3px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
                warning: `<span class="font-bold text-sm">!</span>`
            };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon">${icons[type]}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="toast-close" onclick="removeToast(this.parentElement)">&#x2715;</button>
            `;
            container.appendChild(toast);
            setTimeout(() => removeToast(toast), duration);
        }

        function removeToast(toast) {
            if (!toast || !toast.parentElement) return;
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        }

        // Show session and validation errors
        document.addEventListener("DOMContentLoaded", () => {
            @if (session('status'))
                showToast('Berhasil', "{{ session('status') }}", 'success');
            @endif

            @if (session('error'))
                showToast('Gagal Masuk', "{{ session('error') }}", 'error');
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    // Escape single quotes for JS safety
                    showToast('Gagal Masuk', '{{ addslashes($error) }}', 'error');
                @endforeach
            @endif
        });
    </script>
</body>
</html>
