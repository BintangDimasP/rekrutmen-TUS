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
                        @if($lowongan->prodi_prioritas)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_filter(array_map('trim', explode(',', $lowongan->prodi_prioritas))) as $pp)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-white border border-gray-200 text-[12px] font-medium text-gray-600">{{ $pp }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-[13px] text-gray-500 leading-relaxed">-</div>
                        @endif
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                        <div class="text-[10.5px] font-bold text-gray-300 uppercase tracking-wider mb-2">Skill Utama</div>
                        @if($lowongan->skill_dibutuhkan)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_filter(array_map('trim', explode(',', $lowongan->skill_dibutuhkan))) as $sk)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-white border border-gray-200 text-[12px] font-medium text-gray-600">{{ $sk }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-[13px] text-gray-500 leading-relaxed">-</div>
                        @endif
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
            {{-- FORM (selalu tampil; guard email dihandle via JS toast) --}}
            <div>
                <div class="text-[11px] font-bold text-[#8b1515] uppercase tracking-wider pb-2.5 border-b border-gray-100 mb-3">
                    Dokumen Pelengkap
                </div>
                <div class="text-[12px] text-gray-500 mb-6">Data CV, Ijazah, dan KTP akan ditarik otomatis dari profil Anda. Pastikan profil Anda sudah lengkap.</div>

                <form action="{{ route('pelamar.lowongan.storeApply', $lowongan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Surat Lamaran --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                            Surat Lamaran <span class="text-[#8b1515]">*</span>
                        </label>
                        <div class="file-upload-area" onclick="handleUploadClick(event, 'file_surat_lamaran', 'label_surat_lamaran')" style="cursor:pointer;">
                            <div>
                                <div class="text-[13px] font-medium text-gray-500" id="label_surat_lamaran">Upload Berkas</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Format PDF, maks. 5MB</div>
                            </div>
                        </div>
                        <input id="file_surat_lamaran" type="file" name="file_surat_lamaran" accept=".pdf"
                               style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;"
                               onchange="document.getElementById('label_surat_lamaran').textContent = this.files[0] ? '✓ ' + this.files[0].name : 'Upload Berkas'">
                        @error('file_surat_lamaran')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- SK Penyetaraan (Lulusan Luar Negeri) --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                            SK Penyetaraan <span class="text-gray-400 font-normal normal-case">(opsional, bagi lulusan Luar Negeri)</span>
                        </label>
                        <div class="file-upload-area" onclick="handleUploadClick(event, 'file_sk_penyetaraan', 'label_sk_penyetaraan')" style="cursor:pointer;">
                            <div>
                                <div class="text-[13px] font-medium text-gray-500" id="label_sk_penyetaraan">Upload Berkas</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Format PDF, maks. 5MB</div>
                            </div>
                        </div>
                        <input id="file_sk_penyetaraan" type="file" name="file_sk_penyetaraan" accept=".pdf"
                               style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;"
                               onchange="document.getElementById('label_sk_penyetaraan').textContent = this.files[0] ? '✓ ' + this.files[0].name : 'Upload Berkas'">
                        @error('file_sk_penyetaraan')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Surat Pemberhentian (Bekerja di Instansi Lain) --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                            Surat Pemberhentian <span class="text-gray-400 font-normal normal-case">(opsional, apabila bekerja di Instansi Lain)</span>
                        </label>
                        <div class="file-upload-area" onclick="handleUploadClick(event, 'file_surat_pemberhentian', 'label_surat_pemberhentian')" style="cursor:pointer;">
                            <div>
                                <div class="text-[13px] font-medium text-gray-500" id="label_surat_pemberhentian">Upload Berkas</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">Format PDF, maks. 5MB</div>
                            </div>
                        </div>
                        <input id="file_surat_pemberhentian" type="file" name="file_surat_pemberhentian" accept=".pdf"
                               style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;"
                               onchange="document.getElementById('label_surat_pemberhentian').textContent = this.files[0] ? '✓ ' + this.files[0].name : 'Upload Berkas'">
                        @error('file_surat_pemberhentian')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Form Footer --}}
                    <div class="pt-2 flex justify-center">
                        <button type="submit" id="btn-kirim-lamaran"
                                onclick="return validateLamaranForm()"
                                class="btn-kirim w-full sm:w-auto flex-shrink-0 px-8 py-3 bg-[#8b1515] text-white text-[13.5px] font-bold rounded-xl shadow-sm">
                            Ajukan Lamaran
                        </button>
                    </div>

                </form>

                @push('scripts')
                <script>
                    // Flag dari server: apakah email sudah terverifikasi
                    var emailVerified = {{ auth()->user()->hasVerifiedEmail() ? 'true' : 'false' }};
                    var userEmail    = '{{ auth()->user()->email }}';

                    /**
                     * Intercept klik pada area upload.
                     * Jika email belum terverifikasi → tampilkan toast dan batalkan.
                     */
                    function handleUploadClick(event, inputId, labelId) {
                        if (!emailVerified) {
                            event.preventDefault();
                            event.stopPropagation();
                            showLamaranToast(
                                'Email Belum Diverifikasi',
                                'Verifikasi alamat email <strong>' + userEmail + '</strong> terlebih dahulu sebelum mengunggah dokumen.',
                                'warning'
                            );
                            return;
                        }
                        document.getElementById(inputId).click();
                    }

                    function validateLamaranForm() {
                        if (!emailVerified) {
                            showLamaranToast(
                                'Email Belum Diverifikasi',
                                'Verifikasi alamat email <strong>' + userEmail + '</strong> terlebih dahulu sebelum mengajukan lamaran.',
                                'warning'
                            );
                            return false;
                        }
                        var suratInput = document.getElementById('file_surat_lamaran');
                        if (!suratInput.files || suratInput.files.length === 0) {
                            showLamaranToast('Upload Belum Lengkap', 'Surat Lamaran wajib diunggah sebelum mengirim lamaran. Format file: PDF, maksimal 5MB.', 'error');
                            return false;
                        }
                        return true;
                    }

                    function showLamaranToast(title, message, type) {
                        var existing = document.getElementById('lamaran-toast');
                        if (existing) existing.remove();

                        var colors = {
                            error:   { bar: '#ef4444', icon: '#ef4444' },
                            warning: { bar: '#f59e0b', icon: '#f59e0b' },
                            success: { bar: '#22c55e', icon: '#22c55e' },
                            info:    { bar: '#3b82f6', icon: '#3b82f6' }
                        };
                        var c = colors[type] || colors.error;
                        var icons = {
                            error:   '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
                            warning: '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
                            success: '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
                            info:    '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                        };

                        var toast = document.createElement('div');
                        toast.id = 'lamaran-toast';
                        toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:9999;display:flex;align-items:center;gap:12px;min-width:320px;max-width:420px;background:white;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.12);border:1px solid #e5e7eb;overflow:hidden;animation:toastSlideIn 0.3s ease forwards;';
                        toast.innerHTML =
                            '<div style="width:5px;align-self:stretch;background:' + c.bar + ';flex-shrink:0;border-radius:8px 0 0 8px;"></div>' +
                            '<div style="width:36px;height:36px;border-radius:50%;background:' + c.icon + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;margin:12px 0 12px 10px;">' + (icons[type] || icons.error) + '</div>' +
                            '<div style="flex:1;padding:12px 8px 12px 0;">' +
                                '<div style="font-size:0.875rem;font-weight:700;color:#1f2937;margin-bottom:2px;">' + title + '</div>' +
                                '<div style="font-size:0.75rem;color:#6b7280;line-height:1.5;">' + message + '</div>' +
                            '</div>' +
                            '<button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;padding:8px;margin-right:8px;opacity:0.4;font-size:1.1rem;line-height:1;color:#374151;">&times;</button>';

                        if (!document.getElementById('toast-anim-style')) {
                            var style = document.createElement('style');
                            style.id = 'toast-anim-style';
                            style.textContent = '@keyframes toastSlideIn{from{opacity:0;transform:translateX(60px)}to{opacity:1;transform:translateX(0)}}';
                            document.head.appendChild(style);
                        }

                        document.body.appendChild(toast);
                        setTimeout(function() { if (toast.parentElement) toast.remove(); }, 5500);
                    }
                </script>
                @endpush
            </div>

            @endif {{-- end: existing / tanggal_tutup / else --}}
        </div>
    </div>

</div>
@endsection
