<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Area') — Rekrutmen Telkom University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Custom sidebar scrollbar */
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        aside::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

        /* Active nav glow */
        .nav-active {
            background: rgba(255,255,255,0.18) !important;
            box-shadow: inset 3px 0 0 #fff;
        }

        /* Transition for sidebar collapse on desktop */
        .sidebar-wide  { width: 260px; }
        .sidebar-mini  { width: 72px; }
        aside.collapsed .nav-label { display: none; }
        aside.collapsed .logo-text { display: none; }
        aside.collapsed .user-info { display: none; }
        aside.collapsed .section-title { display: none; }
        aside.collapsed { width: 72px; }
        aside { transition: width 0.3s ease; }
    </style>
</head>

<body class="bg-[#f4f6f9] text-gray-900 overflow-hidden"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: false,
          showLogoutModal: false,
          notifOpen: false,
          notifList: [],
          belumDibaca: 0,
          notifLoading: false,
          async fetchNotif() {
              this.notifLoading = true;
              try {
                  const res = await fetch('{{ route('notifikasi.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                  const data = await res.json();
                  this.notifList = data.notifikasis;
                  this.belumDibaca = data.belum_dibaca;
              } catch(e) {}
              this.notifLoading = false;
          },
          async toggleNotif() {
              this.notifOpen = !this.notifOpen;
              if (this.notifOpen) await this.fetchNotif();
          },
          async bacaSemua() {
              await fetch('{{ route('notifikasi.baca.semua') }}', {
                  method: 'POST',
                  headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
              });
              this.notifList = this.notifList.map(n => ({ ...n, dibaca: true }));
              this.belumDibaca = 0;
          },
          formatTgl(str) {
              const d = new Date(str);
              return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
          }
      }"
      x-init="fetchNotif()">
