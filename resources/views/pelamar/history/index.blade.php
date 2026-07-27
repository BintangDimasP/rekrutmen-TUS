@extends('layouts.admin')

@section('title', 'Riwayat Lamaran')

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
                         :style="searchOpen ? 'width: min(288px, calc(100vw - 8rem)); opacity: 1' : 'width: 36px; opacity: 0'">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari lowongan..."
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
            <div class="relative" @click.outside="prodiOpen = false">
                <button type="button" @click="prodiOpen = !prodiOpen"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="prodiFilter.length > 0 ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
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
            <table class="w-full text-left border-collapse table-fixed" style="min-width:680px">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-left w-[30%]">Posisi</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-left w-[20%]">Prodi</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-left w-[15%]">Tanggal Melamar</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-left w-[15%]">Status</th>
                        <th class="py-3 px-4 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" x-ref="tbody">
                    @forelse($lamarans as $lamaran)
                    <tr class="hover:bg-gray-50 transition-colors"
                        data-row
                        data-status="{{ $lamaran->status }}"
                        data-prodi="{{ $lamaran->lowongan->prodi->nama ?? '' }}"
                        data-posisi="{{ strtolower($lamaran->lowongan->nama_posisi) }}">
                        <td class="py-3.5 px-4">
                            <div class="text-sm font-semibold text-gray-700 text-left">{{ $lamaran->lowongan->nama_posisi }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="text-sm text-gray-600 text-left">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="text-sm text-gray-600 text-left whitespace-nowrap">{{ $lamaran->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-left">
                                @php
                                    $statusColors = [
                                        'menunggu'          => 'bg-gray-50 border border-gray-200 text-gray-500',
                                        'seleksi_tahap1'    => 'bg-blue-800 text-white',
                                        'seleksi_tahap2'    => 'bg-indigo-800 text-white',
                                        'diterima'          => 'bg-green-800 text-white',
                                        'ditolak'           => 'bg-red-800 text-white',
                                        'mengundurkan_diri' => 'bg-gray-800 text-white',
                                    ];
                                    $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                                @endphp
                                <span class="inline-flex px-2 py-1 rounded-lg text-[0.6rem] font-black uppercase tracking-wider {{ $colorClass }}">
                                    {{ $lamaran->status_label }}
                                </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('pelamar.history.show', $lamaran->id) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Lihat Detail Lamaran">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                                    </svg>
                                </div>
                                <h3 class="text-gray-700 font-semibold text-sm">Belum ada riwayat lamaran</h3>
                                <p class="text-gray-400 text-xs">Belum ada data riwayat lamaran yang terdaftar.</p>
                            </div>
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
