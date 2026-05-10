@extends('layouts.admin')

@section('title', 'Detail Lowongan — ' . $lowongan->nama_posisi)

@section('content')

<style>
    .card-header-red {
        background: linear-gradient(135deg, #8b1515 0%, #6b0f0f 100%);
        position: relative; overflow: hidden;
    }
    .card-header-red::before {
        content: ''; position: absolute; top: -50px; right: -50px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.05); border-radius: 50%;
        pointer-events: none;
    }
    .card-header-red::after {
        content: ''; position: absolute; bottom: -80px; right: 60px;
        width: 250px; height: 250px;
        background: rgba(255,255,255,0.04); border-radius: 50%;
        pointer-events: none;
    }

    .info-row-grid {
        display: grid; grid-template-columns: repeat(4,1fr);
        border-radius: 14px; overflow: hidden;
        border: 1px solid #ebebeb;
    }
    .info-row-cell { padding: 14px 16px; background: #fafafa; border-right: 1px solid #ebebeb; }
    .info-row-cell:last-child { border-right: none; }
    @media(max-width:640px) {
        .info-row-grid { grid-template-columns: repeat(2,1fr); }
        .info-row-cell:nth-child(2) { border-right: none; }
        .info-row-cell:nth-child(3),
        .info-row-cell:nth-child(4) { border-top: 1px solid #ebebeb; }
    }

    .desc-item {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 11px 14px;
        background: #fafafa; border-radius: 10px;
        border-left: 3px solid #8b1515;
        font-size: 13.5px; color: #444; line-height: 1.6;
    }
    .desc-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: #8b1515; flex-shrink: 0; margin-top: 8px;
    }

    .file-upload-area {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        border: 1.5px dashed #ddd; border-radius: 12px;
        background: #fafafa; cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .file-upload-area:hover { border-color: #8b1515; background: #fff8f8; }
    .file-icon-box {
        width: 40px; height: 40px; border-radius: 10px;
        background: #f0f0f0; display: flex; align-items: center;
        justify-content: center; flex-shrink: 0; font-size: 18px;
    }

    .btn-kirim { transition: background 0.2s, transform 0.15s, box-shadow 0.2s; }
    .btn-kirim:hover {
        background: #6b0f0f !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(139,21,21,0.28);
    }
    .btn-kirim:active { transform: translateY(0); }
</style>

<div class="max-w-3xl mx-auto pb-16">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('pelamar.lowongan.index') }}" class="hover:text-[#8b1515] transition-colors font-medium">Lowongan</a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-semibold text-gray-800 truncate">{{ $lowongan->nama_posisi }}</span>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-[22px] border border-gray-200 overflow-hidden shadow-sm">

        {{-- RED HEADER --}}
        <div class="card-header-red px-8 py-7">
            <h1 class="text-[22px] font-bold text-white leading-snug relative z-10">{{ $lowongan->nama_posisi }}</h1>
            <p class="text-white/70 text-[13.5px] mt-1 mb-5 relative z-10">
                {{ $lowongan->prodi->nama ?? 'Semua Program Studi' }} — Telkom University Surabaya
            </p>
            <div class="flex flex-wrap gap-2 relative z-10">
                <span class="px-3 py-1 rounded-full text-[11.5px] font-medium text-white border border-white/20 bg-white/10">{{ $lowongan->jenjang_minimal }}</span>
                <span class="px-3 py-1 rounded-full text-[11.5px] font-medium text-white border border-white/20 bg-white/10">IPK ≥ {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                <span class="px-3 py-1 rounded-full text-[11.5px] font-medium text-white border border-white/20 bg-white/10">Full-Time</span>
                <span class="px-3 py-1 rounded-full text-[11.5px] font-medium text-white border border-white/20 bg-white/10">{{ $lowongan->kuota }} Kuota</span>
                <span class="px-3 py-1 rounded-full text-[11.5px] font-medium text-yellow-200 border border-yellow-200/30 bg-yellow-100/10">
                    Tutup {{ $lowongan->tanggal_tutup->format('d M Y') }}
                </span>
            </div>
        </div>

        {{-- BODY --}}
        <div class="px-8 py-7 space-y-7">

            {{-- INFO ROW --}}
            <div class="info-row-grid">
                <div class="info-row-cell">
                    <div class="text-[10px] font-bold text-gray-300 uppercase tracking-wider mb-1.5">Pendidikan</div>
                    <div class="text-[14.5px] font-semibold text-gray-800">{{ $lowongan->jenjang_minimal }}</div>
                </div>
                <div class="info-row-cell">
                    <div class="text-[10px] font-bold text-gray-300 uppercase tracking-wider mb-1.5">Minimal IPK</div>
                    <div class="text-[14.5px] font-semibold text-gray-800">{{ number_format($lowongan->minimal_ipk, 2) }}</div>
                </div>
                <div class="info-row-cell">
                    <div class="text-[10px] font-bold text-gray-300 uppercase tracking-wider mb-1.5">Kuota</div>
                    <div class="text-[14.5px] font-semibold text-gray-800">{{ $lowongan->kuota }} Posisi</div>
                </div>
                <div class="info-row-cell">
                    <div class="text-[10px] font-bold text-gray-300 uppercase tracking-wider mb-1.5">Batas Akhir</div>
                    <div class="text-[14.5px] font-semibold text-[#8b1515]">{{ $lowongan->tanggal_tutup->format('d M Y') }}</div>
                </div>
            </div>

            {{-- KUALIFIKASI KHUSUS --}}
            <div>
                <div class="text-[11px] font-bold text-[#8b1515] uppercase tracking-wider pb-2.5 border-b border-gray-100 mb-3">Kualifikasi Khusus</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <div class="text-[10.5px] font-bold text-gray-300 uppercase tracking-wider mb-2">Prodi Linear / Prioritas</div>
                        <div class="text-[13px] text-gray-500 leading-relaxed">{{ $lowongan->prodi_prioritas ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <div class="text-[10.5px] font-bold text-gray-300 uppercase tracking-wider mb-2">Skill Utama</div>
                        <div class="text-[13px] text-gray-500 leading-relaxed">{{ $lowongan->skill_dibutuhkan ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- DESKRIPSI DARI DATABASE --}}
            @if($lowongan->deskripsi)
            @php
                $lines = explode("\n", $lowongan->deskripsi);
                $sections = [];
                $currentSection = ['title' => null, 'items' => []];

                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '') continue;

                    if (str_starts_with($trimmed, '-')) {
                        // Ini adalah item list
                        $currentSection['items'][] = ltrim($trimmed, '- ');
                    } elseif (str_ends_with($trimmed, ':')) {
                        // Ini adalah judul section baru (berakhir dengan titik dua)
                        if ($currentSection['title'] !== null || !empty($currentSection['items'])) {
                            $sections[] = $currentSection;
                        }
                        $currentSection = ['title' => $trimmed, 'items' => []];
                    } elseif (!empty($currentSection['items'])) {
                        // Baris lanjutan (misal dalam kurung) → gabung ke item sebelumnya
                        $lastIdx = count($currentSection['items']) - 1;
                        $currentSection['items'][$lastIdx] .= ' ' . $trimmed;
                    } else {
                        // Fallback: judul section baru
                        if ($currentSection['title'] !== null || !empty($currentSection['items'])) {
                            $sections[] = $currentSection;
                        }
                        $currentSection = ['title' => $trimmed, 'items' => []];
                    }
                }
                if (!empty($currentSection['items'])) {
                    $sections[] = $currentSection;
                }
            @endphp

            <div class="space-y-6">
            @foreach($sections as $i => $section)
            <div class="{{ $i > 0 ? 'pt-4 border-t border-gray-100' : '' }}">
                @if($section['title'])
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-1 h-4 rounded-full bg-[#8b1515] flex-shrink-0"></div>
                    <span class="text-[11px] font-bold text-[#8b1515] uppercase tracking-wider">{{ $section['title'] }}</span>
                </div>
                @endif
                @if(!empty($section['items']))
                <div class="flex flex-col gap-2">
                    @foreach($section['items'] as $item)
                    <div class="desc-item"><div class="desc-dot"></div><span>{{ $item }}</span></div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
            </div>
            @endif

            <hr class="border-gray-100">

            {{-- STATUS / FORM --}}
            @if($existing)
            <div class="rounded-2xl border border-green-200 bg-green-50 p-8 text-center">
                <div class="text-lg font-bold text-green-700 mb-1">Lamaran Terkirim</div>
                <p class="text-sm text-green-600 mb-4">
                    Anda telah melamar posisi ini
                    @if($existing->created_at) pada {{ $existing->created_at->format('d M Y') }}@endif.
                </p>
                <a href="{{ route('pelamar.history.index') }}"
                   class="inline-block px-6 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-colors">
                    Lihat Status Lamaran
                </a>
            </div>

            @elseif($lowongan->tanggal_tutup < now())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center">
                <div class="text-lg font-bold text-[#8b1515] mb-1">Pendaftaran Ditutup</div>
                <p class="text-sm text-red-400">Batas waktu pendaftaran untuk posisi ini telah berakhir.</p>
            </div>

            @else
            {{-- FORM --}}
            <div>
                <div class="text-[11px] font-bold text-[#8b1515] uppercase tracking-wider pb-2.5 border-b border-gray-100 mb-3">
                    Kirim Lamaran
                </div>
                <div class="text-[12px] text-gray-500 mb-6">Data CV, Ijazah, dan KTP akan ditarik otomatis dari profil Anda. Pastikan profil Anda sudah lengkap.</div>

                <form action="{{ route('pelamar.lowongan.storeApply', $lowongan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Surat Lamaran --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                            Surat Lamaran <span class="text-[#8b1515]">*</span>
                        </label>
                        <div class="file-upload-area" onclick="document.getElementById('file_surat_lamaran').click()" style="cursor:pointer;">
                            <div class="file-icon-box">📄</div>
                            <div>
                                <div class="text-[13px] font-medium text-gray-500" id="label_surat_lamaran">Pilih file atau klik di sini</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Format PDF, maks. 5MB</div>
                            </div>
                        </div>
                        <input id="file_surat_lamaran" type="file" name="file_surat_lamaran" accept=".pdf"
                               style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;"
                               onchange="document.getElementById('label_surat_lamaran').textContent = this.files[0] ? '✓ ' + this.files[0].name : 'Pilih file atau klik di sini'">
                        @error('file_surat_lamaran')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Berkas Pendukung --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                            Berkas Pendukung
                            <span class="text-gray-300 font-normal normal-case text-[11px] tracking-normal">(opsional)</span>
                        </label>
                        <div class="file-upload-area" onclick="document.getElementById('file_berkas_pendukung').click()" style="cursor:pointer;">
                            <div class="file-icon-box">📎</div>
                            <div>
                                <div class="text-[13px] font-medium text-gray-500" id="label_berkas_pendukung">Pilih file atau klik di sini</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Sertifikat, portofolio, dll. (PDF)</div>
                            </div>
                        </div>
                        <input id="file_berkas_pendukung" type="file" name="file_berkas_pendukung" accept=".pdf"
                               style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;"
                               onchange="document.getElementById('label_berkas_pendukung').textContent = this.files[0] ? '✓ ' + this.files[0].name : 'Pilih file atau klik di sini'">
                        @error('file_berkas_pendukung')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Catatan Tambahan</label>
                        <textarea name="catatan" rows="3"
                                  placeholder="Tambahkan pesan singkat jika perlu..."
                                  class="w-full px-4 py-3 text-[13px] text-gray-600 bg-gray-50 border border-gray-200 rounded-xl outline-none resize-none
                                         focus:border-[#8b1515] focus:ring-2 focus:ring-[#8b1515]/10 focus:bg-white transition-all font-[inherit]"></textarea>
                    </div>

                    {{-- Form Footer --}}
                    <div class="pt-2 flex sm:flex-row flex-col sm:items-center items-start justify-between gap-4">
                        <p class="text-[11.5px] text-gray-400 leading-relaxed sm:max-w-md">
                            Dengan mengirim lamaran, Anda menyetujui bahwa data profil Anda akan digunakan dalam proses seleksi.
                        </p>
                        <button type="submit" id="btn-kirim-lamaran"
                                onclick="return validateLamaranForm()"
                                class="btn-kirim w-full sm:w-auto flex-shrink-0 px-8 py-3 bg-[#8b1515] text-white text-[13.5px] font-bold rounded-xl shadow-sm">
                            Kirim Lamaran Sekarang
                        </button>
                    </div>

                </form>

                @push('scripts')
                <script>
                    function validateLamaranForm() {
                        var suratInput = document.getElementById('file_surat_lamaran');
                        if (!suratInput.files || suratInput.files.length === 0) {
                            alert('Surat Lamaran wajib diunggah (format PDF).');
                            return false;
                        }
                        return true;
                    }
                </script>
                @endpush
            </div>
            @endif

        </div>
    </div>

</div>
@endsection