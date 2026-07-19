@extends('layouts.admin')

@section('title', 'Daftar Pengujian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div x-data="{
            search: '',
            seleksiFilter: '',
            statusFilter: '',
            tanggalFilter: '',
            seleksiOpen: false,
            statusOpen: false,
            tanggalOpen: false,
            currentPage: 1,
            perPage: 10,
            get filteredRows() {
                return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                    var matchSeleksi = this.seleksiFilter === '' || row.dataset.tipe === this.seleksiFilter;
                    var matchStatus = this.statusFilter === '' || row.dataset.status === this.statusFilter;
                    var matchTanggal = this.tanggalFilter === '' || row.dataset.tanggal === this.tanggalFilter;
                    var matchSearch = this.search === '' || (row.dataset.nama || '').includes(this.search.toLowerCase());
                    return matchSeleksi && matchStatus && matchTanggal && matchSearch;
                });
            },
            get totalFiltered() { return this.filteredRows.length; },
            get totalPages() { return Math.max(1, Math.ceil(this.totalFiltered / this.perPage)); },
            get paginatedStart() { return (this.currentPage - 1) * this.perPage; },
            get paginatedEnd() { return this.currentPage * this.perPage; },
            get hasFilters() { return this.seleksiFilter !== '' || this.statusFilter !== '' || this.tanggalFilter !== '' || this.search !== ''; },
            updateVisibility() {
                var rows = Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]'));
                var filtered = this.filteredRows;
                rows.forEach(function(row) {
                    var idx = filtered.indexOf(row);
                    row.style.display = (idx === -1 || idx < this.paginatedStart || idx >= this.paginatedEnd) ? 'none' : '';
                }.bind(this));
            },
            clearAll() { this.search = ''; this.seleksiFilter = ''; this.statusFilter = ''; this.tanggalFilter = ''; },
            resetPage() { this.currentPage = 1; this.updateVisibility(); },
            prevPage() { if (this.currentPage > 1) { this.currentPage--; this.updateVisibility(); } },
            nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.updateVisibility(); } },
            goToPage(p) { this.currentPage = p; this.updateVisibility(); }
         }"
         x-init="$nextTick(() => updateVisibility());
                  $watch('search', () => resetPage());
                  $watch('seleksiFilter', () => resetPage());
                  $watch('statusFilter', () => resetPage());
                  $watch('tanggalFilter', () => resetPage());">

        {{-- Filter Chips Bar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 mb-6">
            <div class="flex items-center gap-3 flex-wrap">

            {{-- Search (animated) --}}
            <div class="relative flex items-center" x-data="{ searchOpen: false }" @click.outside="if(!search) searchOpen = false">
                <div class="relative flex items-center">
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
            </div>

            {{-- Tanggal – input date langsung --}}
            <div class="flex items-center">
                <input type="date" x-model="tanggalFilter"
                       class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]/20 transition cursor-pointer"
                       :class="tanggalFilter !== '' ? 'border-[#8b1515] text-[#8b1515] font-medium' : ''">
            </div>

            {{-- Seleksi Chip --}}
            <div class="relative" @click.outside="seleksiOpen = false">
                <button type="button" @click="seleksiOpen = !seleksiOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="seleksiFilter !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    Seleksi
                    <span x-show="seleksiFilter !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform" :class="seleksiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="seleksiOpen" x-transition class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Seleksi</p></div>
                    <div class="p-3 space-y-1">
                        <button type="button" @click="seleksiFilter = seleksiFilter === 'wawancara' ? '' : 'wawancara'; seleksiOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="seleksiFilter === 'wawancara' ? 'bg-gray-50' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="seleksiFilter === 'wawancara' ? 'border-[#8b1515] bg-[#8b1515]' : 'border-gray-300'"><svg x-show="seleksiFilter === 'wawancara'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Wawancara</span>
                        </button>
                        <button type="button" @click="seleksiFilter = seleksiFilter === 'micro' ? '' : 'micro'; seleksiOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 text-left" :class="seleksiFilter === 'micro' ? 'bg-gray-50' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="seleksiFilter === 'micro' ? 'border-[#8b1515] bg-[#8b1515]' : 'border-gray-300'"><svg x-show="seleksiFilter === 'micro'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Micro Teaching</span>
                        </button>
                    </div>
                </div>
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
                <div x-show="statusOpen" x-transition class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p></div>
                    <div class="p-3 space-y-1">
                        <button type="button" @click="statusFilter = statusFilter === 'dinilai' ? '' : 'dinilai'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="statusFilter === 'dinilai' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'dinilai' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'dinilai'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Dinilai</span>
                        </button>
                        <button type="button" @click="statusFilter = statusFilter === 'belum_dinilai' ? '' : 'belum_dinilai'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="statusFilter === 'belum_dinilai' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'belum_dinilai' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'belum_dinilai'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Belum dinilai</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Active filter tags --}}
            <span x-show="tanggalFilter !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                <span x-text="tanggalFilter"></span>
                <button type="button" @click="tanggalFilter = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </span>
            <span x-show="seleksiFilter !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                <span x-text="seleksiFilter === 'wawancara' ? 'Wawancara' : 'Micro Teaching'"></span>
                <button type="button" @click="seleksiFilter = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </span>
            <span x-show="statusFilter !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                <span x-text="statusFilter === 'dinilai' ? 'Dinilai' : 'Belum dinilai'"></span>
                <button type="button" @click="statusFilter = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </span>

            {{-- Clear All --}}
            <button x-show="hasFilters" x-transition type="button" @click="clearAll()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear Filters
            </button>

            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed" style="min-width:750px">
                    <thead class="bg-[#8b1515] text-white">
                        <tr>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[14%]">Tanggal</th>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[15%]">Waktu</th>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[14%]">Seleksi</th>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[18%]">Pelamar</th>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap w-[18%]">Lowongan</th>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-center w-[12%]">Status</th>
                            <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-center w-[9%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                        @forelse($jadwals as $jadwal)
                            @php
                                $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
                                $sudahDinilai = $jadwal->penilaian !== null;
                            @endphp
                            <tr data-row
                                data-tipe="{{ $jadwal->tipe_seleksi == 'wawancara' ? 'wawancara' : 'micro' }}"
                                data-status="{{ $sudahDinilai ? 'dinilai' : 'belum_dinilai' }}"
                                data-tanggal="{{ $jadwal->tanggal->format('Y-m-d') }}"
                                data-nama="{{ strtolower($jadwal->pelamar->nama) }}"
                                class="hover:bg-gray-50/50 transition-colors h-[52px]">
                                <td class="py-3 px-4 max-w-0" title="{{ $jadwal->tanggal->format('d/m/Y') }}">
                                    <span class="text-sm font-medium text-gray-800 truncate block">{{ $jadwal->tanggal->format('d/m/Y') }}</span>
                                </td>
                                <td class="py-3 px-4 max-w-0" title="Sesi {{ $jadwal->sesi }} ({{ $sesiInfo ? $sesiInfo['start'] . ' - ' . $sesiInfo['end'] : '-' }})">
                                    <div class="text-sm font-medium text-gray-800 truncate">Sesi {{ $jadwal->sesi }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $sesiInfo ? $sesiInfo['start'] . ' - ' . $sesiInfo['end'] : '-' }}</div>
                                </td>
                                <td class="py-3 px-4 max-w-0" title="{{ $jadwal->tipe_seleksi == 'wawancara' ? 'Wawancara' : 'Micro Teaching' }}">
                                    <span class="text-sm font-medium text-gray-700 truncate block">{{ $jadwal->tipe_seleksi == 'wawancara' ? 'Wawancara' : 'Micro Teaching' }}</span>
                                </td>
                                <td class="py-3 px-4 max-w-0" title="{{ $jadwal->pelamar->nama }}">
                                    <span class="text-sm font-medium text-gray-800 truncate block">{{ $jadwal->pelamar->nama }}</span>
                                </td>
                                <td class="py-3 px-4 max-w-0" title="{{ $jadwal->lowongan->nama_posisi }} ({{ $jadwal->lowongan->prodi->nama ?? '-' }})">
                                    <div class="text-sm font-medium text-gray-800 truncate">{{ $jadwal->lowongan->nama_posisi }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase tracking-widest mt-0.5 truncate">{{ $jadwal->lowongan->prodi->nama ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($sudahDinilai)
                                        <span class="px-2.5 py-1 rounded-lg text-[0.7rem] font-bold bg-green-800 text-white inline-block">Dinilai</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[0.7rem] font-bold bg-red-800 text-white inline-block">Belum dinilai</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('penguji.pengujian.show', $jadwal->id) }}" class="p-1.5 text-gray-400 hover:text-[#8b1515] transition-colors" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 px-6 text-center">
                                    <p class="text-gray-400 text-sm font-medium mb-5">Belum Ada Pengujian.</p>
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
    </div>
</div>
@endsection
