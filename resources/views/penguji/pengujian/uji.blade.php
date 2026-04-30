@extends('layouts.admin')

@section('title', 'Form Pengujian')

@section('content')
@php
    $pelamar = $jadwal->pelamar;
    $lowongan = $jadwal->lowongan;
    $penilaian = $jadwal->penilaian;
    $isWawancara = $jadwal->tipe_seleksi == 'tahap1';

    if ($isWawancara) {
        $rubriks = [
            'kategori_1' => [
                'title' => 'Kompetensi Kepribadian dan Integritas',
                'items' => [
                    'Kandidat menunjukkan motivasi dan komitmen yang kuat untuk berkarir sebagai dosen.',
                    'Kandidat memiliki pemahaman yang baik tentang etika akademik (anti-plagiarisme, objektivitas).',
                    'Kandidat menunjukkan kestabilan emosi dan ketenangan saat merespons pertanyaan yang menekan.',
                    'Kandidat memiliki kemampuan komunikasi interpersonal yang sopan, empatik, dan profesional.',
                    'Kandidat menunjukkan rekam jejak kejujuran dan integritas dari pengalaman kerja atau studi sebelumnya.'
                ]
            ],
            'kategori_2' => [
                'title' => 'Visi Tri Dharma dan Profesionalisme',
                'items' => [
                    'Kandidat memiliki pemetaan rencana penelitian yang relevan dengan roadmap program studi.',
                    'Kandidat menunjukkan potensi atau rekam jejak yang baik dalam publikasi ilmiah.',
                    'Kandidat memiliki gagasan yang jelas dan aplikatif mengenai program pengabdian kepada masyarakat.',
                    'Kandidat memahami visi-misi institusi dan mampu menyelaraskannya dengan target karir pribadinya.',
                    'Kandidat memiliki pengalaman praktis atau akademis yang sangat mendukung mata kuliah yang akan diampu.'
                ]
            ],
            'kategori_3' => [
                'title' => 'Kemampuan Adaptasi dan Kolaborasi',
                'items' => [
                    'Kandidat menunjukkan sikap terbuka dan mau menerima kritik atau saran yang konstruktif.',
                    'Kandidat memiliki kemampuan bekerja sama yang baik dalam tim (teamwork) dengan rekan sejawat.',
                    'Kandidat menunjukkan fleksibilitas dan kemampuan beradaptasi dengan perubahan lingkungan kerja atau kurikulum.',
                    'Kandidat memiliki inisiatif tinggi dalam mencari solusi ketika dihadapkan pada suatu masalah (problem-solving).',
                    'Kandidat mampu menyampaikan ide dan gagasan secara sistematis, jelas, dan mudah dipahami oleh panelis.'
                ]
            ]
        ];
    } else {
        $rubriks = [
            'kategori_1' => [
                'title' => 'Penguasaan Materi dan Kedalaman Substansi',
                'items' => [
                    'Kandidat menjelaskan konsep atau teori dengan sangat akurat dan terbebas dari miskonsepsi (kesalahan materi).',
                    'Kandidat mampu memberikan contoh kasus atau studi lapangan yang sangat relevan dengan materi.',
                    'Kandidat mampu mengaitkan materi pembelajaran dengan perkembangan ilmu pengetahuan atau tren industri terkini.',
                    'Tingkat kedalaman materi yang disampaikan sangat sesuai dengan kapasitas pemahaman audiens (mahasiswa).',
                    'Kandidat mampu merespons pertanyaan penguji dengan jawaban yang komprehensif, logis, dan memuaskan.'
                ]
            ],
            'kategori_2' => [
                'title' => 'Keterampilan Pedagogik (Penyampaian dan Interaksi)',
                'items' => [
                    'Kandidat memiliki artikulasi suara, intonasi, dan volume yang sangat jelas dan nyaman didengar.',
                    'Gestur tubuh dan kontak mata kandidat menunjukkan kepercayaan diri dan penguasaan panggung yang sangat baik.',
                    'Struktur pengajaran (pembukaan, inti materi, dan penutup/kesimpulan) tersusun dengan sangat sistematis.',
                    'Kandidat menggunakan metode pengajaran dua arah yang interaktif dan memancing partisipasi aktif audiens.',
                    'Kandidat mengelola alokasi waktu pengajaran (time management) dengan sangat efisien sesuai batas waktu yang diberikan.'
                ]
            ],
            'kategori_3' => [
                'title' => 'Pemanfaatan Media Pembelajaran',
                'items' => [
                    'Desain presentasi (slide) atau alat peraga visual terlihat sangat profesional, menarik, dan mudah dibaca.',
                    'Media pembelajaran yang digunakan sangat efektif dalam memperjelas bagian materi yang rumit atau abstrak.',
                    'Kandidat menunjukkan kemahiran dalam mengoperasikan perangkat teknologi pendidikan (software/hardware) secara lancar.',
                    'Media pembelajaran terintegrasi secara mulus ke dalam alur presentasi, bukan sekadar tempelan.',
                    'Terdapat unsur kreativitas atau inovasi tinggi dalam cara kandidat menyajikan informasi melalui media pembelajarannya.'
                ]
            ]
        ];
    }

    $sesiInfo = \App\Models\JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$jadwal->sesi] ?? null;
    $today = \Carbon\Carbon::today();
    $canTest = $today->greaterThanOrEqualTo(\Carbon\Carbon::parse($jadwal->tanggal));
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="penilaianForm()">

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-bold text-white">{{ $isWawancara ? 'Form Penilaian Wawancara' : 'Form Penilaian Micro Teaching' }}</h1>
                    <p class="text-red-200 text-sm mt-1">{{ $pelamar->nama }} · {{ $jadwal->tanggal->format('d M Y') }} · Sesi {{ $jadwal->sesi }} ({{ $sesiInfo['start'] ?? '-' }} - {{ $sesiInfo['end'] ?? '-' }})</p>
                </div>
                @if($penilaian)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Sudah Dinilai
                    </span>
                @endif
            </div>
        </div>

        @if(!$canTest)
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Belum Waktunya Diuji</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto">Jadwal seleksi ini pada tanggal <strong>{{ $jadwal->tanggal->format('d M Y') }}</strong>. Form penilaian akan terbuka pada tanggal tersebut.</p>
            </div>
        @else
            <form action="{{ route('penguji.pengujian.storeNilai', $jadwal->id) }}" method="POST" class="p-6 md:p-8 space-y-8">
                @csrf

                @foreach(['kategori_1', 'kategori_2', 'kategori_3'] as $idx => $key)
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-gray-900">{{ $rubriks[$key]['title'] }}</h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                @foreach($rubriks[$key]['items'] as $item)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="w-full md:w-32 flex-shrink-0">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nilai (1-100)</label>
                            <input type="number" name="{{ $key }}" x-model.number="k{{ $idx + 1 }}" min="0" max="100" required
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-lg font-bold text-center focus:ring-2 focus:ring-[#8b1515] focus:border-[#8b1515] transition-all">
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Catatan & Kalkulasi Total -->
                <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" rows="4" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#8b1515] focus:border-[#8b1515] transition-all placeholder-gray-400" placeholder="Berikan catatan mengenai kandidat...">{{ old('catatan', $penilaian->catatan ?? '') }}</textarea>
                    </div>
                    <div class="w-full md:w-48 bg-[#8b1515] rounded-2xl p-6 text-white text-center flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/10 opacity-50 transform -skew-x-12"></div>
                        <span class="text-sm font-medium text-red-200 mb-1 relative z-10">Total Nilai</span>
                        <span class="text-5xl font-extrabold relative z-10" x-text="kalkulasiTotal()">0</span>
                        <span class="text-xs text-red-300 mt-2 relative z-10" x-text="keterangan()"></span>
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('penilaianForm', () => ({
        k1: {{ old('kategori_1', $penilaian->kategori_1 ?? 'null') }},
        k2: {{ old('kategori_2', $penilaian->kategori_2 ?? 'null') }},
        k3: {{ old('kategori_3', $penilaian->kategori_3 ?? 'null') }},

        kalkulasiTotal() {
            let n1 = Number(this.k1) || 0;
            let n2 = Number(this.k2) || 0;
            let n3 = Number(this.k3) || 0;
            if(n1 === 0 && n2 === 0 && n3 === 0 && this.k1 === null) return '-';
            return Math.round((n1 + n2 + n3) / 3);
        },

        keterangan() {
            let total = this.kalkulasiTotal();
            if(total === '-') return '';
            if(total >= 85) return 'Sangat Baik';
            if(total >= 70) return 'Baik';
            if(total >= 50) return 'Cukup';
            return 'Kurang';
        }
    }))
})
</script>
@endsection
