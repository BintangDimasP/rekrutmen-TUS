@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

@section('content')

    <div class="max-w-6xl mx-auto space-y-6" x-data="{
        search: '',
        filterProdi: '',
        totalFiltered: 0,
        updateRows() {
            const rows = document.querySelectorAll('tr[data-row]');
            let count = 0;
            rows.forEach(row => {
                const matchProdi = this.filterProdi === '' || row.dataset.prodi === this.filterProdi;
                const matchSearch = this.search === '' || row.dataset.posisi.includes(this.search.toLowerCase());
                const visible = matchProdi && matchSearch;
                row.style.display = visible ? '' : 'none';
                if (visible) count++;
            });
            this.totalFiltered = count;
        }
    }"
    x-init="$nextTick(() => updateRows()); $watch('search', () => updateRows()); $watch('filterProdi', () => updateRows())">

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

                    {{-- Prodi Chip --}}
                    <div class="relative" x-data="{ prodiOpen: false }" @click.outside="prodiOpen = false">
                        <button type="button" @click="prodiOpen = !prodiOpen"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                                :class="filterProdi !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
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

                    {{-- Active tag --}}
                    @foreach($prodis as $prodi)
                    <span x-show="filterProdi === '{{ $prodi->id }}'" x-transition class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                        {{ $prodi->nama }}
                        <button type="button" @click="filterProdi = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </span>
                    @endforeach

                    {{-- Clear --}}
                    <button x-show="filterProdi !== '' || search !== ''" x-transition type="button" @click="filterProdi = ''; search = ''" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
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
                <table class="w-full text-left border-collapse table-fixed" style="min-width:750px">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Nama Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Persyaratan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Kuota</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Ditutup</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[12%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[14%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lowongans as $lowongan)
                            <tr x-data="{ showToggleModal: false }" class="hover:bg-gray-50 transition-colors"
                                data-row
                                data-prodi="{{ $lowongan->prodi_id }}"
                                data-posisi="{{ strtolower($lowongan->nama_posisi) }}">
                                <td class="py-3 px-5 max-w-0">
                                    <div class="text-sm font-medium text-gray-800 truncate" title="{{ $lowongan->nama_posisi }}">{{ $lowongan->nama_posisi }}</div>
                                    <div class="text-xs font-medium text-gray-500 mt-0.5 truncate" title="{{ $lowongan->prodi->nama ?? '-' }}">{{ $lowongan->prodi->nama ?? '-' }}</div>
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
                                <td class="py-3 px-5 text-sm font-medium text-gray-600 whitespace-nowrap">
                                    {{ $lowongan->tanggal_tutup->format('d M Y') }}
                                    @if($lowongan->tanggal_tutup->isPast())
                                        <div class="text-[0.65rem] text-red-500 font-medium">Sudah lewat</div>
                                    @else
                                        <div class="text-[0.65rem] text-green-600 font-medium">
                                            {{ $lowongan->tanggal_tutup->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-5">
                                    @if($lowongan->status === 'aktif')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-green-100 text-green-700">
                                            Aktif
                                        </span>
                                    @elseif($lowongan->status === 'draft')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-amber-100 text-amber-700">
                                            Draft
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-gray-100 text-gray-600">
                                            Ditutup
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
                                            <button type="button" @click="showToggleModal = true" title="Tutup Lowongan"
                                                class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" @click="showToggleModal = true" title="Publish Lowongan"
                                                class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 10a6 6 0 0112 0m-6 11V10m-4 4l4-4 4 4" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Toggle Modal --}}
                                        <div x-show="showToggleModal" x-transition.opacity
                                            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                            @click.self="showToggleModal = false" style="display: none;">
                                            <div x-show="showToggleModal" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative whitespace-normal">

                                                {{-- Close Button --}}
                                                <button type="button" @click="showToggleModal = false"
                                                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                                {{-- Question Icon --}}
                                                <div class="mx-auto mb-5 flex justify-center">
                                                    <svg width="68" height="68" viewBox="0 0 24 24" fill="#8b1515"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="drop-shadow-[0_8px_12px_rgba(59,130,246,0.3)]">
                                                        <path fill-rule="evenodd"
                                                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 01-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 01-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 01-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.89-.777.89-2.038 0-2.815zM12 18.75a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>

                                                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">
                                                    {{ $lowongan->status === 'aktif' ? 'Tutup lowongan?' : 'Publish lowongan?' }}
                                                </h2>
                                                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">
                                                    {{ $lowongan->status === 'aktif' ? 'Lowongan akan ditutup dan tidak dapat menerima pelamar baru.' : 'Lowongan akan dipublish dan dapat dilamar oleh kandidat.' }}
                                                </p>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <form method="POST"
                                                        action="{{ route('admin.lowongan.toggleStatus', $lowongan) }}"
                                                        class="contents">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">
                                                            Yes
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="showToggleModal = false"
                                                        class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">
                                                        No
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-700 font-semibold text-sm">Belum ada lowongan</h3>
                                        <p class="text-gray-400 text-xs">Buat lowongan pertama dengan menekan tombol "Tambah
                                            Lowongan".</p>
                                        <a href="{{ route('admin.lowongan.create') }}"
                                            class="mt-1 px-4 py-2 bg-[#8b1515] text-white text-xs font-semibold rounded-lg hover:bg-red-900 transition-colors">
                                            Tambah Lowongan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Empty state when filter yields no results --}}
            <div x-show="totalFiltered === 0" class="py-14 text-center" style="display: none;">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706"/></svg>
                <h3 class="text-sm font-medium text-gray-600 mb-1">Belum ada data lowongan</h3>
                <p class="text-xs text-gray-400">Tidak ada lowongan yang cocok dengan pencarian atau filter.</p>
            </div>

            {{-- Footer --}}
            @include('components.pagination', ['paginator' => $lowongans])
        </div>
    </div>

@endsection
