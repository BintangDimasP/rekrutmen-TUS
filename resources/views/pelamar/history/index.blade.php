@extends('layouts.admin')

@section('title', 'Histori Lamaran')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('pelamar.dashboard') }}" class="hover:text-[#8b1515] transition-colors font-medium">Dashboard</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800">Histori Lamaran</span>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('pelamar.history.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                {{-- Filter Prodi --}}
                <div class="relative w-full sm:w-64">
                                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                    <select name="prodi_id" onchange="this.form.submit()" 
                            class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="">Filter</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="relative w-full sm:w-48">
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                        <option value="">Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="seleksi_tahap1" {{ request('status') == 'seleksi_tahap1' ? 'selected' : '' }}>Seleksi Tahap 1</option>
                        <option value="seleksi_tahap2" {{ request('status') == 'seleksi_tahap2' ? 'selected' : '' }}>Seleksi Tahap 2</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </div>

            @if(request()->filled('prodi_id') || request()->filled('status'))
                <div>
                    <a href="{{ route('pelamar.history.index') }}" class="text-xs text-red-600 hover:underline">Reset Filter</a>
                </div>
            @endif
        </form>
    </div>

    {{-- Table History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <thead>
                    <tr class="bg-[#8b1515] text-white">
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[25%]">Posisi</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Prodi</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Tanggal Melamar</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[20%]">Status</th>
                        <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lamarans as $lamaran)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-5">
                            <div class="text-sm font-semibold text-gray-600 text-center">{{ $lamaran->lowongan->nama_posisi }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-600 text-center">{{ $lamaran->lowongan->prodi->nama ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-5">
                            <div class="text-sm text-gray-600 text-center">{{ $lamaran->created_at->format('d M Y') }}</div>
                            
                        </td>
                        <td class="py-3 px-5 text-center">
                            @php
                                $statusColors = [
                                    'menunggu'       => 'bg-gray-100 text-gray-500 border-gray-200',
                                    'seleksi_tahap1' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'seleksi_tahap2' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    'diterima'       => 'bg-green-50 text-green-600 border-green-100',
                                    'ditolak'        => 'bg-red-50 text-red-600 border-red-100',
                                ];
                                $colorClass = $statusColors[$lamaran->status] ?? $statusColors['menunggu'];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border {{ $colorClass }}">
                                {{ $lamaran->status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('pelamar.history.show', $lamaran->id) }}" class="flex items-center justify-center p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Lihat Detail Lamaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center space-y-4">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto text-gray-200">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="text-sm font-bold text-gray-400">Belum ada histori pendaftaran</div>
                            <a href="{{ route('pelamar.lowongan.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#8b1515] text-white text-xs font-bold rounded-xl shadow-md shadow-[#8b1515]/20">Ayo Mulai Melamar</a>
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
