@extends('layouts.admin')

@section('title', 'Cari Lowongan')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="max-w-xl">
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Temukan Peluang Anda</h1>
            <p class="text-gray-500 mt-2">Daftar lowongan akademik terbaru di lingkungan Telkom University. Filter berdasarkan kualifikasi Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" placeholder="Cari posisi atau prodi..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-100 rounded-xl shadow-sm text-sm focus:border-[#8b1515] focus:ring-4 focus:ring-[#8b1515]/5 transition-all">
            </div>
        </div>
    </div>

    {{-- Lowongan Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-12">
        @forelse($lowongans as $lowongan)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-xl hover:shadow-black/5 hover:-translate-y-1 transition-all duration-300">
            <div class="p-6 flex-1 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-xl bg-[#8b1515]/5 text-[#8b1515] flex items-center justify-center font-bold text-xl group-hover:bg-[#8b1515] group-hover:text-white transition-colors">
                        {{ substr($lowongan->nama_posisi, 0, 1) }}
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.65rem] font-black bg-green-50 text-green-700 uppercase tracking-wider">Terbuka</span>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold text-gray-800 line-clamp-1 decoration-[#8b1515] group-hover:underline">{{ $lowongan->nama_posisi }}</h3>
                    <p class="text-xs text-[#8b1515] font-semibold mt-1">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="px-2 py-1 bg-gray-50 text-gray-400 text-[0.65rem] font-bold rounded">{{ $lowongan->jenjang_minimal }}</span>
                    <span class="px-2 py-1 bg-gray-50 text-gray-400 text-[0.65rem] font-bold rounded">IPK ≥ {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                </div>

                <p class="text-xs text-gray-400 line-clamp-3 leading-relaxed">
                    Sisa kuota: <strong>{{ $lowongan->sisa_kuota }} kursi</strong> dari {{ $lowongan->kuota }} pendaftar. 
                    Posisi ini diprioritaskan bagi lulusan dari bidang {{ $lowongan->prodi_prioritas ?? 'relevan' }}.
                </p>
            </div>

            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-widest">Batas Akhir</span>
                    <span class="text-xs font-bold text-gray-600">{{ $lowongan->tanggal_tutup->format('d M Y') }}</span>
                </div>
                <a href="{{ route('pelamar.lowongan.show', $lowongan) }}" class="px-5 py-2 bg-[#8b1515] text-white text-xs font-bold rounded-xl shadow-md shadow-[#8b1515]/20 hover:bg-[#a01a1a] transition-all">Detail</a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-gray-100 rounded-3xl p-20 text-center space-y-4">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Maaf, belum ada lowongan aktif</h2>
            <p class="text-sm text-gray-500 max-w-sm mx-auto">Saat ini belum ada posisi yang sedang membuka pendaftaran. Silahkan cek kembali di lain waktu.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
