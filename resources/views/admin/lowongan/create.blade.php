@extends('layouts.admin')

@section('title', 'Tambah Lowongan Baru')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    <!-- Single Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6 md:p-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.lowongan.index') }}" class="p-2 bg-white/20 hover:bg-white/30 rounded-xl transition-colors text-white ring-2 ring-white/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                        <h1 class="text-xl font-bold text-white">Tambah Lowongan Baru</h1>
                    </div>
                    <p class="text-red-200 text-sm mt-1.5 ml-14">Isi seluruh informasi posisi yang akan dibuka untuk pelamar.</p>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="p-6 md:p-8">
            <form method="POST" action="{{ route('admin.lowongan.store') }}" class="space-y-8">
                @csrf

                {{-- 1. INFORMASI DASAR --}}
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Nama Posisi <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_posisi" value="{{ old('nama_posisi') }}" placeholder="cth: Dosen Tetap — Sistem Informasi"
                                   class="w-full px-4 py-2.5 rounded-lg border @error('nama_posisi') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            @error('nama_posisi') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Program Studi (Prodi Pembuka) <span class="text-red-500">*</span></label>
                            <select name="prodi_id"
                                    class="w-full px-4 py-2.5 rounded-lg border @error('prodi_id') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="">— Pilih Prodi —</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama }} ({{ $prodi->kode }})
                                    </option>
                                @endforeach
                            </select>
                            @error('prodi_id') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                            <select name="status"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="draft"  {{ old('status','draft') === 'draft'   ? 'selected' : '' }}>Draft (belum tayang)</option>
                                <option value="aktif"  {{ old('status') === 'aktif'            ? 'selected' : '' }}>Aktif (tayang sekarang)</option>
                                <option value="ditutup"{{ old('status') === 'ditutup'          ? 'selected' : '' }}>Ditutup</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Penutupan Pendaftaran <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_tutup" value="{{ old('tanggal_tutup') }}"
                                   class="w-full px-4 py-2.5 rounded-lg border @error('tanggal_tutup') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            @error('tanggal_tutup') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. PERSYARATAN PELAMAR --}}
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                        Persyaratan Pelamar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Jenjang Pendidikan Minimal <span class="text-red-500">*</span></label>
                            <select name="jenjang_minimal"
                                    class="w-full px-4 py-2.5 rounded-lg border @error('jenjang_minimal') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="">— Pilih Jenjang —</option>
                                @foreach(['D3', 'S1', 'S2', 'S3'] as $j)
                                    <option value="{{ $j }}" {{ old('jenjang_minimal') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                            @error('jenjang_minimal') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Minimal IPK <span class="text-red-500">*</span></label>
                            <input type="number" name="minimal_ipk" value="{{ old('minimal_ipk', '3.00') }}"
                                   step="0.01" min="0" max="4" placeholder="cth: 3.00"
                                   class="w-full px-4 py-2.5 rounded-lg border @error('minimal_ipk') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            @error('minimal_ipk') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Prodi yang Diprioritaskan</label>
                            <select name="prodi_prioritas"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="">— Tidak ada prioritas khusus —</option>
                                @foreach($prodiPrioritasOptions as $opt)
                                    <option value="{{ $opt }}" {{ old('prodi_prioritas') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Keahlian / Skill yang Dibutuhkan</label>
                            <select name="skill_dibutuhkan"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="">— Tidak ada skill spesifik —</option>
                                @foreach($skillOptions as $opt)
                                    <option value="{{ $opt }}" {{ old('skill_dibutuhkan') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Kuota Pendaftaran <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-3">
                                <input type="number" name="kuota" value="{{ old('kuota', 1) }}" min="1" placeholder="cth: 5"
                                       class="w-40 px-4 py-2.5 rounded-lg border @error('kuota') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <span class="text-sm text-gray-500">orang. Kuota akan berkurang otomatis seiring masuknya pelamar.</span>
                            </div>
                            @error('kuota') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- 3. DESKRIPSI & DOKUMEN --}}
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                            Deskripsi & Dokumen Persyaratan
                        </h3>
                        <span class="text-gray-400 text-[0.65rem] font-bold uppercase">Sudah diisi otomatis</span>
                    </div>
                    
                    <p class="text-xs text-blue-700 bg-blue-50 rounded-lg px-4 py-2.5 mb-4 leading-relaxed font-medium">
                        Deskripsi berikut sudah diisi template standar dokumen TUS. Anda dapat mengedit atau menambahkan informasi.
                    </p>
                    <textarea name="deskripsi" rows="12"
                              class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-white text-sm font-mono leading-relaxed focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-y">{{ old('deskripsi', $defaultDeskripsi) }}</textarea>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" name="status" value="draft"
                            class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 rounded-lg transition-colors shadow-sm">
                        Simpan sebagai Draft
                    </button>
                    <button type="submit" name="status" value="aktif"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-lg shadow-md transition-colors">
                        Terbitkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
