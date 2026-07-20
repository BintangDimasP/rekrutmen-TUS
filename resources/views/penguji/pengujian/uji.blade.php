@extends('layouts.admin', ['hideSidebar' => true])

@section('title', 'Form Penilaian Wawancara')

@section('content')
@php
    $pelamarLive = $jadwal->pelamar;
    $lamaran     = \App\Models\Lamaran::where('pelamar_id', $pelamarLive->id)
        ->where('lowongan_id', $jadwal->lowongan_id)
        ->first();
    $pelamar   = $lamaran ? $lamaran->effective_pelamar : $pelamarLive;
    $lowongan  = $jadwal->lowongan;
    $penilaian = $jadwal->penilaian;
    $detailNilai = $penilaian->detail_nilai ?? [];

    // 5 indikator wawancara — masing-masing 1 item (k1_item_1 s/d k1_item_5)
    $indikators = [
        1 => 'Motivasi',
        2 => 'Potensi Kontribusi terhadap Program Studi dan Institusi',
        3 => 'Kemampuan Penelitian & Publikasi',
        4 => 'Kemampuan Komunikasi, Terutama Menjawab Pertanyaan Dengan Cepat dan Tepat',
        5 => 'Kontribusi yang Pernah Dilakukan / Memiliki Link Relasi Dengan Pihak Lain',
    ];
    $totalItems = count($indikators); // 5

    $prodis = \App\Models\Prodi::orderBy('nama')->get();

    $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
    $today    = \Carbon\Carbon::today();
    $canTest  = $today->greaterThanOrEqualTo(\Carbon\Carbon::parse($jadwal->tanggal));

    // Deteksi jenjang pendidikan tertinggi (S3 > S2)
    $jenjangList = collect([$pelamar->jenjang, $pelamar->jenjang_2, $pelamar->jenjang_3])
        ->filter()->map(fn($j) => strtolower(trim($j)));
    $hasS3 = $jenjangList->contains(fn($j) => str_contains($j, 's3') || str_contains($j, 'doktor'));
    $hasS2 = $jenjangList->contains(fn($j) => str_contains($j, 's2') || str_contains($j, 'magister') || str_contains($j, 'master'));

    // Opsi status rekrutmen
    $statusRekrutmenOptions = [];
    if ($hasS3) {
        $statusRekrutmenOptions = [
            'on_going'             => 'On Going',
            'praktisi_part_time'   => 'Praktisi Part Time',
            'profesional_full_time'=> 'Profesional Full Time',
        ];
    } elseif ($hasS2) {
        $statusRekrutmenOptions = [
            'praktisi_part_time'   => 'Praktisi Part Time',
            'profesional_full_time'=> 'Profesional Full Time',
        ];
    }
@endphp

