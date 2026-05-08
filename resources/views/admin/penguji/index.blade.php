@extends('layouts.admin')

@section('title', 'Manajemen Penguji')

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

    @if($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl shadow-black/5 border border-red-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 text-white shadow-inner">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Gagal</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">Harap pilih setidaknya satu dosen.</p>
            </div>
        </div>
    @endif

    {{-- Main Container --}}
    <div x-data="{ openAddModal: false, searchDosen: '', filterProdi: '' }" class="max-w-6xl mx-auto space-y-6">

        {{-- Filter & Action --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full lg:w-auto">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" placeholder="Cari nama penguji..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
            </div>
            
            <button type="button" @click="openAddModal = true" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors shrink-0 w-full lg:w-auto cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tunjuk Penguji
            </button>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Nama Penguji</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Homebase (Prodi)</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">NIP/NIDN</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Email Akun</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap">Status Tambahan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pengujis as $penguji)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5 text-sm text-gray-800 font-medium">
                                {{ $penguji->nama }}
                                <div class="text-xs font-mono text-gray-500">{{ $penguji->kode }}</div>
                            </td>
                            <td class="py-3 px-5 text-sm text-gray-600 font-medium">{{ $penguji->prodi?->nama ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $penguji->nip ?? '-' }} / {{ $penguji->nidn ?? '-' }}</td>
                            <td class="py-3 px-5 text-sm text-gray-600">{{ $penguji->email }}</td>
                            <td class="py-3 px-5 text-sm">
                                @if($penguji->is_kaprodi)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-amber-100 text-amber-800">Merangkap Kaprodi</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.penguji.show', $penguji) }}"
                                       class="text-gray-400 hover:text-blue-600 transition-colors flex items-center justify-center p-1.5 rounded" title="Detail Penguji">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 px-5 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                    </div>
                                    <h3 class="text-gray-800 font-medium text-sm">Belum ada penguji</h3>
                                    <p class="text-gray-400 text-xs mt-1">Gunakan tombol "Tunjuk Penguji" untuk mulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Footer Pagination / Tally --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
                <span>Total: <strong>{{ $pengujis->count() }}</strong> penguji aktif</span>
            </div>

        </div>

        {{-- ── Tunjuk Penguji Modal ── --}}
        <div x-show="openAddModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
             @click.self="openAddModal = false" style="display: none;">
            <div x-show="openAddModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden text-left flex flex-col max-h-[85vh]">
                
                {{-- Modal Header --}}
                <div class="bg-[#8b1515] px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <h2 class="text-lg font-bold text-white tracking-tight">Tunjuk Dosen Menjadi Penguji</h2>
                    <button type="button" @click="openAddModal = false" class="w-7 h-7 flex items-center justify-center rounded-lg border-2 border-white/60 text-white hover:bg-white/15 hover:border-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Toolbar: Search + Filter Prodi --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex-shrink-0 space-y-3">
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Pilih satu atau lebih dosen. Akun login akan dibuatkan otomatis dengan kata sandi bawaan <code class="bg-gray-200 px-1 py-0.5 rounded text-gray-700 font-mono">penguji123</code>.
                    </p>
                    <div class="flex gap-3">
                        {{-- Search --}}
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" x-model="searchDosen" placeholder="Cari nama dosen..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                        </div>
                        {{-- Filter Prodi --}}
                        <select x-model="filterProdi" class="pl-3 pr-8 py-2 rounded-lg border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition min-w-[160px]">
                            <option value="">Semua Prodi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form method="POST" action="{{ route('admin.penguji.store') }}" class="flex-1 overflow-hidden flex flex-col min-h-0">
                    @csrf
                    
                    <div class="flex-1 overflow-y-auto">
                        @if($calonPengujis->isEmpty())
                            <div class="p-12 text-center">
                                <span class="text-gray-400 text-sm">Seluruh dosen telah menjadi penguji, atau tidak ada dosen tersedia.</span>
                            </div>
                        @else
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 sticky top-0 z-10 border-b border-gray-100">
                                    <tr>
                                        <th class="py-3 px-5 w-10"></th>
                                        <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                        <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Program Studi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($calonPengujis as $calon)
                                        <tr class="hover:bg-gray-50 transition-colors"
                                            x-show="
                                                (searchDosen === '' || '{{ strtolower($calon->nama) }}'.includes(searchDosen.toLowerCase()))
                                                && (filterProdi === '' || filterProdi === '{{ $calon->prodi_id }}')
                                            ">
                                            <td class="py-3 px-5 text-center">
                                                <input type="checkbox" name="dosen_ids[]" value="{{ $calon->id }}" class="w-4 h-4 text-[#8b1515] bg-gray-100 border-gray-300 rounded focus:ring-[#8b1515] focus:ring-2 cursor-pointer">
                                            </td>
                                            <td class="py-3 px-5">
                                                <div class="text-sm font-semibold text-gray-800">{{ $calon->nama }}</div>
                                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $calon->kode }} &bull; {{ $calon->email }}</div>
                                            </td>
                                            <td class="py-3 px-5">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                                    {{ $calon->prodi?->nama ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 bg-white flex justify-end gap-3 border-t border-gray-100 shadow-[0_-2px_8px_rgba(0,0,0,0.04)] flex-shrink-0">
                        <button type="button" @click="openAddModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#8b1515] hover:bg-red-900 rounded-xl shadow-md transition-colors">Tunjuk Penguji Terpilih</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection
