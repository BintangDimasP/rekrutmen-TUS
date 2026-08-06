@extends('layouts.admin')
@section('title', 'Penjadwalan Seleksi')
@section('content')

<div class="max-w-7xl mx-auto space-y-6" x-data="jadwalIndex()" x-init="$nextTick(() => recalcAll()); $watch('search', () => resetAndRecalc()); $watch('fTanggal', () => resetAndRecalc()); $watch('fProdi', () => resetAndRecalc()); $watch('fStatus', () => resetAndRecalc())">


    {{-- Filter Chips Bar (with attached + button) --}}
    <div class="relative">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20"
             x-data="{
                prodiOpen: false,
                statusOpen: false,
             }"
             @click.outside.window="prodiOpen = false; statusOpen = false">
            <div class="flex items-center gap-3 flex-wrap">

                {{-- Search (animated) --}}
                <div class="relative flex items-center" x-data="{ searchOpen: false }" @click.outside="if(!search) searchOpen = false">
                    <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                            class="absolute left-0 z-10 w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                            :class="searchOpen ? 'pointer-events-none' : 'border border-gray-200'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <div class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                         :style="searchOpen ? 'width: min(288px, calc(100vw - 8rem)); opacity: 1' : 'width: 36px; opacity: 0'">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama pelamar..."
                               @keydown.escape="search = ''; searchOpen = false"
                               class="w-[min(288px,calc(100vw-8rem))] pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                    </div>
                    <button type="button" x-show="searchOpen" x-transition.opacity.duration.200ms
                            @click="search = ''; searchOpen = false"
                            class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Tanggal – input date langsung (tanpa dropdown) --}}
                <div class="flex items-center gap-1.5">
                    <input type="date" x-model="fTanggal"
                           class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]/20 transition cursor-pointer"
                           :class="fTanggal !== '' ? 'border-[#8b1515] text-[#8b1515] font-medium' : ''">
                </div>

                {{-- Prodi Chip --}}
                <div class="relative" @click.outside="prodiOpen = false">
                    <button type="button" @click="prodiOpen = !prodiOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="fProdi !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        Prodi
                        <span x-show="fProdi !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="prodiOpen" x-transition class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p></div>
                        <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                            @foreach($prodis as $prodi)
                            <button type="button" @click="fProdi = fProdi === '{{ $prodi->id }}' ? '' : '{{ $prodi->id }}'; prodiOpen = false"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 transition-colors text-left"
                                    :class="fProdi === '{{ $prodi->id }}' ? 'bg-gray-100' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                      :class="fProdi === '{{ $prodi->id }}' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                    <svg x-show="fProdi === '{{ $prodi->id }}'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Status Chip --}}
                <div class="relative" @click.outside="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="fStatus !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        Status
                        <span x-show="fStatus !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-transition class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p></div>
                        <div class="p-3 space-y-1">
                            @foreach(['belum' => 'Belum Dinilai', 'sebagian' => 'Sebagian Dinilai', 'selesai' => 'Selesai'] as $key => $label)
                            <button type="button" @click="fStatus = fStatus === '{{ $key }}' ? '' : '{{ $key }}'; statusOpen = false"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left transition-colors"
                                    :class="fStatus === '{{ $key }}' ? 'bg-gray-100' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center"
                                      :class="fStatus === '{{ $key }}' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                    <svg x-show="fStatus === '{{ $key }}'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Active filter tags --}}
                <span x-show="fProdi !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                    <span x-text="prodiName(fProdi)"></span>
                    <button type="button" @click="fProdi = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </span>
                <span x-show="fStatus !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                    <span x-text="statusName(fStatus)"></span>
                    <button type="button" @click="fStatus = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </span>

                {{-- Clear All --}}
                <button x-show="hasFilters" x-transition type="button" @click="fTanggal = ''; fProdi = ''; fStatus = ''"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Clear Filters
                </button>
            </div>
        </div>

        {{-- + Button (outside card, flush right) --}}
        <a href="{{ route('admin.jadwal.create') }}"
           class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Buat Jadwal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </a>
    </div>
    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed" style="min-width:1150px; width:100%">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[10%]">Tanggal</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[15%]">Nama Lowongan</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[15%]">Pelamar</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[15%]">Micro Teaching</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[10%]">Status</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[15%]">Wawancara</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[10%]">Status</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                    @forelse($rows as $row)
                    @php
                        $wFirst  = $row->wawancara->first();
                        $mFirst  = $row->micro->first();
                        $wInfo   = $wFirst ? (\App\Models\JadwalSeleksi::SESSIONS['wawancara'][$wFirst->sesi] ?? null) : null;
                        $mInfo   = $mFirst ? (\App\Models\JadwalSeleksi::SESSIONS['micro_teaching'][$mFirst->sesi] ?? null) : null;

                        // Data untuk modal (JSON-safe)
                        $pengujiWIds = $row->wawancara->pluck('penguji_id')->unique()->values()->toArray();
                        $pengujiMIds = $row->micro->pluck('penguji_id')->unique()->values()->toArray();
                        $allPgIds = array_unique(array_merge($pengujiWIds, $pengujiMIds));
                        
                        // Ambil data penguji lengkap
                        $pengujiData = \App\Models\Dosen::whereIn('id', $allPgIds)->get(['id', 'nama', 'kode'])->toArray();
                        
                        $pengujiW = json_encode($pengujiWIds);
                        $pengujiM = json_encode($pengujiMIds);
                        $pengujiDataJson = json_encode($pengujiData);
                        $allPgIds = json_encode($allPgIds);
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors align-middle" data-jadwal-row
                        data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                        data-prodi="{{ $row->lowongan->prodi_id ?? '' }}"
                        data-status="@php
                            $mDone = $row->micro->whereNotNull('penilaian')->count();
                            $wDone = $row->wawancara->whereNotNull('penilaian')->count();
                            $mTotal = $row->micro->count();
                            $wTotal = $row->wawancara->count();
                            if ($mTotal > 0 && $wTotal > 0 && $mDone === $mTotal && $wDone === $wTotal) echo 'selesai';
                            elseif ($mDone > 0 || $wDone > 0) echo 'sebagian';
                            else echo 'belum';
                        @endphp"
                        data-pelamar="{{ strtolower($row->pelamar->nama) }}">

                        {{-- Tanggal --}}
                        <td class="py-4 px-4 align-top">
                            <div class="text-sm font-bold text-gray-800">{{ $row->tanggal->format('d M Y') }}</div>
                        </td>

                        {{-- Lowongan --}}
                        <td class="py-4 px-4 align-top">
                            <div class="text-sm font-bold text-gray-800">{{ $row->lowongan->nama_posisi }}</div>
                            <div class="text-[0.75rem] font-medium text-gray-500 mt-1">{{ $row->lowongan->prodi->nama ?? '-' }}</div>
                        </td>

                        {{-- Pelamar --}}
                        <td class="py-4 px-4 align-top max-w-0">
                            <div class="text-sm font-bold text-gray-800 truncate" title="{{ $row->pelamar->nama }}">{{ $row->pelamar->nama }}</div>
                            <div class="text-[0.75rem] text-gray-500 font-medium mt-1 truncate" title="{{ $row->pelamar->user?->email ?? '' }}">{{ $row->pelamar->user?->email ?? '-' }}</div>
                        </td>

                        {{-- Micro Teaching --}}
                        <td class="py-4 px-4 align-top">
                            @if($mFirst && $mInfo)
                                <div class="text-xs text-gray-700 space-y-1">
                                    <div><strong>Sesi {{ $mFirst->sesi }}:</strong> {{ $mInfo['start'] }} – {{ $mInfo['end'] }}</div>
                                    <div class="leading-snug">
                                        <strong>Penguji:</strong>
                                        @foreach($row->micro as $mj)
                                            <span class="block truncate" title="{{ $mj->penguji->nama }}">{{ $mj->penguji->nama }}</span>
                                        @endforeach
                                    </div>
                                    @if($mFirst->lokasi)
                                        <div class="mt-1">
                                            @if($mFirst->jenis_sesi === 'online')
                                                <div class="flex items-center gap-1 min-w-0">
                                                    <strong class="flex-shrink-0">Link:</strong>
                                                    <a href="{{ $mFirst->lokasi }}" target="_blank" title="{{ $mFirst->lokasi }}"
                                                       class="text-blue-600 hover:underline truncate block min-w-0">{{ $mFirst->lokasi }}</a>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-1 min-w-0">
                                                    <strong class="flex-shrink-0">Lokasi:</strong>
                                                    <span class="truncate block min-w-0" title="{{ $mFirst->lokasi }}">{{ $mFirst->lokasi }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @php
                                        $materiMT = $mFirst->materi_micro_teaching ?? null;
                                    @endphp
                                    @if($materiMT)
                                        <div class="mt-1.5 pt-1.5 border-t border-gray-100">
                                            <strong class="flex-shrink-0 text-amber-800 text-[0.65rem] uppercase block">Materi:</strong>
                                            <span class="text-xs text-gray-700 block line-clamp-2" title="{{ $materiMT }}">{{ $materiMT }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-400 text-xs italic">-</div>
                            @endif
                        </td>

                        {{-- Status Micro --}}
                        <td class="py-4 px-4 text-center align-top">
                            @if($row->lamaran?->status === 'mengundurkan_diri')
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[0.65rem] font-bold inline-block mt-1 leading-tight">Undur<br>Diri</span>
                            @elseif($row->micro->isNotEmpty())
                                @php
                                    $mTotal = $row->micro->count();
                                    $mDone = $row->micro->whereNotNull('penilaian')->count();
                                    $mStatus = ($mTotal > 0 && $mDone === $mTotal) ? 'Done' : 'Pending';
                                @endphp
                                @if($mStatus === 'Done')
                                    <span class="px-2.5 py-1 bg-green-800 text-white rounded-lg text-[0.7rem] font-bold inline-block mt-1">Dinilai</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-800 text-white rounded-lg text-[0.65rem] font-bold inline-block mt-1 leading-tight">Belum<br>Dinilai</span>
                                @endif
                            @else
                                <div class="text-gray-300 text-xs italic mt-1">-</div>
                            @endif
                        </td>

                        {{-- Wawancara --}}
                        <td class="py-4 px-4 align-top">
                            @if($wFirst && $wInfo)
                                <div class="text-xs text-gray-700 space-y-1">
                                    <div><strong>Sesi {{ $wFirst->sesi }}:</strong> {{ $wInfo['start'] }} – {{ $wInfo['end'] }}</div>
                                    <div class="leading-snug">
                                        <strong>Penguji:</strong>
                                        @foreach($row->wawancara as $wj)
                                            <span class="block truncate" title="{{ $wj->penguji->nama }}">{{ $wj->penguji->nama }}</span>
                                        @endforeach
                                    </div>
                                    @if($wFirst->lokasi)
                                        <div class="mt-1">
                                            @if($wFirst->jenis_sesi === 'online')
                                                <div class="flex items-center gap-1 min-w-0">
                                                    <strong class="flex-shrink-0">Link:</strong>
                                                    <a href="{{ $wFirst->lokasi }}" target="_blank" title="{{ $wFirst->lokasi }}"
                                                       class="text-blue-600 hover:underline truncate block min-w-0">{{ $wFirst->lokasi }}</a>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-1 min-w-0">
                                                    <strong class="flex-shrink-0">Lokasi:</strong>
                                                    <span class="truncate block min-w-0" title="{{ $wFirst->lokasi }}">{{ $wFirst->lokasi }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-400 text-xs italic">-</div>
                            @endif
                        </td>

                        {{-- Status Wawancara --}}
                        <td class="py-4 px-4 text-center align-top">
                            @if($row->lamaran?->status === 'mengundurkan_diri')
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[0.65rem] font-bold inline-block mt-1 leading-tight">Undur<br>Diri</span>
                            @elseif($row->wawancara->isNotEmpty())
                                @php
                                    $wTotal = $row->wawancara->count();
                                    $wDone = $row->wawancara->whereNotNull('penilaian')->count();
                                    $wStatus = ($wTotal > 0 && $wDone === $wTotal) ? 'Done' : 'Pending';
                                @endphp
                                @if($wStatus === 'Done')
                                    <span class="px-2.5 py-1 bg-green-800 text-white rounded-lg text-[0.7rem] font-bold inline-block mt-1">Dinilai</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-800 text-white rounded-lg text-[0.65rem] font-bold inline-block mt-1 leading-tight">Belum<br>Dinilai</span>
                                @endif
                            @else
                                <div class="text-gray-300 text-xs italic mt-1">-</div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="py-4 px-4 text-center align-top">
                            @php
                                $anyDone = ($row->micro->isNotEmpty() && $row->micro->whereNotNull('penilaian')->count() > 0)
                                        || ($row->wawancara->isNotEmpty() && $row->wawancara->whereNotNull('penilaian')->count() > 0);
                            @endphp
                            <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                    @click="openEdit({
                                        pelamarId:  {{ $row->pelamar->id }},
                                        lowonganId: {{ $row->lowongan->id }},
                                        prodiId:    {{ $row->lowongan->prodi_id ?? 'null' }},
                                        tanggal:    '{{ $row->tanggal->format('Y-m-d') }}',
                                        pelamarNama:'{{ addslashes($row->pelamar->nama) }}',
                                        wSesi:   {{ $wFirst ? $wFirst->sesi : 'null' }},
                                        mSesi:   {{ $mFirst ? $mFirst->sesi : 'null' }},
                                        jenis:   '{{ $mFirst ? $mFirst->jenis_sesi : 'online' }}',
                                        lokasi:  '{!! $mFirst ? addslashes($mFirst->lokasi ?? '') : '' !!}',
                                        materiMicro: '{!! addslashes($mFirst?->materi_micro_teaching ?? '') !!}',
                                        hasW:    {{ $row->wawancara->isNotEmpty() ? 'true' : 'false' }},
                                        hasM:    {{ $row->micro->isNotEmpty() ? 'true' : 'false' }},
                                        pengujiW: {{ $pengujiW }},
                                        pengujiM: {{ $pengujiM }},
                                        allPgIds: {{ $allPgIds }},
                                        pengujiData: {{ $pengujiDataJson }},
                                        readOnly: {{ ($anyDone || $row->lamaran?->status === 'mengundurkan_diri') ? 'true' : 'false' }},
                                    })"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors shrink-0"
                                    title="{{ ($anyDone || $row->lamaran?->status === 'mengundurkan_diri') ? 'Lihat jadwal (read only)' : 'Edit jadwal' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button type="button"
                                    @click="openDeleteModal = true; delPelamarId = {{ $row->pelamar->id }}; delLowonganId = {{ $row->lowongan->id }}; delPelamarNama = '{{ addslashes($row->pelamar->nama) }}'"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors shrink-0"
                                    title="Hapus jadwal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum ada jadwal</h3>
                                <p class="text-gray-400 text-xs">Belum ada jadwal seleksi yang dibuat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Empty state for filter --}}
        @if($rows->count() > 0)
        <div x-show="totalFiltered === 0" class="py-14 text-center" style="display: none;">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Belum ada data jadwal</h3>
            <p class="text-xs text-gray-400">Tidak ada jadwal yang cocok dengan pencarian atau filter.</p>
        </div>
        @endif

        {{-- Pagination --}}
        @if($rows->count() > 0)
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <span>Menampilkan <strong x-text="totalFiltered === 0 ? 0 : paginatedStart + 1"></strong>–<strong x-text="Math.min(paginatedEnd, totalFiltered)"></strong> dari <strong x-text="totalFiltered"></strong> data</span>
            <div class="flex items-center gap-1">
                {{-- Previous --}}
                <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                        class="px-3 py-1.5 rounded-lg font-medium transition">Prev</button>

                {{-- Page Numbers --}}
                <template x-for="page in pageNumbers" :key="page">
                    <button type="button" @click="goToPage(page)"
                            :class="page === currentPage ? 'bg-[#8b1515] text-white font-bold' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                            class="px-3 py-1.5 rounded-lg font-medium transition"
                            x-text="page"></button>
                </template>

                {{-- Next --}}
                <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                        :class="currentPage >= totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                        class="px-3 py-1.5 rounded-lg font-medium transition">Next</button>
            </div>
        </div>
        @endif
    </div>

