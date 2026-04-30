@extends('layouts.admin')
@section('title', 'Detail Lamaran')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header / Breadcrumb --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('pelamar.history.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Lamaran</h1>
            <p class="text-sm text-gray-500 mt-1">Rincian informasi, dokumen, dan jadwal seleksi Anda.</p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        
        {{-- Section: Info Singkat (Red Header) --}}
        <div class="px-8 py-10 bg-[#8b1515] text-white flex flex-col md:flex-row md:items-end justify-between gap-6 relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
            
            <div class="relative z-10">
                <p class="text-red-200 text-xs font-bold tracking-widest uppercase mb-2">Posisi yang dilamar</p>
                <h2 class="text-3xl font-black mb-3">{{ $lamaran->lowongan->nama_posisi }}</h2>
                <p class="text-red-100 text-sm">
                    Program Studi: <span class="font-bold text-white">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</span>
                    <span class="mx-3 opacity-30">|</span>
                    Dilamar pada {{ $lamaran->created_at->format('d F Y') }}
                </p>
            </div>
            
            <div class="relative z-10">
                @php
                    $statusColors = [
                        'menunggu'       => 'bg-white/20 text-white border-white/30',
                        'seleksi_tahap1' => 'bg-white text-blue-700 border-white',
                        'seleksi_tahap2' => 'bg-white text-indigo-700 border-white',
                        'diterima'       => 'bg-white text-green-700 border-white',
                        'ditolak'        => 'bg-white text-red-700 border-white',
                    ];
                    $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                @endphp
                <span class="inline-flex px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest border backdrop-blur-sm shadow-sm {{ $colorClass }}">
                    {{ $lamaran->status_label }}
                </span>
            </div>
        </div>

        <div class="p-8 md:p-10 space-y-12">
            
            {{-- Section: Dokumen Upload --}}
            <div>
                <h3 class="text-base font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Dokumen Pendukung</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($lamaran->file_surat_lamaran)
                    <a href="{{ Storage::url($lamaran->file_surat_lamaran) }}" target="_blank" class="flex items-center justify-between p-5 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all bg-gray-50/50 group">
                        <div>
                            <p class="text-sm font-bold text-gray-800 group-hover:text-[#8b1515] transition-colors">Surat Lamaran</p>
                            <p class="text-[0.7rem] text-gray-500 mt-1 font-medium">Dokumen PDF/Word</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 group-hover:bg-[#8b1515] group-hover:text-white group-hover:border-[#8b1515] transition-colors shadow-sm">Buka</span>
                    </a>
                    @endif

                    @if($lamaran->file_berkas_pendukung)
                    <a href="{{ Storage::url($lamaran->file_berkas_pendukung) }}" target="_blank" class="flex items-center justify-between p-5 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all bg-gray-50/50 group">
                        <div>
                            <p class="text-sm font-bold text-gray-800 group-hover:text-[#8b1515] transition-colors">Berkas Pendukung</p>
                            <p class="text-[0.7rem] text-gray-500 mt-1 font-medium">Dokumen Lampiran</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 group-hover:bg-[#8b1515] group-hover:text-white group-hover:border-[#8b1515] transition-colors shadow-sm">Buka</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Section: Jadwal Seleksi --}}
            <div>
                <h3 class="text-base font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Jadwal Seleksi</h3>
                
                @if($wawancara || $micro)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($wawancara)
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm relative overflow-hidden group hover:border-blue-300 hover:shadow-md transition-all">
                            <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                            
                            <h4 class="text-[0.7rem] font-black text-blue-600 uppercase tracking-widest mb-5">Tahap 1: Wawancara</h4>
                            
                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal & Sesi</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara->tanggal->translatedFormat('l, d F Y') }} <span class="text-gray-300 font-normal mx-1">|</span> <span class="font-semibold">{{ $wawancara->session_label }}</span></p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara->penguji->nama ?? '-' }}</p>
                                </div>
                                
                                @if($wawancara->link_meeting)
                                <div class="pt-3">
                                    <a href="{{ $wawancara->link_meeting }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gray-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-gray-200 hover:border-blue-600 text-[0.75rem] font-bold rounded-xl transition-all shadow-sm">
                                        Masuk Link Zoom
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($micro)
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm relative overflow-hidden group hover:border-indigo-300 hover:shadow-md transition-all">
                            <div class="absolute top-0 left-0 w-full h-1 bg-indigo-500"></div>
                            
                            <h4 class="text-[0.7rem] font-black text-indigo-600 uppercase tracking-widest mb-5">Tahap 2: Micro Teaching</h4>
                            
                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal & Sesi</p>
                                    <p class="font-bold text-gray-800">{{ $micro->tanggal->translatedFormat('l, d F Y') }} <span class="text-gray-300 font-normal mx-1">|</span> <span class="font-semibold">{{ $micro->session_label }}</span></p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <p class="font-bold text-gray-800">{{ $micro->penguji->nama ?? '-' }}</p>
                                </div>
                                
                                @if($micro->link_meeting)
                                <div class="pt-3">
                                    <a href="{{ $micro->link_meeting }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gray-50 hover:bg-indigo-600 text-indigo-600 hover:text-white border border-gray-200 hover:border-indigo-600 text-[0.75rem] font-bold rounded-xl transition-all shadow-sm">
                                        Masuk Link Zoom
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="p-8 rounded-2xl border border-gray-200 bg-gray-50 text-center">
                        <p class="text-sm font-bold text-gray-600">Belum Ada Jadwal</p>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Jadwal seleksi Anda belum ditentukan. Silakan tunggu informasi lebih lanjut dari pihak panitia.</p>
                    </div>
                @endif
            </div>

            {{-- Catatan Admin --}}
            @if($lamaran->catatan_admin)
            <div>
                <h3 class="text-base font-bold text-gray-800 mb-4 pb-3 border-b border-gray-100">Catatan Tambahan</h3>
                <div class="p-6 rounded-2xl bg-yellow-50 border border-yellow-100/50 text-sm text-yellow-800 leading-relaxed shadow-sm font-medium">
                    {{ $lamaran->catatan_admin }}
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
