@extends('layouts.admin')
@section('title', 'Penjadwalan')
@section('content')

@if(session('success'))
<div x-data="{show:true}" x-init="setTimeout(()=>show=false,5000)" x-show="show"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
     x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
     x-transition:leave-end="opacity-0 translate-x-12"
     class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px] max-w-sm">
    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white">
        <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
        <p class="text-[0.8rem] text-gray-500 leading-snug">{{ session('success') }}</p>
    </div>
    <button @click="show=false" class="text-gray-400 hover:text-gray-600 p-1">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

@if($errors->has('edit'))
<div x-data="{show:true}" x-init="setTimeout(()=>show=false,7000)" x-show="show"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
     x-transition:enter-end="opacity-100 translate-x-0"
     class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-red-100 min-w-[320px] max-w-sm">
    <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 text-white">
        <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold text-gray-800 mb-0.5">Gagal menyimpan</h4>
        <p class="text-[0.8rem] text-gray-500 leading-snug">{{ $errors->first('edit') }}</p>
    </div>
    <button @click="show=false" class="text-gray-400 hover:text-gray-600 p-1">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
@endif

<div class="max-w-7xl mx-auto space-y-6" x-data="jadwalIndex()">


    {{-- Filter & Action --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
        <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex flex-col sm:flex-row gap-3 items-end w-full lg:w-auto">
            <div>
                
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                       class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition w-full sm:w-auto">
            </div>
            <div>
               
                <select name="penguji_id" class="px-4 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white w-full sm:min-w-[200px]">
                    <option value="">Semua Penguji</option>
                    @foreach($pengujis as $p)
                        <option value="{{ $p->id }}" {{ request('penguji_id')==$p->id?'selected':'' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">Filter</button>
                @if(request()->hasAny(['tanggal','penguji_id']))
                    <a href="{{ route('admin.jadwal.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">Reset</a>
                @endif
            </div>
        </form>
        
        <a href="{{ route('admin.jadwal.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors shrink-0 w-full lg:w-auto">
            + Jadwalkan Seleksi
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" style="min-width:1150px">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700">Tanggal</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700">Lowongan</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700">Pelamar</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-56">Wawancara</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center border-l border-red-700 w-24">Status</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-56">Micro Teaching</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center border-l border-red-700 w-24">Status</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center border-l border-red-700 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $row)
                    @php
                        $wFirst  = $row->wawancara->first();
                        $mFirst  = $row->micro->first();
                        $wInfo   = $wFirst ? (\App\Models\JadwalSeleksi::SESSIONS['tahap1'][$wFirst->sesi] ?? null) : null;
                        $mInfo   = $mFirst ? (\App\Models\JadwalSeleksi::SESSIONS['tahap2'][$mFirst->sesi] ?? null) : null;

                        // Data untuk modal (JSON-safe)
                        $pengujiW = $row->wawancara->pluck('penguji_id')->unique()->values()->toJson();
                        $pengujiM = $row->micro->pluck('penguji_id')->unique()->values()->toJson();
                        $allPgIds = $row->wawancara->pluck('penguji_id')
                            ->merge($row->micro->pluck('penguji_id'))
                            ->unique()->values()->toJson();
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors align-middle">

                        {{-- Tanggal --}}
                        <td class="py-4 px-4 align-top">
                            <div class="text-sm font-bold text-gray-800">{{ $row->tanggal->format('d M Y') }}</div>
                        </td>

                        {{-- Lowongan --}}
                        <td class="py-4 px-4 border-l border-gray-100 align-top">
                            <div class="text-sm font-bold text-[#8b1515]">{{ $row->lowongan->nama_posisi }}</div>
                            <div class="text-[0.75rem] font-medium text-gray-500 mt-1">{{ $row->lowongan->prodi->nama ?? '-' }}</div>
                        </td>

                        {{-- Pelamar --}}
                        <td class="py-4 px-4 border-l border-gray-100 align-top">
                            <div class="text-sm font-bold text-gray-800">{{ $row->pelamar->nama }}</div>
                            <div class="text-[0.75rem] text-gray-500 font-mono mt-1">{{ $row->pelamar->user?->email ?? '-' }}</div>
                        </td>

                        {{-- Wawancara --}}
                        <td class="py-4 px-4 border-l border-gray-100 align-top">
                            @if($wFirst && $wInfo)
                                <div class="text-xs text-gray-700 space-y-1">
                                    <div><strong>Sesi {{ $wFirst->sesi }} :</strong>  ({{ $wInfo['start'] }} – {{ $wInfo['end'] }})</div>
                                    <div>
                                        <strong>Penguji:</strong>
                                        
                                            @foreach($row->wawancara as $wj)
                                                {{ $wj->penguji->nama }}
                                            @endforeach
                                        
                                    </div>
                                    @if($wFirst->link_meeting)
                                        <div class="mt-1">
                                            <strong>Link:</strong> <a href="{{ $wFirst->link_meeting }}" target="_blank" class="text-blue-600 hover:underline break-all">{{ $wFirst->link_meeting }}</a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-400 text-xs italic">-</div>
                            @endif
                        </td>

                        {{-- Status Wawancara --}}
                        <td class="py-4 px-4 border-l border-gray-100 text-center align-top">
                            @if($row->wawancara->isNotEmpty())
                                @php
                                    $wTotal = $row->wawancara->count();
                                    $wDone = $row->wawancara->whereNotNull('penilaian')->count();
                                    $wStatus = ($wTotal > 0 && $wDone === $wTotal) ? 'Done' : 'Pending';
                                @endphp
                                @if($wStatus === 'Done')
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-lg text-[0.7rem] font-bold inline-block mt-1">Done</span>
                                @else
                                    <span class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded-lg text-[0.7rem] font-bold inline-block mt-1">Pending</span>
                                @endif
                            @else
                                <div class="text-gray-300 text-xs italic mt-1">-</div>
                            @endif
                        </td>

                        {{-- Micro Teaching --}}
                        <td class="py-4 px-4 border-l border-gray-100 align-top">
                            @if($mFirst && $mInfo)
                                <div class="text-xs text-gray-700 space-y-1">
                                    <div><strong>Sesi {{ $mFirst->sesi }} :</strong>  ({{ $mInfo['start'] }} – {{ $mInfo['end'] }})</div>
                                    <div>
                                        <strong>Penguji:</strong>
                                        
                                            @foreach($row->micro as $mj)
                                                {{ $mj->penguji->nama }}
                                            @endforeach
                                        
                                    </div>
                                    @if($mFirst->link_meeting)
                                        <div class="mt-1">
                                            <strong>Link:</strong> <a href="{{ $mFirst->link_meeting }}" target="_blank" class="text-blue-600 hover:underline break-all">{{ $mFirst->link_meeting }}</a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-400 text-xs italic">-</div>
                            @endif
                        </td>

                        {{-- Status Micro --}}
                        <td class="py-4 px-4 border-l border-gray-100 text-center align-top">
                            @if($row->micro->isNotEmpty())
                                @php
                                    $mTotal = $row->micro->count();
                                    $mDone = $row->micro->whereNotNull('penilaian')->count();
                                    $mStatus = ($mTotal > 0 && $mDone === $mTotal) ? 'Done' : 'Pending';
                                @endphp
                                @if($mStatus === 'Done')
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-lg text-[0.7rem] font-bold inline-block mt-1">Done</span>
                                @else
                                    <span class="px-2.5 py-1 bg-orange-100 text-orange-700 rounded-lg text-[0.7rem] font-bold inline-block mt-1">Pending</span>
                                @endif
                            @else
                                <div class="text-gray-300 text-xs italic mt-1">-</div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="py-4 px-4 border-l border-gray-100 text-center align-top">
                            <button type="button"
                                @click="openEdit({
                                    pelamarId:  {{ $row->pelamar->id }},
                                    lowonganId: {{ $row->lowongan->id }},
                                    tanggal:    '{{ $row->tanggal->format('Y-m-d') }}',
                                    pelamarNama:'{{ addslashes($row->pelamar->nama) }}',
                                    wSesi:   {{ $wFirst ? $wFirst->sesi : 'null' }},
                                    mSesi:   {{ $mFirst ? $mFirst->sesi : 'null' }},
                                    wLink:   '{!! $wFirst ? addslashes($wFirst->link_meeting ?? '') : '' !!}',
                                    mLink:   '{!! $mFirst ? addslashes($mFirst->link_meeting ?? '') : '' !!}',
                                    hasW:    {{ $row->wawancara->isNotEmpty() ? 'true' : 'false' }},
                                    hasM:    {{ $row->micro->isNotEmpty() ? 'true' : 'false' }},
                                    pengujiW: {{ $pengujiW }},
                                    pengujiM: {{ $pengujiM }},
                                    allPgIds: {{ $allPgIds }},
                                })"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                title="Edit jadwal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Jadwal</h3>
                                <p class="text-gray-400 text-xs">Buat jadwal seleksi pertama.</p>
                                <a href="{{ route('admin.jadwal.create') }}" class="mt-1 px-4 py-2 bg-[#8b1515] text-white text-xs font-semibold rounded-lg hover:bg-red-900 transition-colors">
                                    + Jadwalkan Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- ══ MODAL EDIT ══ --}}
