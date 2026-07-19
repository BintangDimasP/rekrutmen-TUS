@extends('layouts.admin')

@section('title', 'Daftar Dosen – ' . $prodi->nama)

@section('content')



    {{-- Main Container --}}
    <div x-data="{ 
            openAddModal: false {{ $errors->any() && !old('edit_dosen_id') && !$errors->has('file') ? ', openAddModal: true' : '' }}, 
            openImportModal: false {{ $errors->has('file') ? ', openImportModal: true' : '' }},
            search: '',
            statusFilter: '',
            get filteredRows() {
                return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                    const name = row.dataset.name || '';
                    const nip = row.dataset.nip || '';
                    const status = row.dataset.status || '';
                    const matchSearch = this.search === '' || 
                        name.includes(this.search.toLowerCase()) || 
                        nip.includes(this.search.toLowerCase()) ||
                        (row.dataset.kode || '').includes(this.search.toLowerCase());
                    const matchStatus = this.statusFilter === '' || 
                        status === this.statusFilter ||
                        (status === 'rangkap' && (this.statusFilter === 'penguji' || this.statusFilter === 'kaprodi'));
                    return matchSearch && matchStatus;
                });
            },
            updateVisibility() {
                const rows = Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]'));
                const filtered = this.filteredRows;
                rows.forEach(row => {
                    row.style.display = filtered.includes(row) ? '' : 'none';
                });
            }
        }"
        x-init="
            $watch('search', () => updateVisibility());
            $watch('statusFilter', () => updateVisibility());
            $nextTick(() => updateVisibility());
        "
        class="max-w-6xl mx-auto">

        <div class="space-y-6">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.prodi.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Prodi</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-semibold text-gray-800">{{ $prodi->nama }}</span>
        </div>

        {{-- Filter & Action (with attached buttons) --}}
        <div class="relative">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 pr-20">
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
                                <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama dosen"
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

                    {{-- Status Chip --}}
                    <div class="relative" x-data="{ statusOpen: false }" @click.outside="statusOpen = false">
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
                                <button type="button" @click="statusFilter = statusFilter === 'penguji' ? '' : 'penguji'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="statusFilter === 'penguji' ? 'bg-gray-100' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'penguji' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'penguji'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                    <span class="text-sm font-medium text-gray-700">Penguji</span>
                                </button>
                                <button type="button" @click="statusFilter = statusFilter === 'kaprodi' ? '' : 'kaprodi'; statusOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 text-left" :class="statusFilter === 'kaprodi' ? 'bg-gray-100' : ''">
                                    <span class="w-4 h-4 rounded border-2 flex items-center justify-center" :class="statusFilter === 'kaprodi' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'"><svg x-show="statusFilter === 'kaprodi'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                    <span class="text-sm font-medium text-gray-700">Kaprodi</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Active tag --}}
                    <template x-if="statusFilter === 'penguji'"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">Penguji <button type="button" @click="statusFilter = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></span></template>
                    <template x-if="statusFilter === 'kaprodi'"><span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">Kaprodi <button type="button" @click="statusFilter = ''" class="ml-0.5 hover:text-gray-900"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></span></template>

                    {{-- Clear --}}
                    <button x-show="statusFilter !== '' || search !== ''" x-transition type="button" @click="statusFilter = ''; search = ''" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Clear
                    </button>

                    {{-- Import Excel Button (next to filter) --}}
                    <button type="button" @click="openImportModal = true"
                            class="ml-auto inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-400 hover:border-gray-300 hover:text-gray-600 hover:bg-gray-50 transition-all" title="Import Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </button>
                </div>
            </div>

            {{-- + Add Dosen button (outside card, flush right corner) --}}
            <button type="button" @click="openAddModal = true"
                    class="absolute top-0 right-0 h-full w-14 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors" title="Tambah Dosen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed" style="min-width:700px">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[15%]">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[15%]">Kode</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[15%]">NIP/NIDN</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[40%]">Email</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[15%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                        @forelse($dosens as $dosen)
                            @php
                                $statusStr = '';
                                if ($dosen->is_penguji && $dosen->is_kaprodi) {
                                    $statusStr = 'rangkap';
                                } elseif ($dosen->is_penguji) {
                                    $statusStr = 'penguji';
                                } elseif ($dosen->is_kaprodi) {
                                    $statusStr = 'kaprodi';
                                }
                            @endphp
                            <tr x-data="{ showDeleteModal: false, openEditModal: false {{ $errors->any() && old('edit_dosen_id') == $dosen->id ? ', openEditModal: true' : '' }} }"
                                class="hover:bg-gray-50 transition-colors"
                                data-row
                                data-name="{{ strtolower(addslashes($dosen->nama)) }}"
                                data-kode="{{ strtolower(addslashes($dosen->kode ?? '')) }}"
                                data-nip="{{ strtolower(addslashes($dosen->nip ?? '')) }} {{ strtolower(addslashes($dosen->nidn ?? '')) }}"
                                data-status="{{ $statusStr }}">
                                <td class="py-3 px-5 text-sm text-gray-800 font-medium truncate text-center max-w-0" title="{{ $dosen->nama }}">{{ $dosen->nama }}</td>
                                <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate text-center max-w-0" title="{{ $dosen->kode }}">{{ $dosen->kode }}</td>
                                <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate text-center max-w-0" title="{{ $dosen->nip ?? '-' }}/{{ $dosen->nidn ?? '-' }}">{{ $dosen->nip ?? '-' }}/{{ $dosen->nidn ?? '-' }}</td>
                                <td class="py-3 px-5 text-sm text-gray-600 font-medium text-center max-w-0">
                                    @php
                                        $userEmails = \App\Models\User::where('dosen_id', $dosen->id)->pluck('email', 'role');
                                    @endphp
                                    @if($userEmails->isNotEmpty())
                                        @foreach($userEmails as $role => $email)
                                            <div class="truncate" title="{{ $email }}">{{ $email }}</div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-sm">
                                    <div class="flex justify-center items-center gap-1.5 flex-nowrap">
                                        @if($dosen->is_kaprodi)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800 whitespace-nowrap">Kaprodi</span>
                                        @endif
                                        @if($dosen->is_penguji)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800 whitespace-nowrap">Penguji</span>
                                        @endif
                                        @if(!$dosen->is_kaprodi && !$dosen->is_penguji)
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-5 text-sm">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" @click="openEditModal = true"
                                            class="text-gray-400 hover:text-amber-600 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="showDeleteModal = true" class="text-gray-400 hover:text-gray-700 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>

                                        {{-- -- Delete Modal -- --}}
                                        <div x-show="showDeleteModal" x-transition.opacity
                                             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                             @click.self="showDeleteModal = false" style="display: none;">
                                            <div x-show="showDeleteModal"
                                                 x-transition:enter="transition ease-out duration-200"
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
                                                
                                                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus dosen ini?</h2>
                                                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Data yang dihapus tidak dapat dikembalikan!</p>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <form method="POST" action="{{ route('admin.dosen.destroy', $dosen) }}" class="contents">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Iya</button>
                                                    </form>
                                                    <button type="button" @click="showDeleteModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all">Tidak</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- -- Edit Modal -- --}}
                                    <div x-show="openEditModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                        @click.self="openEditModal = false"
                                        @if(old('edit_dosen_id') != $dosen->id) style="display: none;" @endif>
                                        <div 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">

                                            <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                                                <h2 class="text-xl font-semibold text-white tracking-tight">Edit Dosen</h2>
                                                <button type="button" @click="openEditModal = false"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <form method="POST" action="{{ route('admin.dosen.update', $dosen) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="edit_dosen_id" value="{{ $dosen->id }}">

                                                <div class="p-6 space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Lengkap</label>
                                                        <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}"
                                                            required placeholder="John Doe"
                                                            class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                        @if($errors->has('nama') && old('edit_dosen_id') == $dosen->id)
                                                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('nama') }}</p>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-800 mb-1.5">Kode Dosen</label>
                                                        <input type="text" name="kode" value="{{ old('kode', $dosen->kode) }}"
                                                            required placeholder="JHD"
                                                            class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium uppercase text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                        @if($errors->has('kode') && old('edit_dosen_id') == $dosen->id)
                                                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('kode') }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-800 mb-1.5">NIP</label>
                                                            <input type="text" name="nip" value="{{ old('nip', $dosen->nip) }}" placeholder="19xxxx"
                                                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                            @if($errors->has('nip') && old('edit_dosen_id') == $dosen->id)
                                                                <p class="text-xs text-red-500 mt-1">{{ $errors->first('nip') }}</p>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-800 mb-1.5">NIDN</label>
                                                            <input type="text" name="nidn"
                                                                value="{{ old('nidn', $dosen->nidn) }}" placeholder="04xxxx"
                                                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                            @if($errors->has('nidn') && old('edit_dosen_id') == $dosen->id)
                                                                <p class="text-xs text-red-500 mt-1">{{ $errors->first('nidn') }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-800 mb-1.5">No. Telepon</label>
                                                        <input type="text" name="no_telepon" value="{{ old('edit_dosen_id') == $dosen->id ? old('no_telepon', $dosen->no_telepon) : $dosen->no_telepon }}" placeholder="08xxx"
                                                            class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                        @if($errors->has('no_telepon') && old('edit_dosen_id') == $dosen->id)
                                                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('no_telepon') }}</p>
                                                        @endif
                                                    </div>

                                                    {{-- Tunjuk Kaprodi --}}
                                                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                                                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tunjuk Sebagai</p>
                                                        </div>
                                                        <div class="p-4">
                                                            <label class="flex items-center gap-3 cursor-pointer group">
                                                                <input type="checkbox" name="is_kaprodi" value="1" {{ old('edit_dosen_id') == $dosen->id ? (old('is_kaprodi') ? 'checked' : '') : ($dosen->is_kaprodi ? 'checked' : '') }}
                                                                    class="w-5 h-5 rounded-md border-gray-300 text-[#8b1515] focus:ring-[#8b1515] cursor-pointer transition-all">
                                                                <div>
                                                                    <span class="text-sm font-semibold text-gray-800 group-hover:text-[#8b1515] transition-colors">Kaprodi</span>
                                                                    <p class="text-xs text-gray-400 mt-0.5">Hanya 1 kaprodi per prodi</p>
                                                                </div>
                                                            </label>
                                                           
                                                        </div>
                                                    </div>

                                                </div>

                                                <div
                                                    class="px-6 py-4 bg-gray-50 flex justify-center border-t border-gray-100">
                                                    <button type="submit"
                                                        class="px-8 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors w-full sm:w-auto">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-5 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-800 font-medium text-sm">Tidak ada dosen</h3>
                                        <p class="text-gray-400 text-xs mt-1">Belum ada dosen terdaftar di program studi ini.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination / Tally --}}
            @include('components.pagination', ['paginator' => $dosens])

        </div>
        </div>

        {{-- -- Add Modal -- --}}
        <div x-show="openAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
            @click.self="openAddModal = false"
            @if(!($errors->any() && !old('edit_dosen_id') && !$errors->has('file'))) style="display: none;" @endif>
            <div 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" 
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">

                <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white tracking-tight">Tambah Dosen</h2>
                    <button type="button" @click="openAddModal = false"
                        class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.dosen.store', $prodi) }}">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ !old('edit_dosen_id') ? old('nama') : '' }}" required placeholder="John Doe"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('nama') && !old('edit_dosen_id'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('nama') }}</p> @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">Kode Dosen</label>
                            <input type="text" name="kode" value="{{ !old('edit_dosen_id') ? old('kode') : '' }}" required placeholder="JHD"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium uppercase text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('kode') && !old('edit_dosen_id'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->first('kode') }}</p> @endif
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1.5">NIP</label>
                                <input type="text" name="nip" value="{{ !old('edit_dosen_id') ? old('nip') : '' }}" placeholder="198xxxx"
                                    class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1.5">NIDN</label>
                                <input type="text" name="nidn" value="{{ !old('edit_dosen_id') ? old('nidn') : '' }}" placeholder="040xxxx"
                                    class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-1.5">No. Telepon</label>
                            <input type="text" name="no_telepon" value="{{ !old('edit_dosen_id') ? old('no_telepon') : '' }}" placeholder="08xxxx"
                                class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                        </div>

                        {{-- Tunjuk Kaprodi --}}
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</p>
                            </div>
                            <div class="p-4">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="is_kaprodi" value="1" {{ old('is_kaprodi') ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-md border-gray-300 text-[#8b1515] focus:ring-[#8b1515] cursor-pointer transition-all">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-800 group-hover:text-[#8b1515] transition-colors">Kaprodi</span>
                                        <p class="text-xs text-gray-400 mt-0.5">Hanya 1 kaprodi per prodi</p>
                                    </div>
                                </label>
                                
                            </div>
                        </div>


                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex justify-center border-t border-gray-100">
                        <button type="submit"
                            class="px-8 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors w-full sm:w-auto">Tambah Dosen</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- -- Import Modal -- --}}
        <div x-show="openImportModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9990] bg-black/40 backdrop-blur-sm"
            @click="openImportModal = false"
            style="display: none;">
        </div>

        <div x-show="openImportModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 pointer-events-none"
            style="display: none;">

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden pointer-events-auto" x-data="{ fileName: '', fileSize: '', isLoading: false, dragOver: false }">

                {{-- Header --}}
                <div class="relative bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-6 py-5">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 400 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="350" cy="10" r="60" fill="white" opacity="0.1"/>
                            <circle cx="50" cy="70" r="40" fill="white" opacity="0.05"/>
                        </svg>
                    </div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div>
                                <h2 id="modal-title" class="text-lg font-bold text-white">Import Data Dosen</h2>
                            </div>
                        </div>
                        <button @click="openImportModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white/80 hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    {{-- Download Template Link --}}
                    <p class="mb-5 text-sm text-gray-500">
                        Belum punya template? <a href="{{ asset('templates/dosen_template.xlsx') }}" download class="text-[#8b1515] font-semibold hover:underline">Download template di sini</a>
                    </p>

                    <form action="{{ route('admin.dosen.import', $prodi) }}" method="POST" enctype="multipart/form-data" @submit="isLoading = true">
                        @csrf
                        
                        {{-- Drag & Drop Upload Area --}}
                        <div class="relative"
                             @dragover.prevent="dragOver = true"
                             @dragleave.prevent="dragOver = false"
                             @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''; fileSize = $event.dataTransfer.files[0] ? (($event.dataTransfer.files[0].size / 1024 / 1024).toFixed(2) + ' MB') : ''">
                            <input type="file" 
                                   name="file" 
                                   accept=".xlsx,.xls,.csv"
                                   @change="fileName = $event.target.files[0]?.name || ''; fileSize = $event.target.files[0] ? (($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB') : ''"
                                   x-ref="fileInput"
                                   class="hidden"
                                   required>
                            
                            <div @click="$refs.fileInput.click()"
                                 :class="dragOver ? 'border-[#8b1515] bg-red-50/50 scale-[1.02]' : (fileName ? 'border-[#8b1515]/40 bg-red-50/30' : 'border-gray-200 hover:border-[#8b1515]/60 hover:bg-gray-50')"
                                 class="w-full p-8 border-2 border-dashed rounded-xl transition-all duration-200 cursor-pointer group">
                                
                                {{-- Empty state --}}
                                <div x-show="!fileName" class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 bg-gray-100 group-hover:bg-[#8b1515]/10 rounded-2xl flex items-center justify-center transition-colors">
                                        <svg class="w-7 h-7 text-gray-400 group-hover:text-[#8b1515] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-semibold text-gray-700">Drag & drop file di sini</p>
                                        <p class="text-xs text-gray-400 mt-1">atau <span class="text-[#8b1515] font-semibold">klik untuk memilih file</span></p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[0.65rem] font-bold rounded">.xlsx</span>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[0.65rem] font-bold rounded">.xls</span>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[0.65rem] font-bold rounded">.csv</span>
                                        <span class="text-[0.65rem] text-gray-400">Max 10MB</span>
                                    </div>
                                </div>

                                {{-- File selected state --}}
                                <div x-show="fileName" class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate" x-text="fileName"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="fileSize"></p>
                                    </div>
                                    <button type="button" @click.stop="fileName = ''; fileSize = ''; $refs.fileInput.value = ''" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if($errors->has('file'))
                            <p class="text-xs text-red-500 mt-2">{!! $errors->first('file') !!}</p>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex justify-center mt-6 pt-5 border-t border-gray-100">
                            <button type="submit" 
                                    :disabled="!fileName || isLoading"
                                    :class="(!fileName || isLoading) ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-[#8b1515] hover:bg-[#6e1010] text-white shadow-lg shadow-red-500/20 hover:shadow-red-500/30'"
                                    class="px-8 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center justify-center gap-2">
                                <template x-if="!isLoading">
                                    <span class="flex items-center gap-2">
                                        Import Data
                                    </span>
                                </template>
                                <template x-if="isLoading">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mengimpor...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- /x-data --}}

@endsection
