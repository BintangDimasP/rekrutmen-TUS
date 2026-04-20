@extends('layouts.admin')

@section('title', 'Dashboard Pelamar')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Welcome Card --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm overflow-hidden relative">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ $pelamar->nama }}! 👋</h1>
            <p class="text-gray-500 mt-1 max-w-2xl">
                Pantau status lamaran Anda, perbarui profil, dan temukan kesempatan karir akademik terbaru di Telkom University dalam satu dasbor terpadu.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-5 pointer-events-none">
            <svg width="200" height="200" viewBox="0 0 24 24" fill="#8b1515"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99z"/></svg>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Lamaran</div>
                    <div class="text-2xl font-black text-gray-800">{{ $totalLamaran }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Sedang Diproses</div>
                    <div class="text-2xl font-black text-gray-800">{{ $lamaranAktif }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Diterima</div>
                    <div class="text-2xl font-black text-gray-800">{{ $lamaranDiterima }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-[#8b1515] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ditolak</div>
                    <div class="text-2xl font-black text-gray-800">{{ $lamaranDitolak }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-bold text-gray-800">Lamaran Terakhir</h3>
                <a href="{{ route('pelamar.history.index') }}" class="text-xs font-bold text-[#8b1515] hover:underline">Lihat Semua</a>
            </div>
            
            <div class="space-y-3">
                @forelse($recentLamarans as $lamaran)
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between group hover:border-[#8b1515]/20 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-[#8b1515]/5 group-hover:text-[#8b1515] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-800">{{ $lamaran->lowongan->nama_posisi }}</div>
                            <div class="text-xs text-gray-400">{{ $lamaran->lowongan->prodi->nama ?? '-' }} • {{ $lamaran->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $statusColors = [
                                'menunggu'       => 'bg-gray-100 text-gray-600',
                                'seleksi_tahap1' => 'bg-blue-50 text-blue-700',
                                'seleksi_tahap2' => 'bg-indigo-50 text-indigo-700',
                                'diterima'       => 'bg-green-50 text-green-700',
                                'ditolak'        => 'bg-red-50 text-red-700',
                            ];
                            $colorClass = $statusColors[$lamaran->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-2.5 py-1 rounded-md text-[0.65rem] font-black uppercase tracking-wider {{ $colorClass }}">
                            {{ $lamaran->status_label }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="bg-gray-50 border-2 border-dashed border-gray-100 rounded-2xl p-12 text-center">
                    <p class="text-gray-400 text-sm">Belum ada aktivitas lamaran.</p>
                    <a href="{{ route('pelamar.lowongan.index') }}" class="inline-flex items-center gap-2 mt-4 text-[#8b1515] font-bold text-sm">
                        Cari Lowongan Pertama Anda 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="space-y-6">
            <div class="bg-[#8b1515] rounded-2xl p-6 text-white shadow-lg shadow-[#8b1515]/20 relative overflow-hidden group">
                <div class="relative z-10">
                    <h3 class="text-lg font-bold">Ayo Cari Lowongan!</h3>
                    <p class="text-white/80 text-xs mt-2 leading-relaxed">Ada <strong>{{ $lowonganCount }}</strong> lowongan aktif yang sedang dibuka. Jangan lewatkan kesempatan Anda.</p>
                    <a href="{{ route('pelamar.lowongan.index') }}" class="inline-block mt-4 px-4 py-2 bg-white text-[#8b1515] text-xs font-bold rounded-lg shadow-sm hover:bg-gray-50 transition-colors">Lihat Lowongan</a>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="text-sm font-bold text-gray-800 mb-4">Lengkapi Profil</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $pelamar->file_cv ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        <span class="text-xs text-gray-600">Upload CV Terbaru</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $pelamar->file_ijazah ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        <span class="text-xs text-gray-600">Dokumen Pendidikan</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full {{ $pelamar->no_telepon ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        <span class="text-xs text-gray-600">Nomor Telepon Aktif</span>
                    </div>
                </div>
                <a href="{{ route('pelamar.profil.index') }}" class="block text-center mt-6 py-2 border border-gray-100 text-gray-500 hover:text-[#8b1515] hover:border-[#8b1515] text-[0.7rem] font-bold rounded-lg transition-all">Perbarui Profil</a>
            </div>
        </div>
    </div>
</div>
@endsection
