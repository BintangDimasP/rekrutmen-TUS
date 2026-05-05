@extends('layouts.admin')

@section('title', 'Manajemen Akses Sistem')

@section('content')

    {{-- Toast Notification --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl shadow-black/5 border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white shadow-inner">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">{{ session('success') }}</p>
            </div>
            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Main Container --}}
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Filter Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" action="{{ route('admin.user.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                {{-- Left: Filter Role --}}
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-48">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <select name="role" onchange="this.form.submit()" 
                                class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pelamar" {{ request('role') == 'pelamar' ? 'selected' : '' }}>Pelamar</option>
                            <option value="penguji" {{ request('role') == 'penguji' ? 'selected' : '' }}>Penguji</option>
                            <option value="kaprodi" {{ request('role') == 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                        </select>
                    </div>
                    @if(request()->filled('role') || request()->filled('search'))
                        <a href="{{ route('admin.user.index') }}" class="text-xs text-red-600 hover:underline">Reset</a>
                    @endif
                </div>

                {{-- Right: Search --}}
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." 
                           class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                    <button type="submit" class="hidden"></button>
                </div>
            </form>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Email</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Role</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr x-data="{ openEditModal: false {{ $errors->any() && old('edit_user_id') == $user->id ? ', openEditModal: true' : '' }} }" class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium">{{ $user->name }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-mono">{{ $user->email }}</td>
                            <td class="py-3 px-5 text-sm">
                                @if($user->role === 'admin')
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
                                            <h2 class="text-xl font-semibold text-white tracking-tight">Kredensial Login</h2>
                                            <button type="button" @click="openEditModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.user.update', $user) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="edit_user_id" value="{{ $user->id }}">
                                            
                                            <div class="p-6 space-y-4 text-left">

                                                <div class="bg-blue-50 text-blue-800 text-xs px-3 py-2 rounded-lg leading-relaxed">
                                                    Memperbarui akun pelamar atau penguji di sini <strong>otomatis tersinkronisasi</strong> dengan master data di tabel pelamar dan dosen.
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Nama Akun</label>
                                                    <input type="text" value="{{ $user->name }}" disabled class="w-full px-4 py-2.5 rounded-xl bg-gray-100 border border-gray-200 text-sm text-gray-500 cursor-not-allowed">
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Email Akses (Login)</label>
                                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all">
                                                    @if($errors->has('email') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('email') }}</p> @endif
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-800 mb-1.5">Reset Kata Sandi</label>
                                                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800 focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all outline-none">
                                                    @if($errors->has('password') && old('edit_user_id') == $user->id) <p class="text-xs text-red-500 mt-1">{{ $errors->first('password') }}</p> @endif
                                                </div>
                                            </div>

                                            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                                                <button type="button" @click="openEditModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                                                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Simpan Perubahan</button>
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
            
            {{-- Footer Pagination / Tally --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between text-xs text-gray-500 gap-3">
                <span>Total: <strong>{{ $users->count() }}</strong> akun</span>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-purple-500"></div>Admin</span>
                    <span class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-amber-500"></div>Kaprodi</span>
                    <span class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-blue-500"></div>Penguji</span>
                    <span class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-green-500"></div>Pelamar</span>
                </div>
            </div>

        </div>
    </div>

@endsection
