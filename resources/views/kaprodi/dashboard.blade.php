@extends('layouts.admin')

@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalPelamar }}</p>
            <p class="text-sm font-medium text-gray-500">Total Pelamar</p>
        </div>
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalProses }}</p>
            <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
        </div>
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalDiterima }}</p>
            <p class="text-sm font-medium text-gray-500">Diterima</p>
        </div>
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalDitolak }}</p>
            <p class="text-sm font-medium text-gray-500">Ditolak</p>
        </div>
    </div>

    {{-- Lamaran Terbaru --}}
    <div>
        <div class="flex items-center justify-between mb-5 px-1">
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Lamaran Terbaru</h3>
            <a href="{{ route('kaprodi.pelamar.index') }}" class="text-xs font-bold text-[#8b1515] hover:underline transition-colors">Lihat Semua</a>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed" style="min-width:550px">
                    <thead class="bg-[#8b1515] text-white">
                        <tr>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[22%]">Nama</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[24%]">Jenjang Pendidikan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[16%]">No. Telepon</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[22%]">Posisi Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[16%]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lamaranTerbaru as $lamaran)
                            @php
                                $statusColors = [
                                    'menunggu'       => 'bg-gray-50 border border-gray-200 text-gray-500',
                                    'seleksi_tahap1' => 'bg-blue-800 text-white',
                                    'seleksi_tahap2' => 'bg-indigo-800 text-white',
                                    'diterima'       => 'bg-green-800 text-white',
                                    'ditolak'        => 'bg-red-800 text-white',
                                ];
                                $colorClass = $statusColors[$lamaran->status] ?? 'bg-gray-50 border border-gray-200 text-gray-500';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $lamaran->pelamar->nama }}</div>
                                    <div class="text-[0.65rem] text-gray-500 mt-0.5">{{ $lamaran->pelamar->user->email ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-5">
                                    <div class="text-sm text-gray-700 font-medium">
                                        {{ $lamaran->pelamar->jenjang ?: '-' }} @if($lamaran->pelamar->institusi) – {{ $lamaran->pelamar->institusi }} @endif
                                    </div>
                                    @if($lamaran->pelamar->prodi_pendidikan)
                                        <div class="text-[0.65rem] text-gray-400 uppercase tracking-widest mt-0.5">{{ $lamaran->pelamar->prodi_pendidikan }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-sm font-semibold text-gray-700">
                                    {{ $lamaran->pelamar->no_telepon ?: '-' }}
                                </td>
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $lamaran->lowongan->nama_posisi }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex px-2 py-1 rounded-lg text-[0.65rem] font-bold uppercase tracking-wide {{ $colorClass }}">
                                        {{ str_replace('_', ' ', $lamaran->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Lamaran</h3>
                                        <p class="text-gray-400 text-xs">Belum ada pelamar yang mendaftar ke prodi Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
