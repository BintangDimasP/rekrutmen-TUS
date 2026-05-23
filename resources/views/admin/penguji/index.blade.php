@extends('layouts.admin')

@section('title', 'Manajemen Penguji')

@section('content')

    {{-- Main Container --}}
    <div x-data="{
            openAddModal: false, searchDosen: '', filterProdi: '',
            searchMain: '{{ request('search') }}',
            filterProdiMain: '{{ request('prodi_id') }}',
            currentPage: 1,
            perPage: 10,
            get filteredRows() {
                return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                    const name = row.dataset.name || '';
                    const kode = row.dataset.kode || '';
                    const prodi = row.dataset.prodi || '';
                    const matchSearch = this.searchMain === '' || name.includes(this.searchMain.toLowerCase()) || kode.includes(this.searchMain.toLowerCase());
                    const matchProdi = this.filterProdiMain === '' || prodi === this.filterProdiMain;
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
            $watch('searchMain', () => resetPage());
            $watch('filterProdiMain', () => resetPage());
         ">

        {{-- Inner layout container --}}
        <div class="max-w-6xl mx-auto space-y-6">

        {{-- Filter & Action --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full lg:w-auto">
                {{-- Left: Filter Prodi --}}
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-48">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <select x-model="filterProdiMain" 
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
                    <input type="text" x-model="searchMain" placeholder="Cari nama penguji..." 
                           class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
            </div>
            
            <button type="button" @click="openAddModal = true" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors shrink-0 w-full lg:w-auto cursor-pointer">
                
                Tunjuk Penguji
            </button>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
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
                            data-prodi="{{ $penguji->prodi_id }}">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium truncate">
                                {{ $penguji->nama }}
                                <div class="text-xs font-medium text-gray-500">{{ $penguji->kode }}</div>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate">{{ $penguji->prodi?->nama ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 truncate">{{ $penguji->nip ?? '-' }} / {{ $penguji->nidn ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate">{{ $pengujiEmails[$penguji->id] ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800">Penguji</span>
                                    @if($penguji->is_kaprodi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800">Kaprodi</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.penguji.show', $penguji) }}"
                                       class="text-gray-400 hover:text-blue-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Detail Penguji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
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

        {{-- ── Tunjuk Penguji Modal ── --}}
        <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9990] bg-black/40 backdrop-blur-sm" @click="openAddModal = false" style="display: none;"></div>

        <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none" style="display: none;">
            <div class="bg-white rounded-2xl w-full max-w-3xl overflow-hidden flex flex-col pointer-events-auto shadow-2xl" style="max-height: 85vh;">
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
                        <select x-model="filterProdi" class="pl-3 pr-8 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]/20 transition min-w-[150px] shadow-sm">
                            <option value="">Semua Prodi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-gray-400 mt-2.5 leading-relaxed">Akun login dibuat otomatis &mdash; kata sandi bawaan <code class="bg-white border border-gray-200 px-1.5 py-0.5 rounded text-gray-500 font-medium">penguji123</code>.</p>
                </div>
                <form method="POST" action="{{ route('admin.penguji.store') }}" class="flex-1 overflow-hidden flex flex-col min-h-0">
                    @csrf
                    <div class="flex-1 overflow-y-auto">
                        @if($calonPengujis->isEmpty())
                            <div class="py-16 text-center">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                <p class="text-sm text-gray-400">Seluruh dosen telah menjadi penguji.</p>
                            </div>
                        @else
                            <table class="w-full text-left border-collapse">
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
                                                <div class="text-xs text-gray-400 font-medium mt-0.5">{{ $calon->kode }} &middot; {{ $calon->email }}</div>
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
                        <button type="submit" class="px-10 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:opacity-90 active:scale-95 shadow-md" style="background: #8b1515;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>{{-- /x-data --}}

@endsection
