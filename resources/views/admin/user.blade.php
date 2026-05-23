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

        {{-- Filter Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                {{-- Left: Filter Role --}}
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-48">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <select x-model="roleFilter" 
                                class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                            <option value="">Semua Role</option>
                            <option value="admin">Admin</option>
                            <option value="pelamar">Pelamar</option>
                            <option value="penguji">Penguji</option>
                            <option value="kaprodi">Kaprodi</option>
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
                    <input type="text" x-model="search" placeholder="Cari nama atau email..." 
                           class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
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
                        <tr x-data="{ openEditModal: false {{ $errors->any() && old('edit_user_id') == $user->id ? ', openEditModal: true' : '' }} }" 
                            class="hover:bg-gray-50 transition-colors h-[52px]"
                            data-row
                            data-name="{{ strtolower(addslashes($user->name)) }}"
                            data-email="{{ strtolower(addslashes($user->email)) }}{{ $user->penguji_user ? ' ' . strtolower(addslashes($user->penguji_user->email)) : '' }}"
                            data-role="{{ $user->role }}"
                            data-is-penguji="{{ $user->role === 'kaprodi' && $user->penguji_user ? '1' : '0' }}">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium truncate">{{ $user->name }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium">
                                <div class="font-medium text-[0.8rem] truncate">{{ $user->email }}</div>
                                @if($user->role === 'kaprodi' && $user->penguji_user)
                                    <div class="font-medium text-[0.8rem] text-blue-600 mt-1 truncate">{{ $user->penguji_user->email }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-sm">
                                @if($user->role === 'kaprodi' && $user->penguji_user)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-bold bg-indigo-100 text-indigo-800 uppercase">Kaprodi & Penguji</span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-purple-100 text-purple-800 uppercase">Admin</span>
                                @elseif($user->role === 'pelamar')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-green-100 text-green-800 uppercase">Pelamar</span>
                                @elseif($user->role === 'penguji')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-blue-100 text-blue-800 uppercase">Penguji</span>
                                @elseif($user->role === 'kaprodi')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800 uppercase">Kaprodi</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-gray-100 text-gray-800 uppercase">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" @click="openEditModal = true" class="text-gray-400 hover:text-amber-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Edit Kredensial">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
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

                                                @if($user->role === 'kaprodi' && $user->penguji_user)
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Kata Sandi (Kaprodi) - <span class="text-blue-600">Lama: {{ $user->password_plain ?? '-' }}</span></label>
                                                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all outline-none">
                                                    @if($errors->has('password') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('password') }}</p> @endif
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Kata Sandi (Penguji) - <span class="text-blue-600">Lama: {{ $user->penguji_user->password_plain ?? '-' }}</span></label>
                                                    <input type="password" name="penguji_password" placeholder="Kosongkan jika tidak ingin diubah" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all outline-none">
                                                    @if($errors->has('penguji_password') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('penguji_password') }}</p> @endif
                                                </div>
                                                @else
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Reset Kata Sandi - <span class="text-blue-600">Lama: {{ $user->password_plain ?? '-' }}</span></label>
                                                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all outline-none">
                                                    @if($errors->has('password') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('password') }}</p> @endif
                                                </div>
                                                @endif
                                            </div>

                                            <div class="px-6 py-4 bg-gray-50 flex justify-center border-t border-gray-100">
                                                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
    </div>

@endsection
