@extends('layouts.admin')

@section('title', 'Manajemen Pelamar')

@section('content')

<div class="max-w-6xl mx-auto space-y-6" 
     x-data="{
        search: '{{ request('search') }}',
        filterProdi: '{{ request('prodi_id') }}',
        currentPage: 1,
        perPage: 10,
        get filteredRows() {
            return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                const name = row.dataset.name || '';
                const phone = row.dataset.phone || '';
                const prodis = row.dataset.prodis || '';
                const matchSearch = this.search === '' || name.includes(this.search.toLowerCase()) || phone.includes(this.search.toLowerCase());
                const matchProdi = this.filterProdi === '' || prodis.split(',').includes(this.filterProdi);
                return matchSearch && matchProdi;
            });
        },
        get totalFiltered() { return this.filteredRows.length; },
        get totalPages() { return Math.max(1, Math.ceil(this.totalFiltered / this.perPage)); },
        get paginatedStart() { return (this.currentPage - 1) * this.perPage; },
        get paginatedEnd() { return this.currentPage * this.perPage; },
        updateVisibility() {
            const rows = Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]'));
            const filtered = this.filteredRows;
            rows.forEach(row => {
                const idx = filtered.indexOf(row);
                row.style.display = (idx === -1 || idx < this.paginatedStart || idx >= this.paginatedEnd) ? 'none' : '';
            });
        },
        resetPage() { this.currentPage = 1; this.updateVisibility(); },
        prevPage() { if (this.currentPage > 1) { this.currentPage--; this.updateVisibility(); } },
        nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.updateVisibility(); } },
        goToPage(p) { this.currentPage = p; this.updateVisibility(); }
     }"
     x-init="
        $nextTick(() => updateVisibility());
        $watch('search', () => resetPage());
        $watch('filterProdi', () => resetPage());
     ">

    {{-- Filter Chips Bar (with attached + button) --}}
    <div class="relative">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20">
            <div class="flex items-center gap-3 flex-wrap">

            {{-- Search (animated) --}}
            <div class="relative flex items-center" x-data="{ searchOpen: false }" @click.outside="if(!search) searchOpen = false">
                <div class="relative flex items-center">
                    {{-- Magnify button --}}
                    <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                            class="absolute left-0 z-10 w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                            :class="searchOpen ? 'pointer-events-none' : 'border border-gray-200'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    {{-- Expanding input --}}
                    <div class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                         :style="searchOpen ? 'width: min(288px, calc(100vw - 8rem)); opacity: 1' : 'width: 36px; opacity: 0'">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama atau no hp..."
                               @keydown.escape="search = ''; searchOpen = false"
                               class="w-[min(288px,calc(100vw-8rem))] pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                    </div>
                    {{-- Close button --}}
                    <button type="button" x-show="searchOpen" x-transition.opacity.duration.200ms
                            @click="search = ''; searchOpen = false"
                            class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Prodi Chip --}}
            <div class="relative" x-data="{ prodiOpen: false }" @click.outside="prodiOpen = false">
                <button type="button" @click="prodiOpen = !prodiOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="filterProdi !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    Prodi
                    <span x-show="filterProdi !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="prodiOpen" x-transition
                     class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p>
                    </div>
                    <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                        @foreach($prodis as $prodi)
                        <button type="button" @click="filterProdi = filterProdi === '{{ $prodi->id }}' ? '' : '{{ $prodi->id }}'; prodiOpen = false"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 transition-colors text-left"
                                :class="filterProdi === '{{ $prodi->id }}' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                  :class="filterProdi === '{{ $prodi->id }}' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                <svg x-show="filterProdi === '{{ $prodi->id }}'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Active filter tag --}}
            @foreach($prodis as $prodi)
            <span x-show="filterProdi === '{{ $prodi->id }}'" x-transition
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                {{ $prodi->nama }}
                <button type="button" @click="filterProdi = ''" class="ml-0.5 hover:text-gray-900">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </span>
            @endforeach

            {{-- Clear All --}}
            <button x-show="filterProdi !== '' || search !== ''" x-transition type="button" @click="filterProdi = ''; search = ''"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear Filters
            </button>
            </div>
        </div>

        {{-- Upload Button (outside card, flush right – opens import modal) --}}
        <button type="button" @click="$dispatch('open-import-modal')"
                class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Import Pelamar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        </button>
    </div>

    {{-- Daftar Pelamar Global Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed" style="min-width:780px">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="sticky left-0 z-10 bg-[#8b1515] py-3 px-4 text-sm font-bold whitespace-nowrap w-[20%]">Nama Pelamar</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[25%]">Jenjang Pendidikan</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[15%]">No Handphone</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[25%]">Lamaran Diajukan</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                    @forelse($pelamars as $pelamar)
                    <tr class="group hover:bg-gray-50 transition-colors h-[52px]"
                        data-row
                        data-name="{{ strtolower(addslashes($pelamar->nama)) }}"
                        data-phone="{{ strtolower(addslashes($pelamar->no_telepon)) }}"
                        data-prodis="{{ $pelamar->lamarans->pluck('lowongan.prodi_id')->filter()->unique()->implode(',') }}">
                        <td class="sticky left-0 z-10 bg-white group-hover:bg-gray-50 py-3 px-4 max-w-0" title="{{ $pelamar->nama }}">
                            <div class="text-sm font-medium text-gray-800 truncate">{{ $pelamar->nama }}</div>
                        </td>
                        @php
                            $dispJenjang = $pelamar->jenjang_3 ? 'S3' : ($pelamar->jenjang_2 ? 'S2' : ($pelamar->jenjang ?? '-'));
                            $dispInstitusi = $pelamar->institusi_3 ?? $pelamar->institusi_2 ?? $pelamar->institusi ?? '-';
                            $dispProdi = $pelamar->prodi_pendidikan_3 ?? $pelamar->prodi_pendidikan_2 ?? $pelamar->prodi_pendidikan ?? '-';
                        @endphp
                        <td class="py-3 px-4 max-w-0" title="{{ $dispJenjang }} ({{ $dispProdi }})">
                            <div class="text-sm text-gray-800 font-medium truncate">{{ $dispJenjang }} - {{ $dispInstitusi }}</div>
                            <div class="text-[0.6rem] text-gray-400  truncate">{{ $dispProdi }}</div>
                        </td>
                        <td class="py-3 px-4 max-w-0" title="{{ $pelamar->no_telepon ?? '-' }}">
                            <span class="text-sm text-gray-800 font-medium truncate block">{{ $pelamar->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 max-w-0">
                            @if($pelamar->lamarans->count() > 0)
                                <div class="flex flex-col gap-1">
                                    @foreach($pelamar->lamarans->take(2) as $lamaran)
                                        <span class="inline-flex text-sm text-gray-800 font-medium" title="{{ $lamaran->lowongan->nama_posisi ?? '-' }}">
                                            <span class="truncate">{{ $lamaran->lowongan->nama_posisi ?? '-' }}</span>
                                        </span>
                                    @endforeach
                                    @if($pelamar->lamarans->count() > 2)
                                        <span class="text-xs text-gray-400">+{{ $pelamar->lamarans->count() - 2 }} lainnya</span>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-gray-100 text-gray-500 border-gray-200">
                                    Belum Melamar
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pelamar.show', $pelamar) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Detail Pelamar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum ada pelamar</h3>
                                <p class="text-gray-400 text-xs">Belum ada pelamar yang terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Empty state when filter yields no results --}}
        @if($pelamars->count() > 0)
        <div x-show="totalFiltered === 0" class="py-14 text-center" style="display: none;">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Belum ada data pelamar</h3>
            <p class="text-xs text-gray-400">Tidak ada pelamar yang cocok dengan pencarian atau filter.</p>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <span>
                Menampilkan <strong x-text="totalFiltered === 0 ? 0 : paginatedStart + 1"></strong>–<strong x-text="Math.min(paginatedEnd, totalFiltered)"></strong> dari <strong x-text="totalFiltered"></strong> data
            </span>
            <div class="flex items-center gap-1">
                <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                        class="px-3 py-1.5 rounded-lg font-medium transition">Prev</button>
                <template x-for="page in totalPages" :key="page">
                    <button type="button" @click="goToPage(page)"
                            x-show="page >= currentPage - 2 && page <= currentPage + 2"
                            :class="page === currentPage ? 'bg-[#8b1515] text-white font-bold' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                            class="px-3 py-1.5 rounded-lg font-medium transition"
                            x-text="page"></button>
                </template>
                <button type="button" @click="nextPage()" :disabled="currentPage >= totalPages"
                        :class="currentPage >= totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                        class="px-3 py-1.5 rounded-lg font-medium transition">Next</button>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Import Modal (di luar container utama agar backdrop full-screen) --}}
<div x-data="{ 
    showModal: {{ ($errors->has('import') || $errors->has('file')) ? 'true' : 'false' }},
    isLoading: false,
    fileName: '',
    fileSize: '',
    dragOver: false
}"
@open-import-modal.window="showModal = true"
x-show="showModal"
x-transition.opacity.duration.200ms
x-cloak
class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
@click.self="showModal = false"
style="display: none;"
aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            
            {{-- Header --}}
            <div class="relative bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-6 py-5">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 400 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="350" cy="10" r="60" fill="white" opacity="0.1"/>
                        <circle cx="50" cy="70" r="40" fill="white" opacity="0.05"/>
                    </svg>
                </div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        
                        <div>
                            <h2 id="modal-title" class="text-lg font-bold text-white">Import Data Pelamar</h2>
                            
                        </div>
                    </div>
                    <button @click="showModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white/80 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-6">
                {{-- Error Messages --}}
                @if ($errors->has('import') || $errors->has('file'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="flex-1">
                            <p class="text-sm text-red-700 font-medium">
                                @if($errors->has('file'))
                                    {{ $errors->first('file') }}
                                @else
                                    {{ $errors->first('import') }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif


                {{-- Download Template Link --}}
                <p class="mb-4 text-sm text-gray-500">
                    Belum punya template? <a href="{{ asset('templates/pelamar_template.xlsx') }}" download class="text-[#8b1515] font-semibold hover:underline">Download template di sini</a>
                </p>

                <form action="{{ route('admin.pelamar.import') }}" method="POST" enctype="multipart/form-data" @submit="isLoading = true">
                    @csrf
                    
                    {{-- Drag & Drop Upload Area --}}
                    <div class="relative"
                         @dragover.prevent="dragOver = true"
                         @dragleave.prevent="dragOver = false"
                         @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''; fileSize = $event.dataTransfer.files[0] ? (($event.dataTransfer.files[0].size / 1024 / 1024).toFixed(2) + ' MB') : ''">
                        <input type="file" 
                               name="file" 
                               accept=".xlsx,.xls,.csv"
                               @change="fileName = $event.target.files[0]?.name || ''; fileSize = $event.target.files[0] ? (($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB') : ''"
                               x-ref="fileInput"
                               class="hidden"
                               required>
                        
                        <div @click="$refs.fileInput.click()"
                             :class="dragOver ? 'border-[#8b1515] bg-red-50/50 scale-[1.02]' : (fileName ? 'border-[#8b1515]/40 bg-red-50/30' : 'border-gray-200 hover:border-[#8b1515]/60 hover:bg-gray-50')"
                             class="w-full p-8 border-2 border-dashed rounded-xl transition-all duration-200 cursor-pointer group">
                            
                            {{-- Empty state --}}
                            <div x-show="!fileName" class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 group-hover:bg-[#8b1515]/10 rounded-2xl flex items-center justify-center transition-colors">
                                    <svg class="w-7 h-7 text-gray-400 group-hover:text-[#8b1515] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-gray-700">Drag & drop file di sini</p>
                                    <p class="text-xs text-gray-400 mt-1">atau <span class="text-[#8b1515] font-semibold">klik untuk memilih file</span></p>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[0.65rem] font-bold rounded">.xlsx</span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[0.65rem] font-bold rounded">.xls</span>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[0.65rem] font-bold rounded">.csv</span>
                                    <span class="text-[0.65rem] text-gray-400">Max 10MB</span>
                                </div>
                            </div>

                            {{-- File selected state --}}
                            <div x-show="fileName" class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="fileName"></p>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="fileSize"></p>
                                </div>
                                <button type="button" @click.stop="fileName = ''; fileSize = ''; $refs.fileInput.value = ''" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>



                    {{-- Action Buttons --}}
                    <div class="flex justify-center mt-6 pt-5 border-t border-gray-100">
                        <button type="submit" 
                                :disabled="!fileName || isLoading"
                                :class="(!fileName || isLoading) ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-[#8b1515] hover:bg-[#6e1010] text-white shadow-lg shadow-red-500/20 hover:shadow-red-500/30'"
                                class="px-8 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2">
                            <template x-if="!isLoading">
                                <span class="flex items-center gap-2">
                                    Import Data
                                </span>
                            </template>
                            <template x-if="isLoading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mengimpor...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>

@endsection
