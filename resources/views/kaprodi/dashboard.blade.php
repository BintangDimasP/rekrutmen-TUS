@extends('layouts.admin')

@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between hover:border-gray-200 transition-colors">
            <div class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-widest mb-3">Total Pelamar</div>
            <div class="text-4xl font-black text-gray-800">{{ $totalPelamar }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between hover:border-gray-200 transition-colors">
            <div class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-widest mb-3">Sedang Diproses</div>
            <div class="text-4xl font-black text-gray-800">{{ $totalProses }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between hover:border-gray-200 transition-colors">
            <div class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-widest mb-3">Diterima</div>
            <div class="text-4xl font-black text-gray-800">{{ $totalDiterima }}</div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between hover:border-gray-200 transition-colors">
            <div class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-widest mb-3">Ditolak</div>
            <div class="text-4xl font-black text-gray-800">{{ $totalDitolak }}</div>
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
                <table class="w-full text-left border-collapse table-fixed">
                    <thead class="bg-[#8b1515] text-white">
                        <tr>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[28%]">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[28%]">Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[22%]">Tanggal Lamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[22%]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lamaranTerbaru as $lamaran)
                            @php
                                $statusColors = [
                                    'menunggu'       => 'text-gray-500',
                                    'seleksi_tahap1' => 'text-blue-600',
                                    'seleksi_tahap2' => 'text-indigo-600',
                                    'diterima'       => 'text-green-600',
                                    'ditolak'        => 'text-red-600',
                                ];
                                $color = $statusColors[$lamaran->status] ?? 'text-gray-500';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $lamaran->pelamar->nama }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $lamaran->pelamar->user?->email }}</div>
                                </td>
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $lamaran->lowongan->nama_posisi }}</div>
                                </td>
                                <td class="py-3 px-5 text-sm text-gray-600">{{ $lamaran->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-5 text-center">
                                    <span class="text-sm font-bold {{ $color }}">{{ $lamaran->status_label }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center">
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
