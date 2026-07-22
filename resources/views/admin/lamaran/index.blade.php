@extends('layouts.admin')

@section('title', 'Daftar Lamaran ')

@section('content')

<div class="max-w-6xl mx-auto space-y-6"
     x-data="{
         search: '',
         statusFilter: '',
         statusOpen: false,
         deleteModalOpen: false,
         deleteActionUrl: '',
         deleteNama: '',
         currentPage: 1,
         perPage: 10,
         get filteredRows() {
             return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                 var matchStatus = this.statusFilter === '' || row.dataset.status === this.statusFilter;
                 var matchSearch = this.search === '' || (row.dataset.nama || '').includes(this.search.toLowerCase());
                 return matchStatus && matchSearch;
             });
         },
         get totalFiltered() { return this.filteredRows.length; },
         get totalPages() { return Math.max(1, Math.ceil(this.totalFiltered / this.perPage)); },
         get paginatedStart() { return (this.currentPage - 1) * this.perPage; },
         get paginatedEnd() { return this.currentPage * this.perPage; },
         get hasFilters() { return this.statusFilter !== '' || this.search !== ''; },
         updateVisibility() {
             var rows = Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]'));
             var filtered = this.filteredRows;
             rows.forEach(function(row) {
                 var idx = filtered.indexOf(row);
                 row.style.display = (idx === -1 || idx < this.paginatedStart || idx >= this.paginatedEnd) ? 'none' : '';
             }.bind(this));
         },
         clearAll() { this.search = ''; this.statusFilter = ''; },
         resetPage() { this.currentPage = 1; this.updateVisibility(); },
         prevPage() { if (this.currentPage > 1) { this.currentPage--; this.updateVisibility(); } },
         nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.updateVisibility(); } },
         goToPage(p) { this.currentPage = p; this.updateVisibility(); }
     }"
     x-init="$nextTick(() => updateVisibility());
              $watch('search', () => resetPage());
              $watch('statusFilter', () => resetPage());">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.lowongan.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Lowongan</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">{{ $lowongan->nama_posisi }}</span>
    </div>

    {{-- Filter Chips Bar (with attached print button) --}}
    <div class="relative">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20">
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

                {{-- Status Chip --}}
                <div class="relative" @click.outside="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="statusFilter !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        Status
                        <span x-show="statusFilter !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p></div>
                        <div class="p-3 space-y-1">
                            <button type="button" @click="statusFilter = statusFilter === 'menunggu' ? '' : 'menunggu'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="statusFilter === 'menunggu' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'menunggu' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'menunggu'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-600">Menunggu</span>
                            </button>
                            <button type="button" @click="statusFilter = statusFilter === 'seleksi_tahap1' ? '' : 'seleksi_tahap1'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="statusFilter === 'seleksi_tahap1' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'seleksi_tahap1' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'seleksi_tahap1'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-600">Seleksi Tahap 1</span>
                            </button>
                            <button type="button" @click="statusFilter = statusFilter === 'seleksi_tahap2' ? '' : 'seleksi_tahap2'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="statusFilter === 'seleksi_tahap2' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'seleksi_tahap2' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'seleksi_tahap2'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-600">Seleksi Tahap 2</span>
                            </button>
                            <button type="button" @click="statusFilter = statusFilter === 'diterima' ? '' : 'diterima'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="statusFilter === 'diterima' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'diterima' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'diterima'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-600">Diterima</span>
                            </button>
                            <button type="button" @click="statusFilter = statusFilter === 'ditolak' ? '' : 'ditolak'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="statusFilter === 'ditolak' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'ditolak' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'ditolak'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-600">Ditolak</span>
                            </button>
                            <button type="button" @click="statusFilter = statusFilter === 'mengundurkan_diri' ? '' : 'mengundurkan_diri'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="statusFilter === 'mengundurkan_diri' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'mengundurkan_diri' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'mengundurkan_diri'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-600">Mengundurkan Diri</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Active filter tag --}}
                <span x-show="statusFilter !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                    <span x-text="{menunggu:'Menunggu',seleksi_tahap1:'Seleksi Tahap 1',seleksi_tahap2:'Seleksi Tahap 2',diterima:'Diterima',ditolak:'Ditolak',mengundurkan_diri:'Mengundurkan Diri'}[statusFilter]"></span>
                    <button type="button" @click="statusFilter = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </span>

                {{-- Clear All --}}
                <button x-show="hasFilters" x-transition type="button" @click="clearAll()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear Filters
                </button>

                @php $jumlahUndur = $lowongan->lamarans->where('status','mengundurkan_diri')->count(); @endphp

                {{-- Export Buttons --}}
                <div class="relative ml-auto flex items-center gap-2">
                    
                    {{-- Export Rekap Nilai --}}
                    <a href="{{ route('admin.lamaran.exportNilai', $lowongan) }}" title="Export Rekap Nilai"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </a>

                    {{-- Export Pelamar Excel --}}
                    <a href="{{ route('admin.lamaran.export', $lowongan) }}" title="Export Data Pelamar"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    </a>
                </div>

            </div>
        </div>

        {{-- Print Berita Acara button (outside card, flush right) --}}
        <a href="{{ route('admin.lowongan.beritaAcara', $lowongan) }}" target="_blank"
           class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Cetak Berita Acara">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed" style="min-width:1000px">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold leading-tight text-center w-[15%]">Nama </th>
                        <th class="py-3 px-5 text-sm font-bold leading-tight text-center w-[15%]">Pendidikan</th>
                        <th class="py-3 px-5 text-sm font-bold leading-tight text-center w-[15%]">No Telepon</th>
                        <th class="py-3 px-5 text-sm font-bold leading-tight text-center w-[15%]">Email</th>
                        <th class="py-3 px-5 text-sm font-bold leading-tight text-center w-[15%]">Status</th>
                        <th class="py-3 px-5 text-sm font-bold leading-tight text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                    @forelse($lowongan->lamarans as $lamaran)
                    <tr class="hover:bg-gray-50 transition-colors"
                        data-row
                        data-status="{{ $lamaran->status }}"
                        data-nama="{{ strtolower($lamaran->pelamar->nama) }}">
                        <td class="py-3 px-5 max-w-0">
                            <div class="text-sm font-medium text-gray-800 text-center truncate" title="{{ $lamaran->pelamar->nama }}">{{ $lamaran->pelamar->nama }}</div>
                            <div class="text-xs font-medium text-gray-400 mt-0.5 text-center truncate" title="{{ $lamaran->pelamar->user?->email }}">{{ $lamaran->pelamar->user?->email }}</div>
                        </td>
                        <td class="py-3 px-5 max-w-0">
                            <div class="text-sm font-medium text-gray-700 text-center truncate" title="{{ $lamaran->pelamar->jenjang }} - {{ $lamaran->pelamar->prodi_pendidikan }}">{{ $lamaran->pelamar->jenjang }} - {{ $lamaran->pelamar->prodi_pendidikan }}</div>
                            <div class="text-xs font-medium text-gray-400 mt-0.5 text-center truncate" title="{{ $lamaran->pelamar->institusi }}">{{ $lamaran->pelamar->institusi }}</div>
                        </td>
                        <td class="py-3 px-5 max-w-0">
                            <div class="text-sm font-medium text-gray-600 text-center truncate" title="{{ $lamaran->pelamar->no_telepon ?? '-' }}">{{ $lamaran->pelamar->no_telepon ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5 max-w-0">
                            <div class="text-sm font-medium text-gray-600 text-center truncate" title="{{ $lamaran->pelamar->user?->email ?? '-' }}">{{ $lamaran->pelamar->user?->email ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5 max-w-0 text-center">
                            @php
                                $statusLabels = [
                                    'seleksi_tahap1' => 'Seleksi Tahap 1',
                                    'seleksi_tahap2' => 'Seleksi Tahap 2',
                                    'diterima'       => 'Diterima',
                                    'ditolak'        => 'Ditolak',
                                    'mengundurkan_diri'=> 'Mengundurkan Diri',
                                ];
                            @endphp
                            
                            @if($lamaran->status === 'menunggu')
                                @if($lamaran->is_direkomendasikan_kaprodi === true)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-800 text-white text-[0.65rem] text-center font-bold">
                                        Direkomendasikan
                                    </span>
                                @elseif($lamaran->is_direkomendasikan_kaprodi === false)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-800 text-white text-[0.65rem] text-center font-bold">
                                        Tidak Direkomendasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-50 border border-gray-200 text-gray-500 text-[0.65rem] text-center font-bold">
                                        Menunggu
                                    </span>
                                @endif
                            @else
                                @php
                                    $badgeClasses = [
                                        'seleksi_tahap1'    => 'bg-blue-800 text-white',
                                        'seleksi_tahap2'    => 'bg-indigo-800 text-white',
                                        'diterima'          => 'bg-green-800 text-white',
                                        'ditolak'           => 'bg-red-800 text-white',
                                        'mengundurkan_diri' => 'bg-gray-800 text-white',
                                    ];
                                    $badgeClass = $badgeClasses[$lamaran->status] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-lg {{ $badgeClass }} text-[0.65rem] text-center font-bold tracking-wide" title="{{ $statusLabels[$lamaran->status] ?? $lamaran->status }}">
                                    {{ $statusLabels[$lamaran->status] ?? $lamaran->status }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.lamaran.show', $lamaran) }}" title="Detail" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.lamaran.cetak', $lamaran) }}" target="_blank" title="Cetak" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                                <button type="button" 
                                        @click="deleteModalOpen = true; deleteActionUrl = '{{ route('admin.lamaran.destroy', $lamaran) }}'; deleteNama = '{{ addslashes($lamaran->pelamar->nama) }}'"
                                        title="Hapus" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-700 hover:bg-red-50 rounded transition-colors inline m-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum ada pelamar</h3>
                                <p class="text-gray-400 text-xs">Belum ada pelamar yang mengajukan lamaran.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <span>Menampilkan <strong x-text="totalFiltered === 0 ? 0 : paginatedStart + 1"></strong>–<strong x-text="Math.min(paginatedEnd, totalFiltered)"></strong> dari <strong x-text="totalFiltered"></strong> data</span>
            <div class="flex items-center gap-1">
                <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                        class="px-3 py-1.5 rounded-lg font-medium transition">Prev</button>
                <template x-for="page in totalPages" :key="page">
                    <button type="button" @click="goToPage(page)"
                            x-show="page >= currentPage - 2 && page <= currentPage + 2"
                            :class="page === currentPage ? 'bg-[#8b1515] text-white font-bold' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                            class="px-3 py-1.5 rounded-lg font-medium transition" x-text="page"></button>
                </template>
                <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                        :class="currentPage >= totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                        class="px-3 py-1.5 rounded-lg font-medium transition">Next</button>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 !m-0" @click.self="deleteModalOpen = false">
        <div x-show="deleteModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">
            
            {{-- Warning Icon --}}
            <div class="mx-auto mb-5 flex justify-center">
                <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                    <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                    <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                </svg>
            </div>
            
            <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Yakin ingin menghapus?</h2>
            <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Hapus lamaran atas nama <br><strong x-text="deleteNama" class="text-gray-700"></strong>?</p>

            <div class="grid grid-cols-2 gap-3">
                <form method="POST" :action="deleteActionUrl" class="contents">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Iya</button>
                </form>
                <button type="button" @click="deleteModalOpen = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">Tidak</button>
            </div>
        </div>
    </div>

</div>
@endsection
