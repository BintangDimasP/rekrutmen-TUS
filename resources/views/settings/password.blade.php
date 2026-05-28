@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-lg mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-8 py-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center ring-2 ring-white/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">Ubah Password</h2>
                    <p class="text-red-200 text-xs mt-0.5">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('settings.password.update') }}" class="p-8 space-y-5">
            @csrf
            @method('PUT')

            {{-- Password Lama --}}
            <div>
                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Password Lama</label>
                <input type="password" name="current_password"
                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                              focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all
                              @error('current_password') border-red-400 bg-red-50 @enderror"
                       placeholder="Masukkan password lama">
                @error('current_password')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Baru --}}
            <div>
                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Password Baru</label>
                <input type="password" name="password"
                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                              focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all
                              @error('password') border-red-400 bg-red-50 @enderror"
                       placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm text-gray-800
                              focus:outline-none focus:bg-white focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/15 transition-all"
                       placeholder="Ulangi password baru">
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full py-3 bg-[#8b1515] hover:bg-red-900 text-white font-bold text-sm rounded-xl shadow-md transition-colors">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
