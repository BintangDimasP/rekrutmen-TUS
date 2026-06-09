<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Rekrutmen Telkom University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
@include('partials.loading-screen')

    <div class="max-w-4xl w-full flex flex-col md:flex-row items-center gap-8 md:gap-16">

        {{-- Ilustrasi Robot --}}
        <div class="flex-shrink-0 w-64 md:w-80 relative">
            <svg viewBox="0 0 400 450" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                {{-- Gear background --}}
                <circle cx="60" cy="180" r="30" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="2"/>
                <circle cx="60" cy="180" r="12" fill="#f9fafb"/>
                <circle cx="340" cy="100" r="20" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="2"/>
                <circle cx="340" cy="100" r="8" fill="#f9fafb"/>

                {{-- Plant --}}
                <path d="M310 420 Q310 390 320 370 Q325 380 330 390" stroke="#9ca3af" stroke-width="2" fill="none"/>
                <ellipse cx="320" cy="365" rx="8" ry="12" fill="#d1d5db"/>
                <ellipse cx="312" cy="375" rx="6" ry="10" fill="#e5e7eb"/>
                <rect x="305" y="420" width="30" height="25" rx="4" fill="#e5e7eb" stroke="#d1d5db" stroke-width="1"/>

                {{-- Robot body --}}
                <rect x="130" y="200" width="140" height="130" rx="16" fill="#dc2626"/>
                <rect x="135" y="205" width="130" height="120" rx="12" fill="#ef4444"/>

                {{-- Robot face/meter --}}
                <circle cx="200" cy="260" r="35" fill="#fef2f2" stroke="#fca5a5" stroke-width="3"/>
                <path d="M180 260 L200 240 L205 255" stroke="#dc2626" stroke-width="3" stroke-linecap="round" fill="none"/>
                <circle cx="200" cy="260" r="5" fill="#dc2626"/>

                {{-- Robot eyes --}}
                <circle cx="170" cy="225" r="6" fill="#1f2937"/>
                <circle cx="230" cy="225" r="6" fill="#1f2937"/>
                <circle cx="172" cy="223" r="2" fill="white"/>
                <circle cx="232" cy="223" r="2" fill="white"/>

                {{-- Robot antenna --}}
                <line x1="200" y1="200" x2="200" y2="170" stroke="#6b7280" stroke-width="3"/>
                <circle cx="200" cy="165" r="6" fill="#dc2626"/>

                {{-- Robot arms --}}
                <path d="M130 240 Q100 250 90 280 Q85 300 100 310" stroke="#dc2626" stroke-width="12" stroke-linecap="round" fill="none"/>
                <path d="M270 240 Q300 250 310 280 Q315 300 300 310" stroke="#dc2626" stroke-width="12" stroke-linecap="round" fill="none"/>

                {{-- Robot legs --}}
                <rect x="160" y="330" width="25" height="50" rx="8" fill="#dc2626"/>
                <rect x="215" y="330" width="25" height="50" rx="8" fill="#dc2626"/>
                <rect x="155" y="370" width="35" height="20" rx="6" fill="#991b1b"/>
                <rect x="210" y="370" width="35" height="20" rx="6" fill="#991b1b"/>

                {{-- Speech bubble --}}
                <rect x="100" y="40" width="200" height="90" rx="16" fill="white" stroke="#e5e7eb" stroke-width="2"/>
                <polygon points="200,130 210,140 220,130" fill="white" stroke="#e5e7eb" stroke-width="2"/>
                <rect x="198" y="128" width="24" height="4" fill="white"/>

                <text x="200" y="75" text-anchor="middle" font-size="22" font-weight="800" fill="#1f2937">Oops!</text>
                <text x="200" y="105" text-anchor="middle" font-size="16" font-weight="600" fill="#dc2626">@yield('code') Error</text>

                {{-- Sparks/bolts --}}
                <path d="M140 185 L135 175 L145 178 L142 168" stroke="#f59e0b" stroke-width="2" fill="none" stroke-linecap="round"/>
                <path d="M260 185 L265 175 L255 178 L258 168" stroke="#f59e0b" stroke-width="2" fill="none" stroke-linecap="round"/>

                {{-- Small screws/parts flying --}}
                <circle cx="120" cy="150" r="4" fill="#d1d5db"/>
                <circle cx="290" cy="155" r="3" fill="#d1d5db"/>
                <rect x="135" y="155" width="8" height="3" rx="1" fill="#9ca3af" transform="rotate(-20 139 156)"/>
            </svg>
        </div>

        {{-- Konten teks --}}
        <div class="text-center md:text-left">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">@yield('heading')</h1>
            <p class="text-gray-600 text-lg mb-8 leading-relaxed max-w-md">@yield('message')</p>

            <div class="flex flex-col sm:flex-row items-center gap-4">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-[#dc2626] hover:bg-[#b91c1c] text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-red-200/50 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
                @auth
                <a href="{{ url('/dashboard') }}" class="text-[#dc2626] font-semibold text-sm hover:underline">
                    Ke Dashboard
                </a>
                @endauth
            </div>
        </div>

    </div>

</body>
</html>
