@extends('layouts.admin')

@section('title', 'Edit Lowongan — ' . $lowongan->nama_posisi)

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-7">
        <a href="{{ route('admin.lowongan.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-[#8b1515] hover:border-[#8b1515] transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Lowongan</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $lowongan->nama_posisi }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.lowongan.update', $lowongan) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Card 1: Informasi Dasar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#8b1515] px-6 py-4">
                <h2 class="text-base font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Informasi Dasar Lowongan
                </h2>
            </div>
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Posisi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_posisi" value="{{ old('nama_posisi', $lowongan->nama_posisi) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border @error('nama_posisi') border-red-400 @else border-gray-200 @enderror text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                    @error('nama_posisi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Program Studi (Prodi Pembuka) <span class="text-red-500">*</span></label>
                    <select name="prodi_id" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id', $lowongan->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }} ({{ $prodi->kode }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            <option value="draft"   {{ old('status', $lowongan->status) === 'draft'   ? 'selected' : '' }}>📋 Draft</option>
                            <option value="aktif"   {{ old('status', $lowongan->status) === 'aktif'   ? 'selected' : '' }}>✅ Aktif</option>
                            <option value="ditutup" {{ old('status', $lowongan->status) === 'ditutup' ? 'selected' : '' }}>🔒 Ditutup</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Penutupan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_tutup" value="{{ old('tanggal_tutup', $lowongan->tanggal_tutup->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                    </div>
                </div>

            </div>
        </div>

        {{-- Card 2: Persyaratan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#8b1515] px-6 py-4">
                <h2 class="text-base font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Persyaratan Pelamar
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenjang Pendidikan Minimal <span class="text-red-500">*</span></label>
                    <select name="jenjang_minimal" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                        @foreach(['D3', 'S1', 'S2', 'S3'] as $j)
                            <option value="{{ $j }}" {{ old('jenjang_minimal', $lowongan->jenjang_minimal) === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimal IPK <span class="text-red-500">*</span></label>
                    <input type="number" name="minimal_ipk" value="{{ old('minimal_ipk', $lowongan->minimal_ipk) }}"
                           step="0.01" min="0" max="4"
                           class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Prodi yang Diprioritaskan</label>
                    <select name="prodi_prioritas" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                        <option value="">— Tidak ada prioritas —</option>
                        @foreach($prodiPrioritasOptions as $opt)
                            <option value="{{ $opt }}" {{ old('prodi_prioritas', $lowongan->prodi_prioritas) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keahlian / Skill yang Dibutuhkan</label>
                    <select name="skill_dibutuhkan" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                        <option value="">— Tidak ada skill spesifik —</option>
                        @foreach($skillOptions as $opt)
                            <option value="{{ $opt }}" {{ old('skill_dibutuhkan', $lowongan->skill_dibutuhkan) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kuota Pendaftaran <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="kuota" value="{{ old('kuota', $lowongan->kuota) }}" min="1"
                               class="w-40 px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                        <span class="text-sm text-gray-500">orang &bull; Terpakai: <strong>{{ $lowongan->lamarans->count() }}</strong> &bull; Sisa: <strong>{{ $lowongan->sisa_kuota }}</strong></span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Card 3: Deskripsi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#8b1515] px-6 py-4">
                <h2 class="text-base font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h10"/></svg>
                    Deskripsi & Dokumen Persyaratan
                </h2>
            </div>
            <div class="p-6">
                <textarea name="deskripsi" rows="18"
                          class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 font-mono leading-relaxed focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all resize-y">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pb-6">
            <a href="{{ route('admin.lowongan.index') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors shadow-sm">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>

@endsection
