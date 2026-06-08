@extends('layouts.admin')

@section('title', 'Cari Lowongan')

@section('content')
    <div class="max-w-5xl mx-auto space-y-8" x-data="lowonganApp()">

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
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                        Prodi
                        <span x-show="prodiFilter.length > 0" x-text="prodiFilter.length" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center"></span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

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

                {{-- Jenjang Chip --}}
                <div class="relative" @click.outside="jenjangOpen = false">
                    <button type="button" @click="jenjangOpen = !jenjangOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="jenjangFilter.length > 0 ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                        Jenjang
                        <span x-show="jenjangFilter.length > 0" x-text="jenjangFilter.length" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center"></span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="jenjangOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="jenjangOpen" x-transition
                         class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Jenjang</p>
                        </div>
                        <div class="p-3 space-y-1">
                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" value="S1" x-model="jenjangFilter" class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                <span class="text-sm font-medium text-gray-700">S1</span>
                            </label>
                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" value="S2" x-model="jenjangFilter" class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                <span class="text-sm font-medium text-gray-700">S2</span>
                            </label>
                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" value="S3" x-model="jenjangFilter" class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-300/30">
                                <span class="text-sm font-medium text-gray-700">S3</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Saved Toggle Chip --}}
                <button type="button" @click="showOnlySaved = !showOnlySaved"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                        :class="showOnlySaved ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                    <svg class="w-3.5 h-3.5" :fill="showOnlySaved ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                    Tersimpan
                </button>

                {{-- Active filter tags --}}
                <template x-for="p in prodiFilter" :key="'p-'+p">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                        <span x-text="p"></span>
                        <button type="button" @click="prodiFilter = prodiFilter.filter(v => v !== p)" class="ml-0.5 hover:text-gray-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>
                <template x-for="j in jenjangFilter" :key="'j-'+j">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                        <span x-text="j"></span>
                        <button type="button" @click="jenjangFilter = jenjangFilter.filter(v => v !== j)" class="ml-0.5 hover:text-gray-900">
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

        {{-- Lowongan Cards Grid (Available) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availableLowongans as $lowongan)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col p-6 relative"
                     x-show="isCardVisible({{ $lowongan->id }}, '{{ $lowongan->prodi->nama ?? '' }}', '{{ $lowongan->jenjang_minimal }}', '{{ addslashes($lowongan->nama_posisi) }}')"
                     x-transition>

                    {{-- Header: Title & Logo --}}
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="flex-1 pt-1">
                            <h3 class="text-base font-bold text-gray-800 leading-tight">{{ $lowongan->nama_posisi }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</p>
                        </div>
                        @if($lowongan->prodi && $lowongan->prodi->logo)
                            <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}"
                                class="w-[60px] h-[60px] rounded-full object-contain bg-white border border-gray-100 p-1 flex-shrink-0">
                        @else
                            <div
                                class="w-[60px] h-[60px] rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-2xl">
                                {{ substr($lowongan->nama_posisi, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info Rows (Location & Time/Quota) --}}
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-black font-medium text-sm">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>Surabaya</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-400 text-xs font-medium">
                            <svg class="w-6 h-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $lowongan->tanggal_tutup->format('j F Y') }} . {{ $lowongan->kuota }} Kuota</span>
                        </div>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-3 mb-8">
                        <span
                            class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">{{ $lowongan->jenjang_minimal }}</span>
                        <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">>
                            {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                        <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">Full-Time</span>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-auto">
                        <a href="{{ route('pelamar.lowongan.show', $lowongan) }}"
                            class="flex-1 py-2.5 bg-[#8b1515] text-white text-center text-sm font-bold rounded-xl shadow-sm hover:bg-red-900 transition-colors duration-300">
                            Details
                        </a>

                        <button type="button" @click="toggleSave({{ $lowongan->id }})" class=""
                            :class="{ 'scale-130': isSaved({{ $lowongan->id }}) }">
                            <svg class="w-5 h-5 transition-colors duration-300"
                                :fill="isSaved({{ $lowongan->id }}) ? '#8b1515' : 'none'" stroke="#8b1515" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-100 rounded-3xl p-16 text-center">
                    <h2 class="text-xl font-bold text-gray-800">Tidak ada lowongan tersedia</h2>
                    <p class="text-sm text-gray-500 mt-2">Coba sesuaikan filter Anda.</p>
                </div>
            @endforelse

            {{-- Frontend Empty State --}}
            <div class="col-span-full bg-white border border-gray-100 rounded-3xl p-16 text-center"
                 x-show="visibleCount === 0 && {{ $availableLowongans->count() }} > 0"
                 x-cloak>
                <h2 class="text-xl font-bold text-gray-800">Tidak ada lowongan yang cocok</h2>
                <p class="text-sm text-gray-500 mt-2">Coba sesuaikan filter Anda atau klik "Clear Filters".</p>
            </div>
        </div>

        {{-- Applied Lowongan Section --}}
        @if($appliedLowongans->count() > 0)
            <div class="pt-8 mt-12 mb-12" x-show="!showOnlySaved">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Sudah Dilamar</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-60">
                    @foreach($appliedLowongans as $lowongan)
                        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 flex flex-col cursor-not-allowed">

                            {{-- Header: Title & Logo --}}
                            <div class="flex items-start justify-between gap-4 mb-5">
                                <div class="flex-1 pt-1">
                                    <h3 class="text-base font-bold text-gray-600 leading-tight">{{ $lowongan->nama_posisi }}</h3>
                                    <p class="text-sm text-gray-400 mt-1">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</p>
                                </div>
                                @if($lowongan->prodi && $lowongan->prodi->logo)
                                    <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}"
                                        class="w-[60px] h-[60px] rounded-full object-contain bg-white border border-gray-200 p-1 flex-shrink-0 grayscale">
                                @else
                                    <div
                                        class="w-[60px] h-[60px] rounded-full bg-gray-300 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-2xl">
                                        {{ substr($lowongan->nama_posisi, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Info Rows (Location & Time/Quota) --}}
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-3 text-gray-500 font-medium text-sm">
                                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span>Surabaya</span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-400 text-xs font-medium">
                                    <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $lowongan->tanggal_tutup->format('j F Y') }} . {{ $lowongan->kuota }} Kuota</span>
                                </div>
                            </div>

                            {{-- Badges --}}
                            <div class="flex flex-wrap gap-3 mb-8">
                                <span
                                    class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl">{{ $lowongan->jenjang_minimal }}</span>
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl">>
                                    {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl">Full-Time</span>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 mt-auto">
                                <button disabled
                                    class="flex-1 py-2.5 bg-gray-300 text-gray-500 text-sm font-bold rounded-xl cursor-not-allowed">
                                    Details
                                </button>

                                <button disabled
                                    class="w-11 h-11 flex items-center justify-center border border-gray-300 rounded-xl bg-transparent text-gray-400 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        @php
            $lowonganData = $availableLowongans->map(function($l) {
                return ['id' => $l->id, 'prodi' => $l->prodi->nama ?? '', 'jenjang' => $l->jenjang_minimal, 'nama' => $l->nama_posisi];
            });
        @endphp
        <script>
            window._lowonganData = @json($lowonganData);
            document.addEventListener('alpine:init', () => {
                Alpine.data('lowonganApp', () => ({
                    savedIds: @json($savedLowonganIds),
                    showOnlySaved: false,
                    prodiFilter: [],
                    jenjangFilter: [],
                    search: '',
                    prodiOpen: false,
                    jenjangOpen: false,

                    get hasFilters() { return this.prodiFilter.length > 0 || this.jenjangFilter.length > 0 || this.showOnlySaved || this.search !== ''; },

                    get visibleCount() {
                        const all = window._lowonganData;
                        return all.filter(l => this.isCardVisibleRaw(l.id, l.prodi, l.jenjang, l.nama)).length;
                    },

                    clearAll() { this.prodiFilter = []; this.jenjangFilter = []; this.showOnlySaved = false; this.search = ''; },

                    isSaved(id) {
                        return this.savedIds.includes(id);
                    },

                    isCardVisible(id, prodi, jenjang, nama) {
                        return this.isCardVisibleRaw(id, prodi, jenjang, nama);
                    },

                    isCardVisibleRaw(id, prodi, jenjang, nama) {
                        if (this.showOnlySaved && !this.savedIds.includes(id)) return false;
                        if (this.prodiFilter.length > 0 && !this.prodiFilter.includes(prodi)) return false;
                        if (this.jenjangFilter.length > 0 && !this.jenjangFilter.includes(jenjang)) return false;
                        if (this.search !== '' && !nama.toLowerCase().includes(this.search.toLowerCase())) return false;
                        return true;
                    },

                    async toggleSave(id) {
                        try {
                            const response = await fetch(`/pelamar/lowongan/${id}/save`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await response.json();

                            if (data.saved) {
                                if (!this.savedIds.includes(id)) this.savedIds.push(id);
                            } else {
                                this.savedIds = this.savedIds.filter(savedId => savedId !== id);
                            }
                        } catch (error) {
                            console.error('Error toggling save status:', error);
                        }
                    }
                }));
            });
        </script>
    @endpush
@endsection