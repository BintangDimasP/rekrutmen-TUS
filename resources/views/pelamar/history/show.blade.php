@extends('layouts.admin')
@section('title', 'Detail Lamaran')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showWithdrawModal: false }">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        
        <a href="{{ route('pelamar.history.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Histori</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">Detail Lamaran</span>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
        
        {{-- Section: Info Singkat (Red Header) --}}
        <div class="px-8 py-10 bg-[#8b1515] text-white flex flex-col md:flex-row md:items-end justify-between gap-6 relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
            
            <div class="relative z-10">
                <h2 class="text-3xl font-black mb-3">{{ $lamaran->lowongan->nama_posisi }}</h2>
                <p class="text-red-100 text-sm">
                    Program Studi: <span class="font-bold text-white">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</span>
                    <span class="mx-3 opacity-30">|</span>
                    Dilamar pada {{ $lamaran->created_at->format('d F Y') }}
                </p>
            </div>
            
            <div class="relative ">
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
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 group-hover:bg-[#8b1515] group-hover:text-white group-hover:border-[#8b1515] transition-colors shadow-sm">Preview</span>
                    </a>
                    @endif

                    @if($lamaran->file_sk_penyetaraan)
                    <a href="{{ Storage::url($lamaran->file_sk_penyetaraan) }}" target="_blank" class="flex items-center justify-between p-5 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all bg-gray-50/50 group">
                        <div>
                            <p class="text-sm font-bold text-gray-800 group-hover:text-[#8b1515] transition-colors">SK Penyetaraan</p>
                            <p class="text-[0.7rem] text-gray-500 mt-1 font-medium">Lulusan Luar Negeri</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 group-hover:bg-[#8b1515] group-hover:text-white group-hover:border-[#8b1515] transition-colors shadow-sm">Preview</span>
                    </a>
                    @endif

                    @if($lamaran->file_surat_pemberhentian)
                    <a href="{{ Storage::url($lamaran->file_surat_pemberhentian) }}" target="_blank" class="flex items-center justify-between p-5 rounded-2xl border border-gray-100 hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5 transition-all bg-gray-50/50 group">
                        <div>
                            <p class="text-sm font-bold text-gray-800 group-hover:text-[#8b1515] transition-colors">Surat Pemberhentian</p>
                            <p class="text-[0.7rem] text-gray-500 mt-1 font-medium">Dari Instansi Lain</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-lg bg-white border border-gray-200 text-[0.7rem] font-bold text-gray-600 group-hover:bg-[#8b1515] group-hover:text-white group-hover:border-[#8b1515] transition-colors shadow-sm">Preview</span>
                    </a>
                    @endif


                </div>
            </div>

            {{-- Section: Jadwal Seleksi --}}
            <div>
                @php
                    $hasAnyPenilaian = (
                        ($wawancara && $wawancara->contains(fn($j) => $j->penilaian !== null)) ||
                        ($micro     && $micro->contains(fn($j) => $j->penilaian !== null))
                    );
                @endphp
                <h3 class="text-base font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">
                    {{ $hasAnyPenilaian ? 'Nilai Hasil Seleksi' : 'Jadwal Seleksi' }}
                </h3>
                
                @if(($wawancara && $wawancara->count() > 0) || ($micro && $micro->count() > 0))
                    @php
                        $microDinilai     = $micro     ? $micro->filter(fn($j) => $j->penilaian !== null)     : collect();
                        $wawancaraDinilai = $wawancara ? $wawancara->filter(fn($j) => $j->penilaian !== null) : collect();

                        $microRata     = $microDinilai->count() > 0
                            ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2)
                            : null;
                        $wawancaraRata = $wawancaraDinilai->count() > 0
                            ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2)
                            : null;
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($micro && $micro->count() > 0)
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm relative overflow-hidden group hover:border-gray-300 hover:shadow-md transition-all">
                            <div class="absolute top-0 left-0 w-full h-1 bg-[#8b1515]"></div>

                            @if($microRata !== null)
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-5">Micro Teaching</h4>

                            <div class="flex items-center justify-between p-5 bg-gradient-to-r from-[#7a1111] to-[#8b1515] rounded-xl shadow-sm mb-5 text-white">
                                <div>
                                    <p class="text-[0.65rem] font-bold uppercase tracking-wider opacity-80">Nilai Rata-rata</p>
                                    <p class="text-[0.6rem] mt-0.5 opacity-70">{{ $microDinilai->count() }} penguji menilai</p>
                                </div>
                                <p class="text-3xl font-black">{{ $microRata }}</p>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-2">Penguji</p>
                                    <div class="space-y-1.5">
                                        @foreach($micro as $microItem)
                                        <p class="text-sm font-semibold text-gray-800">{{ $microItem->penguji->nama ?? '-' }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @else
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-5">Micro Teaching</h4>

                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="font-bold text-gray-800">{{ $micro[0]->tanggal->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Waktu</p>
                                    <p class="font-bold text-gray-800">{{ $micro[0]->session_label }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <div class="space-y-2">
                                        @foreach($micro as $microItem)
                                        <p class="text-sm font-bold text-gray-800">{{ $microItem->penguji->nama ?? '-' }}</p>
                                        @endforeach
                                    </div>
                                </div>

                                @if($micro[0]->link_meeting)
                                <div class="pt-3">
                                    <a href="{{ $micro[0]->link_meeting }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gray-50 hover:bg-[#8b1515] text-[#8b1515] hover:text-white border border-gray-200 hover:border-[#8b1515] text-[0.75rem] font-bold rounded-xl transition-all shadow-sm">
                                        Masuk Link Zoom
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($wawancara && $wawancara->count() > 0)
                        <div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm relative overflow-hidden group hover:border-gray-300 hover:shadow-md transition-all">
                            <div class="absolute top-0 left-0 w-full h-1 bg-[#8b1515]"></div>

                            @if($wawancaraRata !== null)
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-5">Wawancara</h4>

                            <div class="flex items-center justify-between p-5 bg-gradient-to-r from-[#7a1111] to-[#8b1515] rounded-xl shadow-sm mb-5 text-white">
                                <div>
                                    <p class="text-[0.65rem] font-bold uppercase tracking-wider opacity-80">Nilai Rata-rata</p>
                                    <p class="text-[0.6rem] mt-0.5 opacity-70">{{ $wawancaraDinilai->count() }} penguji menilai</p>
                                </div>
                                <p class="text-3xl font-black">{{ $wawancaraRata }}</p>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-2">Penguji</p>
                                    <div class="space-y-1.5">
                                        @foreach($wawancara as $wawancaraItem)
                                        <p class="text-sm font-semibold text-gray-800">{{ $wawancaraItem->penguji->nama ?? '-' }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @else
                            <h4 class="text-[0.7rem] font-black text-[#8b1515] uppercase tracking-widest mb-5">Wawancara</h4>

                            <div class="space-y-4 text-sm text-gray-700">
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara[0]->tanggal->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Waktu</p>
                                    <p class="font-bold text-gray-800">{{ $wawancara[0]->session_label }}</p>
                                </div>
                                <div>
                                    <p class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mb-1">Penguji</p>
                                    <div class="space-y-2">
                                        @foreach($wawancara as $wawancaraItem)
                                        <p class="text-sm font-bold text-gray-800">{{ $wawancaraItem->penguji->nama ?? '-' }}</p>
                                        @endforeach
                                    </div>
                                </div>

                                @if($wawancara[0]->link_meeting)
                                <div class="pt-3">
                                    <a href="{{ $wawancara[0]->link_meeting }}" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gray-50 hover:bg-[#8b1515] text-[#8b1515] hover:text-white border border-gray-200 hover:border-[#8b1515] text-[0.75rem] font-bold rounded-xl transition-all shadow-sm">
                                        Masuk Link Zoom
                                    </a>
                                </div>
                                @endif
                            </div>
                            @endif
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

            {{-- Tombol Mengundurkan Diri --}}
            @if(!in_array($lamaran->status, ['diterima', 'ditolak', 'mengundurkan_diri']))
            <div class="flex justify-center pt-2">
                <button type="button" @click="showWithdrawModal = true"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#8b1515] hover:bg-[#6b1111] text-white text-sm font-semibold transition-all shadow-md shadow-[#8b1515]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mengundurkan Diri
                </button>
            </div>
            @endif

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

    @if(!in_array($lamaran->status, ['diterima', 'ditolak', 'mengundurkan_diri']))
    {{-- Withdraw Confirm Modal — teleport ke body agar blur & stacking benar --}}
    <template x-teleport="body">
        <div x-show="showWithdrawModal" style="display:none;"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="showWithdrawModal = false">

            <div x-show="showWithdrawModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">

                {{-- Close --}}
                <button type="button" @click="showWithdrawModal = false"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                {{-- Warning Icon --}}
                <div class="mx-auto mb-5 flex justify-center">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                        <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                        <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                    </svg>
                </div>

                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Yakin mengundurkan diri?</h2>
                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Anda tidak akan bisa melamar kembali pada posisi ini.</p>

                <div class="grid grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('pelamar.history.withdraw', $lamaran->id) }}" class="contents">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Ya</button>
                    </form>
                    <button type="button" @click="showWithdrawModal = false"
                        class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">Batal</button>
                </div>
            </div>
        </div>
    </template>
    @endif

</div>
@endsection