<div class="flex h-screen overflow-hidden">

    {{-- ── BACKDROP (Mobile) ── --}}
    <div x-show="sidebarOpen"
         x-transition.opacity
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         @click="sidebarOpen = false">
    </div>

    {{-- ── SIDEBAR ── --}}
    <aside :class="[
               sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
               sidebarCollapsed ? 'lg:w-[72px]' : 'lg:w-[260px]'
           ]"
           class="fixed inset-y-0 left-0 z-30 w-[260px] flex flex-col overflow-y-auto overflow-x-hidden transition-all duration-300
                  bg-gradient-to-b from-[#7a1111] via-[#8b1515] to-[#6e1010]
                  lg:static lg:inset-0 shadow-xl">

        {{-- Logo --}}
        <div class="px-6 py-6 border-b border-white/10 flex justify-center lg:justify-start">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Telkom University" class="h-10 w-auto logo-text" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                {{-- Fallback or abbreviated logo for collapsed state --}}
                <div x-show="sidebarCollapsed" class="hidden lg:flex items-center justify-center w-full">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="flex-shrink-0">
                        <rect width="24" height="24" rx="5" fill="white"/>
                        <path d="M12 4L4 8L12 12L20 8L12 4Z" fill="#b91c1c"/>
                        <path d="M4 11V16L12 20L20 16V11L12 15L4 11Z" fill="#b91c1c"/>
                    </svg>
                </div>
            </a>
        </div>

        {{-- User Card --}}
        <div class="px-4 py-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                {{-- Avatar --}}
                <div class="w-11 h-11 rounded-full bg-white/20 flex-shrink-0 flex items-center justify-center text-white font-bold text-base ring-2 ring-white/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="user-info overflow-hidden" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="text-[0.7rem] text-white/70 font-medium tracking-wide uppercase mb-0.5">
                        {{ auth()->user()->role ?? 'Admin' }}
                    </p>
                    <p class="text-white font-bold text-[0.95rem] truncate leading-tight mb-0.5">
                        {{ auth()->user()->name ?? 'Nama User' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5">

            @php
                $role = auth()->user()->role ?? 'admin';
                $menus = [
                    'admin' => [
                        ['label' => 'Dashboard',        'route' => 'admin.dashboard',   'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Manajemen User',   'route' => 'admin.user.index',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => 'Prodi',            'route' => 'admin.prodi.index',  'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
                        ['label' => 'Penguji',          'route' => 'admin.penguji.index',                 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['label' => 'Jadwal Seleksi',   'route' => 'admin.jadwal.index',                  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['label' => 'Lowongan',         'route' => 'admin.lowongan.index',                 'icon' => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z'],
                        ['label' => 'Manajemen Pelamar','route' => 'admin.pelamar.index',                 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                    ],
                    'pelamar' => [
                        ['label' => 'Dashboard',        'route' => 'pelamar.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Profil Saya',      'route' => 'pelamar.profil.index',  'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['label' => 'Cari Lowongan',    'route' => 'pelamar.lowongan.index',  'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                        ['label' => 'Status Lamaran',   'route' => 'pelamar.history.index',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                    'penguji' => [
                        ['label' => 'Dashboard',        'route' => 'penguji.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Pengujian',        'route' => 'penguji.pengujian.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ],
                    'kaprodi' => [
                        ['label' => 'Dashboard',        'route' => 'kaprodi.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ],
                ];
                $currentMenus = $menus[$role] ?? $menus['admin'];
            @endphp

            @foreach($currentMenus as $item)
                @php
                    $isActive = $item['route'] !== '#' && request()->routeIs($item['route']);
                    $href = $item['route'] !== '#' ? route($item['route']) : '#';
                @endphp
                <a href="{{ $href }}"
                   class="group flex items-center gap-3.5 px-3 py-2.5 rounded-lg text-[0.875rem] font-medium text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 relative {{ $isActive ? 'nav-active text-white bg-white/15' : '' }}">
                    <svg class="w-[18px] h-[18px] flex-shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span class="nav-label whitespace-nowrap" :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex flex-col flex-1 h-screen overflow-hidden min-w-0">

        {{-- TOP NAV --}}
        <header class="flex-shrink-0 flex items-center justify-between h-[65px] px-6 bg-white border-b border-gray-200/80 shadow-sm z-10">
            <div class="flex items-center gap-4">
                {{-- Hamburger (mobile → open, desktop → collapse) --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-500 hover:text-[#8b1515] transition-colors lg:hidden focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                        class="hidden lg:flex text-gray-500 hover:text-[#8b1515] transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Page Title --}}
                <div>
                    <h1 class="text-[1.1rem] font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
                </div>
            </div>

            {{-- Top Right Area --}}
            <div class="flex items-center gap-3">

                {{-- Bell Icon --}}
                <div class="relative" @click.outside="notifOpen = false">
                    <button @click="toggleNotif()" class="relative w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#8b1515] transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        {{-- Badge --}}
                        <span x-show="belumDibaca > 0" x-text="belumDibaca > 9 ? '9+' : belumDibaca"
                              class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-[#8b1515] text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5 leading-none"></span>
                    </button>

                    {{-- Notification Dropdown --}}
                    <div x-show="notifOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="display:none;"
                         class="absolute right-0 top-12 w-80 bg-white border border-gray-100 rounded-2xl shadow-2xl z-[80] overflow-hidden">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                            <div>
                                <p class="text-sm font-black text-gray-800 uppercase tracking-widest">Kotak Pesan</p>
                                <p x-show="belumDibaca > 0" x-text="belumDibaca + ' pesan belum dibaca'" class="text-[0.65rem] font-semibold text-[#8b1515]"></p>
                            </div>
                            <button @click="bacaSemua()" x-show="belumDibaca > 0"
                                    class="text-[0.65rem] font-bold text-gray-400 hover:text-[#8b1515] uppercase tracking-wider transition-colors">
                                Tandai semua dibaca
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                            <template x-if="notifLoading">
                                <div class="px-4 py-6 text-center text-sm text-gray-400">Memuat pesan...</div>
                            </template>
                            <template x-if="!notifLoading && notifList.length === 0">
                                <div class="px-4 py-8 text-center">
                                    <p class="text-sm font-medium text-gray-500">Belum ada pesan masuk.</p>
                                    <p class="text-xs text-gray-400 mt-1">Notifikasi akan muncul di sini.</p>
                                </div>
                            </template>
                            <template x-for="notif in notifList" :key="notif.id">
                                <div :class="notif.dibaca ? 'bg-white' : 'bg-red-50/40'" class="px-4 py-3 hover:bg-gray-50 transition-colors cursor-default">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-black text-gray-800 leading-tight" :class="!notif.dibaca ? 'text-[#8b1515]' : ''" x-text="notif.judul"></p>
                                            <p class="text-xs text-gray-600 mt-0.5 leading-relaxed" x-text="notif.pesan"></p>
                                            <p class="text-[0.6rem] text-gray-400 mt-1 font-medium" x-text="formatTgl(notif.created_at)"></p>
                                        </div>
                                        <span x-show="!notif.dibaca" class="w-2 h-2 bg-[#8b1515] rounded-full flex-shrink-0 mt-1"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Logout Button --}}
                <button type="button" @click="showLogoutModal = true" class="bg-[#8b1515] hover:bg-red-800 text-white font-medium text-sm px-6 py-2 rounded-md transition shadow-sm">
                    Log Out
                </button>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto px-7 py-7">
            @yield('content')
        </main>
    </div>

</div>
    {{-- Logout Modal --}}
    <div x-show="showLogoutModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="showLogoutModal = false">
        <div x-show="showLogoutModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
             
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#8b1515]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            
            <h2 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi</h2>
            <p class="text-sm text-gray-500 mb-6">Yakin untuk keluar?</p>

            <div class="flex justify-center gap-3">
                <button type="button" @click="showLogoutModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors w-full">Batal</button>
                <form method="POST" action="{{ route('logout') }}" class="w-full m-0">
                    @csrf
                    <button type="submit" class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Keluar</button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
