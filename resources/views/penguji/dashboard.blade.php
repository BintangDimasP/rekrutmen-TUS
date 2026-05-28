@extends('layouts.admin')

@section('title', 'Dashboard Penguji')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalDiuji }}</p>
            <p class="text-sm font-medium text-gray-500">Total Pelamar Diuji</p>
        </div>
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalDinilai }}</p>
            <p class="text-sm font-medium text-gray-500">Selesai Dinilai</p>
        </div>
        <div class="bg-white rounded-xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] px-5 py-5 h-[120px] flex flex-col justify-end border border-gray-100 transition-all hover:shadow-md">
            <p class="font-bold text-3xl text-gray-800 mb-1">{{ $totalBelumDinilai }}</p>
            <p class="text-sm font-medium text-gray-500">Belum Dinilai</p>
        </div>
    </div>

    {{-- Upcoming Jadwals --}}
    <div>
        <div class="flex items-center justify-between mb-5 px-1">
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Pengujian 7 Hari Kedepan</h3>
            <a href="{{ route('penguji.pengujian.index') }}" class="text-xs font-bold text-[#8b1515] hover:underline transition-colors">Lihat Semua</a>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead class="bg-[#8b1515] text-white">
                        <tr>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Tanggal</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Waktu</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Seleksi</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[18%]">Lowongan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[12%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($upcomingJadwals as $jadwal)
                            @php
                                $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
                                $sudahDinilai = $jadwal->penilaian !== null;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-5 text-sm font-semibold text-gray-800">{{ $jadwal->tanggal->format('d/m/Y') }}</td>
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">Sesi {{ $jadwal->sesi }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $sesiInfo ? $sesiInfo['start'] . ' - ' . $sesiInfo['end'] : '-' }}</div>
                                </td>
                                <td class="py-3 px-5 text-sm font-semibold text-gray-700">
                                    {{ $jadwal->tipe_seleksi == 'wawancara' ? 'Wawancara' : 'Micro Teaching' }}
                                </td>
                                <td class="py-3 px-5 text-sm font-semibold text-gray-800">{{ $jadwal->pelamar->nama }}</td>
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $jadwal->lowongan->nama_posisi }}</div>
                                    <div class="text-[0.65rem] text-gray-500 uppercase tracking-widest mt-0.5">{{ $jadwal->lowongan->prodi->nama ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    @if($sudahDinilai)
                                        <span class="text-sm font-bold text-green-600">Dinilai</span>
                                    @else
                                        <span class="text-sm font-bold text-yellow-600">Pending</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <div class="flex justify-center">
                                        <a href="{{ route('penguji.pengujian.show', $jadwal->id) }}" class="p-1.5 text-gray-400 hover:text-[#8b1515] transition-colors" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 px-6 text-center">
                                    <p class="text-gray-400 text-sm font-medium mb-5">Tidak ada pengujian dalam 7 hari kedepan.</p>
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
