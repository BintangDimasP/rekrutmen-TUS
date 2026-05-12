@extends('layouts.admin')

@section('title', 'Daftar Pengujian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div x-data="{
            tab: 'all',
            currentPage: 1,
            perPage: 10,
            get filteredRows() {
                return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                    return this.tab === 'all' || row.dataset.tipe === this.tab;
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
         x-effect="tab; resetPage()">
        <!-- Filter Dropdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <select x-model="tab" class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="all">Semua Tipe Seleksi</option>
                        <option value="wawancara">Wawancara</option>
                        <option value="micro">Micro Teaching</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead class="bg-[#8b1515] text-white">
                        <tr>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Tanggal</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Waktu</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Seleksi</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[12%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                        @forelse($jadwals as $jadwal)
                            @php
                                $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
                                $sudahDinilai = $jadwal->penilaian !== null;
                            @endphp
                            <tr data-row data-tipe="{{ $jadwal->tipe_seleksi == 'wawancara' ? 'wawancara' : 'micro' }}"
                                class="hover:bg-gray-50/50 transition-colors h-[52px]">
                                
                                {{-- Tanggal --}}
                                <td class="py-3 px-5 text-sm font-semibold text-gray-800">{{ $jadwal->tanggal->format('d/m/Y') }}</td>
                                
                                {{-- Waktu --}}
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">Sesi {{ $jadwal->sesi }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $sesiInfo ? $sesiInfo['start'] . ' - ' . $sesiInfo['end'] : '-' }}</div>
                                </td>

                                {{-- Seleksi --}}
                                <td class="py-3 px-5 text-sm font-semibold text-gray-700">
                                    {{ $jadwal->tipe_seleksi == 'wawancara' ? 'Wawancara' : 'Micro Teaching' }}
                                </td>

                                {{-- Pelamar --}}
                                <td class="py-3 px-5 text-sm font-semibold text-gray-800">{{ $jadwal->pelamar->nama }}</td>

                                {{-- Lowongan --}}
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $jadwal->lowongan->nama_posisi }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase tracking-widest mt-0.5">{{ $jadwal->lowongan->prodi->nama ?? '-' }}</div>
                                </td>

                                {{-- Status --}}
                                <td class="py-3 px-5 text-center">
                                    @if($sudahDinilai)
                                        <span class="text-sm font-bold text-green-600">Dinilai</span>
                                    @else
                                        <span class="text-sm font-bold text-yellow-600">Pending</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3 px-5 text-center">
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
</div>
@endsection