<template x-teleport="body">
<div x-show="modal.open" style="display:none" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    
    {{-- Backdrop --}}
    <div x-show="modal.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         @click="modal.open=false"></div>

    {{-- Modal Panel --}}
    <div x-show="modal.open"
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative z-10">

        <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-white">Edit Jadwal Seleksi</h2>
                <p class="text-xs text-red-200 mt-0.5" x-text="modal.pelamarNama"></p>
            </div>
            <button @click="modal.open=false"
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-white/40 text-white hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.jadwal.updateGroup') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="pelamar_id"  :value="modal.pelamarId">
            <input type="hidden" name="lowongan_id" :value="modal.lowonganId">

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Seleksi</label>
                <input type="date" name="tanggal" x-model="modal.tanggal" @change="loadTaken()"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                <p x-show="modal.loadingTaken" class="text-[0.65rem] text-gray-400 mt-1 italic">Memuat ketersediaan sesi...</p>
            </div>

            {{-- Wawancara sesi --}}
            <div x-show="modal.hasW">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                        Sesi Wawancara
                    </span>
                </label>
                <select name="wawancara_sesi" x-model="modal.wSesi"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white">
                    <option value="">— Pilih Sesi —</option>
                    <template x-for="(info, key) in wSessions" :key="key">
                        <option :value="key"
                                :disabled="isBlockedW(parseInt(key))"
                                x-text="sesiLabel(key, info, isBlockedW(parseInt(key)))">
                        </option>
                    </template>
                </select>
                <p x-show="modal.wSesi && isBlockedW(parseInt(modal.wSesi))"
                   class="text-[0.65rem] text-red-600 mt-1">Sesi ini bentrok — pilih sesi lain.</p>
                <div class="mt-2">
                    <label class="block text-[0.65rem] font-semibold text-gray-500 mb-1 uppercase tracking-wider">Link Zoom Wawancara</label>
                    <input type="url" name="wawancara_link" x-model="modal.wLink" placeholder="https://..."
                           class="w-full px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                </div>
            </div>

            {{-- Micro sesi --}}
            <div x-show="modal.hasM">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-purple-500 inline-block"></span>
                        Sesi Micro Teaching
                    </span>
                </label>
                <select name="micro_sesi" x-model="modal.mSesi"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white">
                    <option value="">— Pilih Sesi —</option>
                    <template x-for="(info, key) in mSessions" :key="key">
                        <option :value="key"
                                :disabled="isBlockedM(parseInt(key))"
                                x-text="sesiLabel(key, info, isBlockedM(parseInt(key)))">
                        </option>
                    </template>
                </select>
                <p x-show="modal.mSesi && isBlockedM(parseInt(modal.mSesi))"
                   class="text-[0.65rem] text-red-600 mt-1">Sesi ini bentrok — pilih sesi lain.</p>
                <div class="mt-2">
                    <label class="block text-[0.65rem] font-semibold text-gray-500 mb-1 uppercase tracking-wider">Link Zoom Micro Teaching</label>
                    <input type="url" name="micro_link" x-model="modal.mLink" placeholder="https://..."
                           class="w-full px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" @click="modal.open=false"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                <button type="submit"
                        :disabled="hasConflict()"
                        :class="hasConflict() ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-[#8b1515] hover:bg-red-900 text-white'"
                        class="px-5 py-2 text-sm font-bold rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</template>
