<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi OTP — Rekrutmen Telkom University</title>

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

        /* OTP input boxes */
        .otp-box {
            width: 52px; height: 60px; text-align: center; font-size: 22px; font-weight: 700;
            border: 1.5px solid #e5e7eb; border-radius: 10px; background: #f9fafb;
            transition: border-color 0.2s, background 0.2s, transform 0.1s;
            font-family: 'Inter', monospace; color: #1f2937;
        }
        .otp-box:focus { outline: none; border-color: #b91c1c; background: #fff; box-shadow: 0 0 0 2px rgba(185,28,28,0.1); }
        .otp-box.filled { border-color: #b91c1c; background: #fff; }

        /* Countdown clock */
        .clock-ring { transition: stroke-dashoffset 1s linear; }
        #countdown-wrap.expired .clock-ring { stroke: #ef4444; }
        #countdown-wrap.expired #countdown-text { color: #ef4444; }
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

            <a href="{{ route('password.otp.email') }}" class="absolute top-8 left-8 flex items-center text-white/80 hover:text-white transition gap-2 z-10 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>

            <div class="z-10 flex flex-col items-center max-w-[360px] text-center w-full">
                <div class="mb-14 flex items-center justify-center gap-2.5">
                    <img src="{{ asset('images/logo2.png') }}" alt="Telkom University Logo" class="w-full h-10 object-contain">
                </div>

                <div class="w-20 h-20 rounded-full border border-white/30 flex items-center justify-center mb-8 bg-white/5 backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold mb-1 tracking-tight">Cek</h1>
                <h2 class="text-[2.2rem] font-serif-italic mb-6 text-white font-medium italic">Email Anda</h2>

                <p class="text-white/85 text-[15px] mb-2 leading-relaxed font-light px-2">
                    Kode OTP 6 digit telah dikirim. Masukkan kode tersebut untuk melanjutkan reset password.
                </p>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12 lg:p-24 bg-white relative">
            <div class="w-full max-w-[420px]">
                <a href="{{ route('password.otp.email') }}" class="flex items-center text-gray-500 hover:text-gray-900 transition gap-2 mb-8 text-sm font-medium w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Ganti Email
                </a>

                {{-- Stepper --}}
                <div class="flex items-center gap-2 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500">Email</span>
                    </div>
                    <div class="flex-1 h-[2px] bg-[#b91c1c]"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-[#b91c1c] text-white flex items-center justify-center text-xs font-bold">2</div>
                        <span class="text-xs font-semibold text-gray-900">OTP</span>
                    </div>
                    <div class="flex-1 h-[2px] bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold">3</div>
                        <span class="text-xs font-medium text-gray-400">Reset</span>
                    </div>
                </div>

                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2 tracking-tight">Masukkan Kode OTP</h2>
                        <p class="text-gray-500 text-[15px]">
                            Kode telah dikirim ke <strong class="text-gray-900">{{ $email }}</strong>
                        </p>
                    </div>

                    {{-- Countdown clock --}}
                    <div id="countdown-wrap" class="flex flex-col items-center gap-1 shrink-0 pt-1">
                        <div class="relative" style="width:40px;height:40px;">
                            <svg width="40" height="40" viewBox="0 0 40 40" class="rotate-[-90deg]">
                                <circle cx="20" cy="20" r="16" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                            <circle id="clock-arc" cx="20" cy="20" r="16" fill="none"
                                        stroke="#b91c1c" stroke-width="3"
                                        stroke-linecap="round"
                                        stroke-dasharray="100.53"
                                        stroke-dashoffset="0"
                                        class="clock-ring"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                                </svg>
                            </div>
                        </div>
                        <span id="countdown-text" class="text-xs font-bold text-gray-600 tabular-nums">00:60</span>
                    </div>
                </div>

                @if($errors->has('otp'))
                    <div class="toast mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        <span>{{ $errors->first('otp') }}</span>
                    </div>
                @endif

                <form id="otp-form" action="{{ route('password.otp.verify') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="otp" id="otp-hidden">

                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 tracking-wider uppercase mb-3">Kode OTP (6 digit)</label>
                        <div class="flex items-center justify-between gap-2">
                            @for($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                       class="otp-box" data-otp-index="{{ $i }}" autocomplete="off">
                            @endfor
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#b91c1c] hover:bg-[#991b1b] text-white font-bold py-3.5 px-4 rounded-lg transition-colors shadow-lg shadow-red-700/20">
                        Verifikasi Kode
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-500">
                        Tidak menerima kode?
                        <form action="{{ route('password.otp.send') }}" method="POST" class="inline" id="resend-form">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <button type="submit" id="resend-btn"
                                    class="text-[#b91c1c] font-semibold hover:underline disabled:opacity-40 disabled:cursor-not-allowed disabled:no-underline"
                                    {{ $remainingCooldown > 0 ? 'disabled' : '' }}>
                                Kirim Ulang<span id="resend-timer" class="font-normal text-gray-400"></span>
                            </button>
                        </form>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // OTP input handler — auto-focus next, paste support, submit on complete
        const boxes = document.querySelectorAll('.otp-box');
        const hidden = document.getElementById('otp-hidden');
        const form = document.getElementById('otp-form');

        function syncHidden() {
            hidden.value = Array.from(boxes).map(b => b.value).join('');
        }

        boxes.forEach((box, idx) => {
            box.addEventListener('input', (e) => {
                let val = e.target.value.replace(/\D/g, '');
                e.target.value = val.slice(0, 1);

                if (e.target.value) {
                    e.target.classList.add('filled');
                    if (idx < boxes.length - 1) boxes[idx + 1].focus();
                } else {
                    e.target.classList.remove('filled');
                }

                syncHidden();

                // Auto-submit kalau semua sudah terisi
                if (Array.from(boxes).every(b => b.value)) {
                    form.submit();
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                    boxes[idx - 1].focus();
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                if (paste.length === 0) return;
                paste.split('').forEach((ch, i) => {
                    if (boxes[i]) {
                        boxes[i].value = ch;
                        boxes[i].classList.add('filled');
                    }
                });
                syncHidden();
                if (paste.length === 6) form.submit();
                else if (boxes[paste.length]) boxes[paste.length].focus();
            });
        });

        // Auto-focus pertama
        if (boxes[0]) boxes[0].focus();

        // ── Countdown ──────────────────────────────────────────────
        const TOTAL_SEC     = {{ $remainingCooldown > 0 ? $remainingCooldown : 60 }};
        const circumference = 2 * Math.PI * 16; // r=16 → ~100.53
        const arc           = document.getElementById('clock-arc');
        const countText     = document.getElementById('countdown-text');
        const wrap          = document.getElementById('countdown-wrap');
        const resendBtn     = document.getElementById('resend-btn');
        const resendTimer   = document.getElementById('resend-timer');

        let remaining = Math.floor(TOTAL_SEC);

        function pad(n) { return String(n).padStart(2, '0'); }

        function tick() {
            if (remaining <= 0) {
                countText.textContent = 'Kedaluwarsa';
                countText.style.color = '#ef4444';
                arc.style.stroke = '#ef4444';
                arc.style.strokeDashoffset = circumference;
                wrap.classList.add('expired');
                // Aktifkan tombol Kirim Ulang
                if (resendBtn) {
                    resendBtn.disabled = false;
                    resendTimer.textContent = '';
                }
                return;
            }

            // Update teks jam
            const m = Math.floor(remaining / 60);
            const s = remaining % 60;
            countText.textContent = pad(m) + ':' + pad(s);

            // Update arc — dari penuh ke kosong
            const progress = remaining / TOTAL_SEC;
            arc.style.strokeDashoffset = circumference * (1 - progress);

            if (remaining <= 10) {
                arc.style.stroke = '#ef4444';
                countText.style.color = '#ef4444';
            }

            // Update teks sisa di tombol Kirim Ulang
            if (resendBtn && resendBtn.disabled) {
                resendTimer.textContent = ' (' + pad(m) + ':' + pad(s) + ')';
            }

            remaining--;
            setTimeout(tick, 1000);
        }

        tick();
    </script>
</body>
</html>
