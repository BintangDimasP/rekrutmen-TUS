@extends('layouts.admin')

@section('title', 'Tambah Lowongan')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.lowongan.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Lowongan</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">Tambah Lowongan</span>
    </div>

    <!-- Single Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">Tambah Lowongan</h1>
                   
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="px-6 pb-6 pt-0 md:px-8 md:pb-8 md:pt-0">
            <form method="POST" action="{{ route('admin.lowongan.store') }}" class="space-y-8"
                  x-data="{
                      kategori: '{{ old('kategori', 'Dosen') }}',
                      get isTendik() { return this.kategori === 'Tenaga Kependidikan'; },
                      defaultDosen: {{ \Illuminate\Support\Js::from($defaultDeskripsi) }},
                      defaultTendik: {{ \Illuminate\Support\Js::from($defaultDeskripsiTendik) }},
                      deskripsi: {{ \Illuminate\Support\Js::from(old('deskripsi', '')) }},
                      init() {
                          if (!this.deskripsi) {
                              this.deskripsi = this.isTendik ? this.defaultTendik : this.defaultDosen;
                          }
                          this.$watch('kategori', val => {
                              if (val === 'Tenaga Kependidikan' && this.deskripsi === this.defaultDosen) {
                                  this.deskripsi = this.defaultTendik;
                              } else if (val === 'Dosen' && this.deskripsi === this.defaultTendik) {
                                  this.deskripsi = this.defaultDosen;
                              }
                          });
                      }
                  }">
                @csrf

                {{-- 1. INFORMASI DASAR --}}
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Kategori --}}
                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-3">Kategori Lowongan <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-3 flex-wrap">
                                <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border cursor-pointer transition-all"
                                       :class="kategori === 'Dosen' ? 'bg-[#8b1515]/5 border-[#8b1515] text-[#8b1515]' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                    <input type="radio" name="kategori" value="Dosen" x-model="kategori" class="hidden">
                                    <span class="text-sm font-semibold">Dosen</span>
                                </label>
                                <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border cursor-pointer transition-all"
                                       :class="kategori === 'Tenaga Kependidikan' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                    <input type="radio" name="kategori" value="Tenaga Kependidikan" x-model="kategori" class="hidden">
                                    <span class="text-sm font-semibold">Tenaga Kependidikan</span>
                                </label>
                            </div>
                            @error('kategori') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Prodi (hanya untuk Dosen) --}}
                        <div class="md:col-span-2" x-show="!isTendik" x-transition>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Program Studi <span class="text-red-500">*</span></label>
                            {{-- hidden select tetap ada untuk JS listener & submit --}}
                            <select name="prodi_id" id="prodi_id"
                                    class="hidden">
                                <option value="" data-nama="">— Pilih Prodi —</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" data-nama="{{ $prodi->nama }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama }} ({{ $prodi->kode }})
                                    </option>
                                @endforeach
                            </select>
                            <div x-data="{
                                    open: false,
                                    val: '{{ old('prodi_id') }}',
                                    opts: [{ v: '', l: '— Pilih Prodi —', k: '' }, @foreach($prodis as $prodi){ v: '{{ $prodi->id }}', l: '{{ addslashes($prodi->nama) }} ({{ $prodi->kode }})', k: '{{ addslashes($prodi->kode) }}' },@endforeach],
                                    get label() { return this.opts.find(o => o.v == this.val)?.l ?? '— Pilih Prodi —'; },
                                    pick(opt) {
                                        this.val = opt.v;
                                        this.open = false;
                                        var sel = document.getElementById('prodi_id');
                                        sel.value = opt.v;
                                        sel.dispatchEvent(new Event('change'));
                                    }
                                 }" @click.outside="open = false" class="relative">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border @error('prodi_id') border-red-400 @else border-gray-200 @enderror bg-white text-sm transition-all"
                                    :class="val ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate"></span>
                                    <svg class="w-4 h-4 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <template x-for="opt in opts" :key="opt.v">
                                            <button type="button" @click="pick(opt)"
                                                class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                                :class="val == opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                                <span x-text="opt.l"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('prodi_id') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nama Lowongan (Dosen: readonly, auto dari JS) / Unit (Tendik: dropdown hardcode) --}}
                        {{-- Single hidden input yang selalu di-submit --}}
                        <input type="hidden" name="nama_posisi" id="nama_posisi_submit" value="{{ old('nama_posisi') }}">

                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">
                                <span x-text="isTendik ? 'Unit' : 'Nama Lowongan'"></span>
                                <span class="text-red-500">*</span>
                            </label>

                            {{-- Untuk DOSEN: readonly display input (tidak di-submit, hanya tampilan) --}}
                            <div x-show="!isTendik">
                                <input type="text" id="nama_posisi" value="{{ old('nama_posisi') }}" placeholder="Dosen Tetap" readonly
                                       class="w-full px-4 py-2.5 rounded-lg border @error('nama_posisi') border-red-400 @else border-gray-200 @enderror bg-gray-50 text-sm font-medium text-gray-700 cursor-not-allowed focus:outline-none transition">
                            </div>

                            {{-- Untuk TENDIK: dropdown Unit (hardcoded) --}}
                            <div x-show="isTendik" class="relative" x-data="{
                                open: false,
                                val: '{{ old('nama_posisi') }}',
                                units: [
                                    'Logistik dan Aset',
                                    'Keuangan',
                                    'Sumber Daya Manusia',
                                    'Pusat Teknologi Informasi',
                                    'Pemasaran & Admisi',
                                    'Pengembangan Karir, Alumni, & Endowment',
                                    'Kemahasiswaan',
                                ],
                                get label() { return this.val || '— Pilih Unit —'; },
                                pick(u) {
                                    this.val = u;
                                    this.open = false;
                                    document.getElementById('nama_posisi_submit').value = u;
                                }
                            }" @click.outside="open = false" style="display:none;">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border @error('nama_posisi') border-red-400 @else border-gray-200 @enderror bg-white text-sm transition-all"
                                    :class="val ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate"></span>
                                    <svg class="w-4 h-4 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden mt-1" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <template x-for="u in units" :key="u">
                                            <button type="button" @click="pick(u)"
                                                class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                                :class="val === u ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                                <span x-text="u"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('nama_posisi') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                      
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Penutupan<span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_tutup" value="{{ old('tanggal_tutup') }}"
                                   class="w-full px-4 py-2.5 rounded-lg border @error('tanggal_tutup') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            @error('tanggal_tutup') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Kuota Pendaftaran <span class="text-red-500">*</span></label>
                            
                                <input type="number" name="kuota" value="{{ old('kuota', 1) }}" min="1" placeholder="cth: 5"
                                       class="w-full px-4 py-2.5 rounded-lg border @error('kuota') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            
                            @error('kuota') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
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
                            <div x-data="{
                                    open: false,
                                    val: '{{ old('jenjang_minimal', 'S1') }}',
                                    get opts() {
                                        return isTendik
                                            ? [{ v: '', l: '— Pilih Jenjang —' }, { v: 'SMA/SMK', l: 'SMA/SMK' }, { v: 'D3', l: 'D3' }, { v: 'D4', l: 'D4' }, { v: 'S1', l: 'S1' }, { v: 'S2', l: 'S2' }]
                                            : [{ v: '', l: '— Pilih Jenjang —' }, { v: 'D3', l: 'D3' }, { v: 'S1', l: 'S1' }, { v: 'S2', l: 'S2' }, { v: 'S3', l: 'S3' }];
                                    },
                                    get label() { return this.opts.find(o => o.v === this.val)?.l ?? '— Pilih Jenjang —'; }
                                 }" @click.outside="open = false" class="relative">
                                <input type="hidden" name="jenjang_minimal" :value="val">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border @error('jenjang_minimal') border-red-400 @else border-gray-200 @enderror bg-white text-sm transition-all"
                                    :class="val ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label"></span>
                                    <svg class="w-4 h-4 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5">
                                        <template x-for="opt in opts" :key="opt.v">
                                            <button type="button" @click="val = opt.v; open = false"
                                                class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                                :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                                <span x-text="opt.l"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('jenjang_minimal') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div x-show="!isTendik" x-transition>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Minimal IPK <span class="text-red-500">*</span></label>
                            <input type="number" name="minimal_ipk" value="{{ old('minimal_ipk', '3.00') }}"
                                   step="0.01" min="0" max="4" placeholder="cth: 3.00"
                                   oninput="if(parseFloat(this.value)>4){this.value='4'}"
                                   :disabled="isTendik"
                                   class="w-full px-4 py-2.5 rounded-lg border @error('minimal_ipk') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition disabled:bg-gray-100">
                            @error('minimal_ipk') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div x-show="!isTendik" x-transition>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Prodi yang Diprioritaskan</label>
                            <div x-data="{
                                    open: false,
                                    selected: {{ \Illuminate\Support\Js::from(
                                        old('prodi_prioritas')
                                            ? array_values(array_filter(explode('||', old('prodi_prioritas'))))
                                            : []
                                    ) }},
                                    allOpts: {{ \Illuminate\Support\Js::from($prodiPrioritasOptions) }},
                                    get label() {
                                        return this.selected.length === 0 ? '— Tidak ada prioritas khusus —' : this.selected.join(', ');
                                    },
                                    get joined() { return this.selected.join('||'); },
                                    toggle(v) {
                                        const idx = this.selected.indexOf(v);
                                        if (idx === -1) this.selected.push(v);
                                        else this.selected.splice(idx, 1);
                                    },
                                    isChecked(v) { return this.selected.includes(v); },
                                    setFirst(detail) {
                                        const v = typeof detail === 'object' ? detail.nama : detail;
                                        const isUserChange = typeof detail === 'object' ? detail.isUserChange : false;
                                        if (!v || !this.allOpts.includes(v)) return;
                                        if (isUserChange || this.selected.length === 0) {
                                            this.selected = [v];
                                        }
                                    }
                                 }"
                                 @click.outside="open = false"
                                 @set-prodi-pembuka.window="setFirst($event.detail)"
                                 class="relative">

                                {{-- Satu hidden input, nilai dipisah '||', di-split di server (hanya jika Dosen) --}}
                                <input type="hidden" name="prodi_prioritas" :value="joined" :disabled="isTendik">

                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm transition-all"
                                    :class="selected.length > 0 ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate mr-2 text-left"></span>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <span x-show="selected.length > 0" x-text="selected.length"
                                              class="w-5 h-5 rounded-full bg-[#8b1515] text-white text-[0.6rem] font-bold flex items-center justify-center"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>

                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <button type="button" @click="selected = []"
                                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100"
                                            :class="selected.length === 0 ? 'text-gray-900 font-semibold bg-gray-100' : 'text-gray-500'">
                                            — Tidak ada prioritas khusus —
                                        </button>
                                        @foreach($prodiPrioritasOptions as $opt)
                                        <button type="button" @click="toggle({{ \Illuminate\Support\Js::from($opt) }})"
                                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100 text-gray-700">
                                            <span class="w-4 h-4 rounded border-2 flex-shrink-0 flex items-center justify-center transition-colors"
                                                  :class="isChecked({{ \Illuminate\Support\Js::from($opt) }}) ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                                <svg x-show="isChecked({{ \Illuminate\Support\Js::from($opt) }})" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                            <span>{{ $opt }}</span>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Keahlian / Skill yang Dibutuhkan</label>
                            <div x-data="{
                                    open: false,
                                    selected: {{ \Illuminate\Support\Js::from(
                                        old('skill_dibutuhkan')
                                            ? array_values(array_filter(explode('||', old('skill_dibutuhkan'))))
                                            : []
                                    ) }},
                                    allOptsDosen: {{ \Illuminate\Support\Js::from($skillOptions) }},
                                    allOptsTendik: {{ \Illuminate\Support\Js::from($skillOptionsTendik ?? []) }},
                                    get activeOpts() { return isTendik ? this.allOptsTendik : this.allOptsDosen; },
                                    get label() {
                                        return this.selected.length === 0 ? '— Tidak ada skill spesifik —' : this.selected.join(', ');
                                    },
                                    get joined() { return this.selected.join('||'); },
                                    toggle(v) {
                                        const idx = this.selected.indexOf(v);
                                        if (idx === -1) this.selected.push(v);
                                        else this.selected.splice(idx, 1);
                                    },
                                    isChecked(v) { return this.selected.includes(v); }
                                 }" @click.outside="open = false" class="relative">
                                <input type="hidden" name="skill_dibutuhkan" :value="joined">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm transition-all"
                                    :class="selected.length > 0 ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate mr-2 text-left"></span>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <span x-show="selected.length > 0" x-text="selected.length"
                                              class="w-5 h-5 rounded-full bg-[#8b1515] text-white text-[0.6rem] font-bold flex items-center justify-center"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <button type="button" @click="selected = []"
                                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100"
                                            :class="selected.length === 0 ? 'text-gray-900 font-semibold bg-gray-100' : 'text-gray-500'">
                                            — Tidak ada skill spesifik —
                                        </button>
                                        <template x-for="opt in activeOpts" :key="opt">
                                        <button type="button" @click="toggle(opt)"
                                            class="w-full min-w-0 flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100 text-gray-700 text-left">
                                            <span class="w-4 h-4 rounded border-2 flex-shrink-0 flex items-center justify-center transition-colors"
                                                  :class="isChecked(opt) ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                                <svg x-show="isChecked(opt)" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                            <span x-text="opt" class="min-w-0 break-words"></span>
                                        </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>

                {{-- 3. DESKRIPSI & DOKUMEN --}}
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                                Deskripsi & Dokumen Persyaratan
                            </h3>
                        </div>
                        <textarea name="deskripsi" rows="12" x-model="deskripsi"
                                  class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-white text-sm font-medium leading-relaxed focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-y"></textarea>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" name="status" value="aktif"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-lg shadow-md transition-colors">
                        Tambah Lowongan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const prodiSelect = document.getElementById('prodi_id');
        const namaPosisiDisplay = document.getElementById('nama_posisi');
        const namaPosisiSubmit = document.getElementById('nama_posisi_submit');

        function setProdiPembuka(prodiNama, isUserChange = false) {
            if (!prodiNama) return;
            window.dispatchEvent(new CustomEvent('set-prodi-pembuka', { detail: { nama: prodiNama, isUserChange: isUserChange } }));
        }

        prodiSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const prodiNama = opt.getAttribute('data-nama');
            if (prodiNama) {
                const val = 'Dosen Tetap S1 ' + prodiNama;
                if (namaPosisiDisplay) namaPosisiDisplay.value = val;
                if (namaPosisiSubmit) namaPosisiSubmit.value = val;
                setProdiPembuka(prodiNama, true);
            } else {
                if (namaPosisiDisplay) namaPosisiDisplay.value = '';
                if (namaPosisiSubmit) namaPosisiSubmit.value = '';
            }
        });

        // Jika prodi sudah dipilih saat load (old value)
        if (prodiSelect.value) {
            const opt = prodiSelect.options[prodiSelect.selectedIndex];
            const nama = opt?.getAttribute('data-nama');
            if (nama) {
                const val = 'Dosen Tetap S1 ' + nama;
                if (namaPosisiDisplay) namaPosisiDisplay.value = val;
                if (namaPosisiSubmit) namaPosisiSubmit.value = val;
                // Tunggu Alpine init
                setTimeout(() => setProdiPembuka(nama, false), 50);
            }
        }
    });
</script>

@endsection
