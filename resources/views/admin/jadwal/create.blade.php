@extends('layouts.admin')

@section('title', 'Penjadwalan Seleksi')

@section('content')
    <div class="max-w-7xl mx-auto" x-data="jadwalForm">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('admin.jadwal.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Jadwal Seleksi</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-semibold text-gray-800">Penjadwalan Seleksi</span>
        </div>

        {{-- Error Laravel --}}
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-sm font-semibold text-red-800 mb-1">Gagal menyimpan jadwal:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li class="text-xs text-red-700">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Toast Notification --}}
        <div x-show="error" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-end="opacity-0 translate-x-12" x-cloak
            class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px] max-w-sm">
            <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center flex-shrink-0 text-white">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Perhatian</h4>
                <p class="text-[0.8rem] text-gray-500 leading-snug" x-text="error"></p>
            </div>
            <button @click="error = ''" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.jadwal.store') }}" @submit.prevent="submitForm($event)">
            @csrf

            {{-- ══ INFORMASI DASAR ══ --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal Seleksi</label>
                        <input type="date" name="tanggal" x-model="tanggal" @change="onTanggalChange()"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition">
                    </div>

                    {{-- Prodi --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Program Studi</label>
                        {{-- hidden select untuk form submit & x-model --}}
                        <select name="prodi_id" x-model="prodiId" @change="onProdiChange()" class="hidden">
                            <option value="">— Pilih —</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        <div x-data="{
                                open: false,
                                opts: [{ v: '', l: '— Pilih —' }, @foreach($prodis as $prodi){ v: '{{ $prodi->id }}', l: '{{ addslashes($prodi->nama) }}' },@endforeach],
                                get label() { return this.opts.find(o => o.v == prodiId)?.l ?? '— Pilih —'; }
                             }" @click.outside="open = false" class="relative">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm transition-all"
                                :class="prodiId ? 'text-gray-800' : 'text-gray-400'">
                                <span x-text="label" class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="prodiId = opt.v; onProdiChange(); open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="prodiId == opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lowongan --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Lowongan</label>
                        <div x-show="loadingLowongan"
                            class="flex items-center gap-2 text-xs text-gray-400 px-3 py-2 border border-gray-200 rounded-lg h-[38px]">
                            <svg class="w-3.5 h-3.5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                                <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                            Memuat...
                        </div>
                        {{-- hidden select untuk form submit --}}
                        <select name="lowongan_id" x-model="lowonganId" @change="onLowonganChange()" class="hidden">
                            <option value="">— Pilih —</option>
                            <template x-for="l in lowongans" :key="l.id">
                                <option :value="l.id" x-text="l.nama_posisi"></option>
                            </template>
                        </select>
                        <div x-show="!loadingLowongan"
                             x-data="{ open: false }" @click.outside="open = false" class="relative">
                            <button type="button" @click="if(prodiId) open = !open" :disabled="!prodiId"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="lowonganId ? 'text-gray-800' : 'text-gray-400'">
                                <span x-text="lowonganId ? (lowongans.find(l => l.id == lowonganId)?.nama_posisi ?? '— Pilih —') : '— Pilih —'" class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                    <template x-for="l in lowongans" :key="l.id">
                                        <button type="button" @click="lowonganId = l.id; onLowonganChange(); open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="lowonganId == l.id ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="l.nama_posisi"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Search Pelamar --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Cari Pelamar</label>
                        <input type="text" x-model="search" placeholder="Cari nama atau email..."
                            :disabled="!lowonganId || loadingPelamar || pelamars.length === 0"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>
                </div>
            </div>

            {{-- ══ TABEL PENJADWALAN ══ --}}

            {{-- Toolbar tabel --}}
            <div class="flex items-center justify-between gap-3 mb-3"
                x-show="lowonganId && !loadingPelamar && pelamars.length > 0">
                <div class="flex items-center gap-3">
                    
                    <span x-show="!tanggal"
                        class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1">
                        Pilih tanggal agar cek bentrok aktif.
                    </span>
                </div>
            </div>

            {{-- TABEL UTAMA --}}
            <div class="rounded-xl border border-gray-200 overflow-hidden mb-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" style="min-width: 1030px; table-layout: fixed;">
                        <colgroup>
                            <col style="width: 40px">       {{-- No --}}
                            <col style="width: 190px">      {{-- Pelamar --}}
                            <col style="width: 220px">      {{-- Penguji Micro --}}
                            <col style="width: 220px">      {{-- Penguji Wawancara --}}
                            <col style="width: 220px">      {{-- Sesi --}}
                            <col style="width: 210px">      {{-- Link Meeting --}}
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="bg-[#8b1515] text-white text-xs font-bold text-center px-3 py-3">No.</th>
                                <th class="bg-[#8b1515] text-white text-xs font-bold text-center px-3 py-3">Pelamar</th>
                                <th class="bg-[#8b1515] text-white text-xs font-bold text-center px-3 py-3 border-l border-white/20">
                                    Penguji Micro Teaching
                                    <span class="font-normal text-white/70 block mt-0.5 text-xs">30 mnt pertama</span>
                                </th>
                                <th class="bg-[#8b1515] text-white text-xs font-bold text-center px-3 py-3 border-l border-white/20">
                                    Penguji Wawancara
                                    <span class="font-normal text-white/70 block mt-0.5 text-xs">30 mnt kedua</span>
                                </th>
                                <th class="bg-[#8b1515] text-white text-xs font-bold text-center px-3 py-3 border-l border-white/20">Sesi Seleksi</th>
                                <th class="bg-[#8b1515] text-white text-xs font-bold text-center px-3 py-3 border-l border-white/20">Jenis &amp; Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- State: belum pilih lowongan --}}
                            <tr x-show="!lowonganId">
                                <td colspan="6" class="py-12 text-center bg-gray-50 border-b border-gray-100">
                                    <p class="text-xs text-gray-400">Isi informasi dasar di atas terlebih dahulu.</p>
                                </td>
                            </tr>

                            {{-- State: loading --}}
                            <tr x-show="loadingPelamar">
                                <td colspan="6" class="py-12 text-center bg-white border-b border-gray-100">
                                    <div class="flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-800 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                                            <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                        </svg>
                                    </div>
                                </td>
                            </tr>

                            {{-- State: tidak ada pelamar --}}
                            <tr x-show="!loadingPelamar && lowonganId && pelamars.length === 0">
                                <td colspan="6" class="py-12 text-center bg-white border-b border-gray-100">
                                    <p class="text-xs text-gray-400">Tidak ada pelamar siap dijadwalkan di lowongan ini.</p>
                                </td>
                            </tr>
                            <template x-for="(p, idx) in filteredPelamars" :key="p.id">
                                <tr class="border-t border-gray-100 align-top transition-colors" :class="{
                                        'bg-red-50':   getConflictWarning(p.id),
                                        'hover:bg-gray-50': !getConflictWarning(p.id)
                                    }">

                                    {{-- No --}}
                                    <td class="py-3 px-3 text-xs text-gray-700 font-medium" x-text="idx + 1"></td>

                                    {{-- Pelamar --}}
                                    <td class="py-3 px-3 align-top">
                                        <div class="flex items-start gap-2">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate" :title="p.nama" x-text="p.nama"></p>
                                                <p class="text-[0.65rem] font-medium text-gray-500 mt-0.5 truncate" :title="p.email" x-text="p.email"></p>
                                                <p x-show="getConflictWarning(p.id)" x-text="getConflictWarning(p.id)"
                                                    class="text-[0.62rem] text-red-600 mt-1 leading-snug"></p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ═══ MICRO: Penguji (multi, tag style) ═══ --}}
                                    <td class="py-3 px-3 align-top border-l border-gray-100">
                                        <div x-show="loadingPenguji" class="text-xs text-gray-400">Memuat...</div>
                                        <template x-if="!loadingPenguji">
                                            <div>
                                                <div class="flex flex-wrap gap-1 mb-1.5" x-show="assignments[p.id]?.penguji_micro_ids?.length > 0">
                                                    <template x-for="pgId in assignments[p.id]?.penguji_micro_ids ?? []" :key="pgId">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 border border-red-100 text-[0.65rem] text-red-800 max-w-full">
                                                            <span class="truncate max-w-[120px]" :title="getPengujiNama(pgId)" x-text="getPengujiNama(pgId)"></span>
                                                            <button type="button" @click="removePengujiM(p.id, pgId)" class="text-red-300 hover:text-red-600 transition-colors ml-0.5 flex-shrink-0">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            </button>
                                                            <input type="hidden" :name="`schedule[${p.id}][penguji_micro_ids][]`" :value="pgId">
                                                        </span>
                                                    </template>
                                                </div>
                                                <div x-data="{ open:false, t:0, l:0, w:0,
                                                        place(){ const r=$refs.trg.getBoundingClientRect(); this.t=r.bottom+4; this.l=r.left; this.w=r.width; },
                                                        toggle(){ this.open=!this.open; if(this.open) $nextTick(()=>this.place()); } }"
                                                     @click.outside="open=false" class="relative">
                                                    <button type="button" x-ref="trg" @click="toggle()"
                                                        class="w-full flex items-center justify-between px-2 py-2 rounded-lg border border-gray-200 bg-gray-50 text-xs text-gray-600 hover:border-gray-300 transition-all">
                                                        <span class="truncate">+ Tambah penguji micro</span>
                                                        <svg class="w-3.5 h-3.5 text-gray-400 ml-1 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                    <div x-show="open" x-transition @click.outside="open=false"
                                                         :style="`top:${t}px;left:${l}px;width:${w}px;`"
                                                         class="fixed z-[200] bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                                        <div class="p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                                            <template x-if="availablePengujisM(p.id).length === 0">
                                                                <div class="px-3 py-2 text-xs text-gray-400">Semua penguji sudah dipilih</div>
                                                            </template>
                                                            <template x-for="pg in availablePengujisM(p.id)" :key="pg.id">
                                                                <button type="button" @click="addPengujiMById(p.id, pg.id); open=false"
                                                                    class="w-full text-left px-3 py-2 text-xs rounded-lg hover:bg-gray-100 text-gray-700 transition-colors truncate"
                                                                    x-text="`${pg.nama} (${pg.kode})`"></button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </td>

                                    {{-- ═══ WAWANCARA: Penguji (multi, tag style) ═══ --}}
                                    <td class="py-3 px-3 align-top border-l border-gray-100">
                                        <div x-show="loadingPenguji" class="text-xs text-gray-400">Memuat...</div>
                                        <template x-if="!loadingPenguji">
                                            <div>
                                                <div class="flex flex-wrap gap-1 mb-1.5" x-show="assignments[p.id]?.penguji_wawancara_ids?.length > 0">
                                                    <template x-for="pgId in assignments[p.id]?.penguji_wawancara_ids ?? []" :key="pgId">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 border border-red-100 text-[0.65rem] text-red-800 max-w-full">
                                                            <span class="truncate max-w-[120px]" :title="getPengujiNama(pgId)" x-text="getPengujiNama(pgId)"></span>
                                                            <button type="button" @click="removePengujiW(p.id, pgId)" class="text-red-300 hover:text-red-600 transition-colors ml-0.5 flex-shrink-0">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            </button>
                                                            <input type="hidden" :name="`schedule[${p.id}][penguji_wawancara_ids][]`" :value="pgId">
                                                        </span>
                                                    </template>
                                                </div>
                                                <div x-data="{ open:false, t:0, l:0, w:0,
                                                        place(){ const r=$refs.trg.getBoundingClientRect(); this.t=r.bottom+4; this.l=r.left; this.w=r.width; },
                                                        toggle(){ this.open=!this.open; if(this.open) $nextTick(()=>this.place()); } }"
                                                     @click.outside="open=false" class="relative">
                                                    <button type="button" x-ref="trg" @click="toggle()"
                                                        class="w-full flex items-center justify-between px-2 py-2 rounded-lg border border-gray-200 bg-gray-50 text-xs text-gray-600 hover:border-gray-300 transition-all">
                                                        <span class="truncate">+ Tambah penguji wawancara</span>
                                                        <svg class="w-3.5 h-3.5 text-gray-400 ml-1 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                    <div x-show="open" x-transition @click.outside="open=false"
                                                         :style="`top:${t}px;left:${l}px;width:${w}px;`"
                                                         class="fixed z-[200] bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                                        <div class="p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                                            <template x-if="availablePengujisW(p.id).length === 0">
                                                                <div class="px-3 py-2 text-xs text-gray-400">Semua penguji sudah dipilih</div>
                                                            </template>
                                                            <template x-for="pg in availablePengujisW(p.id)" :key="pg.id">
                                                                <button type="button" @click="addPengujiWById(p.id, pg.id); open=false"
                                                                    class="w-full text-left px-3 py-2 text-xs rounded-lg hover:bg-gray-100 text-gray-700 transition-colors truncate"
                                                                    x-text="`${pg.nama} (${pg.kode})`"></button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </td>

                                    {{-- ═══ SESI (Unified) ═══ --}}
                                    <td class="py-3 px-3 align-top border-l border-gray-100">
                                        <div x-data="{ open:false, t:0, l:0, w:0,
                                                place(){ const r=$refs.trg.getBoundingClientRect(); this.t=r.bottom+4; this.l=r.left; this.w=Math.max(r.width, 280); },
                                                toggle(){ this.open=!this.open; if(this.open) $nextTick(()=>this.place()); } }"
                                             @click.outside="open=false" class="relative">
                                            <input type="hidden" :name="`schedule[${p.id}][sesi]`" x-model="assignments[p.id].sesi">
                                            <button type="button" x-ref="trg" @click="toggle()"
                                                class="w-full flex items-center justify-between px-2 py-2 rounded-lg border text-xs transition-all"
                                                :class="isSesiConflict(p.id) ? 'border-red-400 bg-red-50 text-red-700' : (assignments[p.id].sesi ? 'border-gray-200 bg-gray-50 text-gray-700' : 'border-gray-200 bg-gray-50 text-gray-500')">
                                                <span class="truncate" x-text="assignments[p.id].sesi ? sesiLabel(assignments[p.id].sesi) : '— Pilih Sesi —'"></span>
                                                <svg class="w-3.5 h-3.5 text-gray-400 ml-1 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" x-transition @click.outside="open=false"
                                                 :style="`top:${t}px;left:${l}px;width:${w}px;`"
                                                 class="fixed z-[200] bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                                <div class="p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                                    <template x-for="(info, sesiKey) in sessions.micro_teaching" :key="sesiKey">
                                                        <button type="button"
                                                            @click="if(!isSesiDisabled(p.id, parseInt(sesiKey))){ assignments[p.id].sesi = sesiKey; open=false; }"
                                                            :disabled="isSesiDisabled(p.id, parseInt(sesiKey))"
                                                            class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors flex items-center gap-2"
                                                            :class="isSesiDisabled(p.id, parseInt(sesiKey)) ? 'text-gray-300 cursor-not-allowed bg-gray-50' : (assignments[p.id].sesi == sesiKey ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700')">
                                                            <span class="flex-1">
                                                                <span x-text="sesiLabel(sesiKey, false)"></span>
                                                            </span>
                                                            <template x-if="isSesiDisabled(p.id, parseInt(sesiKey))">
                                                                <span class="inline-flex items-center gap-1 text-[0.6rem] text-gray-800 font-medium whitespace-nowrap" :title="getSesiDisabledReason(p.id, parseInt(sesiKey))">
                                                                    <svg class="w-3 h-3 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                                                    <span x-text="getSesiDisabledReason(p.id, parseInt(sesiKey))"></span>
                                                                </span>
                                                            </template>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ═══ JENIS & LOKASI ═══ --}}
                                    <td class="py-3 px-3 align-top border-l border-gray-100">
                                        <div class="space-y-1.5">
                                            {{-- Toggle Online/Offline --}}
                                            <div class="flex rounded-lg overflow-hidden border border-gray-200 text-xs font-semibold">
                                                <button type="button"
                                                    @click="assignments[p.id].jenis = 'online'; assignments[p.id].lokasi = ''"
                                                    :class="assignments[p.id].jenis === 'online' ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                                                    class="flex-1 py-1.5 transition-colors">
                                                    Online
                                                </button>
                                                <button type="button"
                                                    @click="assignments[p.id].jenis = 'offline'; assignments[p.id].lokasi = ''"
                                                    :class="assignments[p.id].jenis === 'offline' ? 'bg-amber-500 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                                                    class="flex-1 py-1.5 transition-colors">
                                                    Offline
                                                </button>
                                            </div>
                                            {{-- Input Lokasi --}}
                                            <input type="hidden" :name="`schedule[${p.id}][jenis]`" x-model="assignments[p.id].jenis">
                                            <template x-if="assignments[p.id].jenis === 'online'">
                                                <input type="url" :name="`schedule[${p.id}][lokasi]`"
                                                    x-model="assignments[p.id].lokasi"
                                                    placeholder="https://meet.google.com/..."
                                                    class="w-full px-2 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition bg-gray-50 placeholder-gray-400">
                                            </template>
                                            <template x-if="assignments[p.id].jenis === 'offline'">
                                                <input type="text" :name="`schedule[${p.id}][lokasi]`"
                                                    x-model="assignments[p.id].lokasi"
                                                    placeholder="Ruang Kelas..."
                                                    class="w-full px-2 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-700 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition bg-gray-50 placeholder-gray-400">
                                            </template>
                                        </div>
                                    </td>



                                </tr>
                            </template>
                            <tr x-show="!loadingPelamar && pelamars.length > 0 && filteredPelamars.length === 0">
                                <td colspan="6" class="py-8 text-center text-xs text-gray-400">Tidak ada hasil pencarian.</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-4">
                        <button type="button" @click="showConfirmModal = true" :disabled="!canSubmit()" :class="canSubmit()
                                    ? 'bg-red-800 hover:bg-red-900 text-white cursor-pointer'
                                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            class="flex-shrink-0 px-6 py-2 text-xs font-semibold rounded-lg transition-colors">
                            Simpan Jadwal
                        </button>
                    </div>
                </div>

            </form>

            {{-- Confirmation Modal --}}
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="showConfirmModal = false">
            <div x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">

                {{-- Close Button --}}
                <button type="button" @click="showConfirmModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                {{-- Warning Icon --}}
                <div class="mx-auto mb-5 flex justify-center">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                        <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                        <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                    </svg>
                </div>

                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Simpan Jadwal?</h2>
                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">
                    Simpan jadwal untuk <span class="font-bold text-gray-700" x-text="readyCount"></span> pelamar pada <span class="font-bold text-gray-700" x-text="formatDate(tanggal)"></span>?
                </p>

                <div class="flex justify-center gap-3">
                    <button type="button" @click="doSubmit()" class="flex-1 w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Ya, Simpan</button>
                    <button type="button" @click="showConfirmModal = false" class="flex-1 w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Struktur assignments (BARU — multi-penguji sebagai array ID, sesi tunggal):
         * {
         *   [pelamar_id]: {
         *     wawancara: { pengujiIds: [1, 3], sesi: '2', link: 'https://...' },
         *     micro:     { pengujiIds: [2],    sesi: '1' }
         *   }
         * }
         *
         * Form name attributes yang dikirim:
         *   schedule[101][wawancara][penguji_ids][]  → array penguji
         *   schedule[101][wawancara][sesi]
         *   schedule[101][wawancara][link]
         *   schedule[101][micro][penguji_ids][]
         *   schedule[101][micro][sesi]
         */
        const _jadwalSessions = @json($sessions);
        const _jadwalBaseUrl = '{{ url("/") }}';

        document.addEventListener('alpine:init', () => {
            Alpine.data('jadwalForm', () => ({

                tanggal: '',
                prodiId: '',
                lowonganId: '',
                search: '',
                error: '',

                lowongans: [],
                pengujis: [],
                pelamars: [],

                loadingLowongan: false,
                loadingPenguji: false,
                loadingPelamar: false,

                sessions: _jadwalSessions,
                takenMap: {},
                takenPelamarMap: {},
                assignments: {},
                showConfirmModal: false,

                // ─── COMPUTED ──────────────────────────────────────

                get filteredPelamars() {
                    if (!this.search.trim()) return this.pelamars;
                    const q = this.search.toLowerCase();
                    return this.pelamars.filter(p =>
                        p.nama.toLowerCase().includes(q) ||
                        (p.email && p.email.toLowerCase().includes(q))
                    );
                },

                get readyCount() {
                    return this.pelamars.filter(p =>
                        this.isRowComplete(p.id) && !this.getConflictWarning(p.id)
                    ).length;
                },

                // ─── INIT ──────────────────────────────────────────

                initAssignment(pelamarId) {
                    if (!this.assignments[pelamarId]) {
                        this.assignments[pelamarId] = {
                            penguji_micro_ids: [],
                            penguji_wawancara_ids: [],
                            sesi: '',
                            jenis: 'online',
                            lokasi: '',
                        };
                    }
                },

                // ─── HELPERS ───────────────────────────────────────

                showToast(msg) {
                    this.error = msg;
                    setTimeout(() => this.error = '', 5000);
                },
                getPengujiNama(pgId) {
                    const pg = this.pengujis.find(x => Number(x.id) === Number(pgId));
                    return pg ? `${pg.nama} (${pg.kode})` : `#${pgId}`;
                },

                availablePengujisW(pelamarId) {
                    const selected = this.assignments[pelamarId]?.penguji_wawancara_ids ?? [];
                    return this.pengujis.filter(pg => !selected.map(Number).includes(Number(pg.id)));
                },

                availablePengujisM(pelamarId) {
                    const selected = this.assignments[pelamarId]?.penguji_micro_ids ?? [];
                    return this.pengujis.filter(pg => !selected.map(Number).includes(Number(pg.id)));
                },

                addPengujiW(pelamarId, event) {
                    const val = event.target.value;
                    if (!val) return;
                    const ids = this.assignments[pelamarId].penguji_wawancara_ids;
                    if (!ids.map(Number).includes(Number(val))) ids.push(Number(val));
                    event.target.value = ''; 
                },

                addPengujiWById(pelamarId, val) {
                    if (!val) return;
                    const ids = this.assignments[pelamarId].penguji_wawancara_ids;
                    if (!ids.map(Number).includes(Number(val))) ids.push(Number(val));
                    this._autoResetSesiIfConflicted(pelamarId);
                },

                removePengujiW(pelamarId, pgId) {
                    const a = this.assignments[pelamarId];
                    a.penguji_wawancara_ids = a.penguji_wawancara_ids.filter(id => Number(id) !== Number(pgId));
                },

                addPengujiM(pelamarId, event) {
                    const val = event.target.value;
                    if (!val) return;
                    const ids = this.assignments[pelamarId].penguji_micro_ids;
                    if (!ids.map(Number).includes(Number(val))) ids.push(Number(val));
                    event.target.value = '';
                    this._autoResetSesiIfConflicted(pelamarId);
                },

                addPengujiMById(pelamarId, val) {
                    if (!val) return;
                    const ids = this.assignments[pelamarId].penguji_micro_ids;
                    if (!ids.map(Number).includes(Number(val))) ids.push(Number(val));
                    this._autoResetSesiIfConflicted(pelamarId);
                },

                removePengujiM(pelamarId, pgId) {
                    const a = this.assignments[pelamarId];
                    a.penguji_micro_ids = a.penguji_micro_ids.filter(id => Number(id) !== Number(pgId));
                },

                /**
                 * Jika sesi yang sudah dipilih sekarang menjadi bentrok setelah
                 * penguji ditambah, otomatis reset sesi agar admin tahu harus pilih ulang.
                 */
                _autoResetSesiIfConflicted(pelamarId) {
                    const a = this.assignments[pelamarId];
                    if (!a || !a.sesi) return;
                    if (this.isSesiDisabled(pelamarId, parseInt(a.sesi))) {
                        a.sesi = '';
                    }
                },

                /**
                 * Cek apakah sesi tertentu di-lock untuk pelamar ini.
                 * 
                 * Hanya mengecek:
                 * 1. Penguji MT yang dipilih untuk pelamar INI → bentrok MT di DB
                 * 2. Penguji WWC yang dipilih untuk pelamar INI → bentrok WWC di DB
                 * 3. Pelamar ini sendiri sudah punya jadwal di sesi ini (lintas prodi)
                 * 4. Pelamar LAIN di form ini sudah mengambil sesi ini DAN berbagi penguji
                 *    (atau pelamar lain di sesi ini mengakibatkan penguji bentrok)
                 */
                isSesiDisabled(pelamarId, sesiNum) {
                    const a = this.assignments[pelamarId];
                    if (!a) return false;

                    // 1. Cek penguji MT yang dipilih untuk pelamar ini — bentrok di DB?
                    for (const pgId of (a.penguji_micro_ids ?? [])) {
                        const taken = (this.takenMap[pgId] ?? this.takenMap[String(pgId)])?.micro_teaching ?? [];
                        if (taken.map(Number).includes(sesiNum)) return true;
                    }

                    // 2. Cek penguji WWC yang dipilih untuk pelamar ini — bentrok di DB?
                    for (const pgId of (a.penguji_wawancara_ids ?? [])) {
                        const taken = (this.takenMap[pgId] ?? this.takenMap[String(pgId)])?.wawancara ?? [];
                        if (taken.map(Number).includes(sesiNum)) return true;
                    }

                    // 3. Cek pelamar sendiri sudah punya jadwal di sesi ini (lintas prodi/lowongan)
                    const pelamarTaken = this.takenPelamarMap[pelamarId] ?? this.takenPelamarMap[String(pelamarId)] ?? [];
                    if (pelamarTaken.map(Number).includes(sesiNum)) return true;

                    // 4. Cek bentrok dengan pelamar LAIN di form ini (shared penguji di sesi sama)
                    for (const [otherPid, otherA] of Object.entries(this.assignments)) {
                        if (parseInt(otherPid) === parseInt(pelamarId)) continue;
                        if (parseInt(otherA.sesi) !== sesiNum) continue;

                        // Cek apakah ada penguji MT yang sama
                        for (const pgId of (a.penguji_micro_ids ?? [])) {
                            if ((otherA.penguji_micro_ids ?? []).map(Number).includes(Number(pgId))) return true;
                        }
                        // Cek apakah ada penguji WWC yang sama
                        for (const pgId of (a.penguji_wawancara_ids ?? [])) {
                            if ((otherA.penguji_wawancara_ids ?? []).map(Number).includes(Number(pgId))) return true;
                        }
                    }

                    return false;
                },

                /**
                 * Mendapatkan alasan kenapa sesi di-lock (untuk tooltip).
                 */
                getSesiDisabledReason(pelamarId, sesiNum) {
                    const a = this.assignments[pelamarId];
                    if (!a) return '';

                    // Cek penguji MT bentrok di DB
                    for (const pgId of (a.penguji_micro_ids ?? [])) {
                        const taken = (this.takenMap[pgId] ?? this.takenMap[String(pgId)])?.micro_teaching ?? [];
                        if (taken.map(Number).includes(sesiNum)) return 'Penguji bentrok';
                    }

                    // Cek penguji WWC bentrok di DB
                    for (const pgId of (a.penguji_wawancara_ids ?? [])) {
                        const taken = (this.takenMap[pgId] ?? this.takenMap[String(pgId)])?.wawancara ?? [];
                        if (taken.map(Number).includes(sesiNum)) return 'Penguji bentrok';
                    }

                    // Cek pelamar sudah punya jadwal
                    const pelamarTaken = this.takenPelamarMap[pelamarId] ?? this.takenPelamarMap[String(pelamarId)] ?? [];
                    if (pelamarTaken.map(Number).includes(sesiNum)) {
                        return 'Pelamar bentrok';
                    }

                    // Cek bentrok pelamar lain di form ini
                    for (const [otherPid, otherA] of Object.entries(this.assignments)) {
                        if (parseInt(otherPid) === parseInt(pelamarId)) continue;
                        if (parseInt(otherA.sesi) !== sesiNum) continue;

                        for (const pgId of (a.penguji_micro_ids ?? [])) {
                            if ((otherA.penguji_micro_ids ?? []).map(Number).includes(Number(pgId))) {
                                return 'Penguji bentrok';
                            }
                        }
                        for (const pgId of (a.penguji_wawancara_ids ?? [])) {
                            if ((otherA.penguji_wawancara_ids ?? []).map(Number).includes(Number(pgId))) {
                                return 'Penguji bentrok';
                            }
                        }
                    }

                    return 'Tidak tersedia';
                },

                isSesiConflict(pelamarId) {
                    const sesi = parseInt(this.assignments[pelamarId]?.sesi ?? '0');
                    if (!sesi) return false;
                    return this.isSesiDisabled(pelamarId, sesi);
                },

                _isTakenFor(pgId, tipe, sesiNum) {
                    const taken = (this.takenMap[pgId] ?? this.takenMap[String(pgId)])?.[tipe] ?? [];
                    return taken.map(Number).includes(sesiNum);
                },

                _inFormConflict(pelamarId, pgId, tipe, sesiNum) {
                    for (const [otherPid, otherA] of Object.entries(this.assignments)) {
                        if (parseInt(otherPid) === parseInt(pelamarId)) continue;
                        const ids = tipe === 'wawancara' ? otherA.penguji_wawancara_ids : otherA.penguji_micro_ids;
                        if (
                            ids.map(Number).includes(Number(pgId)) &&
                            parseInt(otherA.sesi) === sesiNum
                        ) return true;
                    }
                    return false;
                },

                getConflictWarning(pelamarId) {
                    return '';
                },

                isValidUrl(url) {
                    if (!url) return false;
                    const pattern = /^(http|https):\/\/[^ "]+$/;
                    return pattern.test(url);
                },

                isRowComplete(pelamarId) {
                    const a = this.assignments[pelamarId];
                    if (!a) return false;
                    const lokasiOk = a.jenis === 'online'
                        ? this.isValidUrl(a.lokasi)
                        : a.lokasi.trim() !== '';
                    return (
                        a.penguji_micro_ids.length > 0 &&
                        a.penguji_wawancara_ids.length > 0 &&
                        a.sesi !== '' &&
                        lokasiOk
                    );
                },

                hasPartialRow() {
                    return this.pelamars.some(p => {
                        const a = this.assignments[p.id];
                        if (!a) return false;
                        const hasAnyInput =
                            a.penguji_micro_ids.length > 0 ||
                            a.penguji_wawancara_ids.length > 0 ||
                            a.sesi !== '' ||
                            a.lokasi.trim() !== '';
                        return hasAnyInput && !this.isRowComplete(p.id);
                    });
                },

                canSubmit() {
                    return (
                        this.tanggal &&
                        this.lowonganId &&
                        this.readyCount > 0 &&
                        !this.hasPartialRow() &&
                        this.pelamars.every(p => !this.getConflictWarning(p.id))
                    );
                },

                sesiLabel(sesiKey) {
                    const info = this.sessions.micro_teaching?.[sesiKey]; // Both use same block_label
                    if (!info) return `Sesi ${sesiKey}`;
                    return info.block_label || `Sesi ${sesiKey}`;
                },

                formatDate(d) {
                    if (!d) return '-';
                    return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    });
                },

                // ─── EVENT HANDLERS ────────────────────────────────

                async onProdiChange() {
                    this.lowonganId = '';
                    this.lowongans = [];
                    this.pengujis = [];
                    this.pelamars = [];
                    this.assignments = {};
                    this.takenMap = {};
                    this.error = '';
                    if (!this.prodiId) return;

                    this.loadingLowongan = this.loadingPenguji = true;
                    try {
                        const [resL, resP] = await Promise.all([
                            fetch(`${_jadwalBaseUrl}/admin/api/lowongan-by-prodi?prodi_id=${this.prodiId}`),
                            fetch(`${_jadwalBaseUrl}/admin/api/penguji-by-prodi?prodi_id=${this.prodiId}`)
                        ]);
                        if (!resL.ok || !resP.ok) throw new Error();
                        this.lowongans = await resL.json();
                        this.pengujis = await resP.json();
                        if (!this.lowongans.length) this.showToast('Tidak ada lowongan aktif untuk prodi ini.');
                        if (!this.pengujis.length) this.showToast('Tidak ada penguji untuk prodi ini.');
                    } catch {
                        this.showToast('Terjadi kesalahan saat memuat data.');
                    } finally {
                        this.loadingLowongan = this.loadingPenguji = false;
                    }
                },

                async onLowonganChange() {
                    this.pelamars = [];
                    this.assignments = {};
                    this.error = '';
                    if (!this.lowonganId) return;

                    this.loadingPelamar = true;
                    try {
                        const res = await fetch(`${_jadwalBaseUrl}/admin/api/pelamar-by-lowongan?lowongan_id=${this.lowonganId}`);
                        if (!res.ok) throw new Error();
                        this.pelamars = await res.json();
                        this.pelamars.forEach(p => this.initAssignment(p.id));
                        if (!this.pelamars.length) this.showToast('Tidak ada pelamar siap dijadwalkan di lowongan ini.');
                    } catch {
                        this.showToast('Gagal memuat daftar pelamar.');
                    } finally {
                        this.loadingPelamar = false;
                    }
                    if (this.tanggal) await this.loadTakenSessions();
                },

                async onTanggalChange() {
                    Object.values(this.assignments).forEach(a => {
                        a.sesi = '';
                        a.lokasi = '';
                    });
                    this.takenMap = {};
                    if (this.tanggal) await this.loadTakenSessions();
                },

                async loadTakenSessions() {
                    if (!this.tanggal || !this.pengujis.length) return;
                    try {
                        const ids = this.pengujis.map(p => p.id).join(',');
                        const res = await fetch(
                            `${_jadwalBaseUrl}/admin/api/sesi-taken-all?tanggal=${this.tanggal}&penguji_ids=${ids}`
                        );
                        if (!res.ok) throw new Error();
                        this.takenMap = await res.json();
                    } catch (e) {
                        console.error('Gagal memuat sesi terpakai:', e);
                    }

                    // Muat juga sesi terpakai per PELAMAR (bukan penguji)
                    if (!this.pelamars.length) return;
                    try {
                        const pIds = this.pelamars.map(p => p.id).join(',');
                        const res2 = await fetch(
                            `${_jadwalBaseUrl}/admin/api/sesi-taken-pelamar?tanggal=${this.tanggal}&pelamar_ids=${pIds}`
                        );
                        if (!res2.ok) throw new Error();
                        this.takenPelamarMap = await res2.json();
                    } catch (e) {
                        console.error('Gagal memuat sesi pelamar terpakai:', e);
                    }
                },

                submitForm(e) {
                    if (!this.canSubmit()) {
                        this.showToast('Harap lengkapi jadwal minimal 1 pelamar dan pastikan tidak ada bentrok waktu.');
                        return;
                    }
                    this.showConfirmModal = true;
                },

                doSubmit() {
                    this.showConfirmModal = false;
                    this.$nextTick(() => {
                        this.$root.querySelector('form').submit();
                    });
                },
            }));
        });
    </script>

@endsection