</div>

<script>
const _sess = @json(\App\Models\JadwalSeleksi::SESSIONS);
const _base  = '{{ url("/") }}';

function jadwalIndex() {
    return {
        modal: {
            open: false,
            pelamarId: null, lowonganId: null, pelamarNama: '',
            tanggal: '', wSesi: '', mSesi: '',
            wLink: '', mLink: '',
            origW: null, origM: null,
            hasW: false, hasM: false,
            pengujiW: [], pengujiM: [], allPgIds: [],
            takenMap: {}, loadingTaken: false,
        },

        get wSessions() { return _sess['tahap1'] ?? {}; },
        get mSessions() { return _sess['tahap2'] ?? {}; },

        openEdit(d) {
            this.modal.pelamarId   = d.pelamarId;
            this.modal.lowonganId  = d.lowonganId;
            this.modal.pelamarNama = d.pelamarNama;
            this.modal.tanggal     = d.tanggal;
            this.modal.wSesi       = d.wSesi !== null ? String(d.wSesi) : '';
            this.modal.mSesi       = d.mSesi !== null ? String(d.mSesi) : '';
            this.modal.wLink       = d.wLink || '';
            this.modal.mLink       = d.mLink || '';
            this.modal.origW       = d.wSesi;
            this.modal.origM       = d.mSesi;
            this.modal.hasW        = d.hasW;
            this.modal.hasM        = d.hasM;
            this.modal.pengujiW    = d.pengujiW;
            this.modal.pengujiM    = d.pengujiM;
            this.modal.allPgIds    = d.allPgIds;
            this.modal.takenMap    = {};
            this.modal.open        = true;
            this.loadTaken();
        },

        async loadTaken() {
            const { tanggal, allPgIds } = this.modal;
            if (!tanggal || !allPgIds.length) return;
            this.modal.loadingTaken = true;
            try {
                const res = await fetch(`${_base}/admin/api/sesi-taken-all?tanggal=${tanggal}&penguji_ids=${allPgIds.join(',')}`);
                this.modal.takenMap = res.ok ? await res.json() : {};
            } catch { this.modal.takenMap = {}; }
            this.modal.loadingTaken = false;
        },

        _takenFor(pgId, tipe) {
            return (this.modal.takenMap[String(pgId)]?.[tipe] ?? []).map(Number);
        },

        isBlockedW(sesiNum) {
            const orig = this.modal.origW;
            if (sesiNum === 4 && parseInt(this.modal.mSesi) === 1) return true;

            for (const pgId of this.modal.pengujiW) {
                const taken = this._takenFor(pgId, 'tahap1').filter(s => s !== orig);
                if (taken.includes(sesiNum)) return true;
                if (sesiNum === 4 && orig !== 4) {
                    if (this._takenFor(pgId, 'tahap2').includes(1)) return true;
                }
            }
            return false;
        },

        isBlockedM(sesiNum) {
            const orig = this.modal.origM;
            if (sesiNum === 1 && parseInt(this.modal.wSesi) === 4) return true;

            for (const pgId of this.modal.pengujiM) {
                const taken = this._takenFor(pgId, 'tahap2').filter(s => s !== orig);
                if (taken.includes(sesiNum)) return true;
                if (sesiNum === 1 && orig !== 1) {
                    if (this._takenFor(pgId, 'tahap1').includes(4)) return true;
                }
            }
            return false;
        },

        isValidUrl(url) {
            if (!url) return false;
            const pattern = /^(http|https):\/\/[^ "]+$/;
            return pattern.test(url);
        },

        hasConflict() {
            if (parseInt(this.modal.wSesi) === 4 && parseInt(this.modal.mSesi) === 1) return true;
            if (this.modal.hasW && this.modal.wSesi && this.isBlockedW(parseInt(this.modal.wSesi))) return true;
            if (this.modal.hasM && this.modal.mSesi && this.isBlockedM(parseInt(this.modal.mSesi))) return true;
            
            // Link validation
            if (this.modal.hasW && this.modal.wSesi && !this.isValidUrl(this.modal.wLink)) return true;
            if (this.modal.hasM && this.modal.mSesi && !this.isValidUrl(this.modal.mLink)) return true;

            return false;
        },

        sesiLabel(key, info, isBlocked) {
            const time = (info.start && info.end) ? `${info.start}–${info.end}` : '';
            const base = time ? `Sesi ${key} (${time})` : `Sesi ${key}`;
            return isBlocked ? base + ' ✗' : base;
        },
    };
}
</script>

@endsection
