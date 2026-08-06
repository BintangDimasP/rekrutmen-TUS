@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

@section('content')

    <div class="max-w-6xl mx-auto space-y-6" x-data="{
        search: '',
        filterProdi: '',
        filterKategori: '',
        totalFiltered: 0,
        updateRows() {
            const rows = document.querySelectorAll('tr[data-row]');
            let count = 0;
            rows.forEach(row => {
                const matchProdi = this.filterProdi === '' || row.dataset.prodi === this.filterProdi;
                const matchSearch = this.search === '' || row.dataset.posisi.includes(this.search.toLowerCase());
                const matchKategori = this.filterKategori === '' || row.dataset.kategori === this.filterKategori;
                const visible = matchProdi && matchSearch && matchKategori;
                row.style.display = visible ? '' : 'none';
                if (visible) count++;
            });
            this.totalFiltered = count;
        }
    }"
    x-init="$nextTick(() => updateRows()); $watch('search', () => updateRows()); $watch('filterProdi', () => updateRows()); $watch('filterKategori', val => { if (val !== 'Dosen') filterProdi = ''; updateRows(); })">

        {{-- Filter & Action (with attached + button) --}}
        <div class="relative">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20">
                <div class="flex items-center gap-3 flex-wrap">

                    {{-- Search (animated) --}}
                    <div class="relative flex items-center" x-data="{ searchOpen: false }" @click.outside="if(!search) searchOpen = false">
                        <button type="button" @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                                class="absolute left-0 z-10 w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 transition-colors"
                                :class="searchOpen ? 'pointer-events-none' : 'border border-gray-200'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                        <div class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                             :style="searchOpen ? 'width: min(288px, calc(100vw - 8rem)); opacity: 1' : 'width: 36px; opacity: 0'">
                            <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama posisi..."
                                   @keydown.escape="search = ''; searchOpen = false"
                                   class="w-[min(288px,calc(100vw-8rem))] pl-10 pr-9 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors shadow-sm">
                        </div>
                        <button type="button" x-show="searchOpen" x-transition.opacity.duration.200ms
                                @click="search = ''; searchOpen = false"
                                class="absolute right-2.5 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="relative" x-data="{ katOpen: false }" @click.outside="katOpen = false">
                        <button type="button" @click="katOpen = !katOpen"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                                :class="filterKategori !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                            Kategori
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="katOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="katOpen" x-transition class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                            <div class="p-3 space-y-1">
                                <button type="button" @click="filterKategori = filterKategori === 'Dosen' ? '' : 'Dosen'; katOpen = false"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left"
                                        :class="filterKategori === 'Dosen' ? 'bg-gray-50' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="filterKategori === 'Dosen' ? 'border-[#8b1515] bg-[#8b1515]' : 'border-gray-300'">
                                        <svg x-show="filterKategori === 'Dosen'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">Dosen</span>
                                </button>
                                <button type="button" @click="filterKategori = filterKategori === 'Tenaga Kependidikan' ? '' : 'Tenaga Kependidikan'; katOpen = false"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left"
                                        :class="filterKategori === 'Tenaga Kependidikan' ? 'bg-gray-50' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="filterKategori === 'Tenaga Kependidikan' ? 'border-blue-500 bg-blue-500' : 'border-gray-300'">
                                        <svg x-show="filterKategori === 'Tenaga Kependidikan'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">Tenaga Kependidikan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Prodi Chip — hanya tampil jika filter Dosen aktif --}}
                    <div x-show="filterKategori === 'Dosen'" x-transition class="relative" x-data="{ prodiOpen: false }" @click.outside="prodiOpen = false">
                        <button type="button" @click="prodiOpen = !prodiOpen"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                                :class="filterProdi !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                            Prodi
                            <span x-show="filterProdi !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="prodiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="prodiOpen" x-transition class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                            <div class="px-4 py-3 border-b border-gray-100"><p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Prodi</p></div>
                            <div class="p-3 space-y-1 max-h-64 overflow-y-auto">
                                @foreach($prodis as $prodi)
                                <button type="button" @click="filterProdi = filterProdi === '{{ $prodi->id }}' ? '' : '{{ $prodi->id }}'; prodiOpen = false"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left" :class="filterProdi === '{{ $prodi->id }}' ? 'bg-gray-50' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="filterProdi === '{{ $prodi->id }}' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                        <svg x-show="filterProdi === '{{ $prodi->id }}'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">{{ $prodi->nama }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Active prodi tag — hanya jika Dosen aktif --}}
                    @foreach($prodis as $prodi)
                    <span x-show="filterKategori === 'Dosen' && filterProdi === '{{ $prodi->id }}'" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                        {{ $prodi->nama }}
                        <button type="button" @click="filterProdi = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </span>
                    @endforeach

                    <button x-show="filterProdi !== '' || search !== '' || filterKategori !== ''" x-transition type="button" @click="filterProdi = ''; search = ''; filterKategori = ''" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Clear
                    </button>
                </div>
            </div>

            {{-- + Button (outside card, flush right) --}}
            <a href="{{ route('admin.lowongan.create') }}"
               class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Tambah Lowongan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed" style="min-width:1150px">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="sticky left-0 z-20 bg-[#8b1515] py-3 px-5 text-sm font-bold whitespace-nowrap text-left w-[300px]">Nama Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[220px]">Persyaratan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[110px]">Kuota</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[100px]">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[160px]">Tenggat</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[120px]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[140px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lowongans as $lowongan)
                            <tr x-data="{ showToggleModal: false, showDeleteModal: false }" class="group hover:bg-gray-50 transition-colors"
                                data-row
                                data-prodi="{{ $lowongan->prodi_id }}"
                                data-posisi="{{ strtolower($lowongan->nama_posisi) }}"
                                data-kategori="{{ $lowongan->kategori }}">
                                <td class="sticky left-0 z-10 bg-white group-hover:bg-gray-50 py-3.5 px-5 max-w-0 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    <div class="text-sm font-semibold text-gray-900 leading-snug whitespace-normal" title="{{ $lowongan->nama_posisi }}">{{ $lowongan->nama_posisi }}</div>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-xs text-gray-400 font-medium">{{ $lowongan->prodi->nama ?? $lowongan->kategori }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-5 max-w-0">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-indigo-50 text-indigo-700">{{ $lowongan->jenjang_minimal }}</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-gray-100 text-gray-600">IPK
                                            = {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                                    </div>
                                    @if($lowongan->skill_dibutuhkan)
                                        @php
                                            $skills = array_filter(array_map('trim', explode(',', $lowongan->skill_dibutuhkan)));
                                            $showSkills = array_slice($skills, 0, 2);
                                            $moreCount = count($skills) - count($showSkills);
                                        @endphp
                                        <div class="flex flex-wrap gap-1 mt-1.5">
                                            @foreach($showSkills as $sk)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-medium bg-gray-100 text-gray-600">{{ $sk }}</span>
                                            @endforeach
                                            @if($moreCount > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-medium bg-gray-100 text-gray-400 cursor-default" title="{{ implode(', ', $skills) }}">+{{ $moreCount }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-center max-w-0">
                                    <div class="text-sm font-medium text-gray-800">{{ $lowongan->sisa_kuota }}</div>
                                    <div class="text-[0.65rem] font-medium text-gray-500">dari {{ $lowongan->kuota }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#8b1515]/10 text-[#8b1515] text-sm font-bold">
                                        {{ $lowongan->lamarans->whereNotIn('status', ['mengundurkan_diri'])->count() }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-sm font-medium text-center text-gray-600 whitespace-nowrap">
                                    {{ $lowongan->tanggal_tutup->format('d M Y') }}
                                    @if($lowongan->tanggal_tutup->endOfDay()->isPast())
                                        <div class="text-[0.65rem] text-red-500 font-medium">Sudah lewat</div>
                                    @else
                                        <div class="text-[0.65rem] text-green-600 font-medium">
                                            {{ $lowongan->tanggal_tutup->endOfDay()->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-center">
                                    @if($lowongan->status === 'aktif')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.7rem] font-semibold bg-green-800 text-white">
                                            Aktif
                                        </span>
                                    @elseif($lowongan->status === 'draft')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.7rem] font-semibold bg-amber-800 text-white">
                                            Draft
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[0.7rem] font-semibold bg-red-800 text-white">
                                            Non aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.lowongan.show', $lowongan) }}" title="Detail"
                                            class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.lowongan.edit', $lowongan) }}" title="Edit"
                                            class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($lowongan->status === 'aktif')
                                            <button type="button" @click="showToggleModal = true" title="Non-aktifkan Lowongan"
                                                class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" @click="showToggleModal = true" title="Aktifkan Lowongan"
                                                class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 10a6 6 0 0112 0m-6 11V10m-4 4l4-4 4 4" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button type="button" @click="showDeleteModal = true" title="Hapus Lowongan"
                                            class="flex items-center justify-center p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>

                                        {{-- Toggle Modal --}}
                                        <div x-show="showToggleModal" x-transition.opacity
                                            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                            @click.self="showToggleModal = false" style="display: none;">
                                            <div x-show="showToggleModal" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative whitespace-normal">

                                                {{-- Warning / Info Icon --}}
                                                <div class="mx-auto mb-5 flex justify-center">
                                                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                                                        <path fill-rule="evenodd" fill="#8b1515"
                                                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 01-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 01-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 01-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.89-.777.89-2.038 0-2.815zM12 18.75a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>

                                                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">
                                                    {{ $lowongan->status === 'aktif' ? 'Unpublish Lowongan?' : 'Publish Lowongan?' }}
                                                </h2>
                                                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">
                                                    {{ $lowongan->status === 'aktif' ? 'Lowongan akan di unpublish dan tidak dapat menerima pelamar baru.' : 'Lowongan akan di publish dan dapat dilamar oleh kandidat.' }}
                                                </p>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <form method="POST"
                                                        action="{{ route('admin.lowongan.toggleStatus', $lowongan) }}"
                                                        class="contents">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">
                                                            Iya
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="showToggleModal = false"
                                                        class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">
                                                        Tidak
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Delete Modal --}}
                                        <div x-show="showDeleteModal" x-transition.opacity
                                            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                            @click.self="showDeleteModal = false" style="display: none;" x-cloak>
                                            <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative whitespace-normal">

                                                {{-- Warning Icon --}}
                                                <div class="mx-auto mb-5 flex justify-center">
                                                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                                                        <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                                                        <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                                        <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                                                    </svg>
                                                </div>

                                                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus lowongan ini?</h2>
                                                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">
                                                    <strong class="text-gray-800 block mb-1">{{ $lowongan->nama_posisi }}</strong>
                                                    Data yang dihapus tidak dapat dikembalikan!
                                                </p>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <form method="POST" action="{{ route('admin.lowongan.destroy', $lowongan) }}" class="contents">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Iya</button>
                                                    </form>
                                                    <button type="button" @click="showDeleteModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all">Tidak</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-700 font-semibold text-sm">Belum ada lowongan</h3>
                                        <p class="text-gray-400 text-xs">Belum ada lowongan yang dibuat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Empty state when filter yields no results --}}
            @if($lowongans->count() > 0)
            <div x-show="totalFiltered === 0" class="py-14 text-center" style="display: none;">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706"/></svg>
                <h3 class="text-sm font-medium text-gray-600 mb-1">Belum ada data lowongan</h3>
                <p class="text-xs text-gray-400">Tidak ada lowongan yang cocok dengan pencarian atau filter.</p>
            </div>

            {{-- Footer --}}
            @include('components.pagination', ['paginator' => $lowongans])
            @endif
        </div>
    </div>

@endsection
