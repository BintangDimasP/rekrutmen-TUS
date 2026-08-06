@extends('layouts.admin')

@section('title', 'Edit Lowongan ')

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
            <form method="POST" action="{{ route('admin.lowongan.update', $lowongan->id) }}" class="space-y-8"
                  x-data="{
                      kategori: '{{ old('kategori', $lowongan->kategori ?? 'Dosen') }}',
                      get isTendik() { return this.kategori === 'Tenaga Kependidikan'; },
                      defaultDosen: {{ \Illuminate\Support\Js::from($defaultDeskripsi ?? '') }},
                      defaultTendik: {{ \Illuminate\Support\Js::from($defaultDeskripsiTendik ?? '') }},
                      deskripsi: {{ \Illuminate\Support\Js::from(old('deskripsi', $lowongan->deskripsi)) }},
                      init() {
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
                @method('PUT')

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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    <span class="text-sm font-semibold">Dosen</span>
                                </label>
                                <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border cursor-pointer transition-all"
                                       :class="kategori === 'Tenaga Kependidikan' ? 'bg-blue-50 border-blue-500 text-blue-600' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                    <input type="radio" name="kategori" value="Tenaga Kependidikan" x-model="kategori" class="hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="text-sm font-semibold">Tenaga Kependidikan</span>
                                </label>
                            </div>
                            @error('kategori') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Prodi (hanya untuk Dosen) --}}
                        <div class="md:col-span-2" x-show="!isTendik" x-transition>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Program Studi <span class="text-red-500">*</span></label>
                            <select name="prodi_id" id="prodi_id" class="hidden">
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" data-nama="{{ $prodi->nama }}" {{ old('prodi_id', $lowongan->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama }} ({{ $prodi->kode }})
                                    </option>
                                @endforeach
                            </select>
                            <div x-data="{
                                    open: false,
                                    val: '{{ old('prodi_id', $lowongan->prodi_id) }}',
                                    opts: [@foreach($prodis as $prodi){ v: '{{ $prodi->id }}', l: '{{ addslashes($prodi->nama) }} ({{ $prodi->kode }})' },@endforeach],
                                    get label() { return this.opts.find(o => o.v == this.val)?.l ?? '— Pilih Prodi —'; },
                                    pick(opt) { this.val = opt.v; this.open = false; var sel = document.getElementById('prodi_id'); sel.value = opt.v; sel.dispatchEvent(new Event('change')); }
                                 }" @click.outside="open = false" class="relative">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm transition-all"
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

                        {{-- Nama Lowongan (Dosen: readonly) / Unit (Tendik: dropdown hardcode) --}}
                        <input type="hidden" name="nama_posisi" id="nama_posisi_submit" value="{{ old('nama_posisi', $lowongan->nama_posisi) }}">

                        <div class="md:col-span-2">
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">
                                <span x-text="isTendik ? 'Unit' : 'Nama Lowongan'"></span>
                                <span class="text-red-500">*</span>
                            </label>

                            <div x-show="!isTendik">
                                <input type="text" id="nama_posisi" value="{{ old('nama_posisi', $lowongan->nama_posisi) }}" readonly
                                       class="w-full px-4 py-2.5 rounded-lg border @error('nama_posisi') border-red-400 @else border-gray-200 @enderror bg-gray-50 text-sm font-medium text-gray-700 cursor-not-allowed focus:outline-none transition">
                            </div>

                            <div x-show="isTendik" class="relative" x-data="{
                                open: false,
                                val: '{{ old('nama_posisi', $lowongan->nama_posisi) }}',
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
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Penutupan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_tutup" value="{{ old('tanggal_tutup', $lowongan->tanggal_tutup->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 rounded-lg border @error('tanggal_tutup') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            @error('tanggal_tutup') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Kuota Pendaftaran <span class="text-red-500">*</span></label>
                            <input type="number" name="kuota" value="{{ old('kuota', $lowongan->kuota) }}" min="1"
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
                            <div x-data="{
                                    open: false,
                                    val: '{{ old('jenjang_minimal', $lowongan->jenjang_minimal) }}',
                                    get opts() {
                                        return isTendik
                                            ? [{ v: '', l: '— Pilih Jenjang —' }, { v: 'SMA/SMK', l: 'SMA/SMK' }, { v: 'D3', l: 'D3' }, { v: 'D4', l: 'D4' }, { v: 'S1', l: 'S1' }, { v: 'S2', l: 'S2' }]
                                            : [{ v: '', l: '— Pilih Jenjang —' }, { v: 'D3', l: 'D3' }, { v: 'S1', l: 'S1' }, { v: 'S2', l: 'S2' }, { v: 'S3', l: 'S3' }];
                                    },
                                    get label() { return this.opts.find(o => o.v === this.val)?.l ?? '— Pilih —'; }
                                 }" @click.outside="open = false" class="relative">
                                <input type="hidden" name="jenjang_minimal" :value="val">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm transition-all"
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
                        </div>

                        <div x-show="!isTendik" x-transition>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Minimal IPK <span class="text-red-500">*</span></label>
                            <input type="number" name="minimal_ipk" value="{{ old('minimal_ipk', $lowongan->minimal_ipk) }}"
                                   step="0.01" min="0" max="4"
                                   oninput="if(parseFloat(this.value)>4){this.value='4'}"
                                   :disabled="isTendik"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition disabled:bg-gray-100">
                        </div>

                        <div x-show="!isTendik" x-transition>
                            <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Prodi yang Diprioritaskan</label>
                            <div x-data="{
                                    open: false,
                                    selected: {{ \Illuminate\Support\Js::from(
                                        old('prodi_prioritas')
                                            ? array_values(array_filter(explode('||', old('prodi_prioritas'))))
                                            : ($lowongan->prodi_prioritas ? array_values(array_filter(array_map('trim', explode(',', $lowongan->prodi_prioritas)))) : [])
                                    ) }},
                                    get label() { return this.selected.length === 0 ? '— Tidak ada prioritas —' : this.selected.join(', '); },
                                    get joined() { return this.selected.join('||'); },
                                    toggle(v) { const i = this.selected.indexOf(v); if (i === -1) this.selected.push(v); else this.selected.splice(i, 1); },
                                    isChecked(v) { return this.selected.includes(v); }
                                 }" @click.outside="open = false" class="relative">
                                <input type="hidden" name="prodi_prioritas" :value="joined" :disabled="isTendik">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm transition-all"
                                    :class="selected.length > 0 ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate mr-2 text-left"></span>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <span x-show="selected.length > 0" x-text="selected.length" class="w-5 h-5 rounded-full bg-[#8b1515] text-white text-[0.6rem] font-bold flex items-center justify-center"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <button type="button" @click="selected = []" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100" :class="selected.length === 0 ? 'text-gray-900 font-semibold bg-gray-100' : 'text-gray-500'">— Tidak ada prioritas —</button>
                                        @foreach($prodiPrioritasOptions as $opt)
                                        <button type="button" @click="toggle({{ \Illuminate\Support\Js::from($opt) }})" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100 text-gray-700">
                                            <span class="w-4 h-4 rounded border-2 flex-shrink-0 flex items-center justify-center transition-colors" :class="isChecked({{ \Illuminate\Support\Js::from($opt) }}) ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
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
                                            : ($lowongan->skill_dibutuhkan ? array_values(array_filter(array_map('trim', explode(',', $lowongan->skill_dibutuhkan)))) : [])
                                    ) }},
                                    allOptsDosen: {{ \Illuminate\Support\Js::from($skillOptions) }},
                                    allOptsTendik: {{ \Illuminate\Support\Js::from($skillOptionsTendik ?? []) }},
                                    get activeOpts() { return this.isTendik ? this.allOptsTendik : this.allOptsDosen; },
                                    get label() { return this.selected.length === 0 ? '— Pilih Skill —' : this.selected.join(', '); },
                                    get joined() { return this.selected.join('||'); },
                                    toggle(v) { const i = this.selected.indexOf(v); if (i === -1) this.selected.push(v); else this.selected.splice(i, 1); },
                                    isChecked(v) { return this.selected.includes(v); }
                                 }" @click.outside="open = false" class="relative">
                                <input type="hidden" name="skill_dibutuhkan" :value="joined">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-sm transition-all"
                                    :class="selected.length > 0 ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate mr-2 text-left"></span>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <span x-show="selected.length > 0" x-text="selected.length" class="w-5 h-5 rounded-full bg-[#8b1515] text-white text-[0.6rem] font-bold flex items-center justify-center"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <button type="button" @click="selected = []" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100" :class="selected.length === 0 ? 'text-gray-900 font-semibold bg-gray-100' : 'text-gray-500'">— Tidak ada skill spesifik —</button>
                                        <template x-for="opt in activeOpts" :key="opt">
                                        <button type="button" @click="toggle(opt)" class="w-full min-w-0 flex items-center gap-2.5 px-3 py-2.5 text-sm rounded-lg transition-colors hover:bg-gray-100 text-gray-700 text-left">
                                            <span class="w-4 h-4 rounded border-2 flex-shrink-0 flex items-center justify-center transition-colors" :class="isChecked(opt) ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
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
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                            Deskripsi & Dokumen Persyaratan
                        </h3>
                        <textarea name="deskripsi" rows="12" x-model="deskripsi"
                                  class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-white text-sm font-medium leading-relaxed focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-y"></textarea>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <input type="hidden" name="status" value="{{ $lowongan->status }}">
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