<div class="max-w-5xl mx-auto pb-24" x-data="wawancaraForm()">

    {{-- ── HEADER CARD ── --}}
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 mb-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-50 rounded-full blur-3xl -mr-20 -mt-20 opacity-50 pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#8b1515] to-[#6e1010] text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-900/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-red-50 text-[#8b1515] text-xs font-bold uppercase tracking-wider mb-2 border border-red-100">
                        Wawancara
                    </div>
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $pelamar->nama }}</h1>
                    <p class="text-gray-500 mt-1 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $jadwal->tanggal->format('d M Y') }} &bull; Sesi {{ $jadwal->sesi }} ({{ $sesiInfo['start'] ?? '-' }} - {{ $sesiInfo['end'] ?? '-' }})
                    </p>
                </div>
            </div>

            @if($penilaian)
            <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <span class="block font-bold text-sm">Sudah Dinilai</span>
                    <span class="text-green-600 text-xs font-medium">Rata-rata: {{ $penilaian->total_nilai }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Skala --}}
        <div class="relative mt-6 pt-6 border-t border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Panduan Skala Penilaian:</span>
                <div class="flex flex-wrap items-center gap-4 md:gap-6 text-sm">
                    <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[11px] font-bold text-gray-600 border border-gray-200">1</span><span class="text-gray-600 font-medium">Sangat Kurang</span></div>
                    <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[11px] font-bold text-gray-600 border border-gray-200">2</span><span class="text-gray-600 font-medium">Kurang</span></div>
                    <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[11px] font-bold text-gray-600 border border-gray-200">3</span><span class="text-gray-600 font-medium">Cukup</span></div>
                    <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[11px] font-bold text-gray-600 border border-gray-200">4</span><span class="text-gray-600 font-medium">Baik</span></div>
                    <div class="flex items-center gap-2"><span class="w-5 h-5 rounded-full bg-[#8b1515] text-white flex items-center justify-center text-[11px] font-bold shadow-sm">5</span><span class="text-gray-800 font-bold">Sangat Baik</span></div>
                </div>
            </div>
        </div>
    </div>

    @if(!$canTest)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center mt-8">
            <div class="w-20 h-20 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-5 border border-yellow-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Penilaian Belum Dibuka</h3>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">Jadwal seleksi dilaksanakan pada tanggal <span class="font-bold text-gray-700">{{ $jadwal->tanggal->format('d M Y') }}</span>. Form ini akan dapat diisi pada hari tersebut.</p>
        </div>
    @elseif($penilaian)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center mt-8">
            <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-5 border border-green-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Penilaian Sudah Selesai</h3>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">Anda sudah melakukan penilaian untuk pelamar ini. Nilai tidak dapat diubah setelah disubmit.</p>
            <a href="{{ route('penguji.pengujian.show', $jadwal->id) }}" class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-[#8b1515] hover:bg-[#6e1010] text-white font-bold rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat Detail Penilaian
            </a>
        </div>
    @else
        <form id="wawancaraForm" action="{{ route('penguji.pengujian.storeNilai', $jadwal->id) }}" method="POST" @submit.prevent="submitForm">
            @csrf

            {{-- ── INDIKATOR PENILAIAN ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-5 md:px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white border border-white/20 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-base md:text-lg leading-tight">Indikator Penilaian Wawancara</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($indikators as $itemIdx => $label)
                    @php $fieldName = "k1_item_{$itemIdx}"; @endphp
                    <div class="p-6 md:p-8 flex flex-col gap-6 hover:bg-gray-50/50 transition-colors">
                        <p class="text-[15px] font-medium text-gray-800 leading-relaxed">
                            <span class="text-[#8b1515] font-extrabold mr-2">{{ $itemIdx }}.</span>
                            {{ $label }}
                        </p>
                        <div class="flex items-center justify-between gap-2 w-full max-w-xl mx-auto">
                            @for($s = 1; $s <= 5; $s++)
                            <button type="button"
                                class="group relative flex items-center justify-center w-12 h-12 md:w-14 md:h-14 rounded-full border-2 transition-all duration-300 focus:outline-none flex-shrink-0"
                                :class="{'border-[#8b1515] bg-[#8b1515] text-white shadow-lg transform scale-110 shadow-red-900/30': scores['{{ $fieldName }}'] === {{ $s }}, 'border-gray-300 bg-white text-gray-400 hover:border-[#8b1515] hover:text-[#8b1515] hover:bg-red-50': scores['{{ $fieldName }}'] !== {{ $s }} }"
                                @click="setScore('{{ $fieldName }}', {{ $s }})"
                            >
                                <span class="font-bold text-base md:text-lg">{{ $s }}</span>
                            </button>
                            @endfor
                            <input type="hidden" name="{{ $fieldName }}" :value="scores['{{ $fieldName }}'] || ''">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── CATATAN & INFO TAMBAHAN ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-5 md:px-6 py-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h3 class="text-white font-bold text-base">Catatan & Informasi Tambahan</h3>
                </div>

                <div class="p-6 md:p-8 space-y-6">
                    {{-- Rekomendasi --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Rekomendasi <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            @foreach([
                                'direkomendasikan'       => 'Direkomendasikan',
                                'tidak_direkomendasikan' => 'Tidak Direkomendasikan',
                                'perlu_dipertimbangkan'  => 'Masih Perlu Dipertimbangkan',
                            ] as $val => $rekLabel)
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="rekomendasi" value="{{ $val }}"
                                    x-model="rekomendasi"
                                    class="w-4 h-4 accent-[#8b1515] cursor-pointer"
                                    {{ old('rekomendasi') === $val ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-[#8b1515] transition-colors">{{ $rekLabel }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('rekomendasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Bidang Keahlian --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Bidang Keahlian <span class="text-red-500">*</span></label>
                            <input type="text" name="bidang_keahlian" x-model="bidangKeahlian"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white"
                                placeholder="Ketikkan bidang keahlian...">
                            @error('bidang_keahlian')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Rekomendasi Prodi Tujuan --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Rekomendasi Prodi Tujuan <span class="text-red-500">*</span></label>
                            <select name="prodi_tujuan" id="prodi_tujuan" x-model="prodiTujuan" class="hidden">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->nama }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                            <div x-data="{
                                    open: false,
                                    opts: [
                                        { v: '', l: '-- Pilih Prodi --' },
                                        @foreach($prodis as $prodi){ v: '{{ addslashes($prodi->nama) }}', l: '{{ addslashes($prodi->nama) }}' },@endforeach
                                    ],
                                    get label() { return this.opts.find(o => o.v === prodiTujuan)?.l ?? '-- Pilih Prodi --'; }
                                 }" @click.outside="open = false" class="relative">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm transition-all focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]"
                                    :class="prodiTujuan ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate"></span>
                                    <svg class="w-4 h-4 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180 text-[#8b1515]' : 'text-gray-400'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <template x-for="opt in opts" :key="opt.v">
                                            <button type="button" @click="prodiTujuan = opt.v; open = false;"
                                                class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                                :class="prodiTujuan === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                                <span x-text="opt.l"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('prodi_tujuan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Rekrutmen (muncul jika S2 atau S3) --}}
                        @if(!empty($statusRekrutmenOptions))
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Status Rekrutmen
                                <span class="text-red-500">*</span>
                                <span class="ml-2 text-xs font-normal text-gray-400">(Terdeteksi jenjang {{ $hasS3 ? 'S3' : 'S2' }})</span>
                            </label>
                            <select name="status_rekrutmen" id="status_rekrutmen" x-model="statusRekrutmen" class="hidden">
                                <option value="">-- Pilih Status --</option>
                                @foreach($statusRekrutmenOptions as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div x-data="{
                                    open: false,
                                    opts: [
                                        { v: '', l: '-- Pilih Status --' },
                                        @foreach($statusRekrutmenOptions as $val => $label){ v: '{{ addslashes($val) }}', l: '{{ addslashes($label) }}' },@endforeach
                                    ],
                                    get label() { return this.opts.find(o => o.v === statusRekrutmen)?.l ?? '-- Pilih Status --'; }
                                 }" @click.outside="open = false" class="relative">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm transition-all focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515]"
                                    :class="statusRekrutmen ? 'text-gray-800' : 'text-gray-400'">
                                    <span x-text="label" class="truncate"></span>
                                    <svg class="w-4 h-4 ml-2 flex-shrink-0 transition-transform" :class="open ? 'rotate-180 text-[#8b1515]' : 'text-gray-400'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                    <div class="p-1 space-y-0.5 max-h-52 overflow-y-auto">
                                        <template x-for="opt in opts" :key="opt.v">
                                            <button type="button" @click="statusRekrutmen = opt.v; open = false;"
                                                class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                                :class="statusRekrutmen === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                                <span x-text="opt.l"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('status_rekrutmen')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Penilaian</label>
                        <textarea name="catatan" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-none"
                            placeholder="Tuliskan catatan penilaian (opsional)...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Validation Error --}}
            @if($errors->any())
            <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold text-sm">Terdapat kesalahan pengisian:</p>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- ── SUMMARY & SUBMIT ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row mb-6">
                {{-- Rincian per indikator --}}
                <div class="flex-1 p-6 md:p-8 md:border-r border-gray-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-800 mb-5 flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Rincian Skor per Indikator
                        </h3>
                        <div class="space-y-2.5">
                            @foreach($indikators as $itemIdx => $label)
                            @php $fieldName = "k1_item_{$itemIdx}"; @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors gap-3">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <div class="w-6 h-6 rounded-md bg-red-50 text-[#8b1515] font-bold text-xs flex items-center justify-center flex-shrink-0">{{ $itemIdx }}</div>
                                    <span class="text-xs font-semibold text-gray-700 leading-snug break-words">{{ $label }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <div class="hidden sm:block w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-[#8b1515] to-red-500 transition-all duration-500"
                                            :style="'width: ' + (scores['{{ $fieldName }}'] ? (scores['{{ $fieldName }}'] / 5 * 100) : 0) + '%'"></div>
                                    </div>
                                    <span class="font-extrabold text-sm text-[#8b1515] w-8 text-right"
                                        x-text="scores['{{ $fieldName }}'] ?? '-'"></span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Score + Submit --}}
                <div class="w-full md:w-80 lg:w-96 bg-gradient-to-br from-[#7a1111] to-[#8b1515] p-6 md:p-8 flex flex-col justify-between text-white relative overflow-hidden flex-shrink-0">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full opacity-[0.03] -mr-10 -mt-10 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-black rounded-full opacity-[0.08] -ml-12 -mb-12 pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-8">
                            <span class="text-red-200 text-xs font-bold uppercase tracking-widest">Progress</span>
                            <div class="bg-black/20 px-3 py-1.5 rounded-lg text-sm font-bold border border-white/10 shadow-inner">
                                <span x-text="filledCount()" :class="isComplete() ? 'text-green-400' : 'text-white'"></span>
                                <span class="text-red-300">/{{ $totalItems }}</span>
                            </div>
                        </div>

                        <div class="mb-10">
                            <span class="text-red-200 text-xs font-bold uppercase tracking-widest block mb-3">Rata-rata Nilai</span>
                            <div class="flex flex-wrap items-end gap-3">
                                <span class="text-6xl font-black leading-none tracking-tighter" x-text="totalScore()"></span>
                                <span class="text-xs font-bold px-2.5 py-1.5 rounded bg-white/20 text-white backdrop-blur-sm mb-1.5 shadow-sm border border-white/20 whitespace-nowrap"
                                    x-text="totalLabel()" x-show="isComplete()" style="display: none;"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="relative z-10 w-full h-14 bg-white text-[#8b1515] hover:bg-gray-50 hover:shadow-lg hover:-translate-y-0.5 disabled:bg-white/10 disabled:text-red-200 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none font-extrabold rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-md"
                        :disabled="!canSubmit()"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        SIMPAN PENILAIAN
                    </button>
                </div>
            </div>

        </form>

        {{-- Custom Confirm Modal --}}
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" @click.self="showConfirmModal = false">
            <div x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative">
                
                <div class="mx-auto mb-5 flex justify-center">
                    <svg width="68" height="68" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_8px_12px_rgba(140,10,10,0.25)]">
                        <path d="M10.29 3.86L1.82 18A2 2 0 003.54 21h16.92a2 2 0 001.72-3L13.71 3.86a2 2 0 00-3.42 0z" fill="#8b1515"/>
                        <path d="M12 9v4" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="12" cy="16.5" r="1.5" fill="white"/>
                    </svg>
                </div>
                
                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">Simpan penilaian?</h2>
                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">Nilai yang telah disimpan tidak dapat diubah.</p>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="processSubmit()" class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">Iya</button>
                    <button type="button" @click="showConfirmModal = false" class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 active:scale-95 rounded-xl shadow-md transition-all border-2 border-[#8b1515]">Tidak</button>
                </div>
            </div>
        </div>

    @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('wawancaraForm', () => ({
        showConfirmModal: false,
        formEvent: null,
        scores: {
            @for($i = 1; $i <= $totalItems; $i++)
                'k1_item_{{ $i }}': {{ $detailNilai["k1_item_{$i}"] ?? 'null' }},
            @endfor
        },
        rekomendasi: '{{ old('rekomendasi', '') }}',
        prodiTujuan: '{{ old('prodi_tujuan', '') }}',
        bidangKeahlian: '{{ old('bidang_keahlian', $penilaian->bidang_keahlian ?? '') }}',
        statusRekrutmen: '{{ old('status_rekrutmen', '') }}',
        hasStatusRekrutmen: {{ !empty($statusRekrutmenOptions) ? 'true' : 'false' }},

        setScore(field, val) {
            this.scores[field] = this.scores[field] === val ? null : val;
        },

        filledCount() {
            let c = 0;
            for (let i = 1; i <= {{ $totalItems }}; i++) {
                if (this.scores['k1_item_' + i] !== null) c++;
            }
            return c;
        },

        isComplete() {
            return this.filledCount() === {{ $totalItems }};
        },

        canSubmit() {
            if (!this.isComplete()) return false;
            if (this.rekomendasi === '') return false;
            if (this.prodiTujuan === '') return false;
            if (this.bidangKeahlian.trim() === '') return false;
            if (this.hasStatusRekrutmen && this.statusRekrutmen === '') return false;
            return true;
        },

        totalScore() {
            if (!this.isComplete()) return '-';
            let sum = 0;
            for (let i = 1; i <= {{ $totalItems }}; i++) {
                sum += Number(this.scores['k1_item_' + i]) || 0;
            }
            return (sum / {{ $totalItems }}).toFixed(2);
        },

        totalLabel() {
            let s = this.totalScore();
            if (s === '-') return '';
            s = parseFloat(s);
            if (s >= 4.5) return 'Sangat Baik';
            if (s >= 3.5) return 'Baik';
            if (s >= 2.5) return 'Cukup';
            if (s >= 1.5) return 'Kurang';
            return 'Sangat Kurang';
        },

        submitForm(e) {
            this.formEvent = e.target;
            this.showConfirmModal = true;
        },

        async processSubmit() {
            if (!this.formEvent) return;
            const form = this.formEvent;
            const submitBtn = form.querySelector('button[type="submit"]');

            this.showConfirmModal = false;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'MENYIMPAN...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    if (window.opener && !window.opener.closed) {
                        window.opener.location.reload();
                    }
                    window.close();
                    // Fallback if window.close() is blocked
                    window.location.href = "{{ route('penguji.pengujian.show', $jadwal->id) }}";
                } else {
                    throw new Error('Terjadi kesalahan saat menyimpan data.');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'SIMPAN PENILAIAN';
            }
        }
    }))
})
</script>
@endsection
