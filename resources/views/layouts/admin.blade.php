<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Admin Area') — Rekrutmen Telkom University</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Poppins', sans-serif; }

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
          notifTab: 'all',
          notifList: [],
          belumDibaca: 0,
          notifLoading: false,
          get filteredNotifs() {
              const role = '{{ auth()->user()->role }}';
              const allowed = role === 'admin' ? ['sistem', 'pelamar'] : (role === 'kaprodi' ? ['pelamar'] : null);
              if (this.notifTab === 'all') {
                  return allowed ? this.notifList.filter(n => allowed.includes(n.tipe)) : this.notifList;
              }
              return this.notifList.filter(n => n.tipe === this.notifTab);
          },
          countByTab(tab) {
              const role = '{{ auth()->user()->role }}';
              const allowed = role === 'admin' ? ['sistem', 'pelamar'] : (role === 'kaprodi' ? ['pelamar'] : null);
              const base = allowed ? this.notifList.filter(n => allowed.includes(n.tipe)) : this.notifList;
              const items = tab === 'all' ? base : this.notifList.filter(n => n.tipe === tab);
              return items.filter(n => !n.dibaca).length;
          },
          async fetchNotif() {
              this.notifLoading = true;
              try {
                  const res = await fetch('{{ route('notifikasi.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                  const data = await res.json();
                  this.notifList = data.notifikasis;
                  const roleCheck = '{{ auth()->user()->role }}';
                  if (roleCheck === 'admin') {
                      this.belumDibaca = data.notifikasis.filter(n => (n.tipe === 'sistem' || n.tipe === 'pelamar') && !n.dibaca).length;
                  } else if (roleCheck === 'kaprodi') {
                      this.belumDibaca = data.notifikasis.filter(n => n.tipe === 'pelamar' && !n.dibaca).length;
                  } else {
                      this.belumDibaca = data.belum_dibaca;
                  }
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
              if (!str) return '';
              const d = new Date(str);
              const now = new Date();
              const diff = Math.floor((now - d) / 1000);
              if (diff < 60) return 'Baru saja';
              if (diff < 3600) return Math.floor(diff/60) + ' menit lalu';
              if (diff < 86400) return Math.floor(diff/3600) + ' jam lalu';
              return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
          },
          tipeIcon(tipe) {
              if (tipe === 'jadwal') return 'calendar';
              if (tipe === 'status') return 'badge';
              return 'info';
          }
      }"
      x-init="fetchNotif()">
@include('partials.loading-screen')<div class="flex h-screen overflow-hidden">

    @empty($hideSidebar)
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
        <div class="px-6 py-6 border-b border-white/10 flex justify-center items-center">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center w-full">
                <img src="{{ asset('images/logo.png') }}" alt="Telkom University" class="h-10 w-auto transition-all duration-300" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                {{-- Fallback or abbreviated logo for collapsed state (Masukkan path gambar icon kecil Anda di sini) --}}
                <img x-show="sidebarCollapsed" src="{{ asset('images/logo-icon.png') }}" alt="Icon" class="hidden lg:block h-10 w-auto transition-all duration-300" style="display: none;">
            </a>
        </div>

        {{-- User Card --}}
        @php $isRangkap = auth()->check() && auth()->user()->is_penguji && auth()->user()->is_kaprodi; @endphp
        <div class="px-4 py-5 border-b border-white/10"
             @if($isRangkap) x-data="{ open: false }" @click.outside="open = false" @endif>

            @if($isRangkap)
            @php
                $currentRole = auth()->user()->role;
                $targetRole  = $currentRole === 'kaprodi' ? 'penguji' : 'kaprodi';
                $targetLabel = ucfirst($targetRole);
            @endphp

            {{-- Clickable profile block (dropdown trigger) --}}
            <button @click="open = !open" type="button"
                    class="w-full flex items-center gap-3 text-left rounded-xl px-1 py-1 -mx-1
                           hover:bg-white/8 transition-colors group"
                    :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                <div class="w-11 h-11 rounded-full bg-white/20 flex-shrink-0 flex items-center justify-center text-white font-bold text-base ring-2 ring-white/30 overflow-hidden">
                    @if(auth()->user()->foto_profil_url)
                        <img src="{{ auth()->user()->foto_profil_url }}" alt="Foto Profil" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>
                <div class="user-info flex-1 overflow-hidden min-w-0" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="text-[0.7rem] text-white/70 font-medium tracking-wide uppercase mb-0.5">
                        {{ auth()->user()->role ?? 'Admin' }}
                    </p>
                    <p class="text-white font-bold text-[0.95rem] truncate leading-tight">
                        {{ auth()->user()->name ?? 'Nama User' }}
                    </p>
                </div>
                {{-- Chevron --}}
                <svg class="w-3.5 h-3.5 text-white/40 flex-shrink-0 transition-transform duration-200 group-hover:text-white/60"
                     :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"
                     fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Glass dropdown panel --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                 style="display:none;"
                 class="mt-2 rounded-xl overflow-hidden
                        bg-white/10 backdrop-blur-md border border-white/20 shadow-xl"
                 :class="sidebarCollapsed ? 'lg:hidden' : ''">

                {{-- Current role --}}
                <div class="flex items-center gap-2.5 px-3 py-2.5 cursor-default">
                    <span class="w-1.5 h-1.5 rounded-full bg-white flex-shrink-0"></span>
                    <span class="text-[0.78rem] font-semibold text-white">{{ ucfirst($currentRole) }}</span>
                    <span class="ml-auto text-[0.65rem] text-white/50 font-medium">Aktif</span>
                </div>

                <div class="h-px bg-white/15 mx-3"></div>

                {{-- Switch to other role --}}
                <form method="POST" action="{{ route('role.switch') }}">
                    @csrf
                    <input type="hidden" name="role" value="{{ $targetRole }}">
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5
                                   text-white/70 hover:text-white hover:bg-white/10
                                   text-[0.78rem] font-semibold transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-white/30 flex-shrink-0"></span>
                        <span>Pindah ke {{ $targetLabel }}</span>
                    </button>
                </form>
            </div>

            @else
            {{-- Non-rangkap: profile block biasa, tidak interaktif --}}
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-white/20 flex-shrink-0 flex items-center justify-center text-white font-bold text-base ring-2 ring-white/30 overflow-hidden">
                    @if(auth()->user()->foto_profil_url)
                        <img src="{{ auth()->user()->foto_profil_url }}" alt="Foto Profil" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>
                <div class="user-info overflow-hidden" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <p class="text-[0.7rem] text-white/70 font-medium tracking-wide uppercase mb-0.5">
                        {{ auth()->user()->role ?? 'Admin' }}
                    </p>
                    <p class="text-white font-bold text-[0.95rem] truncate leading-tight">
                        {{ auth()->user()->name ?? 'Nama User' }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5">

            @php
                $role = auth()->user()->role ?? 'admin';
                $menus = [
                    'admin' => [
                        ['label' => 'Dashboard',        'route' => 'admin.dashboard',    'match' => 'admin.dashboard',     'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Pengguna',   'route' => 'admin.user.index',   'match' => 'admin.user.*',        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => 'Program Studi',            'route' => 'admin.prodi.index',  'match' => 'admin.prodi.*',       'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
                        ['label' => 'Penguji',          'route' => 'admin.penguji.index','match' => 'admin.penguji.*',     'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['label' => 'Lowongan',         'route' => 'admin.lowongan.index','match' => ['admin.lowongan.*', 'admin.lamaran.*'],   'icon' => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z'],
                        ['label' => 'Jadwal Seleksi',   'route' => 'admin.jadwal.index', 'match' => 'admin.jadwal.*',      'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['label' => 'Pelamar','route' => 'admin.pelamar.index','match' => 'admin.pelamar.*',    'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                        ['label' => 'Pengaturan',       'route' => 'settings.index',     'match' => 'settings.*',          'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ],
                    'pelamar' => [
                        ['label' => 'Dashboard',        'route' => 'pelamar.dashboard',      'match' => 'pelamar.dashboard',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Profil',           'route' => 'pelamar.profil.index',   'match' => 'pelamar.profil.*',       'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['label' => 'Cari Lowongan',    'route' => 'pelamar.lowongan.index', 'match' => 'pelamar.lowongan.*',     'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                        ['label' => 'Riwayat Lamaran',  'route' => 'pelamar.history.index',  'match' => 'pelamar.history.*',      'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label' => 'Pengaturan',       'route' => 'settings.index',         'match' => 'settings.*',             'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ],
                    'penguji' => [
                        ['label' => 'Dashboard',        'route' => 'penguji.dashboard',        'match' => 'penguji.dashboard',         'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Pengujian',        'route' => 'penguji.pengujian.index',  'match' => 'penguji.pengujian.*',       'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['label' => 'Pengaturan',       'route' => 'settings.index',           'match' => 'settings.*',               'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ],
                    'kaprodi' => [
                        ['label' => 'Dashboard',        'route' => 'kaprodi.dashboard',     'match' => 'kaprodi.dashboard',     'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label' => 'Pelamar',          'route' => 'kaprodi.pelamar.index', 'match' => 'kaprodi.pelamar.*',     'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
                        ['label' => 'Pengaturan',       'route' => 'settings.index',        'match' => 'settings.*',            'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ],
                ];
                $currentMenus = $menus[$role] ?? $menus['admin'];
            @endphp

            @foreach($currentMenus as $item)
                @php
                    $isActive = $item['route'] !== '#' && request()->routeIs($item['match'] ?? $item['route']);
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

        {{-- Logout Sidebar --}}
        <div class="px-3 py-4 border-t border-white/10 mt-auto">
            <button type="button" @click="showLogoutModal = true"
                    class="group flex items-center gap-3.5 px-3 py-2.5 rounded-lg text-[0.875rem] font-medium text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 relative w-full">
                <svg class="w-[18px] h-[18px] flex-shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                <span class="nav-label whitespace-nowrap" :class="sidebarCollapsed ? 'lg:hidden' : ''">Log Out</span>
            </button>
        </div>

    </aside>
    @endempty

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex flex-col flex-1 h-screen overflow-hidden min-w-0">

        @empty($hideSidebar)
        {{-- TOP NAV --}}
        <header class="flex-shrink-0 flex items-center justify-between h-[65px] px-6 bg-white border-b border-gray-200/80 shadow-sm z-40">
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
                        <span x-show="belumDibaca > 0" x-text="belumDibaca > 9 ? '9+' : belumDibaca"
                              class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-[#8b1515] text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5 leading-none"></span>
                    </button>

                    {{-- Notification Dropdown --}}
                    <div x-show="notifOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="display:none;"
                         class="absolute right-0 top-12 w-[calc(100vw-2rem)] max-w-[360px] bg-white border border-gray-200 rounded-2xl shadow-xl z-[80] overflow-hidden">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100">
                            <h3 class="text-[0.95rem] font-bold text-gray-900">Notifikasi</h3>
                            <button @click="fetchNotif()" class="w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Refresh">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Filter Tabs — segmented pill --}}
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5">
                                @php
                                    $role = auth()->user()->role;
                                    if ($role === 'admin') {
                                        $tabs = [['key'=>'all','label'=>'Semua'],['key'=>'sistem','label'=>'Sistem'],['key'=>'pelamar','label'=>'Pelamar']];
                                    } elseif ($role === 'kaprodi') {
                                        $tabs = [['key'=>'all','label'=>'Semua'],['key'=>'pelamar','label'=>'Pelamar']];
                                    } else {
                                        $tabs = [['key'=>'all','label'=>'Semua'],['key'=>'jadwal','label'=>'Jadwal'],['key'=>'status','label'=>'Status']];
                                    }
                                @endphp
                                @foreach($tabs as $tab)
                                <button type="button" @click="notifTab = '{{ $tab['key'] }}'"
                                        class="flex-1 inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-md text-[0.7rem] font-semibold transition-all duration-150 whitespace-nowrap"
                                        :class="notifTab === '{{ $tab['key'] }}' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                    <span>{{ $tab['label'] }}</span>
                                    <span x-show="countByTab('{{ $tab['key'] }}') > 0"
                                          x-text="countByTab('{{ $tab['key'] }}')"
                                          class="text-[0.6rem] font-bold min-w-[14px] px-1 py-0.5 rounded-full leading-none"
                                          :class="notifTab === '{{ $tab['key'] }}' ? 'bg-gray-200 text-gray-700' : 'bg-gray-300 text-gray-500'"></span>
                                </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- List --}}
                        <div class="max-h-[340px] overflow-y-auto">

                            {{-- Loading skeleton --}}
                            <template x-if="notifLoading">
                                <div class="flex flex-col gap-3 px-5 py-4">
                                    <template x-for="i in 3" :key="i">
                                        <div class="flex items-start gap-3 animate-pulse">
                                            <div class="w-9 h-9 rounded-full bg-gray-100 flex-shrink-0"></div>
                                            <div class="flex-1 space-y-2 pt-1">
                                                <div class="h-2.5 bg-gray-100 rounded w-3/4"></div>
                                                <div class="h-2 bg-gray-100 rounded w-full"></div>
                                                <div class="h-2 bg-gray-100 rounded w-1/3"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Empty --}}
                            <template x-if="!notifLoading && filteredNotifs.length === 0">
                                <div class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500">Tidak ada notifikasi</p>
                                    <p class="text-xs text-gray-400 mt-1">Belum ada pesan di kategori ini.</p>
                                </div>
                            </template>

                            {{-- Items --}}
                            <template x-if="!notifLoading && filteredNotifs.length > 0">
                                <div class="divide-y divide-gray-50">
                                    <template x-for="notif in filteredNotifs" :key="notif.id">
                                        <div x-data="{ expanded: false }"
                                             :class="!notif.dibaca ? 'bg-blue-50/20' : 'bg-white'">

                                            {{-- Row --}}
                                            <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors"
                                                 class="cursor-pointer"
                                                 @click="expanded = !expanded;
                                                     if(expanded && !notif.dibaca) {
                                                         fetch('/notifikasi/' + notif.id + '/baca', {
                                                             method: 'POST',
                                                             headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                                                         });
                                                         notif.dibaca = true;
                                                         belumDibaca = Math.max(0, belumDibaca - 1);
                                                     }">

                                                {{-- Type icon --}}
                                                <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center mt-0.5"
                                                     :class="notif.tipe === 'jadwal' ? 'bg-blue-100' : notif.tipe === 'status' ? 'bg-green-100' : notif.tipe === 'sistem' ? 'bg-gray-100' : notif.tipe === 'pelamar' ? 'bg-orange-100' : 'bg-gray-100'">
                                                    <template x-if="notif.tipe === 'jadwal'">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    </template>
                                                    <template x-if="notif.tipe === 'status'">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </template>
                                                    <template x-if="notif.tipe === 'sistem'">
                                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                    </template>
                                                    <template x-if="notif.tipe === 'pelamar'">
                                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                    </template>
                                                    <template x-if="notif.tipe !== 'jadwal' && notif.tipe !== 'status' && notif.tipe !== 'sistem' && notif.tipe !== 'pelamar'">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </template>
                                                </div>

                                                {{-- Content --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <p class="text-[0.8rem] font-bold leading-snug"
                                                           :class="!notif.dibaca ? 'text-gray-900' : 'text-gray-700'"
                                                           x-text="notif.judul"></p>
                                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                                            <span x-show="!notif.dibaca" class="w-2 h-2 bg-[#8b1515] rounded-full mt-1"></span>
                                                            {{-- Chevron for all types --}}
                                                            <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 transition-transform duration-200"
                                                                 :class="expanded ? 'rotate-180' : ''"
                                                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <p class="text-[0.75rem] text-gray-500 mt-0.5 leading-relaxed"
                                                       :class="!expanded ? 'line-clamp-1' : ''"
                                                       x-text="notif.pesan"></p>
                                                    <p class="text-[0.65rem] text-gray-400 mt-1.5 font-medium" x-text="formatTgl(notif.created_at)"></p>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                </div>
                            </template>

                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 bg-gray-50/60">
                            <button @click="bacaSemua()" x-show="belumDibaca > 0"
                                    class="text-xs font-semibold text-gray-500 hover:text-gray-800 underline underline-offset-2 transition-colors">
                                Tandai semua dibaca
                            </button>
                            <span x-show="belumDibaca === 0" class="text-xs text-gray-400">Semua sudah dibaca</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @endempty

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-x-auto overflow-y-auto px-4 py-4 sm:px-6 sm:py-6 lg:px-7 lg:py-7">
            @include('components.toast')
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
             class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">
            
            {{-- Warning Icon --}}
            <div class="mx-auto mb-5 flex justify-center">
                <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                    <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                    <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                </svg>
            </div>
            
            <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Yakin ingin keluar?</h2>
            <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Anda harus login kembali untuk masuk!</p>

            <div class="grid grid-cols-2 gap-3">
                <form method="POST" action="{{ route('logout') }}" class="contents">
                    @csrf
                    <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Iya</button>
                </form>
                <button type="button" @click="showLogoutModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">Tidak</button>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
