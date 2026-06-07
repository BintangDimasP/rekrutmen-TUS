@extends('layouts.admin')

@section('title', 'Histori Lamaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-6"
     x-data="{
         search: '',
         statusFilter: [],
         prodiFilter: [],
         statusOpen: false,
         prodiOpen: false,

         get hasFilters() { return this.statusFilter.length > 0 || this.prodiFilter.length > 0 || this.search !== ''; },

         clearAll() { this.statusFilter = []; this.prodiFilter = []; this.search = ''; },

         removeStatus(val) { this.statusFilter = this.statusFilter.filter(v => v !== val); },
         removeProdi(val) { this.prodiFilter = this.prodiFilter.filter(v => v !== val); },

         matchRow(row) {
             const matchStatus = this.statusFilter.length === 0 || this.statusFilter.includes(row.dataset.status);
             const matchProdi  = this.prodiFilter.length === 0 || this.prodiFilter.includes(row.dataset.prodi);
             const matchSearch = this.search === '' || (row.dataset.posisi || '').includes(this.search.toLowerCase());
             return matchStatus && matchProdi && matchSearch;
         },

         updateRows() {
             this.$refs.tbody.querySelectorAll('tr[data-row]').forEach(row => {
                 row.style.display = this.matchRow(row) ? '' : 'none';
             });
             const visible = this.$refs.tbody.querySelectorAll('tr[data-row]:not([style*=none])').length;
             this.$refs.count.textContent = visible;
         }
     }"
     x-init="$watch('statusFilter', () => updateRows()); $watch('prodiFilter', () => updateRows()); $watch('search', () => updateRows());">

    {{-- Filter Chips Bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4">
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
                         :style="searchOpen ? 'width: 288px; opacity: 1' : 'width: 36px; opacity: 0'">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari lowongan..."
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

{{-- Prodi Chip --}}
            <div class="relative" @click.outside="prodiOpen = false">
                <button type="button" @click="prodiOpen = !prodiOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="prodiFilter.length > 0 ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    Prodi
                    <span x-show="prodiFilter.length > 0" x-text="prodiFilter.length" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center"></span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Dropdown --}}
                <div x-show="prodiOpen" x-transition
                     class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p>
                    </div>
                    <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                        @foreach($prodis as $prodi)
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" value="{{ $prodi->nama }}" x-model="prodiFilter"
                                   class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                            <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- Status Chip --}}
            <div class="relative" @click.outside="statusOpen = false">
                <button type="button" @click="statusOpen = !statusOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="statusFilter.length > 0 ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Status
                    <span x-show="statusFilter.length > 0" x-text="statusFilter.length" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center"></span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Dropdown --}}
                <div x-show="statusOpen" x-transition
                     class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Status</p>
                    </div>
                    <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                        @php
                            $statuses = [
                                'menunggu'         => ['label' => 'Menunggu',                             'color' => 'text-gray-700'],
                                'seleksi_tahap1'   => ['label' => 'Seleksi Tahap 1 (Administrasi)',       'color' => 'text-gray-700'],
                                'seleksi_tahap2'   => ['label' => 'Seleksi Tahap 2 (Micro & Wawancara)',  'color' => 'text-gray-700'],
                                'diterima'         => ['label' => 'Diterima',                             'color' => 'text-gray-700'],
                                'ditolak'          => ['label' => 'Ditolak',                              'color' => 'text-gray-700'],
                                'mengundurkan_diri'=> ['label' => 'Mengundurkan Diri',                    'color' => 'text-gray-700'],
                            ];
                        @endphp
                        @foreach($statuses as $key => $info)
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors">
                            <input type="checkbox" value="{{ $key }}" x-model="statusFilter"
                                   class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                            <span class="text-sm font-medium {{ $info['color'] }}">{{ $info['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            

            {{-- Active filter tags --}}
            <template x-for="s in statusFilter" :key="'s-'+s">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                    <span x-text="s.replace('_', ' ')"></span>
                    <button type="button" @click="removeStatus(s)" class="ml-0.5 hover:text-gray-900">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </span>
            </template>
            <template x-for="p in prodiFilter" :key="'p-'+p">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                    <span x-text="p"></span>
                    <button type="button" @click="removeProdi(p)" class="ml-0.5 hover:text-gray-900">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </span>
            </template>

            {{-- Clear All --}}
            <button x-show="hasFilters" x-transition type="button" @click="clearAll()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear Filters
            </button>

            {{-- Search has been moved to the left --}}
        </div>
    </div>

    {{-- Table History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[25%]">Posisi</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Prodi</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Tanggal Melamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Status</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tbody">
                    @forelse($lamarans as $lamaran)
                    <tr class="hover:bg-gray-50 transition-colors"
                        data-row
                        data-status="{{ $lamaran->status }}"
                        data-prodi="{{ $lamaran->lowongan->prodi->nama ?? '' }}"
                        data-posisi="{{ strtolower($lamaran->lowongan->nama_posisi) }}">
                        <td class="py-3 px-5">
                            <div class="text-sm font-semibold text-gray-600 text-center">{{ $lamaran->lowongan->nama_posisi }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-600 text-center">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-600 text-center">{{ $lamaran->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="py-3 px-5 text-center">
                            @php
                                $statusColors = [
                                    'menunggu'       => 'bg-gray-100 text-gray-500 border-gray-200',
                                    'seleksi_tahap1' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'seleksi_tahap2' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    'diterima'       => 'bg-green-50 text-green-600 border-green-100',
                                    'ditolak'        => 'bg-red-50 text-red-600 border-red-100',
                                    'mengundurkan_diri' => 'bg-slate-50 text-slate-600 border-slate-200',
                                ];
                                $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border {{ $colorClass }}">
                                {{ $lamaran->status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('pelamar.history.show', $lamaran->id) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Lihat Detail Lamaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center space-y-4">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-200">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="text-sm font-bold text-gray-400">Belum ada histori pendaftaran</div>
                            <a href="{{ route('pelamar.lowongan.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#8b1515] text-white text-xs font-bold rounded-xl shadow-md shadow-[#8b1515]/20">Ayo Mulai Melamar</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('components.pagination', ['paginator' => $lamarans])
    </div>
</div>
@endsection