{{-- -- MODAL EDIT -- --}}
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
         class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative z-10 max-h-[90vh] flex flex-col">

        <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-base font-bold text-white" x-text="modal.readOnly ? 'Detail Jadwal Seleksi' : 'Edit Jadwal Seleksi'"></h2>
                <p class="text-xs text-red-200 mt-0.5" x-text="modal.pelamarNama"></p>
            </div>
            <button @click="modal.open=false"
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-white/40 text-white hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.jadwal.updateGroup') }}" method="POST" class="px-6 pb-6 pt-4 flex flex-col overflow-y-auto flex-1">
            <div class="hidden">
                @csrf
                @method('PUT')
                <input type="hidden" name="pelamar_id"  :value="modal.pelamarId">
                <input type="hidden" name="lowongan_id" :value="modal.lowonganId">
            </div>

            <div class="space-y-5 flex-1">
            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Seleksi</label>
                <input type="date" name="tanggal" x-model="modal.tanggal" @change="loadTaken()"
                       :disabled="modal.readOnly"
                       :class="modal.readOnly ? 'bg-gray-50 text-gray-500 cursor-not-allowed' : ''"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                <p x-show="modal.loadingTaken && !modal.readOnly" class="text-[0.7rem] text-gray-400 mt-1 italic">Memuat ketersediaan sesi...</p>
            </div>

            {{-- Penguji Micro Teaching --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Penguji Micro Teaching <span class="font-normal text-gray-400">(30 mnt pertama)</span></label>
                {{-- Read-only: tampilkan sebagai teks --}}
                <template x-if="modal.readOnly">
                    <div class="flex flex-wrap gap-1">
                        <template x-for="pgId in modal.selectedMPenguji" :key="pgId">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-50 border border-red-100 text-[0.65rem] text-red-800" x-text="getPengujiNama(pgId)"></span>
                        </template>
                        <span x-show="modal.selectedMPenguji.length === 0" class="text-xs text-gray-400 italic">-</span>
                    </div>
                </template>
                {{-- Editable --}}
                <template x-if="!modal.readOnly">
                    <div>
                        <div x-show="modal.loadingPengujis" class="text-xs text-gray-400 italic px-3 py-2 border border-gray-200 rounded-lg">Memuat...</div>
                        <template x-if="!modal.loadingPengujis">
                            <div>
                                <div class="flex flex-wrap gap-1 mb-2" x-show="modal.selectedMPenguji.length > 0">
                                    <template x-for="pgId in modal.selectedMPenguji" :key="pgId">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 border border-gray-200 text-[0.65rem] text-gray-700">
                                            <span x-text="getPengujiNama(pgId)"></span>
                                            <button type="button" @click="toggleMPenguji(pgId)" class="text-gray-400 hover:text-gray-700 ml-0.5">&times;</button>
                                            <input type="hidden" name="micro_penguji_ids[]" :value="pgId">
                                        </span>
                                    </template>
                                </div>
                                {{-- Alpine dropdown penguji micro --}}
                                <div x-data="{ openMP: false }" @click.outside="openMP = false" class="relative">
                                    <button type="button" @click="openMP = !openMP"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-400 transition hover:border-gray-300">
                                        <span>+ Tambah penguji micro</span>
                                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="openMP ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openMP" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                        <div class="p-1 space-y-0.5 max-h-44 overflow-y-auto">
                                            <template x-for="pg in modal.availablePengujis" :key="pg.id">
                                                <button type="button"
                                                    :disabled="modal.selectedMPenguji.includes(parseInt(pg.id))"
                                                    @click="if(!modal.selectedMPenguji.includes(parseInt(pg.id))){ toggleMPenguji(pg.id); openMP = false; }"
                                                    class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                                    :class="modal.selectedMPenguji.includes(parseInt(pg.id)) ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-100 text-gray-700'">
                                                    <span x-text="`${pg.nama} (${pg.kode})`"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Penguji Wawancara --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Penguji Wawancara <span class="font-normal text-gray-400">(30 mnt kedua)</span></label>
                {{-- Read-only --}}
                <template x-if="modal.readOnly">
                    <div class="flex flex-wrap gap-1">
                        <template x-for="pgId in modal.selectedWPenguji" :key="pgId">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 border border-blue-100 text-[0.65rem] text-blue-800" x-text="getPengujiNama(pgId)"></span>
                        </template>
                        <span x-show="modal.selectedWPenguji.length === 0" class="text-xs text-gray-400 italic">-</span>
                    </div>
                </template>
                {{-- Editable --}}
                <template x-if="!modal.readOnly">
                    <div>
                        <div x-show="modal.loadingPengujis" class="text-xs text-gray-400 italic px-3 py-2 border border-gray-200 rounded-lg">Memuat...</div>
                        <template x-if="!modal.loadingPengujis">
                            <div>
                                <div class="flex flex-wrap gap-1 mb-2" x-show="modal.selectedWPenguji.length > 0">
                                    <template x-for="pgId in modal.selectedWPenguji" :key="pgId">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 border border-gray-200 text-[0.65rem] text-gray-700">
                                            <span x-text="getPengujiNama(pgId)"></span>
                                            <button type="button" @click="toggleWPenguji(pgId)" class="text-gray-400 hover:text-gray-700 ml-0.5">&times;</button>
                                            <input type="hidden" name="wawancara_penguji_ids[]" :value="pgId">
                                        </span>
                                    </template>
                                </div>
                                {{-- Alpine dropdown penguji wawancara --}}
                                <div x-data="{ openWP: false }" @click.outside="openWP = false" class="relative">
                                    <button type="button" @click="openWP = !openWP"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-400 transition hover:border-gray-300">
                                        <span>+ Tambah penguji wawancara</span>
                                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="openWP ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openWP" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                        <div class="p-1 space-y-0.5 max-h-44 overflow-y-auto">
                                            <template x-for="pg in modal.availablePengujis" :key="pg.id">
                                                <button type="button"
                                                    :disabled="modal.selectedWPenguji.includes(parseInt(pg.id))"
                                                    @click="if(!modal.selectedWPenguji.includes(parseInt(pg.id))){ toggleWPenguji(pg.id); openWP = false; }"
                                                    class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                                    :class="modal.selectedWPenguji.includes(parseInt(pg.id)) ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-100 text-gray-700'">
                                                    <span x-text="`${pg.nama} (${pg.kode})`"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Sesi (unified, sama persis dengan create) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sesi Seleksi <span class="font-normal text-gray-400">(60 menit)</span></label>
                <template x-if="modal.readOnly">
                    <p class="text-sm text-gray-700 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg" x-text="modal.wSesi ? (mSessions[modal.wSesi]?.label ?? 'Sesi ' + modal.wSesi) : '-'"></p>
                </template>
                <template x-if="!modal.readOnly">
                    <div>
                        {{-- Alpine dropdown sesi --}}
                        <div x-data="{ openSesi: false }" @click.outside="openSesi = false" class="relative">
                            <input type="hidden" name="sesi" :value="modal.wSesi">
                            <button type="button" @click="openSesi = !openSesi"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg border text-sm transition-all"
                                :class="{
                                    'border-red-400 bg-red-50 text-gray-800': modal.wSesi && isSesiBlocked(parseInt(modal.wSesi)),
                                    'border-gray-200 bg-white text-gray-800': modal.wSesi && !isSesiBlocked(parseInt(modal.wSesi)),
                                    'border-gray-200 bg-white text-gray-400': !modal.wSesi
                                }">
                                <span x-text="modal.wSesi ? (mSessions[modal.wSesi]?.label ?? 'Sesi ' + modal.wSesi) : '– Pilih Sesi –'"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="openSesi ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openSesi" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                    <template x-for="(info, key) in mSessions" :key="key">
                                        <button type="button"
                                            :disabled="isSesiBlocked(parseInt(key))"
                                            @click="if(!isSesiBlocked(parseInt(key))){ modal.wSesi = key; openSesi = false; }"
                                            class="w-full text-left px-3 py-2 text-xs rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                                            :class="modal.wSesi == key ? 'bg-gray-100 text-gray-900 font-semibold' : (isSesiBlocked(parseInt(key)) ? 'text-gray-400' : 'hover:bg-gray-100 text-gray-700')">
                                            <span x-text="sesiLabel(key, info, isSesiBlocked(parseInt(key)))"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <p x-show="modal.wSesi && isSesiBlocked(parseInt(modal.wSesi))"
                           class="text-[0.7rem] text-red-600 mt-1">Sesi ini bentrok – pilih sesi lain.</p>
                    </div>
                </template>
            </div>

            {{-- Jenis & Lokasi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis & Lokasi</label>
                <template x-if="modal.readOnly">
                    <div class="space-y-2">
                        <div class="inline-flex rounded-lg p-1 bg-gray-100/80 border border-gray-200">
                            <span class="px-4 py-1.5 text-xs font-bold rounded-md"
                                :class="modal.jenis === 'online' ? 'bg-white shadow-sm text-[#8b1515]' : 'text-gray-500'">Online</span>
                            <span class="px-4 py-1.5 text-xs font-bold rounded-md"
                                :class="modal.jenis === 'offline' ? 'bg-white shadow-sm text-[#8b1515]' : 'text-gray-500'">Offline</span>
                        </div>
                        <p class="text-sm px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                            <template x-if="modal.jenis === 'online'">
                                <a x-show="modal.lokasi" :href="modal.lokasi" target="_blank" x-text="modal.lokasi" class="text-blue-600 hover:underline break-all text-xs"></a>
                            </template>
                            <template x-if="modal.jenis === 'offline'">
                                <span x-text="modal.lokasi" class="text-gray-800 text-sm"></span>
                            </template>
                            <span x-show="!modal.lokasi" class="text-gray-400 italic">-</span>
                        </p>
                    </div>
                </template>
                <template x-if="!modal.readOnly">
                    <div class="space-y-2">
                        {{-- Toggle Jenis --}}
                        <div class="inline-flex rounded-lg p-1 bg-gray-100/80 border border-gray-200">
                            <button type="button" @click="modal.jenis = 'online'; modal.lokasi = ''"
                                class="px-4 py-1.5 text-xs font-bold rounded-md transition-all duration-200"
                                :class="modal.jenis === 'online' ? 'bg-white shadow-sm text-[#8b1515]' : 'text-gray-500 hover:text-gray-700'">
                                Online (Zoom)
                            </button>
                            <button type="button" @click="modal.jenis = 'offline'; modal.lokasi = ''"
                                class="px-4 py-1.5 text-xs font-bold rounded-md transition-all duration-200"
                                :class="modal.jenis === 'offline' ? 'bg-white shadow-sm text-[#8b1515]' : 'text-gray-500 hover:text-gray-700'">
                                Offline (Kampus)
                            </button>
                        </div>
                        {{-- Input Lokasi --}}
                        <input type="hidden" name="jenis_sesi" :value="modal.jenis">
                        <template x-if="modal.jenis === 'online'">
                            <input type="url" name="lokasi" x-model="modal.lokasi" placeholder="https://meet.google.com/..."
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                        </template>
                        <template x-if="modal.jenis === 'offline'">
                            <input type="text" name="lokasi" x-model="modal.lokasi" placeholder="Ruang Kelas x.xx"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                        </template>
                    </div>
                </template>
            </div>

            {{-- Materi Micro Teaching --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Materi Micro Teaching</label>
                <template x-if="modal.readOnly">
                    <p class="text-xs text-gray-700 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg whitespace-pre-line" x-text="modal.materiMicro || '-'"></p>
                </template>
                <template x-if="!modal.readOnly">
                    <textarea name="materi_micro_teaching" x-model="modal.materiMicro" rows="2"
                              placeholder="Persiapkan slide & materi 15 menit..."
                              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-y"></textarea>
                </template>
            </div>

            </div>

            {{-- Buttons --}}
            <div class="flex justify-center gap-3 pt-5 mt-5 border-t border-gray-100">
                
                <button x-show="!modal.readOnly" type="submit"
                        :disabled="hasConflict()"
                        :class="hasConflict() ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-[#8b1515] hover:bg-red-900 text-white'"
                        class="px-5 py-2 text-sm font-bold rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
    </div>
</div>
</template>{{-- Delete Modal --}}
<template x-teleport="body">
<div x-show="openDeleteModal" x-transition.opacity
    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 !mt-0"
    style="display: none;">
    <div x-show="openDeleteModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        @click.outside="openDeleteModal = false"
        class="bg-white rounded-[24px] shadow-2xl w-full max-w-[360px] overflow-hidden text-center p-8 relative">

        <div class="mx-auto mb-5 flex justify-center">
            <svg width="68" height="68" viewBox="0 0 24 24" fill="none" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                <path fill-rule="evenodd" fill="#8b1515" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 01-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 01-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 01-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.89-.777.89-2.038 0-2.815zM12 18.75a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" clip-rule="evenodd"/>
            </svg>
        </div>

        <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus Jadwal?</h2>
        <p class="text-[0.85rem] font-medium text-gray-500 mb-8">
            Seluruh jadwal seleksi untuk <strong class="text-gray-700" x-text="delPelamarNama"></strong> akan dihapus permanen.
        </p>

        <div class="grid grid-cols-2 gap-3">
            <form method="POST" action="{{ route('admin.jadwal.destroyGroup') }}" class="contents">
                @csrf
                @method('DELETE')
                <input type="hidden" name="pelamar_id" :value="delPelamarId">
                <input type="hidden" name="lowongan_id" :value="delLowonganId">
                <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">
                    Iya
                </button>
            </form>
            <button type="button" @click="openDeleteModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">
                Tidak
            </button>
        </div>
    </div>
</div>
</template>

</div>

<script>
const _sess = @json(\App\Models\JadwalSeleksi::SESSIONS);
const _base  = '{{ url("/") }}';

function jadwalTable() {
    // stub – pagination is handled in jadwalIndex
    return {};
}

function jadwalIndex() {
    return {
        search: '',
        fTanggal: '{{ request('tanggal') }}',
        fProdi: '{{ request('prodi_id') }}',
        fStatus: '{{ request('status') }}',
        currentPage: 1,
        perPage: 10,
        totalFiltered: 0,
        openDeleteModal: false,
        delPelamarId: null,
        delLowonganId: null,
        delPelamarNama: '',

        get hasFilters() { return this.fTanggal !== '' || this.fProdi !== '' || this.fStatus !== ''; },
        get paginatedStart() { return (this.currentPage - 1) * this.perPage; },
        get paginatedEnd()   { return this.currentPage * this.perPage; },
        get totalPages()     { return Math.max(1, Math.ceil(this.totalFiltered / this.perPage)); },
        get pageNumbers() {
            const pages = [];
            for (let i = Math.max(1, this.currentPage - 2); i <= Math.min(this.totalPages, this.currentPage + 2); i++) pages.push(i);
            return pages;
        },

        prodiName(id) {
            const m = { @foreach($prodis as $p)'{{ $p->id }}': '{{ addslashes($p->nama) }}',@endforeach };
            return m[id] ?? '';
        },
        statusName(s) {
            return {'belum':'Belum Dinilai','sebagian':'Sebagian Dinilai','selesai':'Selesai'}[s] ?? s;
        },

        initPagination() {},

        recalcAll() {
            const rows = Array.from(document.querySelectorAll('tr[data-jadwal-row]'));
            const query = this.search.toLowerCase().trim();
            const visible = rows.filter(row => {
                const matchT = this.fTanggal === '' || row.dataset.tanggal === this.fTanggal;
                const matchP = this.fProdi === '' || row.dataset.prodi === this.fProdi;
                const matchS = this.fStatus === '' || row.dataset.status === this.fStatus;
                const matchQ = query === '' || (row.dataset.pelamar || '').includes(query);
                return matchT && matchP && matchS && matchQ;
            });
            this.totalFiltered = visible.length;

            // Hide all rows
            rows.forEach(r => r.style.display = 'none');
            // Show only paginated visible rows
            visible.forEach((row, idx) => {
                row.style.display = (idx >= this.paginatedStart && idx < this.paginatedEnd) ? '' : 'none';
            });
        },

        resetAndRecalc() {
            this.currentPage = 1;
            this.recalcAll();
        },

        prevPage() { if (this.currentPage > 1) { this.currentPage--; this.recalcAll(); } },
        nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.recalcAll(); } },
        goToPage(p) { this.currentPage = p; this.recalcAll(); },

        modal: {
            open: false,
            readOnly: false,
            pelamarId: null, lowonganId: null, prodiId: null, pelamarNama: '',
            tanggal: '', wSesi: '', mSesi: '',
            jenis: 'online', lokasi: '', materiMicro: '',
            origW: null, origM: null,
            hasW: false, hasM: false,
            pengujiW: [], pengujiM: [], allPgIds: [],
            takenMap: {}, takenPelamarMap: {}, loadingTaken: false,
            availablePengujis: [], loadingPengujis: false,
            selectedWPenguji: [], selectedMPenguji: [],
            pengujiDataMap: {}, // Menyimpan data penguji dengan key ID
        },

        get wSessions() { return _sess['wawancara'] ?? {}; },
        get mSessions() { return _sess['micro_teaching'] ?? {}; },

        openEdit(d) {
            this.modal.pelamarId   = d.pelamarId;
            this.modal.lowonganId  = d.lowonganId;
            this.modal.prodiId     = d.prodiId;
            this.modal.pelamarNama = d.pelamarNama;
            this.modal.readOnly    = d.readOnly || false;
            this.modal.tanggal     = d.tanggal;
            this.modal.wSesi       = d.wSesi !== null ? String(d.wSesi) : '';
            this.modal.mSesi       = d.mSesi !== null ? String(d.mSesi) : '';
            this.modal.jenis       = d.jenis || 'online';
            this.modal.lokasi      = d.lokasi || '';
            this.modal.materiMicro = d.materiMicro || '';
            this.modal.origW       = d.wSesi;
            this.modal.origM       = d.mSesi;
            this.modal.hasW        = d.hasW;
            this.modal.hasM        = d.hasM;
            this.modal.pengujiW    = d.pengujiW;
            this.modal.pengujiM    = d.pengujiM;
            this.modal.allPgIds    = d.allPgIds;
            this.modal.takenMap    = {};
            this.modal.takenPelamarMap = {};
            this.modal.selectedWPenguji = [...d.pengujiW];
            this.modal.selectedMPenguji = [...d.pengujiM];
            
            // Simpan data penguji dalam map untuk lookup cepat
            this.modal.pengujiDataMap = {};
            if (d.pengujiData && Array.isArray(d.pengujiData)) {
                d.pengujiData.forEach(pg => {
                    this.modal.pengujiDataMap[pg.id] = pg;
                });
            }
            
            this.modal.open        = true;
            this.loadTaken();
            this.loadPengujis();
        },

        toggleWPenguji(id) {
            id = parseInt(id);
            const idx = this.modal.selectedWPenguji.indexOf(id);
            if (idx === -1) {
                this.modal.selectedWPenguji.push(id);
            } else {
                this.modal.selectedWPenguji.splice(idx, 1);
            }
            // Update allPgIds for taken calculation
            this.modal.allPgIds = [...new Set([...this.modal.selectedWPenguji, ...this.modal.selectedMPenguji])];
            this.loadTaken();
        },

        toggleMPenguji(id) {
            id = parseInt(id);
            const idx = this.modal.selectedMPenguji.indexOf(id);
            if (idx === -1) {
                this.modal.selectedMPenguji.push(id);
            } else {
                this.modal.selectedMPenguji.splice(idx, 1);
            }
            // Update allPgIds for taken calculation
            this.modal.allPgIds = [...new Set([...this.modal.selectedWPenguji, ...this.modal.selectedMPenguji])];
            this.loadTaken();
        },

        async loadPengujis() {
            if (!this.modal.prodiId) return;
            this.modal.loadingPengujis = true;
            try {
                const res = await fetch(`${_base}/admin/api/penguji-by-prodi?prodi_id=${this.modal.prodiId}`);
                const apiPengujis = res.ok ? await res.json() : [];
                
                // Merge dengan data yang sudah ada di pengujiDataMap
                const merged = {};
                
                // Tambah dari pengujiDataMap
                Object.values(this.modal.pengujiDataMap).forEach(pg => {
                    merged[pg.id] = pg;
                });
                
                // Tambah dari API (overwrite jika sudah ada)
                apiPengujis.forEach(pg => {
                    merged[pg.id] = pg;
                });
                
                this.modal.availablePengujis = Object.values(merged);
            } catch { 
                this.modal.availablePengujis = Object.values(this.modal.pengujiDataMap);
            }
            this.modal.loadingPengujis = false;
        },

        async loadTaken() {
            const { tanggal, allPgIds, pelamarId } = this.modal;
            if (!tanggal || !allPgIds.length) return;
            this.modal.loadingTaken = true;
            try {
                const res = await fetch(`${_base}/admin/api/sesi-taken-all?tanggal=${tanggal}&penguji_ids=${allPgIds.join(',')}`);
                this.modal.takenMap = res.ok ? await res.json() : {};

                if (pelamarId) {
                    const res2 = await fetch(`${_base}/admin/api/sesi-taken-pelamar?tanggal=${tanggal}&pelamar_ids=${pelamarId}&exclude_lowongan_id=${this.modal.lowonganId}`);
                    this.modal.takenPelamarMap = res2.ok ? await res2.json() : {};
                }
            } catch { 
                this.modal.takenMap = {}; 
                this.modal.takenPelamarMap = {};
            }
            this.modal.loadingTaken = false;
        },

        _takenFor(pgId, tipe) {
            return (this.modal.takenMap[String(pgId)]?.[tipe] ?? []).map(Number);
        },

        isBlockedW(sesiNum) {
            return this.isSesiBlocked(sesiNum);
        },

        isBlockedM(sesiNum) {
            return this.isSesiBlocked(sesiNum);
        },

        isValidUrl(url) {
            if (!url) return false;
            const pattern = /^(http|https):\/\/[^ "]+$/;
            return pattern.test(url);
        },

        hasConflict() {
            if (this.modal.wSesi && this.isSesiBlocked(parseInt(this.modal.wSesi))) return true;
            
            // Lokasi validation
            const lokasiOk = this.modal.jenis === 'online'
                ? this.isValidUrl(this.modal.lokasi)
                : this.modal.lokasi.trim() !== '';
            if (this.modal.wSesi && !lokasiOk) return true;

            // Penguji harus dipilih minimal 1 untuk masing-masing
            if (this.modal.selectedMPenguji.length === 0) return true;
            if (this.modal.selectedWPenguji.length === 0) return true;

            // Sesi harus dipilih
            if (!this.modal.wSesi) return true;

            return false;
        },

        sesiLabel(key, info, isBlocked) {
            const base = info.block_label || `Sesi ${key}`;
            return isBlocked ? base + ' (Bentrok)' : base;
        },

        // Unified sesi check: cek bentrok untuk SEMUA penguji (micro + wawancara) pada sesi ini
        isSesiBlocked(sesiNum) {
            const orig = this.modal.origW; // sesi asli sebelum edit

            // Cek pelamar (konflik di lowongan lain)
            const pelamarTaken = this.modal.takenPelamarMap?.[String(this.modal.pelamarId)] || [];
            if (pelamarTaken.map(Number).includes(sesiNum)) return true;

            // Cek penguji micro
            for (const pgId of this.modal.selectedMPenguji) {
                const taken = this._takenFor(pgId, 'micro_teaching').filter(s => s !== orig);
                if (taken.includes(sesiNum)) return true;
            }

            // Cek penguji wawancara
            for (const pgId of this.modal.selectedWPenguji) {
                const taken = this._takenFor(pgId, 'wawancara').filter(s => s !== orig);
                if (taken.includes(sesiNum)) return true;
            }

            return false;
        },

        getPengujiNama(pgId) {
            pgId = parseInt(pgId);
            // Cek di pengujiDataMap dulu (data dari jadwal yang sudah ada)
            if (this.modal.pengujiDataMap[pgId]) {
                const pg = this.modal.pengujiDataMap[pgId];
                return `${pg.nama} (${pg.kode})`;
            }
            // Fallback ke availablePengujis (data dari API)
            const penguji = this.modal.availablePengujis.find(p => p.id === pgId);
            return penguji ? `${penguji.nama} (${penguji.kode})` : `Penguji #${pgId}`;
        },

        filterBySearch() {
            this.resetAndRecalc();
        },
    };
}
</script>

@endsection
