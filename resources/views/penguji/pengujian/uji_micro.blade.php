@extends('layouts.admin')


@section('title', 'Form Penilaian Micro Teaching')

@section('content')
@php
    $pelamarLive = $jadwal->pelamar;
    $lamaran = \App\Models\Lamaran::where('pelamar_id', $pelamarLive->id)
        ->where('lowongan_id', $jadwal->lowongan_id)
        ->first();
    $pelamar  = $lamaran ? $lamaran->effective_pelamar : $pelamarLive;
    $lowongan = $jadwal->lowongan;
    $penilaian = $jadwal->penilaian;
    $detailNilai = $penilaian->detail_nilai ?? [];

    $rubriks = [
        'kategori_1' => [
            'title' => 'Perencanaan Pembelajaran',
            'items' => [
                'Calon dosen menyampaikan rencana pembelajaran yang mencakup materi, tujuan, dan aturan kegiatan pembelajaran serta penilaian hasil belajar.',
                'Calon dosen menyampaikan outline mengenai materi yang akan disampaikan.',
            ]
        ],
        'kategori_2' => [
            'title' => 'Penguasaan Materi',
            'items' => [
                'Calon dosen menunjukkan penguasaan materi pembelajaran.',
                'Materi yang disampaikan terupdate dengan isu terkini dan relevan terhadap kebutuhan kompetensi yang ditetapkan prodi.',
                'Calon dosen mengaitkan materi dengan keilmuan lain yang relevan.',
            ]
        ],
        'kategori_3' => [
            'title' => 'Sistematika (Kemudahan Dipahami)',
            'items' => [
                'Calon dosen menjelaskan materi secara sistematis / runtut.',
                'Calon dosen menjelaskan materi dengan memberikan contoh konkret/nyata.',
                'Calon dosen menggunakan metode mengajar yang variatif (ceramah, studi kasus, eksperimen, maupun lainnya).',
                'Calon dosen menggunakan bahasa lisan dan tulis secara jelas, baik, dan benar.',
                'Calon dosen mengkolaborasikan beberapa media dan atau software dalam penyampaian materi.',
                'Calon dosen memberikan refleksi dari materi yang disampaikan.',
            ]
        ],
        'kategori_4' => [
            'title' => 'Pengelolaan Kelas dan Interaksi',
            'items' => [
                'Calon dosen memberikan kesempatan untuk adanya interaksi (tanya jawab dan diskusi).',
                'Calon dosen mampu menciptakan kelas yang interaktif dan menarik perhatian.',
                'Calon dosen melaksanakan pembelajaran sesuai dengan alokasi waktu yang direncanakan.',
            ]
        ],
        'kategori_5' => [
            'title' => 'Sikap dan Etika',
            'items' => [
                'Calon dosen berpakaian sopan dan bersikap profesional selama melaksanakan pembelajaran.',
            ]
        ],
    ];

    // item counts per kategori
    $itemCounts = [1 => 2, 2 => 3, 3 => 6, 4 => 3, 5 => 1];
    $totalItems = array_sum($itemCounts); // 15

    $prodis = \App\Models\Prodi::orderBy('nama')->get();

    $kelompokKeahlianOptions = [
        'scout'  => 'Smart Computing Technology (SCoT)',
        'ethes'  => 'Electrical Engineering and Advanced Technologies (ETHES)',
        'riib'   => 'Rekayasa Industri dan Inovasi Bisnis (RIIB)',
    ];

    $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
    $today = \Carbon\Carbon::today();
    $canTest = $today->greaterThanOrEqualTo(\Carbon\Carbon::parse($jadwal->tanggal));
@endphp

