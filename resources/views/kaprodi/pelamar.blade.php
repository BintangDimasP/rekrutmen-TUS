@extends('layouts.admin')

@section('title', 'Daftar Pelamar — ' . auth()->user()->prodi?->nama)

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="kaprodiPelamar()" x-init="init()">

    {{-- Filter Chips Bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
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
                         :style="searchOpen ? 'width: 288px; opacity: 1' : 'width: 36px; opacity: 0'">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama atau no hp..."
                               @keydown.escape="search = ''; searchOpen = false"
                               class="w-[288px] pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                    </div>
                    <button type="button" x-show="searchOpen" x-transition.opacity.duration.200ms
                            @click="search = ''; searchOpen = false"
                            class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Lowongan Chip --}}
            <div class="relative" @click.outside="lowonganOpen = false">
                <button type="button" @click="lowonganOpen = !lowonganOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="lowongan_id !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Lowongan
                    <span x-show="lowongan_id !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform" :class="lowonganOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="lowonganOpen" x-transition class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Lowongan</p></div>
                    <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                        <template x-for="low in lowongans" :key="low.id">
                            <button type="button" @click="lowongan_id = (lowongan_id === String(low.id)) ? '' : String(low.id); lowonganOpen = false"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left"
                                    :class="String(lowongan_id) === String(low.id) ? 'bg-gray-50' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors"
                                      :class="String(lowongan_id) === String(low.id) ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                    <svg x-show="String(lowongan_id) === String(low.id)" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-sm font-medium text-gray-700" x-text="low.nama_posisi"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Status Chip --}}
            <div class="relative" @click.outside="statusOpen = false">
                <button type="button" @click="statusOpen = !statusOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="status !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Status
                    <span x-show="status !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="statusOpen" x-transition class="absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p></div>
                    <div class="p-3 space-y-1">
                        <button type="button" @click="status = status === 'menunggu' ? '' : 'menunggu'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="status === 'menunggu' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="status === 'menunggu' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="status === 'menunggu'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Menunggu</span>
                        </button>
                        <button type="button" @click="status = status === 'seleksi_tahap1' ? '' : 'seleksi_tahap1'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="status === 'seleksi_tahap1' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="status === 'seleksi_tahap1' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="status === 'seleksi_tahap1'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Seleksi Tahap 1</span>
                        </button>
                        <button type="button" @click="status = status === 'seleksi_tahap2' ? '' : 'seleksi_tahap2'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="status === 'seleksi_tahap2' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="status === 'seleksi_tahap2' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="status === 'seleksi_tahap2'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Seleksi Tahap 2</span>
                        </button>
                        <button type="button" @click="status = status === 'diterima' ? '' : 'diterima'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="status === 'diterima' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="status === 'diterima' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="status === 'diterima'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Diterima</span>
                        </button>
                        <button type="button" @click="status = status === 'ditolak' ? '' : 'ditolak'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="status === 'ditolak' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="status === 'ditolak' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="status === 'ditolak'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Ditolak</span>
                        </button>
                        <button type="button" @click="status = status === 'mengundurkan_diri' ? '' : 'mengundurkan_diri'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="status === 'mengundurkan_diri' ? 'bg-gray-100' : ''">
                            <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="status === 'mengundurkan_diri' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="status === 'mengundurkan_diri'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            <span class="text-sm font-medium text-gray-700">Mengundurkan Diri</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Active filter tags --}}
            <span x-show="lowongan_id !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                <span x-text="selectedLowonganName"></span>
                <button type="button" @click="lowongan_id = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </span>
            <span x-show="status !== ''" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                <span x-text="selectedStatusLabel"></span>
                <button type="button" @click="status = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </span>

            {{-- Clear All --}}
            <button x-show="hasFilters" x-transition type="button" @click="clearAll()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear Filters
            </button>

        </div>
    </div>

    {{-- Tabel Pelamar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Nama Pelamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Jenjang Pendidikan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">No Handphone</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[22%]">Lowongan Dilamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[14%]">Status</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[12%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="paginatedRows.length === 0">
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Pelamar</h3>
                                    <p class="text-gray-400 text-xs">Belum ada pelamar yang mendaftar ke lowongan di prodi Anda.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="lamaran in paginatedRows" :key="lamaran.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5 max-w-0" :title="lamaran.nama">
                                <div class="text-sm font-medium text-gray-800 truncate" x-text="lamaran.nama"></div>
                                <div class="text-xs text-gray-400 font-medium mt-0.5 truncate" x-text="lamaran.email"></div>
                            </td>
                            <td class="py-3 px-5 max-w-0" :title="(lamaran.jenjang || '-') + ' (' + (lamaran.prodi_pendidikan || '-') + ')'">
                                <div class="text-sm text-gray-600 font-medium truncate" x-text="lamaran.jenjang || '-'"></div>
                                <div class="text-[0.7rem] text-gray-400 uppercase tracking-widest mt-0.5 truncate" x-text="lamaran.prodi_pendidikan || '-'"></div>
                            </td>
                            <td class="py-3 px-5 max-w-0" :title="lamaran.no_telepon || '-'">
                                <span class="text-sm text-gray-600 font-medium block truncate" x-text="lamaran.no_telepon || '-'"></span>
                            </td>
                            <td class="py-3 px-5 max-w-0">
                                <span class="inline-flex items-center gap-1 text-xs text-[#8b1515] font-semibold" :title="lamaran.lowongan_nama">
                            
                                    <span class="truncate" x-text="lamaran.lowongan_nama"></span>
                                </span>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <template x-if="lamaran.status === 'menunggu'"><span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-gray-100 text-gray-600 border-gray-200">Menunggu</span></template>
                                <template x-if="lamaran.status === 'seleksi_tahap1'"><span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-blue-50 text-blue-700 border-blue-200">Seleksi Tahap 1</span></template>
                                <template x-if="lamaran.status === 'seleksi_tahap2'"><span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-indigo-50 text-indigo-700 border-indigo-200">Seleksi Tahap 2</span></template>
                                <template x-if="lamaran.status === 'diterima'"><span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-green-50 text-green-700 border-green-200">Diterima</span></template>
                                <template x-if="lamaran.status === 'ditolak'"><span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-red-50 text-red-700 border-red-200">Ditolak</span></template>
                                <template x-if="lamaran.status === 'mengundurkan_diri'"><span class="inline-flex px-2.5 py-1 rounded-md text-[0.75rem] font-bold border bg-slate-50 text-slate-700 border-slate-200">Mengundurkan Diri</span></template>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <a :href="'/kaprodi/pelamar/' + lamaran.pelamar_id + '?lamaran_id=' + lamaran.id" class="inline-flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    </template>
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

@push('scripts')
<script>
    window._kaprodiLowongans = @json($lowongans);
    window._kaprodiInitial = {
        search: @json(request('search', '')),
        lowongan_id: @json(request('lowongan_id', '')),
        status: @json(request('status', ''))
    };
</script>
<script>
    function kaprodiPelamar() {
        return {
            search: window._kaprodiInitial.search,
            lowongan_id: window._kaprodiInitial.lowongan_id,
            status: window._kaprodiInitial.status,
            currentPage: 1,
            perPage: 10,
            lamarans: [],
            searchTimeout: null,
            lowongans: window._kaprodiLowongans,
            statusOpen: false,
            lowonganOpen: false,

            init() {
                var self = this;
                this.fetchPelamar();
                this.$watch('search', function() { self.debouncedSearch(); });
                this.$watch('lowongan_id', function() { self.resetPage(); self.fetchPelamar(); });
                this.$watch('status', function() { self.resetPage(); self.fetchPelamar(); });
            },

            async fetchPelamar() {
                try {
                    var url = '/kaprodi/pelamar/filter?search=' + encodeURIComponent(this.search) + '&lowongan_id=' + encodeURIComponent(this.lowongan_id) + '&status=' + encodeURIComponent(this.status);
                    var response = await fetch(url);
                    var data = await response.json();
                    this.lamarans = data.lamarans;
                    this.currentPage = 1;
                } catch (error) {
                    console.error('Error fetching pelamar:', error);
                }
            },

            get filteredRows() { return this.lamarans; },
            get totalFiltered() { return this.filteredRows.length; },
            get totalPages() { return Math.max(1, Math.ceil(this.totalFiltered / this.perPage)); },
            get paginatedStart() { return (this.currentPage - 1) * this.perPage; },
            get paginatedEnd() { return this.currentPage * this.perPage; },
            get paginatedRows() { return this.filteredRows.slice(this.paginatedStart, this.paginatedEnd); },
            get hasFilters() { return this.lowongan_id !== '' || this.status !== '' || this.search !== ''; },

            get selectedLowonganName() {
                if (!this.lowongan_id) return '';
                var id = String(this.lowongan_id);
                for (var i = 0; i < this.lowongans.length; i++) {
                    if (String(this.lowongans[i].id) === id) return this.lowongans[i].nama_posisi;
                }
                return '';
            },

            get selectedStatusLabel() {
                var labels = { menunggu: 'Menunggu', seleksi_tahap1: 'Seleksi Tahap 1', seleksi_tahap2: 'Seleksi Tahap 2', diterima: 'Diterima', ditolak: 'Ditolak', mengundurkan_diri: 'Mengundurkan Diri' };
                return labels[this.status] || '';
            },

            clearAll() { this.search = ''; this.lowongan_id = ''; this.status = ''; },
            resetPage() { this.currentPage = 1; },
            prevPage() { if (this.currentPage > 1) { this.currentPage--; } },
            nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; } },
            goToPage(p) { this.currentPage = p; },

            debouncedSearch() {
                clearTimeout(this.searchTimeout);
                var self = this;
                this.searchTimeout = setTimeout(function() {
                    self.resetPage();
                    self.fetchPelamar();
                }, 300);
            }
        };
    }
</script>
@endpush
@endsection
