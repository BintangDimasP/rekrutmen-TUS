@extends('layouts.admin')

@section('title', 'Edit Lowongan — ' . $lowongan->nama_posisi)

@section('content')

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.lowongan.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Lowongan</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $lowongan->nama_posisi }}</span>
    </div>

    <!-- Single Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-4">
                        
                        <h1 class="text-xl font-bold text-white">Edit Lowongan </h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="px-6 pb-6 pt-0 md:px-8 md:pb-8 md:pt-0">
            <form method="POST" action="{{ route('admin.lowongan.update', $lowongan) }}" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- 1. INFORMASI DASAR --}}
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Program Studi (Prodi Pembuka) <span class="text-red-500">*</span></label>
                            <select name="prodi_id" id="prodi_id" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition cursor-pointer">
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" data-nama="{{ $prodi->nama }}" {{ old('prodi_id', $lowongan->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama }} ({{ $prodi->kode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Nama Posisi <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_posisi" id="nama_posisi" value="{{ old('nama_posisi', $lowongan->nama_posisi) }}" readonly
                                   class="w-full px-4 py-2.5 rounded-lg border @error('nama_posisi') border-red-400 @else border-gray-200 @enderror bg-gray-50 text-sm font-medium text-gray-700 cursor-not-allowed focus:outline-none transition">
                            @error('nama_posisi') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="draft"   {{ old('status', $lowongan->status) === 'draft'   ? 'selected' : '' }}>Draft</option>
                                <option value="aktif"   {{ old('status', $lowongan->status) === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                                
                            </select>
                        </div>
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Penutupan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_tutup" value="{{ old('tanggal_tutup', $lowongan->tanggal_tutup->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
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
                            <select name="jenjang_minimal" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                @foreach(['D3', 'S1', 'S2', 'S3'] as $j)
                                    <option value="{{ $j }}" {{ old('jenjang_minimal', $lowongan->jenjang_minimal) === $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Minimal IPK <span class="text-red-500">*</span></label>
                            <input type="number" name="minimal_ipk" value="{{ old('minimal_ipk', $lowongan->minimal_ipk) }}"
                                   step="0.01" min="0" max="4"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Prodi yang Diprioritaskan</label>
                            <select name="prodi_prioritas" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="">— Tidak ada prioritas —</option>
                                @foreach($prodiPrioritasOptions as $opt)
                                    <option value="{{ $opt }}" {{ old('prodi_prioritas', $lowongan->prodi_prioritas) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Keahlian / Skill yang Dibutuhkan</label>
                            <select name="skill_dibutuhkan" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <option value="">— Tidak ada skill spesifik —</option>
                                @foreach($skillOptions as $opt)
                                    <option value="{{ $opt }}" {{ old('skill_dibutuhkan', $lowongan->skill_dibutuhkan) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Kuota Pendaftaran <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-3">
                                <input type="number" name="kuota" value="{{ old('kuota', $lowongan->kuota) }}" min="1"
                                       class="w-40 px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                                <span class="text-sm text-gray-500">orang &bull; Terpakai: <strong>{{ $lowongan->lamarans->count() }}</strong> &bull; Sisa: <strong class="{{ $lowongan->sisa_kuota <= 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $lowongan->sisa_kuota }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. DESKRIPSI & DOKUMEN --}}
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                        Deskripsi & Dokumen Persyaratan
                    </h3>
                    <textarea name="deskripsi" rows="12"
                              class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-white text-sm font-mono leading-relaxed focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-y">{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-center pt-4 border-t border-gray-100 gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#8b1515] hover:bg-red-900 text-white text-sm font-bold rounded-lg shadow-md shadow-red-900/20 transition-all">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const prodiSelect = document.getElementById('prodi_id');
        const namaPosisiInput = document.getElementById('nama_posisi');

        prodiSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const prodiNama = selectedOption.getAttribute('data-nama');
            
            if (prodiNama) {
                namaPosisiInput.value = 'Dosen Tetap S1 ' + prodiNama;
            }
        });
    });
</script>

@endsection
