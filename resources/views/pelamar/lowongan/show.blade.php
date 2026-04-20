@extends('layouts.admin')

@section('title', 'Detail Lowongan — ' . $lowongan->nama_posisi)

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('pelamar.lowongan.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center text-gray-400 hover:text-[#8b1515] hover:border-[#8b1515] transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight">{{ $lowongan->nama_posisi }}</h1>
            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">{{ $lowongan->prodi->nama ?? 'Semua Program Studi' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left: Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Main Info --}}
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm space-y-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="space-y-1">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Pendidikan Minimal</span>
                        <p class="text-sm font-bold text-gray-800">{{ $lowongan->jenjang_minimal }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Minimal IPK</span>
                        <p class="text-sm font-bold text-gray-800">{{ number_format($lowongan->minimal_ipk, 2) }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Kuota</span>
                        <p class="text-sm font-bold text-gray-800">{{ $lowongan->kuota }} Posisi</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Batas Akhir</span>
                        <p class="text-sm font-bold text-[#8b1515]">{{ $lowongan->tanggal_tutup->format('d M Y') }}</p>
                    </div>
                </div>

                <hr class="border-gray-50">

                <div class="space-y-6">
                    <div class="space-y-3">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Kualifikasi Khusus
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#8b1515] shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <span class="text-[0.6rem] font-black text-gray-400 uppercase block tracking-tighter">Prodi Linear/Prioritas</span>
                                    <span class="text-xs font-bold text-gray-700">{{ $lowongan->prodi_prioritas ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#8b1515] shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[0.6rem] font-black text-gray-400 uppercase block tracking-tighter">Skill Utama</span>
                                    <span class="text-xs font-bold text-gray-700">{{ $lowongan->skill_dibutuhkan ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Deskripsi Pekerjaan & Persyaratan
                        </h3>
                        <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($lowongan->deskripsi)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Apply Form --}}
        <div class="space-y-6">
            @if($existing)
                <div class="bg-green-50 rounded-3xl p-8 border border-green-100 text-center space-y-4">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-green-800">Sudah Dilamar</h3>
                        <p class="text-xs text-green-600 mt-1">Anda telah mengajukan lamaran untuk posisi ini pada {{ $existing->created_at->format('d M Y') }}.</p>
                    </div>
                    <a href="{{ route('pelamar.history.index') }}" class="block px-6 py-3 bg-green-600 text-white font-bold text-xs rounded-2xl hover:bg-green-700 transition-all">Lihat Status Lamaran</a>
                </div>
            @elseif($lowongan->tanggal_tutup < now())
                <div class="bg-red-50 rounded-3xl p-8 border border-red-100 text-center space-y-4">
                    <div class="w-16 h-16 bg-red-100 text-[#8b1515] rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-800">Pendaftaran Ditutup</h3>
                        <p class="text-xs text-red-600 mt-1">Maaf, batas waktu pendaftaran untuk posisi ini telah berakhir.</p>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm space-y-6 sticky top-8">
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-gray-800">Lamar Posisi Ini</h3>
                        <p class="text-xs text-gray-400 leading-relaxed">Pastikan profil Anda sudah lengkap sebelum mengirimkan lamaran.</p>
                    </div>

                    <form action="{{ route('pelamar.lowongan.storeApply', $lowongan) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest">Surat Lamaran (Wajib PDF)</label>
                            <input type="file" name="file_surat_lamaran" required class="w-full text-xs p-3 rounded-xl border border-gray-100 bg-gray-50 focus:ring-4 focus:ring-[#8b1515]/5 outline-none transition-all">
                            @error('file_surat_lamaran') <p class="text-[0.6rem] text-red-500 font-bold">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest">Berkas Pendukung (Opsional)</label>
                            <input type="file" name="file_berkas_pendukung" class="w-full text-xs p-3 rounded-xl border border-gray-100 bg-gray-50 focus:ring-4 focus:ring-[#8b1515]/5 outline-none transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest">Catatan Tambahan</label>
                            <textarea name="catatan" rows="3" placeholder="Pesan singkat..." class="w-full text-xs p-4 rounded-xl border border-gray-100 bg-gray-50 focus:ring-4 focus:ring-[#8b1515]/5 outline-none transition-all"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-[#8b1515] text-white font-black text-xs rounded-2xl shadow-xl shadow-[#8b1515]/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            KIRIM LAMARAN SEKARANG
                        </button>
                    </form>
                    
                    <div class="p-4 rounded-2xl bg-gray-50 flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[0.65rem] text-gray-400 leading-relaxed italic">Data CV, Ijazah, dan KTP akan otomatis ditarik dari profil Anda.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
