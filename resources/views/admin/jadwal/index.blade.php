@extends('layouts.admin')
@section('title', 'Penjadwalan')
@section('content')

<div class="max-w-7xl mx-auto space-y-6" x-data="jadwalIndex()" x-init="initPagination(); $watch('search', () => filterBySearch())">


    {{-- Filter Chips Bar (with attached + button) --}}
    <div class="relative">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20">
            <div class="flex items-center gap-3 flex-wrap">

                {{-- Tanggal Chip --}}
                <div class="relative" x-data="{ tanggalOpen: false }" @click.outside="tanggalOpen = false">
                    <button type="button" @click="tanggalOpen = !tanggalOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="'{{ request('tanggal') }}' !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Tanggal
                        @if(request('tanggal'))
                        <span class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        @endif
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="tanggalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="tanggalOpen" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Pilih Tanggal</p></div>
                        <form method="GET" action="{{ route('admin.jadwal.index') }}" class="p-4">
                            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition cursor-pointer">
                            <button type="submit" class="w-full mt-3 px-4 py-2 bg-[#8b1515] text-white text-sm font-bold rounded-lg hover:bg-red-900 transition-colors">Apply</button>
                        </form>
                    </div>
                </div>

                {{-- Prodi Chip --}}
                <div class="relative" x-data="{ prodiOpen: false }" @click.outside="prodiOpen = false">
                    <button type="button" @click="prodiOpen = !prodiOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="'{{ request('prodi_id') }}' !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        Prodi
                        @if(request('prodi_id'))<span class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>@endif
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="prodiOpen" x-transition class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p></div>
                        <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                            @foreach($prodis as $prodi)
                            <a href="{{ route('admin.jadwal.index', array_merge(request()->query(), ['prodi_id' => $prodi->id])) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors {{ request('prodi_id') == $prodi->id ? 'bg-gray-50' : '' }}">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center {{ request('prodi_id') == $prodi->id ? 'border-[#8b1515] bg-[#8b1515]' : 'border-gray-300' }}">
                                    @if(request('prodi_id') == $prodi->id)<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                </span>
                                <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Status Chip --}}
                <div class="relative" x-data="{ statusOpen: false }" @click.outside="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="'{{ request('status') }}' !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Status
                        @if(request('status'))<span class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>@endif
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p></div>
                        <div class="p-3 space-y-1">
                            @php $statuses = ['belum' => ['Belum Dinilai', 'text-yellow-600'], 'sebagian' => ['Sebagian Dinilai', 'text-blue-600'], 'selesai' => ['Selesai', 'text-green-600']]; @endphp
                            @foreach($statuses as $key => [$label, $color])
                            <a href="{{ route('admin.jadwal.index', array_merge(request()->query(), ['status' => $key])) }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 {{ request('status') == $key ? 'bg-gray-50' : '' }}">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center {{ request('status') == $key ? 'border-[#8b1515] bg-[#8b1515]' : 'border-gray-300' }}">
                                    @if(request('status') == $key)<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                </span>
                                <span class="text-sm font-medium {{ $color }}">{{ $label }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Active filter tags --}}
                @if(request('tanggal'))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 border border-red-200 text-xs font-semibold text-[#8b1515]">
                    {{ \Carbon\Carbon::parse(request('tanggal'))->format('d M Y') }}
                    <a href="{{ route('admin.jadwal.index', array_merge(array_diff_key(request()->query(), ['tanggal' => '']))) }}" class="ml-0.5 hover:text-red-800"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></a>
                </span>
                @endif
                @if(request('prodi_id'))
                @foreach($prodis as $p) @if($p->id == request('prodi_id'))
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-50 border border-blue-200 text-xs font-semibold text-blue-700">
                    {{ $p->nama }}
                    <a href="{{ route('admin.jadwal.index', array_merge(array_diff_key(request()->query(), ['prodi_id' => '']))) }}" class="ml-0.5 hover:text-blue-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></a>
                </span>
                @endif @endforeach
                @endif
                @if(request('status'))
                @php $statusLabels = ['belum' => 'Belum Dinilai', 'sebagian' => 'Sebagian Dinilai', 'selesai' => 'Selesai']; @endphp
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-green-50 border border-green-200 text-xs font-semibold text-green-700">
                    {{ $statusLabels[request('status')] ?? request('status') }}
                    <a href="{{ route('admin.jadwal.index', array_merge(array_diff_key(request()->query(), ['status' => '']))) }}" class="ml-0.5 hover:text-green-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></a>
                </span>
                @endif

                @if(request('tanggal') || request('prodi_id') || request('status'))
                <a href="{{ route('admin.jadwal.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-red-600 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Clear Filters
                </a>
                @endif

                {{-- Search (animated) --}}
                <div class="relative ml-auto flex items-center" x-data="{ searchOpen: false }" @click.outside="if(!search) searchOpen = false">
                    <div class="relative flex items-center">
                        {{-- Magnify button --}}
                        <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                                class="absolute left-0 z-10 w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                                :class="searchOpen ? 'pointer-events-none' : 'border border-gray-200'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                        {{-- Expanding input --}}
                        <div class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                             :style="searchOpen ? 'width: 288px; opacity: 1' : 'width: 36px; opacity: 0'">
                            <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama pelamar..."
                                   @keydown.escape="search = ''; searchOpen = false"
                                   class="w-[288px] pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                        </div>
                        {{-- Close button --}}
                        <button type="button" x-show="searchOpen" x-transition.opacity.duration.200ms
                                @click="search = ''; searchOpen = false"
                                class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                
            </div>
        </div>

        {{-- + Button (outside card, flush right) --}}
        <a href="{{ route('admin.jadwal.create') }}"
           class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Buat Jadwal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </a>
    </div>
    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="jadwalTable()">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed" style="min-width:1150px; width:100%">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-[12%]">Tanggal</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-[14%]">Lowongan</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-[14%]">Pelamar</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-[16%]">Micro Teaching</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center border-l border-red-700 w-[8%]">Status</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap border-l border-red-700 w-[16%]">Wawancara</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center border-l border-red-700 w-[8%]">Status</th>
                        <th class="py-3 px-4 text-xs font-bold whitespace-nowrap text-center border-l border-red-700 w-[12%]">Aksi</th>
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
                    <tr class="hover:bg-gray-50/60 transition-colors align-middle" data-row data-pelamar="{{ strtolower($row->pelamar->nama) }}">

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
                            <div class="text-[0.75rem] text-gray-500 font-medium mt-1">{{ $row->pelamar->user?->email ?? '-' }}</div>
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

                        {{-- Aksi --}}
                        <td class="py-4 px-4 border-l border-gray-100 text-center align-top">
                            @php
                                $anyDone = ($row->micro->isNotEmpty() && $row->micro->whereNotNull('penilaian')->count() > 0)
                                        || ($row->wawancara->isNotEmpty() && $row->wawancara->whereNotNull('penilaian')->count() > 0);
                            @endphp
                            <button type="button"
                                @click="openEdit({
                                    pelamarId:  {{ $row->pelamar->id }},
                                    lowonganId: {{ $row->lowongan->id }},
                                    prodiId:    {{ $row->lowongan->prodi_id ?? 'null' }},
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
                                    pengujiData: {{ $pengujiDataJson }},
                                    readOnly: {{ $anyDone ? 'true' : 'false' }},
                                })"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                title="{{ $anyDone ? 'Lihat jadwal (read only)' : 'Edit jadwal' }}">
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

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <span>Menampilkan <strong x-text="paginatedStart + 1"></strong>–<strong x-text="Math.min(paginatedEnd, totalFiltered)"></strong> dari <strong x-text="totalFiltered"></strong> data</span>
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
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 border border-red-100 text-[0.65rem] text-red-800">
                                            <span x-text="getPengujiNama(pgId)"></span>
                                            <button type="button" @click="toggleMPenguji(pgId)" class="text-red-300 hover:text-red-600 ml-0.5">&times;</button>
                                            <input type="hidden" name="micro_penguji_ids[]" :value="pgId">
                                        </span>
                                    </template>
                                </div>
                                <select @change="toggleMPenguji($event.target.value); $event.target.value = ''"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white">
                                    <option value="">+ Tambah penguji micro</option>
                                    <template x-for="pg in modal.availablePengujis" :key="pg.id">
                                        <option :value="pg.id" :disabled="modal.selectedMPenguji.includes(parseInt(pg.id))"
                                            x-text="`${pg.nama} (${pg.kode})`"></option>
                                    </template>
                                </select>
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
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 border border-blue-100 text-[0.65rem] text-blue-800">
                                            <span x-text="getPengujiNama(pgId)"></span>
                                            <button type="button" @click="toggleWPenguji(pgId)" class="text-blue-300 hover:text-blue-600 ml-0.5">&times;</button>
                                            <input type="hidden" name="wawancara_penguji_ids[]" :value="pgId">
                                        </span>
                                    </template>
                                </div>
                                <select @change="toggleWPenguji($event.target.value); $event.target.value = ''"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white">
                                    <option value="">+ Tambah penguji wawancara</option>
                                    <template x-for="pg in modal.availablePengujis" :key="pg.id">
                                        <option :value="pg.id" :disabled="modal.selectedWPenguji.includes(parseInt(pg.id))"
                                            x-text="`${pg.nama} (${pg.kode})`"></option>
                                    </template>
                                </select>
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
                        <select name="sesi" x-model="modal.wSesi"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white"
                                :class="{ 'border-red-400 bg-red-50': modal.wSesi && isSesiBlocked(parseInt(modal.wSesi)) }">
                            <option value="">— Pilih Sesi —</option>
                            <template x-for="(info, key) in mSessions" :key="key">
                                <option :value="key"
                                        :disabled="isSesiBlocked(parseInt(key))"
                                        x-text="sesiLabel(key, info, isSesiBlocked(parseInt(key)))">
                                </option>
                            </template>
                        </select>
                        <p x-show="modal.wSesi && isSesiBlocked(parseInt(modal.wSesi))"
                           class="text-[0.7rem] text-red-600 mt-1">Sesi ini bentrok — pilih sesi lain.</p>
                    </div>
                </template>
            </div>

            {{-- Link Meeting --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link Meeting</label>
                <template x-if="modal.readOnly">
                    <p class="text-sm px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                        <a x-show="modal.wLink" :href="modal.wLink" target="_blank" x-text="modal.wLink" class="text-blue-600 hover:underline break-all text-xs"></a>
                        <span x-show="!modal.wLink" class="text-gray-400 italic">-</span>
                    </p>
                </template>
                <template x-if="!modal.readOnly">
                    <input type="url" name="link" x-model="modal.wLink" placeholder="https://meet.google.com/..."
                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
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
</template>
</div>

<script>
const _sess = @json(\App\Models\JadwalSeleksi::SESSIONS);
const _base  = '{{ url("/") }}';

function jadwalIndex() {
    return {
        search: '',
        modal: {
            open: false,
            readOnly: false,
            pelamarId: null, lowonganId: null, prodiId: null, pelamarNama: '',
            tanggal: '', wSesi: '', mSesi: '',
            wLink: '', mLink: '',
            origW: null, origM: null,
            hasW: false, hasM: false,
            pengujiW: [], pengujiM: [], allPgIds: [],
            takenMap: {}, loadingTaken: false,
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
            
            // Link validation
            if (this.modal.wSesi && !this.isValidUrl(this.modal.wLink)) return true;

            // Penguji harus dipilih minimal 1 untuk masing-masing
            if (this.modal.selectedMPenguji.length === 0) return true;
            if (this.modal.selectedWPenguji.length === 0) return true;

            // Sesi harus dipilih
            if (!this.modal.wSesi) return true;

            return false;
        },

        sesiLabel(key, info, isBlocked) {
            const base = info.block_label || `Sesi ${key}`;
            return isBlocked ? base + ' ✗' : base;
        },

        // Unified sesi check: cek bentrok untuk SEMUA penguji (micro + wawancara) pada sesi ini
        isSesiBlocked(sesiNum) {
            const orig = this.modal.origW; // sesi asli sebelum edit

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
            const rows = document.querySelectorAll('tr[data-row]');
            const query = this.search.toLowerCase().trim();
            rows.forEach(row => {
                if (!query) {
                    row.style.display = '';
                } else {
                    const pelamar = row.dataset.pelamar || '';
                    row.style.display = pelamar.includes(query) ? '' : 'none';
                }
            });
        },
    };
}
</script>

@endsection
