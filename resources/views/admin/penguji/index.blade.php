@extends('layouts.admin')

@section('title', 'Manajemen Penguji')

@section('content')

    {{-- Main Container --}}
    <div x-data="{
            openAddModal: false, searchDosen: '', filterProdi: '',
            calonList: @js($calonPengujis->map(fn($c) => ['id' => $c->id, 'nama' => $c->nama, 'nama_lower' => strtolower($c->nama), 'prodi_id' => (string)$c->prodi_id])->values()),
            get visibleCalonCount() {
                return this.calonList.filter(c =>
                    (this.searchDosen === '' || c.nama_lower.includes(this.searchDosen.toLowerCase())) &&
                    (this.filterProdi === '' || this.filterProdi == c.prodi_id)
                ).length;
            },
            searchMain: '{{ request('search') }}',
            filterProdiMain: '{{ request('prodi_id') }}',
            filterStatus: '',
            currentPage: 1,
            perPage: 10,
            get filteredRows() {
                return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                    const name = row.dataset.name || '';
                    const kode = row.dataset.kode || '';
                    const prodi = row.dataset.prodi || '';
                    const status = row.dataset.status || '';
                    const matchSearch = this.searchMain === '' || name.includes(this.searchMain.toLowerCase()) || kode.includes(this.searchMain.toLowerCase());
                    const matchProdi = this.filterProdiMain === '' || prodi === this.filterProdiMain;
                    const matchStatus = this.filterStatus === '' || status === this.filterStatus;
                    return matchSearch && matchProdi && matchStatus;
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
            $watch('searchMain', () => resetPage());
            $watch('filterProdiMain', () => resetPage());
            $watch('filterStatus', () => resetPage());
         ">

        {{-- Inner layout container --}}
        <div class="max-w-6xl mx-auto space-y-6">

        {{-- Filter Chips Bar (with attached + button) --}}
        <div class="relative">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20">
                <div class="flex items-center gap-3 flex-wrap">

                {{-- Search (animated) --}}
                <div class="relative flex items-center" x-data="{ searchOpen: false }" @click.outside="if(!searchMain) searchOpen = false">
                    <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                            class="absolute left-0 z-10 w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                            :class="searchOpen ? 'pointer-events-none' : 'border border-gray-200'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <div class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                         :style="searchOpen ? 'width: min(288px, calc(100vw - 8rem)); opacity: 1' : 'width: 36px; opacity: 0'">
                        <input type="text" x-model="searchMain" x-ref="searchInput" placeholder="Cari penguji..."
                               @keydown.escape="searchMain = ''; searchOpen = false"
                               class="w-[min(288px,calc(100vw-8rem))] pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                    </div>
                    <button type="button" x-show="searchOpen" x-transition.opacity.duration.200ms
                            @click="searchMain = ''; searchOpen = false"
                            class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Prodi Chip --}}
                <div class="relative" x-data="{ prodiOpen: false }" @click.outside="prodiOpen = false">
                    <button type="button" @click="prodiOpen = !prodiOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="filterProdiMain !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        Prodi
                        <span x-show="filterProdiMain !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="prodiOpen" x-transition class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p></div>
                        <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                            @foreach($prodis as $prodi)
                            <button type="button" @click="filterProdiMain = filterProdiMain === '{{ $prodi->id }}' ? '' : '{{ $prodi->id }}'; prodiOpen = false"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left" :class="filterProdiMain === '{{ $prodi->id }}' ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="filterProdiMain === '{{ $prodi->id }}' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                    <svg x-show="filterProdiMain === '{{ $prodi->id }}'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Status Chip --}}
                <div class="relative" x-data="{ statusOpen: false }" @click.outside="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="filterStatus !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Status
                        <span x-show="filterStatus !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p></div>
                        <div class="p-3 space-y-1">
                            <button type="button" @click="filterStatus = filterStatus === 'penguji' ? '' : 'penguji'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="filterStatus === 'penguji' ? 'bg-gray-100' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="filterStatus === 'penguji' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="filterStatus === 'penguji'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-700">Penguji saja</span>
                            </button>
                            <button type="button" @click="filterStatus = filterStatus === 'rangkap' ? '' : 'rangkap'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="filterStatus === 'rangkap' ? 'bg-gray-100' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="filterStatus === 'rangkap' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="filterStatus === 'rangkap'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span class="text-sm font-medium text-gray-700">Penguji & Kaprodi</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Active tags --}}
                @foreach($prodis as $prodi)
                <span x-show="filterProdiMain === '{{ $prodi->id }}'" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                    {{ $prodi->nama }}
                    <button type="button" @click="filterProdiMain = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </span>
                @endforeach
                <template x-if="filterStatus === 'penguji'"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">Penguji saja <button type="button" @click="filterStatus = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></span></template>
                <template x-if="filterStatus === 'rangkap'"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">Penguji & Kaprodi <button type="button" @click="filterStatus = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></span></template>

                {{-- Clear All --}}
                <button x-show="filterProdiMain !== '' || filterStatus !== '' || searchMain !== ''" x-transition type="button" @click="filterProdiMain = ''; filterStatus = ''; searchMain = ''" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Clear Filters
                </button>
                </div>
            </div>

            {{-- + Button (outside card, flush right, visually attached) --}}
            <button type="button" @click="openAddModal = true"
                    class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Tunjuk Penguji">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed" style="min-width:750px">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Prodi</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">NIP/NIDN</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Email</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                        @forelse($pengujis as $penguji)
                        <tr class="hover:bg-gray-50 transition-colors h-[52px]"
                            data-row
                            data-name="{{ strtolower(addslashes($penguji->nama)) }}"
                            data-kode="{{ strtolower(addslashes($penguji->kode)) }}"
                            data-prodi="{{ $penguji->prodi_id }}"
                            data-status="{{ $penguji->is_kaprodi ? 'rangkap' : 'penguji' }}">
                            <td class="py-3 px-5 max-w-0" title="{{ $penguji->nama }} ({{ $penguji->kode }})">
                                <div class="text-sm text-gray-800 font-medium truncate">{{ $penguji->nama }}</div>
                                <div class="text-xs font-medium text-gray-500 truncate">{{ $penguji->kode }}</div>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate max-w-0" title="{{ $penguji->prodi?->nama ?? '-' }}">{{ $penguji->prodi?->nama ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate max-w-0" title="{{ $penguji->nip ?? '-' }} / {{ $penguji->nidn ?? '-' }}">{{ $penguji->nip ?? '-' }} / {{ $penguji->nidn ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate max-w-0" title="{{ $pengujiEmails[$penguji->id] ?? '-' }}">{{ $pengujiEmails[$penguji->id] ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center gap-1.5 flex-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800 whitespace-nowrap">Penguji</span>
                                    @if($penguji->is_kaprodi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800 whitespace-nowrap">Kaprodi</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.penguji.show', $penguji) }}"
                                       class="text-gray-400 hover:text-amber-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Edit Penguji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 px-5 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                    </div>
                                    <h3 class="text-gray-800 font-medium text-sm">Belum ada penguji</h3>
                                    <p class="text-gray-400 text-xs mt-1">Gunakan tombol "Tunjuk Penguji" untuk mulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
        </div>

        </div>{{-- /inner layout --}}

        {{-- -- Tunjuk Penguji Modal -- --}}
        <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9990] bg-black/40 backdrop-blur-sm" @click="openAddModal = false" style="display: none;"></div>

        <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none" style="display: none;">
            <div class="bg-white rounded-2xl w-full max-w-3xl overflow-hidden flex flex-col pointer-events-auto shadow-2xl" style="height: 600px; max-height: 85vh;">
                <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="background: #8b1515;">
                    <div>
                        <h2 class="text-base font-semibold text-white">Tunjuk Penguji</h2>
                        
                    </div>
                    <button type="button" @click="openAddModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all" style="border: 1.5px solid rgba(255,255,255,0.3);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-5 pt-4 pb-3 border-b border-gray-100 bg-gray-50/50 flex-shrink-0">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" x-model="searchDosen" placeholder="Cari nama dosen..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]/20 transition shadow-sm">
                        </div>
                        <select x-model="filterProdi"
                            class="hidden"
                            id="filterProdiSelect">
                            <option value="">Semua Prodi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                        <div x-data="{
                                open: false,
                                opts: [{ v: '', l: 'Semua Prodi' }, @foreach($prodis as $prodi){ v: '{{ $prodi->id }}', l: '{{ addslashes($prodi->nama) }}' },@endforeach],
                                get label() { return this.opts.find(o => o.v == filterProdi)?.l ?? 'Semua Prodi'; }
                             }" @click.outside="open = false" class="relative min-w-[150px]">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between pl-3 pr-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 shadow-sm transition hover:border-gray-300">
                                <span x-text="label" class="truncate"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="filterProdi = opt.v; open = false"
                                            class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors"
                                            :class="filterProdi == opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <form method="POST" action="{{ route('admin.penguji.store') }}" class="flex-1 overflow-hidden flex flex-col min-h-0">
                    @csrf
                    <div class="flex-1 overflow-y-auto">
                        @if($calonPengujis->isEmpty())
                            <div class="h-full flex flex-col items-center justify-center py-10 text-center">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                <h3 class="text-sm font-medium text-gray-600 mb-1">Belum ada data dosen</h3>
                                <p class="text-xs text-gray-400">Tambahkan dosen terlebih dahulu melalui halaman Prodi.</p>
                            </div>
                        @else
                            {{-- Empty state when filter/search matches nothing --}}
                            <div x-show="visibleCalonCount === 0" class="h-full flex flex-col items-center justify-center py-10 text-center" style="display: none;">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <h3 class="text-sm font-medium text-gray-600 mb-1">Belum ada data dosen</h3>
                                <p class="text-xs text-gray-400">Tidak ada dosen yang cocok dengan pencarian atau filter.</p>
                            </div>

                            <table class="w-full text-left border-collapse" x-show="visibleCalonCount > 0">
                                <thead class="sticky top-0 z-10 bg-white border-b border-gray-100 shadow-sm">
                                    <tr>
                                        <th class="py-3 px-5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Dosen</th>
                                        <th class="py-3 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Prodi</th>
                                        <th class="py-3 px-5 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($calonPengujis as $calon)
                                        <tr class="hover:bg-[#8b1515]/[0.03] transition-colors group"
                                            x-show="(searchDosen === '' || '{{ strtolower($calon->nama) }}'.includes(searchDosen.toLowerCase())) && (filterProdi === '' || filterProdi === '{{ $calon->prodi_id }}')">
                                            <td class="py-3.5 px-5">
                                                <div class="text-sm font-medium text-gray-800 group-hover:text-[#8b1515] transition-colors">{{ $calon->nama }}</div>
                                                <div class="text-xs text-gray-400 font-medium mt-0.5">{{ $calon->kode }}</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-500">{{ $calon->prodi?->nama ?? '-' }}</span>
                                            </td>
                                            <td class="py-3.5 px-5 text-center">
                                                <input type="checkbox" name="dosen_ids[]" value="{{ $calon->id }}" class="w-4 h-4 rounded border-gray-300 cursor-pointer focus:ring-2 focus:ring-[#8b1515]/20" style="accent-color: #8b1515;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-gray-100 flex-shrink-0 flex items-center justify-center gap-3">
                        <button type="submit" class="px-10 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:opacity-90 active:scale-95 shadow-md" style="background: #8b1515;">Tunjuk</button>
                    </div>
                </form>
            </div>
        </div>

    </div>{{-- /x-data --}}

@endsection
