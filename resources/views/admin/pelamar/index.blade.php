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
     x-init="$nextTick(() => updateVisibility())"
     x-effect="search; filterProdi; resetPage()">

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            {{-- Left: Filter Prodi --}}
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <select x-model="filterProdi" 
                            class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="">Semua Prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Right: Search --}}
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari nama pelamar atau no hp..." 
                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
            </div>
        </div>
    </div>

    {{-- Daftar Pelamar Global Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Nama Pelamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Jenjang Pendidikan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">No Handphone</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[28%]">Lamaran Diajukan</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                    @forelse($pelamars as $pelamar)
                    <tr class="hover:bg-gray-50 transition-colors h-[52px]"
                        data-row
                        data-name="{{ strtolower(addslashes($pelamar->nama)) }}"
                        data-phone="{{ strtolower(addslashes($pelamar->no_telepon)) }}"
                        data-prodis="{{ $pelamar->lamarans->pluck('lowongan.prodi_id')->filter()->unique()->implode(',') }}">
                        <td class="py-4 px-5">
                            <div class="text-sm font-semibold text-gray-800 truncate">{{ $pelamar->nama }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-700 font-medium truncate">{{ $pelamar->jenjang ?? '-' }}</div>
                            <div class="text-[0.7rem] text-gray-400 uppercase tracking-widest mt-0.5 truncate">{{ $pelamar->prodi_pendidikan ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <span class="text-sm text-gray-600 font-mono">{{ $pelamar->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-5">
                            @if($pelamar->lamarans->count() > 0)
                                <div class="flex flex-col gap-1">
                                    @foreach($pelamar->lamarans->take(2) as $lamaran)
                                        <span class="inline-flex items-center gap-1 text-xs text-[#8b1515] font-semibold">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pelamar.show', $pelamar) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Detail & Edit Pelamar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Pelamar Terdaftar</h3>
                                <p class="text-gray-400 text-xs">Semua pelamar yang telah melakukan registrasi akan muncul di sini.</p>
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

</div>

@endsection