<div class="max-w-5xl mx-auto pb-24" x-data="microForm()">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 mb-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-50 rounded-full blur-3xl -mr-20 -mt-20 opacity-50 pointer-events-none"></div>
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#8b1515] to-[#6e1010] text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-900/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-red-50 text-[#8b1515] text-xs font-bold uppercase tracking-wider mb-2 border border-red-100">
                        Micro Teaching
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

        <!-- Scale legend -->
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
        <form id="microForm" action="{{ route('penguji.pengujian.storeNilai', $jadwal->id) }}" method="POST">
            @csrf

            <!-- Rubrik Penilaian -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">

                @foreach($rubriks as $key => $rubrik)
                @php
                    $kNum = (int) str_replace('kategori_', '', $key);
                @endphp
                <!-- Category Header -->
                <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] {{ !$loop->last ? 'border-b border-red-800' : '' }} px-5 md:px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-white font-bold text-lg border border-white/20 flex-shrink-0">
                        {{ $kNum }}
                    </div>
                    <h3 class="text-white font-bold text-base md:text-lg leading-tight">{{ $rubrik['title'] }}</h3>
                </div>

                <!-- Questions -->
                <div class="divide-y divide-gray-100 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                    @foreach($rubrik['items'] as $itemIdx => $item)
                    @php $fieldName = "k{$kNum}_item_" . ($itemIdx + 1); @endphp
                    <div class="p-6 md:p-8 flex flex-col gap-6 hover:bg-gray-50/50 transition-colors">
                        <div class="flex-1">
                            <p class="text-[15px] font-medium text-gray-800 leading-relaxed">
                                <span class="text-[#8b1515] font-extrabold mr-2">{{ $itemIdx + 1 }}.</span>
                                {{ $item }}
                            </p>
                        </div>
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
                @endforeach
            </div>

            <!-- Catatan & Info Tambahan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] px-5 md:px-6 py-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h3 class="text-white font-bold text-base">Catatan & Informasi Tambahan</h3>
                </div>

                <div class="p-6 md:p-8 space-y-6">
                    
                    <!-- Rekomendasi -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Rekomendasi <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            @foreach([
                                'direkomendasikan'          => ['label' => 'Direkomendasikan',           'color' => 'green'],
                                'tidak_direkomendasikan'    => ['label' => 'Tidak Direkomendasikan',     'color' => 'red'],
                                'perlu_dipertimbangkan'     => ['label' => 'Masih Perlu Dipertimbangkan','color' => 'yellow'],
                            ] as $val => $opt)
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="radio" name="rekomendasi" value="{{ $val }}"
                                    x-model="rekomendasi"
                                    class="w-4 h-4 accent-[#8b1515] cursor-pointer"
                                    {{ old('rekomendasi') === $val ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-[#8b1515] transition-colors">{{ $opt['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('rekomendasi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rekomendasi Prodi Tujuan -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Rekomendasi Prodi Tujuan <span class="text-red-500">*</span></label>
                            <select name="prodi_tujuan"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white appearance-none cursor-pointer">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->nama }}" {{ old('prodi_tujuan') === $prodi->nama ? 'selected' : '' }}>
                                        {{ $prodi->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('prodi_tujuan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kelompok Keahlian -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kelompok Keahlian <span class="text-red-500">*</span></label>
                            <select name="kelompok_keahlian"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white appearance-none cursor-pointer">
                                <option value="">-- Pilih Kelompok Keahlian --</option>
                                @foreach($kelompokKeahlianOptions as $val => $label)
                                    <option value="{{ $val }}" {{ old('kelompok_keahlian') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelompok_keahlian')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bidang Keahlian Kandidat -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bidang Keahlian Kandidat <span class="text-red-500">*</span></label>
                        <input type="text" name="bidang_keahlian"
                            value="{{ old('bidang_keahlian') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition"
                            placeholder="Contoh: Machine Learning, Sistem Embedded, Manajemen Operasi...">
                        @error('bidang_keahlian')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Penilaian</label>
                        <textarea name="catatan" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition resize-none"
                            placeholder="Tuliskan catatan penilaian (opsional)...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Validation Error -->
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

            <!-- Summary & Submit -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row mb-6">
                <!-- Category Breakdown -->
                <div class="flex-1 p-6 md:p-8 md:border-r border-gray-100 flex flex-col justify-center">
                    <h3 class="text-sm font-extrabold text-gray-800 mb-5 flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Rincian Skor per Kategori
                    </h3>
                    <div class="space-y-3">
                        @foreach($rubriks as $key => $rubrik)
                        @php $kNum = (int) str_replace('kategori_', '', $key); @endphp
                        <div class="flex items-center justify-between p-3.5 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3 pr-4">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8b1515] font-bold text-sm flex items-center justify-center flex-shrink-0">{{ $kNum }}</div>
                                <span class="text-[13px] md:text-sm font-semibold text-gray-700 line-clamp-1 leading-snug" title="{{ $rubrik['title'] }}">{{ $rubrik['title'] }}</span>
                            </div>
                            <div class="flex items-center gap-4 flex-shrink-0">
                                <div class="hidden sm:block w-24 lg:w-32 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-[#8b1515] to-red-500 transition-all duration-500"
                                        :style="'width: ' + (categoryAvg({{ $kNum }}, {{ $itemCounts[$kNum] }}) === '-' ? 0 : (parseFloat(categoryAvg({{ $kNum }}, {{ $itemCounts[$kNum] }})) / 5 * 100)) + '%'"></div>
                                </div>
                                <div class="w-10 text-right">
                                    <span class="font-extrabold text-base text-[#8b1515]"
                                        x-text="categoryAvg({{ $kNum }}, {{ $itemCounts[$kNum] }}) === '-' ? '-' : categoryAvg({{ $kNum }}, {{ $itemCounts[$kNum] }})"></span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Score Summary & Submit -->
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
                            <div class="flex items-end gap-3">
                                <span class="text-6xl font-black leading-none tracking-tighter" x-text="totalScore()"></span>
                                <span class="text-xs font-bold px-2.5 py-1.5 rounded bg-white/20 text-white backdrop-blur-sm mb-1.5 shadow-sm border border-white/20"
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
    @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('microForm', () => ({
        scores: {
            @php
                $allFields = [];
                foreach ($itemCounts as $k => $count) {
                    for ($i = 1; $i <= $count; $i++) {
                        $allFields[] = "k{$k}_item_{$i}";
                    }
                }
            @endphp
            @foreach($allFields as $field)
                '{{ $field }}': {{ $detailNilai[$field] ?? 'null' }},
            @endforeach
        },
        rekomendasi: '{{ old('rekomendasi', '') }}',

        setScore(field, val) {
            this.scores[field] = this.scores[field] === val ? null : val;
        },

        categoryFilledCount(k, itemCount) {
            let c = 0;
            for (let i = 1; i <= itemCount; i++) {
                if (this.scores['k' + k + '_item_' + i] !== null) c++;
            }
            return c;
        },

        categorySum(k, itemCount) {
            let sum = 0;
            for (let i = 1; i <= itemCount; i++) {
                sum += Number(this.scores['k' + k + '_item_' + i]) || 0;
            }
            return sum;
        },

        categoryAvg(k, itemCount) {
            let filled = this.categoryFilledCount(k, itemCount);
            if (filled === 0) return '-';
            return (this.categorySum(k, itemCount) / filled).toFixed(2);
        },

        filledCount() {
            const counts = @json($itemCounts);
            let c = 0;
            for (const [k, n] of Object.entries(counts)) {
                c += this.categoryFilledCount(parseInt(k), n);
            }
            return c;
        },

        isComplete() {
            return this.filledCount() === {{ $totalItems }};
        },

        canSubmit() {
            return this.isComplete() && this.rekomendasi !== '';
        },

        totalScore() {
            if (!this.isComplete()) return '-';
            const counts = @json($itemCounts);
            let totalAvg = 0;
            let numCategories = Object.keys(counts).length;
            for (const [k, n] of Object.entries(counts)) {
                totalAvg += this.categorySum(parseInt(k), n) / n;
            }
            return (totalAvg / numCategories).toFixed(2);
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
        }
    }))
})
</script>
@endsection
