<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Pelamar — Telkom University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* File upload custom style */
        .file-upload-area {
            display: flex; align-items: center; gap: 0.75rem;
            border: 1.5px dashed #d1d5db; border-radius: 0.5rem;
            padding: 0.75rem 1rem; cursor: pointer;
            background: #fafafa; transition: all .2s;
        }
        .file-upload-area:hover { border-color: #8b1515; background: #fff5f5; }
        .file-upload-area:hover .upload-icon { color: #8b1515; }
        .upload-icon { color: #9ca3af; flex-shrink: 0; transition: color .2s; }

        /* File selected state */
        .file-upload-area.has-file {
            border-style: solid;
            border-color: #8b1515;
            background: #fef2f2;
        }
        .file-upload-area.has-file .upload-icon { color: #8b1515; }
        .file-upload-area.has-file .file-label { display: none; }
        .file-upload-area .file-selected { display: none; }
        .file-upload-area.has-file .file-selected { display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 0; }

        /* Step bubble */
        .step-bubble { transition: background .3s, transform .3s, box-shadow .3s; }
        .step-bubble.active { transform: scale(1.12); }
        .step-bubble.completed .num-label { display: none; }
        .step-bubble:not(.completed) .check-icon { display: none; }

        /* Step content */
        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeInUp 0.4s ease forwards; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Card wrapper transition */
        #cardWrapper {
            transition: max-width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Toast */
        #toast-container {
            position: fixed; top: 1.25rem; right: 1.25rem;
            z-index: 9999; display: flex; flex-direction: column; gap: 0.625rem;
            width: 360px; pointer-events: none;
        }
        .toast {
            pointer-events: all;
            display: flex; position: relative; overflow: hidden;
            background: white; border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            animation: slideIn .3s ease forwards;
            padding: 1.25rem 1rem 1.25rem 4rem;
        }
        .toast::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 6px;
        }
        .toast.removing { animation: slideOut .3s ease forwards; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(60px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(60px); }
        }
        
        .toast-icon { position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        
        .toast-success::before { background: #22c55e; }
        .toast-success .toast-icon { background: #22c55e; }
        
        .toast-error::before { background: #ef4444; }
        .toast-error .toast-icon { background: #ef4444; }
        
        .toast-warning::before { background: #facc15; }
        .toast-warning .toast-icon { background: #facc15; }
        
        .toast-info::before { background: #3b82f6; }
        .toast-info .toast-icon { background: #3b82f6; }

        .toast-content { flex: 1; }
        .toast-title { font-weight: 700; color: #111827; font-size: 0.95rem; margin-bottom: 0.2rem; line-height: 1.2; }
        .toast-message { color: #6b7280; font-size: 0.8rem; line-height: 1.3; }
        
        .toast-close {
            position: absolute; right: 0.75rem; top: 0.75rem;
            cursor: pointer; opacity: 0.4;
            background: none; border: none; font-size: 1.2rem;
            line-height: 1; padding: 0; color: #1f2937; flex-shrink: 0;
            transition: opacity 0.2s;
        }
        .toast-close:hover { opacity: 1; }

        /* Form input focus */
        .form-input:focus {
            outline: none;
            border-color: #8b1515 !important;
            box-shadow: 0 0 0 3px rgba(139,21,21,.1) !important;
        }
        .form-input.error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239,68,68,.1) !important;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

{{-- ── Toast Container ── --}}
<div id="toast-container"></div>

{{-- ── Navbar ── --}}
<nav class="sticky top-0 z-50 flex items-center justify-between px-8 h-[60px] shadow-md bg-[#8b1515]">
    <div class="flex items-center gap-2.5">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                <div class="relative w-[120px] h-14 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('storage/images/logo2.png') }}" alt="Telkom University Logo" class="w-full h-8 object-contain">
                </div>
            </a>
    </div>
    @if(Route::has('login'))
        <a href="{{ route('login') }}" class="text-white text-sm font-semibold px-5 py-2 rounded-lg border border-white/25 bg-[#6b1111] hover:bg-[#7f1d1d] transition">Masuk</a>
    @endif
</nav>

{{-- ── Main Wrapper ── --}}
<div class="flex flex-col items-center pt-16 pb-20 px-4 w-full min-h-[calc(100vh-60px)]">

    {{-- Step Indicator --}}
    <div class="w-full max-w-2xl flex items-center justify-center mb-16" id="stepIndicator">
        @php $steps = [1=>'Akun',2=>'Data Diri',3=>'Pendidikan',4=>'Dokumen',5=>'Akademik']; @endphp
        @foreach($steps as $n => $label)
            <div class="relative flex flex-col items-center">
                <div
                    id="bubble-{{ $n }}"
                    class="step-bubble w-11 h-11 rounded-full flex items-center justify-center text-white text-sm font-bold cursor-default select-none
                    {{ $n === 1 ? 'active bg-[#8b1515] shadow-lg shadow-[#8b1515]/30' : 'bg-gray-300' }}"
                >
                    <span class="num-label">{{ $n }}</span>
                    {{-- Checkmark --}}
                    <svg class="check-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span id="label-{{ $n }}" class="absolute top-12 text-[0.65rem] font-semibold whitespace-nowrap {{ $n===1 ? 'text-[#8b1515]' : 'text-gray-400' }}">
                    {{ $label }}
                </span>
            </div>
            @if($n < 5)
                <div id="line-{{ $n }}" class="flex-1 h-[3px] mx-1 max-w-[72px] rounded bg-gray-300 transition-colors duration-300"></div>
            @endif
        @endforeach
    </div>

    {{-- Form --}}
    <div id="cardWrapper" class="w-full max-w-md transition-all duration-500 ease-in-out">
        <form id="registerForm" action="#" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

        {{-- ═══════════════════════════════════════ --}}
        {{-- STEP 1 — BUAT AKUN                      --}}
        {{-- ═══════════════════════════════════════ --}}
        <div class="step-content active bg-white rounded-xl shadow-md overflow-hidden" id="step-1">
            <div class="bg-[#8b1515] px-7 py-5">
                <h2 class="text-white text-xl font-bold">Buat Akun</h2>
            </div>
            <div class="p-7">
                <div class="grid grid-cols-1 gap-5">
                    {{-- Email --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Email <span class="text-red-600">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="nama@email.com" value="{{ old('email') }}">
                    </div>
                    {{-- Password --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Password <span class="text-red-600">*</span>
                        </label>
                        <input type="password" id="password" name="password" oninput="checkPasswordStrength(this.value)"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Min. 8 karakter">
                        
                        {{-- Password Strength --}}
                        <div class="mt-1 flex gap-1 h-1.5 w-full">
                            <div id="pb-1" class="h-full w-1/4 rounded bg-gray-200 transition-colors duration-300"></div>
                            <div id="pb-2" class="h-full w-1/4 rounded bg-gray-200 transition-colors duration-300"></div>
                            <div id="pb-3" class="h-full w-1/4 rounded bg-gray-200 transition-colors duration-300"></div>
                            <div id="pb-4" class="h-full w-1/4 rounded bg-gray-200 transition-colors duration-300"></div>
                        </div>
                        <span id="password-text" class="text-[0.65rem] text-gray-500 font-medium">Kekuatan password...</span>
                    </div>
                </div>
            </div>
            <div class="px-7 py-4 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="nextStep(1)" class="inline-flex items-center gap-2 bg-[#8b1515] hover:bg-[#6b1111] text-white text-sm font-bold px-7 py-2.5 rounded-lg shadow-md shadow-[#8b1515]/25 transition">
                    Lanjut
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════ --}}
        {{-- STEP 2 — DATA DIRI                      --}}
        {{-- ═══════════════════════════════════════ --}}
        <div class="step-content bg-white rounded-xl shadow-md overflow-hidden" id="step-2">
            <div class="bg-[#8b1515] px-7 py-5">
                <h2 class="text-white text-xl font-bold">Data Diri</h2>
            </div>
            <div class="p-7">
                <div class="grid grid-cols-2 gap-5">
                    {{-- NIK --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="nik" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            NIK (Nomor Induk Kependudukan) <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="nik" name="nik" maxlength="16"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="16 digit NIK sesuai KTP" value="{{ old('nik') }}">
                    </div>
                    {{-- Nama --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="nama" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Nama Lengkap <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="nama" name="nama"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Nama sesuai KTP" value="{{ old('nama') }}">
                    </div>
                    {{-- Tempat & Tanggal Lahir --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="tempat_lahir" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Tempat Lahir <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Kota tempat lahir" value="{{ old('tempat_lahir') }}">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="tanggal_lahir" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Tanggal Lahir <span class="text-red-600">*</span>
                        </label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 transition" value="{{ old('tanggal_lahir') }}">
                    </div>
                    {{-- Telepon & Jenis Kelamin --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="no_telepon" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            No. Telepon <span class="text-red-600">*</span>
                        </label>
                        <input type="tel" id="no_telepon" name="no_telepon"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="08xxxxxxxxxx" value="{{ old('no_telepon') }}">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="jenis_kelamin" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jenis Kelamin <span class="text-red-600">*</span>
                        </label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih —</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    {{-- Kewarganegaraan & Status Pernikahan --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="kewarganegaraan" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Kewarganegaraan <span class="text-red-600">*</span>
                        </label>
                        <select id="kewarganegaraan" name="kewarganegaraan"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih —</option>
                            <option value="WNI" {{ old('kewarganegaraan') == 'WNI' ? 'selected' : '' }}>WNI</option>
                            <option value="WNA" {{ old('kewarganegaraan') == 'WNA' ? 'selected' : '' }}>WNA</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="status_pernikahan" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Status Pernikahan <span class="text-red-600">*</span>
                        </label>
                        <select id="status_pernikahan" name="status_pernikahan"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih —</option>
                            <option value="Belum Kawin" {{ old('status_pernikahan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_pernikahan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                    </div>
                    {{-- Alamat Domisili --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="alamat_domisili" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Alamat Domisili <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alamat_domisili" name="alamat_domisili" rows="2"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 resize-y transition"
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"></textarea>
                    </div>
                    {{-- Alamat KTP --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="alamat_ktp" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Alamat Sesuai KTP <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alamat_ktp" name="alamat_ktp" rows="2"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 resize-y transition"
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos"></textarea>
                    </div>
                </div>
            </div>
            <div class="px-7 py-4 border-t border-gray-100 flex justify-between">
                <button type="button" onclick="prevStep(2)"
                    class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] text-sm font-semibold px-5 py-2.5 rounded-lg bg-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" onclick="nextStep(2)"
                    class="inline-flex items-center gap-2 bg-[#8b1515] hover:bg-[#6b1111] text-white text-sm font-bold px-7 py-2.5 rounded-lg shadow-md shadow-[#8b1515]/25 transition">
                    Lanjut
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════ --}}
        {{-- STEP 3 — RIWAYAT PENDIDIKAN             --}}
        {{-- ═══════════════════════════════════════ --}}
        <div class="step-content bg-white rounded-xl shadow-md overflow-hidden" id="step-3">
            <div class="bg-[#8b1515] px-7 py-5">
                <h2 class="text-white text-xl font-bold">Riwayat Pendidikan</h2>
            </div>
            <div class="p-7">
                <div class="grid grid-cols-2 gap-5">
                    {{-- Info dipindah ke toast notification --}}

                    {{-- Jenjang & IPK --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="jenjang" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jenjang Pendidikan <span class="text-red-600">*</span>
                        </label>
                        <select id="jenjang" name="jenjang"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih jenjang —</option>
                            <option value="S1" {{ old('jenjang') == 'S1' ? 'selected' : '' }}>S1 (Sarjana)</option>
                            <option value="S2" {{ old('jenjang') == 'S2' ? 'selected' : '' }}>S2 (Magister)</option>
                            <option value="S3" {{ old('jenjang') == 'S3' ? 'selected' : '' }}>S3 (Doktor)</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="ipk" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            IPK <span class="text-red-600">*</span>
                        </label>
                        <input type="number" id="ipk" name="ipk" min="0" max="4" step="0.01"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Contoh: 3.75" value="{{ old('ipk') }}">
                    </div>
                    {{-- Institusi --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="institusi" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Institusi Pendidikan <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="institusi" name="institusi"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Nama universitas / institut / perguruan tinggi" value="{{ old('institusi') }}">
                    </div>
                    {{-- Prodi --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="prodi_pendidikan" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Program Studi <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="prodi_pendidikan" name="prodi_pendidikan"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Nama program studi" value="{{ old('prodi_pendidikan') }}">
                    </div>
                    {{-- Akreditas & No Ijazah --}}
                    <div class="flex flex-col gap-1.5">
                        <label for="akreditas" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Akreditasi Prodi <span class="text-gray-400 normal-case ml-1 px-1.5 py-0.5 bg-gray-100 rounded text-[0.65rem] font-medium">Opsional</span>
                        </label>
                        <select id="akreditas" name="akreditas"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih Akreditasi —</option>
                            <option value="A" {{ old('akreditas') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('akreditas') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ old('akreditas') == 'C' ? 'selected' : '' }}>C</option>
                            <option value="Unggul" {{ old('akreditas') == 'Unggul' ? 'selected' : '' }}>Unggul</option>
                            <option value="Baik Sekali" {{ old('akreditas') == 'Baik Sekali' ? 'selected' : '' }}>Baik Sekali</option>
                            <option value="Baik" {{ old('akreditas') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Tidak Terakreditasi" {{ old('akreditas') == 'Tidak Terakreditasi' ? 'selected' : '' }}>Tidak Terakreditasi</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="no_ijazah" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            No. Ijazah <span class="text-gray-400 normal-case ml-1 px-1.5 py-0.5 bg-gray-100 rounded text-[0.65rem] font-medium">Opsional</span>
                        </label>
                        <input type="text" id="no_ijazah" name="no_ijazah"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Nomor Ijazah Anda" value="{{ old('no_ijazah') }}">
                    </div>

                    {{-- Section Divider --}}
                    <div class="col-span-2">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[0.72rem] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Dokumen Pendidikan</span>
                            <div class="flex-1 border-t border-gray-400"></div>
                        </div>
                    </div>

                    {{-- Upload Ijazah --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Scan Ijazah
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="ijazah" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 12l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Pilih file PDF / JPG</p>
                                <p class="text-xs text-gray-400">Maks. 5MB</p>
                            </div>
                        </label>
                        <input type="file" id="ijazah" name="ijazah" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this,'ijazah-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="ijazah-name"></p>
                    </div>

                    {{-- Upload Transkrip --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Transkrip Nilai
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="transkrip" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 12l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Pilih file PDF / JPG</p>
                                <p class="text-xs text-gray-400">Maks. 5MB</p>
                            </div>
                        </label>
                        <input type="file" id="transkrip" name="transkrip" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this,'transkrip-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="transkrip-name"></p>
                    </div>
                </div>
            </div>
            <div class="px-7 py-4 border-t border-gray-100 flex justify-between">
                <button type="button" onclick="prevStep(3)"
                    class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] text-sm font-semibold px-5 py-2.5 rounded-lg bg-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" onclick="nextStep(3)"
                    class="inline-flex items-center gap-2 bg-[#8b1515] hover:bg-[#6b1111] text-white text-sm font-bold px-7 py-2.5 rounded-lg shadow-md shadow-[#8b1515]/25 transition">
                    Lanjut
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════ --}}
        {{-- STEP 4 — DOKUMEN PENDUKUNG (semua opsional) --}}
        {{-- ═══════════════════════════════════════ --}}
        <div class="step-content bg-white rounded-xl shadow-md overflow-hidden" id="step-4">
            <div class="bg-[#8b1515] px-7 py-5">
                <h2 class="text-white text-xl font-bold">Dokumen Pendukung</h2>
            </div>
            <div class="p-7">
                <div class="grid grid-cols-2 gap-5">
                    {{-- Info dipindah ke toast notification --}}

                    {{-- Section: Dokumen Dasar --}}
                    <div class="col-span-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[0.72rem] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Dokumen Dasar</span>
                            <div class="flex-1 border-t border-gray-400"></div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            CV (Curriculum Vitae)
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="cv" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Upload CV (PDF)</p>
                                <p class="text-xs text-gray-400">Maks. 5MB</p>
                            </div>
                        </label>
                        <input type="file" id="cv" name="cv" class="hidden" accept=".pdf" onchange="showFileName(this,'cv-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="cv-name"></p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Pas Foto
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="pas_foto" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Upload Foto (JPG/PNG)</p>
                                <p class="text-xs text-gray-400">Latar putih, maks. 2MB</p>
                            </div>
                        </label>
                        <input type="file" id="pas_foto" name="pas_foto" class="hidden" accept=".jpg,.jpeg,.png" onchange="showFileName(this,'foto-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="foto-name"></p>
                    </div>

                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Scan KTP
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="ktp" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Upload Scan KTP (PDF / JPG)</p>
                                <p class="text-xs text-gray-400">Maks. 2MB</p>
                            </div>
                        </label>
                        <input type="file" id="ktp" name="ktp" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this,'ktp-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="ktp-name"></p>
                    </div>

                    {{-- Section: Sertifikat Kompetensi --}}
                    <div class="col-span-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[0.72rem] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Sertifikat Kompetensi / Keahlian Khusus</span>
                            <div class="flex-1 border-t border-gray-400"></div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Kategori Sertifikat
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <select name="kategori_sertifikat" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih kategori —</option>
                            <option value="kompetensi" {{ old('kategori_sertifikat') == 'kompetensi' ? 'selected' : '' }}>Kompetensi</option>
                            <option value="keahlian_khusus" {{ old('kategori_sertifikat') == 'keahlian_khusus' ? 'selected' : '' }}>Keahlian Khusus</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            File Sertifikat (PDF)
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="sertifikat_kompetensi" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 12l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Upload PDF</p>
                                <p class="text-xs text-gray-400">Maks. 5MB</p>
                            </div>
                        </label>
                        <input type="file" id="sertifikat_kompetensi" name="sertifikat_kompetensi" class="hidden" accept=".pdf" onchange="showFileName(this,'sertif-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="sertif-name"></p>
                    </div>

                    {{-- Section: Sertifikat Bahasa --}}
                    <div class="col-span-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[0.72rem] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Sertifikat Kemampuan Bahasa Inggris</span>
                            <div class="flex-1 border-t border-gray-400"></div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jenis Tes Bahasa
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <select name="jenis_tes_bahasa" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="" disabled selected>— Pilih jenis tes —</option>
                            <option value="PBT" {{ old('jenis_tes_bahasa') == 'PBT' ? 'selected' : '' }}>PBT (Paper-Based TOEFL)</option>
                            <option value="TOEFL_ITP" {{ old('jenis_tes_bahasa') == 'TOEFL_ITP' ? 'selected' : '' }}>TOEFL (ITP/Institutional)</option>
                            <option value="EPrT" {{ old('jenis_tes_bahasa') == 'EPrT' ? 'selected' : '' }}>EPrT (English Proficiency Test)</option>
                            <option value="CBT" {{ old('jenis_tes_bahasa') == 'CBT' ? 'selected' : '' }}>CBT (Computer-Based TOEFL)</option>
                            <option value="IBT" {{ old('jenis_tes_bahasa') == 'IBT' ? 'selected' : '' }}>IBT (Internet-Based TOEFL)</option>
                            <option value="IELTS" {{ old('jenis_tes_bahasa') == 'IELTS' ? 'selected' : '' }}>IELTS</option>
                            <option value="AcEPT" {{ old('jenis_tes_bahasa') == 'AcEPT' ? 'selected' : '' }}>AcEPT (Academic English Proficiency Test)</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Skor
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <input type="number" name="skor_bahasa" min="0" step="0.5"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Contoh: 550" value="{{ old('skor_bahasa') }}">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Tanggal Tes
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <input type="date" name="tanggal_tes_bahasa"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 transition" value="{{ old('tanggal_tes_bahasa') }}">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Bukti Sertifikat Bahasa (PDF)
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <label for="sertifikat_bahasa" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 12l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Upload PDF</p>
                                <p class="text-xs text-gray-400">Maks. 5MB</p>
                            </div>
                        </label>
                        <input type="file" id="sertifikat_bahasa" name="sertifikat_bahasa" class="hidden" accept=".pdf" onchange="showFileName(this,'bahasa-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="bahasa-name"></p>
                    </div>
                </div>
            </div>
            <div class="px-7 py-4 border-t border-gray-100 flex justify-between">
                <button type="button" onclick="prevStep(4)"
                    class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] text-sm font-semibold px-5 py-2.5 rounded-lg bg-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" onclick="nextStep(4)"
                    class="inline-flex items-center gap-2 bg-[#8b1515] hover:bg-[#6b1111] text-white text-sm font-bold px-7 py-2.5 rounded-lg shadow-md shadow-[#8b1515]/25 transition">
                    Lanjut
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════ --}}
        {{-- STEP 5 — RIWAYAT AKADEMIK               --}}
        {{-- ═══════════════════════════════════════ --}}
        <div class="step-content bg-white rounded-xl shadow-md overflow-hidden" id="step-5">
            <div class="bg-[#8b1515] px-7 py-5">
                <h2 class="text-white text-xl font-bold">Riwayat Akademik</h2>
            </div>
            <div class="p-7">
                <div class="grid grid-cols-2 gap-5">

                    {{-- Section: Identitas Akademik --}}
                    <div class="col-span-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[0.72rem] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Identitas Akademik</span>
                            <div class="flex-1 border-t border-gray-400"></div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="nidn" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            NIDN
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Jika ada</span>
                        </label>
                        <input type="text" id="nidn" name="nidn" maxlength="20"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Nomor Induk Dosen Nasional" value="{{ old('nidn') }}">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="homebase" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Homebase Asal
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Jika ber-NIDN</span>
                        </label>
                        <input type="text" id="homebase" name="homebase"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Perguruan tinggi asal NIDN" value="{{ old('homebase') }}">
                    </div>

                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="jabatan_akademik" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jabatan Fungsional Akademik
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Jika ada</span>
                        </label>
                        <select id="jabatan_akademik" name="jabatan_akademik"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 cursor-pointer transition">
                            <option value="non_jabatan" selected {{ old('jabatan_akademik') == 'non_jabatan' ? 'selected' : '' }}>Non Jabatan (NJAD)</option>
                            <option value="guru_besar" {{ old('jabatan_akademik') == 'guru_besar' ? 'selected' : '' }}>Guru Besar (GB)</option>
                            <option value="lektor_kepala" {{ old('jabatan_akademik') == 'lektor_kepala' ? 'selected' : '' }}>Lektor Kepala (LK)</option>
                            <option value="lektor" {{ old('jabatan_akademik') == 'lektor' ? 'selected' : '' }}>Lektor (L)</option>
                            <option value="asisten_ahli" {{ old('jabatan_akademik') == 'asisten_ahli' ? 'selected' : '' }}>Asisten Ahli (AA)</option>
                        </select>
                    </div>

                    {{-- Section: Riset & Publikasi --}}
                    <div class="col-span-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[0.72rem] font-bold text-gray-400 uppercase tracking-wider whitespace-nowrap">Riset & Publikasi</span>
                            <div class="flex-1 border-t border-gray-400"></div>
                        </div>
                    </div>

                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="minat_riset" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Bidang Riset yang Diminati
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Opsional</span>
                        </label>
                        <textarea id="minat_riset" name="minat_riset" rows="3"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 resize-y transition"
                            placeholder="Tuliskan bidang riset atau topik penelitian yang Anda minati..."></textarea>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="h_index" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Nilai H-Index Scopus
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Jika ada</span>
                        </label>
                        <input type="number" id="h_index" name="h_index" min="0"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Contoh: 5" value="{{ old('h_index') }}">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Foto Kartu Dosen NIDN/NUPTK
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Jika ada</span>
                        </label>
                        <label for="kartu_dosen" class="file-upload-area">
                            <svg class="upload-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div class="file-label">
                                <p class="text-sm font-semibold text-gray-700">Upload Foto (JPG / PNG)</p>
                                <p class="text-xs text-gray-400">Maks. 2MB</p>
                            </div>
                        </label>
                        <input type="file" id="kartu_dosen" name="kartu_dosen" class="hidden" accept=".jpg,.jpeg,.png,.pdf" onchange="showFileName(this,'kartu-name')">
                        <p class="text-xs text-red-600 min-h-[16px]" id="kartu-name"></p>
                    </div>
                </div>
            </div>
            <div class="px-7 py-4 border-t border-gray-100 flex justify-between">
                <button type="button" onclick="prevStep(5)"
                    class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] text-sm font-semibold px-5 py-2.5 rounded-lg bg-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="submit" class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white text-sm font-bold px-7 py-2.5 rounded-lg shadow-md shadow-green-700/25 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Daftar Sekarang
                </button>
            </div>
        </div>

        </form>
    </div>
</div>

<script>
    // ── Toast System ─────────────────────────────────────
    function showToast(title, message, type = 'error', duration = 4000) {
        const container = document.getElementById('toast-container');
        const icons = {
            success: `<svg class="w-4 h-4 stroke-[3px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
            error:   `<svg class="w-4 h-4 stroke-[3px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
            info:    `<svg class="w-4 h-4 stroke-[3px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            warning: `<span class="font-bold text-sm">!</span>`
        };
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type]}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button type="button" class="toast-close" onclick="removeToast(this.parentElement)">&#x2715;</button>
        `;
        container.appendChild(toast);
        setTimeout(() => removeToast(toast), duration);
    }

    function removeToast(toast) {
        if (!toast || !toast.parentElement) return;
        toast.classList.add('removing');
        setTimeout(() => toast.remove(), 300);
    }

    // ── Step Helpers ──────────────────────────────────────
    const TOTAL = 5;

    let hasShownStep3Toast = false;
    let hasShownStep4Toast = false;

    // Card width per step
    const stepWidths = { 1: '28rem', 2: '48rem', 3: '48rem', 4: '48rem', 5: '48rem' };

    function showStep(n) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + n).classList.add('active');

        // Animate card wrapper width
        const wrapper = document.getElementById('cardWrapper');
        wrapper.style.maxWidth = stepWidths[n] || '48rem';

        updateIndicator(n);
        window.scrollTo({ top: 0, behavior: 'smooth' });

        if (n === 3 && !hasShownStep3Toast) {
            showToast('Pemberitahuan', 'Data akan tersimpan sesuai jenjang yang dipilih. Pilih S1, S2, atau S3 untuk mengisi riwayat pendidikan pada masing-masing jenjang.', 'info', 8000);
            hasShownStep3Toast = true;
        }

        if (n === 4 && !hasShownStep4Toast) {
            showToast('Opsional', 'Semua dokumen pada langkah ini opsional. Anda dapat melewatinya dan mengisi nanti dari profil Anda.', 'info', 8000);
            hasShownStep4Toast = true;
        }
    }

    function updateIndicator(active) {
        for (let i = 1; i <= TOTAL; i++) {
            const b = document.getElementById('bubble-' + i);
            const l = document.getElementById('label-' + i);
            b.classList.remove('active', 'completed', 'bg-[#8b1515]', 'bg-gray-300', 'shadow-lg', 'shadow-[#8b1515]/30');
            b.style.backgroundColor = '';

            if (i < active) {
                b.classList.add('completed');
                b.style.backgroundColor = '#8b1515';
                if (l) { l.classList.remove('text-gray-400'); l.style.color = '#8b1515'; }
            } else if (i === active) {
                b.classList.add('active');
                b.style.backgroundColor = '#8b1515';
                b.classList.add('shadow-lg');
                if (l) { l.classList.remove('text-gray-400'); l.style.color = '#8b1515'; }
            } else {
                b.classList.add('bg-gray-300');
                if (l) { l.style.color = ''; l.classList.add('text-gray-400'); }
            }

            const line = document.getElementById('line-' + i);
            if (line) {
                if (i < active) { line.style.backgroundColor = '#8b1515'; line.classList.remove('bg-gray-300'); }
                else { line.style.backgroundColor = ''; line.classList.add('bg-gray-300'); }
            }
        }
    }

    // ── Validation ────────────────────────────────────────
    const requiredFields = {
        1: ['email', 'password'],
        2: ['nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'no_telepon', 'jenis_kelamin', 'kewarganegaraan', 'status_pernikahan', 'alamat_domisili', 'alamat_ktp'],
        3: ['jenjang', 'ipk', 'institusi', 'prodi_pendidikan'],
        4: [],
        5: [],
    };

    function validateStep(step) {
        const fields = requiredFields[step] || [];
        let valid = true;
        let firstError = null;

        fields.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('error');
            if (!el.value.trim()) {
                el.classList.add('error');
                valid = false;
                if (!firstError) firstError = el;
            }
        });

        if (!valid) {
            showToast('Error', 'Harap lengkapi semua kolom yang wajib diisi (ditandai *).', 'error');
            if (firstError) firstError.focus();
            return false;
        }

        if (step === 1) {
            const pw  = document.getElementById('password').value;
            if (pw.length < 8) {
                document.getElementById('password').classList.add('error');
                showToast('Peringatan', 'Password minimal 8 karakter.', 'warning');
                return false;
            }
        }

        if (step === 2) {
            const nik = document.getElementById('nik').value;
            if (!/^\d{16}$/.test(nik)) {
                document.getElementById('nik').classList.add('error');
                showToast('Peringatan', 'NIK harus terdiri dari 16 digit angka.', 'warning');
                return false;
            }
        }

        return true;
    }

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        
        const bars = [
            document.getElementById('pb-1'),
            document.getElementById('pb-2'),
            document.getElementById('pb-3'),
            document.getElementById('pb-4')
        ];
        const text = document.getElementById('password-text');

        bars.forEach(b => b.className = 'h-full w-1/4 rounded bg-gray-200 transition-colors duration-300');

        if (password.length === 0) {
            text.innerText = 'Kekuatan password...';
            text.className = 'text-[0.65rem] text-gray-500 font-medium';
            return;
        }

        if (strength <= 2) {
            bars[0].classList.replace('bg-gray-200', 'bg-red-500');
            text.innerText = 'Lemah';
            text.className = 'text-[0.65rem] text-red-500 font-medium';
        } else if (strength === 3) {
            bars[0].classList.replace('bg-gray-200', 'bg-yellow-400');
            bars[1].classList.replace('bg-gray-200', 'bg-yellow-400');
            text.innerText = 'Sedang';
            text.className = 'text-[0.65rem] text-yellow-500 font-medium';
        } else if (strength === 4) {
            bars[0].classList.replace('bg-gray-200', 'bg-green-400');
            bars[1].classList.replace('bg-gray-200', 'bg-green-400');
            bars[2].classList.replace('bg-gray-200', 'bg-green-400');
            text.innerText = 'Kuat';
            text.className = 'text-[0.65rem] text-green-500 font-medium';
        } else if (strength >= 5) {
            bars[0].classList.replace('bg-gray-200', 'bg-green-600');
            bars[1].classList.replace('bg-gray-200', 'bg-green-600');
            bars[2].classList.replace('bg-gray-200', 'bg-green-600');
            bars[3].classList.replace('bg-gray-200', 'bg-green-600');
            text.innerText = 'Sangat Kuat';
            text.className = 'text-[0.65rem] text-green-600 font-medium';
        }
    }

    function nextStep(from) {
        if (!validateStep(from)) return;
        if (from < TOTAL) showStep(from + 1);
    }

    function prevStep(from) {
        if (from > 1) showStep(from - 1);
    }

    // ── File Name Display ─────────────────────────────────
    function showFileName(input, displayId) {
        // Find the label element for this input
        const label = document.querySelector('label[for="' + input.id + '"].file-upload-area');
        if (!label) return;

        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const shortName = fileName.length > 28 ? fileName.substring(0, 25) + '...' : fileName;
            label.classList.add('has-file');

            // Create or update file-selected element
            let selectedEl = label.querySelector('.file-selected');
            if (!selectedEl) {
                selectedEl = document.createElement('div');
                selectedEl.className = 'file-selected';
                label.appendChild(selectedEl);
            }
            selectedEl.innerHTML = `
                <svg class="w-4 h-4 shrink-0 text-[#8b1515]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-sm font-medium text-[#8b1515] truncate">${shortName}</span>
                <span class="text-[0.65rem] text-gray-400 shrink-0 ml-auto">Ganti</span>
            `;
        } else {
            label.classList.remove('has-file');
            const selectedEl = label.querySelector('.file-selected');
            if (selectedEl) selectedEl.remove();
        }

        // Clear old display element
        const el = document.getElementById(displayId);
        if (el) el.textContent = '';
    }

    // Init
    @if ($errors->any())
        let delay = 100;
        @foreach ($errors->all() as $error)
            setTimeout(() => {
                showToast('Validasi Gagal', '{!! addslashes($error) !!}', 'error', 7000);
            }, delay);
            delay += 300;
        @endforeach
        showStep(1);
    @else
        showStep(1);
    @endif

    // Clear error state on input
    document.querySelectorAll('.form-input').forEach(el => {
        el.addEventListener('input', () => el.classList.remove('error'));
        el.addEventListener('change', () => el.classList.remove('error'));
    });

    // Intercept Enter key / Early Form Submit
    document.querySelector('form').addEventListener('submit', function(e) {
        let activeStep = 1;
        for (let i = 1; i <= TOTAL; i++) {
            if (document.getElementById('step-' + i).classList.contains('active')) {
                activeStep = i;
                break;
            }
        }
        
        // If we are not on the last step, prevent submission and try to go to next step
        if (activeStep < TOTAL) {
            e.preventDefault();
            nextStep(activeStep);
        } else {
            // If on the last step, just validate it first
            if (!validateStep(activeStep)) {
                e.preventDefault();
            }
        }
    });
</script>
</body>
</html>
