@extends('layouts.admin')

@section('title', 'Lamar Posisi — ' . $lowongan->nama_posisi)

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">

    {{-- Info Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-[#8b1515] p-6 text-white relative">
            <div class="flex items-center gap-4 relative z-10">
                <a href="{{ route('pelamar.lowongan.index') }}" class="w-10 h-10 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Konfirmasi Pendaftaran</h1>
                    <p class="text-white/70 text-xs mt-0.5">Anda sedang melamar posisi <strong>{{ $lowongan->nama_posisi }}</strong></p>
                </div>
            </div>
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                <svg width="150" height="150" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99z"/></svg>
            </div>
        </div>
        
        <div class="p-8 space-y-8">
            {{-- Summary Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                <div class="space-y-4 pr-4">
                    <h3 class="text-[0.7rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Detail Lowongan</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-xs text-gray-400">Posisi:</div>
                            <div class="text-sm font-bold text-gray-800">{{ $lowongan->nama_posisi }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">Prodi:</div>
                            <div class="text-sm font-bold text-gray-800">{{ $lowongan->prodi->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400">Persyaratan Utama:</div>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-[0.65rem] font-bold border border-indigo-100">{{ $lowongan->jenjang_minimal }}</span>
                                <span class="bg-gray-50 text-gray-600 px-2 py-0.5 rounded text-[0.65rem] font-bold border border-gray-200">IPK ≥ {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 pl-0 md:pl-8 pt-4 md:pt-0">
                    <h3 class="text-[0.7rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Profil Anda</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Jenjang Anda:</div>
                                <div class="text-sm font-bold text-gray-800">{{ $pelamar->jenjang }} - IPK: {{ number_format($pelamar->ipk, 2) }}</div>
                            </div>
                        </div>
                        <p class="text-[0.65rem] text-gray-500 leading-relaxed italic">
                            * Data riwayat pendidikan, akademik, dan dokumen dasar (KTP, CV, Ijazah) akan otomatis dilampirkan dari profil Anda yang sudah tersimpan.
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-50">

            {{-- Form Upload Spesifik --}}
            <form action="{{ route('pelamar.lowongan.storeApply', $lowongan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                            Berkas Spesifik Lowongan
                        </h3>
                        <p class="text-[0.7rem] text-gray-400 mb-4 uppercase tracking-tighter">Wajib diunggah setiap melamar posisi berbeda</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[0.65rem] font-black text-gray-500 uppercase flex items-center justify-between">
                            Surat Lamaran <span class="text-[#8b1515] lowercase font-medium">Wajib (PDF)</span>
                        </label>
                        <input type="file" name="file_surat_lamaran" required class="text-xs p-2.5 rounded-xl border border-gray-100 bg-gray-50 w-full focus:ring-4 focus:ring-[#8b1515]/5 transition-all">
                        <p class="text-[0.6rem] text-gray-400 italic font-medium mt-1">Sertakan surat lamaran resmi yang ditujukan ke Telkom University.</p>
                        @error('file_surat_lamaran') <p class="text-[0.6rem] text-red-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[0.65rem] font-black text-gray-500 uppercase flex items-center justify-between">
                            Berkas Pendukung <span class="text-gray-400 lowercase font-medium">Opsional (PDF)</span>
                        </label>
                        <input type="file" name="file_berkas_pendukung" class="text-xs p-2.5 rounded-xl border border-gray-100 bg-gray-50 w-full focus:ring-4 focus:ring-[#8b1515]/5 transition-all">
                        <p class="text-[0.6rem] text-gray-400 italic font-medium mt-1">Contoh karya ilmiah, sertifikat project, atau portofolio relevan.</p>
                        @error('file_berkas_pendukung') <p class="text-[0.6rem] text-red-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-[0.65rem] font-black text-gray-500 uppercase">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" rows="3" placeholder="Tuliskan jika ada pesan khusus untuk penguji atau admin prodi..." class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-sm focus:border-[#8b1515] transition-all"></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                    <p class="text-[0.65rem] text-gray-400 max-w-sm italic">
                        Dengan menekan tombol di samping, Anda menyatakan bahwa seluruh data profil dan lampiran yang diunggah adalah benar.
                    </p>
                    <button type="submit" class="px-8 py-3 bg-[#8b1515] text-white font-bold rounded-xl shadow-lg shadow-[#8b1515]/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Kirim Lamaran Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
