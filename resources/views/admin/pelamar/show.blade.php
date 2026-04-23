@extends('layouts.admin')

@section('title', 'Detail Pelamar — ' . $pelamar->nama)

@section('content')

    {{-- Toast --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-12"
             class="fixed top-6 right-6 z-[100] flex items-center gap-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100 min-w-[320px]">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 text-white">
                <svg class="w-5 h-5 stroke-[2.5px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 mb-0.5">Berhasil</h4>
                <p class="text-[0.8rem] text-gray-500 font-medium leading-snug">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.pelamar.index') }}"
           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-[#8b1515] hover:border-[#8b1515] transition-all shadow-sm flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $pelamar->nama }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $pelamar->user?->email }} &bull; Terdaftar {{ $pelamar->created_at->diffForHumans() }}</p>
        </div>
    </div>

    {{-- ═══════════════════════ DATA DIRI ═══════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Data Diri</h2>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            @php
                $fields = [
                    'NIK' => $pelamar->nik,
                    'Nama Lengkap' => $pelamar->nama,
                    'Jenis Kelamin' => $pelamar->jenis_kelamin === 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin === 'P' ? 'Perempuan' : '—'),
                    'Tempat Lahir' => $pelamar->tempat_lahir,
                    'Tanggal Lahir' => $pelamar->tanggal_lahir?->format('d F Y'),
                    'No. Telepon' => $pelamar->no_telepon,
                    'Email' => $pelamar->user?->email,
                ];
            @endphp
            @foreach($fields as $label => $value)
                <div @if(in_array($label, ['Email', 'NIK'])) class="sm:col-span-2" @endif>
                    <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $label }}</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $value ?? '—' }}</p>
                </div>
            @endforeach
            <div class="sm:col-span-2">
                <p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Alamat</p>
                <p class="text-sm font-semibold text-gray-800 leading-relaxed">{{ $pelamar->alamat ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════ PENDIDIKAN ═══════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Riwayat Pendidikan</h2>
        </div>
        <div class="p-6 space-y-5">
            @foreach([
                ['level' => '', 'label' => 'Pendidikan Utama'],
                ['level' => '_2', 'label' => 'Pendidikan Kedua (S2/S3)'],
                ['level' => '_3', 'label' => 'Pendidikan Ketiga'],
            ] as $edu)
            @php
                $jenjang = $pelamar->{'jenjang' . $edu['level']};
            @endphp
            @if($jenjang)
            <div class="rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-bold text-[#8b1515] uppercase tracking-wider mb-3">{{ $edu['label'] }}</p>
                <div class="grid grid-cols-2 gap-3">
                    <div><p class="text-[0.65rem] font-bold text-gray-400 uppercase mb-0.5">Jenjang</p><p class="text-sm font-semibold text-gray-800">{{ $jenjang }}</p></div>
                    <div><p class="text-[0.65rem] font-bold text-gray-400 uppercase mb-0.5">IPK</p><p class="text-sm font-semibold text-gray-800">{{ $pelamar->{'ipk' . $edu['level']} ?? '—' }}</p></div>
                    <div class="col-span-2"><p class="text-[0.65rem] font-bold text-gray-400 uppercase mb-0.5">Institusi</p><p class="text-sm font-semibold text-gray-800">{{ $pelamar->{'institusi' . $edu['level']} ?? '—' }}</p></div>
                    <div class="col-span-2"><p class="text-[0.65rem] font-bold text-gray-400 uppercase mb-0.5">Program Studi</p><p class="text-sm font-semibold text-gray-800">{{ $pelamar->{'prodi_pendidikan' . $edu['level']} ?? '—' }}</p></div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════ AKADEMIK ═══════════════════════ --}}
    @if($pelamar->nidn || $pelamar->homebase || $pelamar->jabatan_akademik)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-4">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Riwayat Akademik</h2>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div><p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">NIDN</p><p class="text-sm font-semibold text-gray-800 font-mono">{{ $pelamar->nidn ?? '—' }}</p></div>
            <div><p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Homebase Asal</p><p class="text-sm font-semibold text-gray-800">{{ $pelamar->homebase ?? '—' }}</p></div>
            <div><p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Jabatan Akademik</p><p class="text-sm font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $pelamar->jabatan_akademik ?? '—') }}</p></div>
            <div><p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">H-Index Scopus</p><p class="text-sm font-semibold text-gray-800">{{ $pelamar->h_index ?? '—' }}</p></div>
            @if($pelamar->minat_riset)
            <div class="sm:col-span-2"><p class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest mb-1">Bidang Riset</p><p class="text-sm font-semibold text-gray-800 leading-relaxed">{{ $pelamar->minat_riset }}</p></div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════════════════ LAMARAN & STATUS ═══════════════════════ --}}
    @forelse($pelamar->lamarans as $lamaran)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-[#8b1515]/5 border-b border-[#8b1515]/10 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-[#8b1515] uppercase tracking-wider">Lamaran: {{ $lamaran->lowongan?->nama_posisi ?? '—' }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ $lamaran->lowongan?->prodi?->nama ?? '-' }} &bull; Diajukan {{ $lamaran->created_at->format('d M Y') }}</p>
            </div>
            @php
                $statusColors = [
                    'menunggu'       => 'bg-gray-100 text-gray-600 border-gray-200',
                    'seleksi_tahap1' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'seleksi_tahap2' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'diterima'       => 'bg-green-50 text-green-700 border-green-200',
                    'ditolak'        => 'bg-red-50 text-red-700 border-red-200',
                ];
            @endphp
            <span class="inline-flex px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusColors[$lamaran->status] ?? $statusColors['menunggu'] }}">
                {{ $lamaran->status_label }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.lamaran.update', $lamaran) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Jadwal Wawancara --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Wawancara</label>
                    <input type="date" name="tanggal_wawancara"
                           value="{{ $lamaran->tanggal_wawancara?->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                </div>
                <div>
                    <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Link Zoom / Meeting</label>
                    <input type="url" name="link_zoom" placeholder="https://zoom.us/j/..."
                           value="{{ $lamaran->link_zoom }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                </div>
            </div>

            {{-- Status Lamaran --}}
            <div>
                <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Ubah Status Lamaran</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['menunggu' => 'Menunggu', 'seleksi_tahap1' => 'Seleksi Tahap 1', 'seleksi_tahap2' => 'Seleksi Tahap 2', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                    <label class="relative">
                        <input type="radio" name="status" value="{{ $val }}" class="sr-only peer"
                               {{ $lamaran->status === $val ? 'checked' : '' }}>
                        <span class="cursor-pointer inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold border-2 transition-all
                            peer-checked:border-[#8b1515] peer-checked:bg-[#8b1515] peer-checked:text-white
                            border-gray-200 text-gray-600 hover:border-gray-300 bg-white select-none">
                            {{ $label }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Catatan Admin --}}
            <div>
                <label class="block text-[0.7rem] font-bold text-gray-500 uppercase tracking-widest mb-2">Catatan Admin (Opsional)</label>
                <textarea name="catatan_admin" rows="2" placeholder="Catatan untuk pelamar atau internal..."
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-none">{{ $lamaran->catatan_admin }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#8b1515] hover:bg-red-900 text-white text-sm font-bold rounded-lg shadow-md transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
        <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-gray-700 font-semibold text-sm">Belum Ada Lamaran</h3>
        <p class="text-gray-400 text-xs mt-1">Pelamar ini belum pernah mengajukan lamaran ke lowongan manapun.</p>
    </div>
    @endforelse

</div>

@endsection
