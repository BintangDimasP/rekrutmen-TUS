@extends('layouts.admin')

@section('title', 'Manajemen Akses Sistem')

@section('content')

    {{-- Main Container --}}
    <div class="max-w-6xl mx-auto space-y-6" 
         x-data="{
            search: '{{ request('search') }}',
            roleFilter: '{{ request('role') }}',
            currentPage: 1,
            perPage: 10,
            openAddAdminModal: {{ $errors->has('name') || $errors->has('username') || $errors->has('password') ? 'true' : 'false' }},
            get filteredRows() {
                return Array.from(this.$refs.tableBody.querySelectorAll('tr[data-row]')).filter(row => {
                    const name = row.dataset.name || '';
                    const email = row.dataset.email || '';
                    const role = row.dataset.role || '';
                    const matchSearch = this.search === '' || 
                        name.includes(this.search.toLowerCase()) || 
                        email.includes(this.search.toLowerCase());
                    const matchRole = this.roleFilter === '' || 
                        role === this.roleFilter ||
                        (this.roleFilter === 'penguji' && row.dataset.isPenguji === '1');
                    return matchSearch && matchRole;
                });
            },
            get totalFiltered() { return this.filteredRows.length; },
            get totalPages() { return this.totalFiltered === 0 ? 1 : Math.ceil(this.totalFiltered / this.perPage); },
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
            $watch('search', () => resetPage());
            $watch('roleFilter', () => resetPage());
         ">

        {{-- Filter Chips Bar --}}
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>

                        {{-- Expanding input --}}
                        <div class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                             :style="searchOpen ? 'width: min(288px, calc(100vw - 8rem)); opacity: 1' : 'width: 36px; opacity: 0'">
                            <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari nama atau email..."
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

                {{-- Role Chip --}}
                <div class="relative" x-data="{ roleOpen: false }" @click.outside="roleOpen = false">
                    <button type="button" @click="roleOpen = !roleOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="roleFilter !== '' ? 'bg-[#8b1515] text-white border-[#8b1515]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Role
                        <span x-show="roleFilter !== ''" class="ml-0.5 w-5 h-5 rounded-full bg-white/20 text-[0.65rem] font-bold flex items-center justify-center">1</span>
                        <svg class="w-3 h-3 ml-0.5 transition-transform" :class="roleOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="roleOpen" x-transition
                         class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden" style="display:none;">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Filter by Role</p>
                        </div>
                        <div class="p-3 space-y-1">
                            @php $roles = ['admin' => 'Admin', 'pelamar' => 'Pelamar', 'penguji' => 'Penguji', 'kaprodi' => 'Kaprodi']; @endphp
                            @foreach($roles as $key => $label)
                            <button type="button" @click="roleFilter = roleFilter === '{{ $key }}' ? '' : '{{ $key }}'; roleOpen = false"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 transition-colors text-left"
                                    :class="roleFilter === '{{ $key }}' ? 'bg-gray-100' : ''">
                                <span class="w-4 h-4 rounded border-2 flex items-center justify-center transition-colors flex-shrink-0"
                                      :class="roleFilter === '{{ $key }}' ? 'border-gray-500 bg-gray-600' : 'border-gray-300'">
                                    <svg x-show="roleFilter === '{{ $key }}'" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Active filter tag --}}
                <template x-if="roleFilter !== ''">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 border border-gray-300 text-xs font-semibold text-gray-700">
                        <span x-text="roleFilter.charAt(0).toUpperCase() + roleFilter.slice(1)"></span>
                        <button type="button" @click="roleFilter = ''" class="ml-0.5 hover:text-gray-900">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                </template>

                {{-- Clear All --}}
                <button x-show="roleFilter !== '' || search !== ''" x-transition type="button" @click="roleFilter = ''; search = ''"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear Filters
                </button>
            </div>

            {{-- Tombol + Tambah Admin (inside the filter bar) --}}
            <button type="button" @click="openAddAdminModal = true"
                    class="absolute top-0 right-0 h-full w-14 z-20 flex items-center justify-center bg-[#8b1515] text-white rounded-r-2xl hover:bg-red-900 transition-colors cursor-pointer" title="Tambah Admin">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </button>
        </div>
        </div>{{-- end .relative --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed" style="min-width:600px">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[25%]">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[35%]">Email</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Role</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tableBody">
                        @forelse($users as $user)
                        @php
                            $isMulti = $user->is_penguji && $user->is_kaprodi;
                            // Untuk filter: dosen rangkap match baik 'penguji' maupun 'kaprodi'
                            $matchPenguji = $user->is_penguji ? '1' : '0';
                        @endphp
                        <tr x-data="{ openEditModal: false, openDeleteModal: false {{ $errors->any() && old('edit_user_id') == $user->id ? ', openEditModal: true' : '' }} }" 
                            class="hover:bg-gray-50 transition-colors h-[52px]"
                            data-row
                            data-name="{{ strtolower(addslashes($user->name)) }}"
                            data-email="{{ strtolower(addslashes($user->email)) }}"
                            data-role="{{ $user->role }}"
                            data-is-penguji="{{ $matchPenguji }}">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium truncate max-w-0" title="{{ $user->name }}">{{ $user->name }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium truncate max-w-0" title="{{ $user->email }}">{{ $user->email }}</td>
                            <td class="py-3 px-5 text-sm font-medium">
                                @if($isMulti)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-indigo-100 text-indigo-800 uppercase">Kaprodi & Penguji</span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-purple-100 text-purple-800 uppercase">Admin</span>
                                @elseif($user->role === 'pelamar')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-green-100 text-green-800 uppercase">Pelamar</span>
                                @elseif($user->role === 'penguji')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800 uppercase">Penguji</span>
                                @elseif($user->role === 'kaprodi')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800 uppercase">Kaprodi</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" @click="openEditModal = true" class="text-gray-400 hover:text-amber-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Edit Kredensial">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button type="button" @click="openDeleteModal = true" class="text-gray-400 hover:text-gray-700 transition-colors flex items-center justify-center p-1.5 rounded" title="Hapus / Cabut Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endif
                                </div>
                                
                                {{-- ── Edit Modal ── --}}
                                <div x-show="openEditModal" x-transition.opacity
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                    @click.self="openEditModal = false" style="display: none;">
                                    <div x-show="openEditModal"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden text-left">
                                        
                                        <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                                            <h2 class="text-xl font-semibold text-white tracking-tight">Edit Akun</h2>
                                            <button type="button" @click="openEditModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.user.update', $user) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="edit_user_id" value="{{ $user->id }}">
                                            
                                            <div class="p-6 space-y-4 text-left">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Akun</label>
                                                    <input type="text" value="{{ $user->name }}" disabled class="w-full px-4 py-2.5 rounded-xl bg-gray-100 border border-gray-200 text-sm text-gray-500 cursor-not-allowed">
                                                </div>

                                                @if($user->role === 'pelamar')
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Email Akses (Login)</label>
                                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                    @if($errors->has('email') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('email') }}</p> @endif
                                                </div>
                                                @endif

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Reset Kata Sandi <span class="text-xs text-gray-400 font-normal">(kosongkan jika tidak ingin diubah)</span></label>
                                                    <input type="password" name="password" placeholder="Masukkan password baru..." class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all outline-none">
                                                    @if($errors->has('password') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('password') }}</p> @endif
                                                </div>
                                            </div>

                                            <div class="px-6 py-4 bg-gray-50 flex justify-center border-t border-gray-100">
                                                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- ── Delete / Cabut Role Modal ── --}}
                                @if($user->id !== auth()->id())
                                <div x-show="openDeleteModal" x-transition.opacity
                                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                     @click.self="openDeleteModal = false" style="display: none;">
                                    <div x-show="openDeleteModal"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">
                                        
                                        {{-- Close Button --}}
                                        <button type="button" @click="openDeleteModal = false" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>

                                        {{-- Warning Icon --}}
                                        <div class="mx-auto mb-5 flex justify-center">
                                            <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                                                <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                                                <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                                <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                                            </svg>
                                        </div>
                                        
                                        @if($user->role === 'admin')
                                            <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus admin ini?</h2>
                                            <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Akun admin akan dihapus<br>secara permanen!</p>
                                        @elseif($user->role === 'pelamar')
                                            <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Hapus akun ini?</h2>
                                            <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Seluruh data pelamar akan<br>dihapus permanen!</p>
                                        @else
                                            <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Cabut role dosen ini?</h2>
                                            <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Role akan dicabut, dosen kembali<br>tanpa akses. Data tetap aman.</p>
                                        @endif

                                        <div class="grid grid-cols-2 gap-3">
                                            <form method="POST" action="{{ route('admin.user.destroy', $user) }}" class="contents">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Yes</button>
                                            </form>
                                            <button type="button" @click="openDeleteModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all">No</button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 px-5 text-center">
                                <span class="text-gray-400 text-sm">Tidak ada user terdaftar.</span>
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
                    {{-- Previous --}}
                    <button type="button" @click="prevPage()" 
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] cursor-pointer'"
                            class="px-3 py-1.5 rounded-lg font-medium transition">Prev</button>

                    {{-- Page Numbers --}}
                    <template x-for="page in totalPages" :key="page">
                        <button type="button" @click="goToPage(page)"
                                x-show="page >= currentPage - 2 && page <= currentPage + 2"
                                :class="page === currentPage ? 'bg-[#8b1515] text-white font-bold' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515]'"
                                class="px-3 py-1.5 rounded-lg font-medium transition cursor-pointer"
                                x-text="page"></button>
                    </template>

                    {{-- Next --}}
                    <button type="button" @click="nextPage()" 
                            :disabled="currentPage >= totalPages"
                            :class="currentPage >= totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] cursor-pointer'"
                            class="px-3 py-1.5 rounded-lg font-medium transition">Next</button>
                </div>
            </div>
        </div>

        {{-- Modal Tambah Admin (inside x-data scope) --}}
        <template x-teleport="body">
            <div x-show="openAddAdminModal" style="display:none;"
                 class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                 @click.self="openAddAdminModal = false">
                <div x-show="openAddAdminModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

                    <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between">
                        <h2 class="text-base font-bold text-white">Tambah Admin Baru</h2>
                        <button type="button" @click="openAddAdminModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border border-white/40 text-white hover:bg-white/10 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.user.store') }}" class="p-6 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Nama admin"
                                   class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('name'))<p class="text-xs text-red-500 mt-1">{{ $errors->first('name') }}</p>@endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest mb-1.5">Username <span class="text-red-500">*</span></label>
                            <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden focus-within:border-[#8b1515] focus-within:ring-2 focus-within:ring-[#8b1515]/15 transition-all">
                                <input type="text" name="username" value="{{ old('username') }}" required
                                       placeholder="username"
                                       class="flex-1 px-4 py-2.5 bg-transparent text-sm focus:outline-none">
                                <span class="px-3 py-2.5 text-xs text-gray-400 bg-gray-100 border-l border-gray-200 whitespace-nowrap font-medium">@admin.telkomuniversity.ac.id</span>
                            </div>
                            @if($errors->has('username'))<p class="text-xs text-red-500 mt-1">{{ $errors->first('username') }}</p>@endif
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-widest mb-1.5">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required
                                   placeholder="Min. 8 karakter"
                                   class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                            @if($errors->has('password'))<p class="text-xs text-red-500 mt-1">{{ $errors->first('password') }}</p>@endif
                        </div>

                        <div class="pt-2 flex justify-center">
                            <button type="submit" class="px-8 py-2.5 bg-[#8b1515] hover:bg-red-900 text-white text-sm font-bold rounded-xl shadow-md transition-all">
                                Tambah Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

@endsection
