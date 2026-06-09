<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Pelamar — Telkom University</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
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
            border-color: #9ca3af;
            background: #f3f4f6;
        }
        .file-upload-area.has-file .upload-icon { color: #6b7280; }
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

        /* Override browser autofill blue/yellow background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active,
        select:-webkit-autofill,
        textarea:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #f9fafb inset !important;
            -webkit-text-fill-color: #111827 !important;
            box-shadow: 0 0 0 1000px #f9fafb inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
@include('partials.loading-screen')

{{-- ── Toast Container ── --}}
<div id="toast-container"></div>

{{-- ── Navbar ── --}}
<nav class="sticky top-0 z-50 flex items-center justify-between px-4 sm:px-8 h-[60px] shadow-md bg-[#8b1515]">
    <div class="flex items-center gap-2.5">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                <div class="relative w-[120px] h-14 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('images/logo2.png') }}" alt="Telkom University Logo" class="w-full h-8 object-contain">
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
        <form id="registerForm" action="#" method="POST" enctype="multipart/form-data" novalidate data-no-loading>
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
                            placeholder="16 digit NIK sesuai KTP" value="{{ old('nik') }}"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
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
                    <div class="flex flex-col gap-1.5"
                         x-data="{
                            open: false,
                            query: '{{ old('tempat_lahir') }}',
                            places: [
                                {l:'Kota Banda Aceh',v:'Banda Aceh'},{l:'Kota Sabang',v:'Sabang'},{l:'Kota Langsa',v:'Langsa'},{l:'Kota Lhokseumawe',v:'Lhokseumawe'},{l:'Kota Subulussalam',v:'Subulussalam'},
                                {l:'Kabupaten Aceh Besar',v:'Aceh Besar'},{l:'Kabupaten Aceh Barat',v:'Aceh Barat'},{l:'Kabupaten Aceh Selatan',v:'Aceh Selatan'},{l:'Kabupaten Aceh Tengah',v:'Aceh Tengah'},{l:'Kabupaten Aceh Tenggara',v:'Aceh Tenggara'},{l:'Kabupaten Aceh Timur',v:'Aceh Timur'},{l:'Kabupaten Aceh Utara',v:'Aceh Utara'},{l:'Kabupaten Bener Meriah',v:'Bener Meriah'},{l:'Kabupaten Bireuen',v:'Bireuen'},{l:'Kabupaten Gayo Lues',v:'Gayo Lues'},{l:'Kabupaten Nagan Raya',v:'Nagan Raya'},{l:'Kabupaten Pidie',v:'Pidie'},{l:'Kabupaten Pidie Jaya',v:'Pidie Jaya'},{l:'Kabupaten Simeulue',v:'Simeulue'},{l:'Kabupaten Aceh Barat Daya',v:'Aceh Barat Daya'},{l:'Kabupaten Aceh Jaya',v:'Aceh Jaya'},{l:'Kabupaten Aceh Singkil',v:'Aceh Singkil'},
                                {l:'Kota Medan',v:'Medan'},{l:'Kota Binjai',v:'Binjai'},{l:'Kota Tebing Tinggi',v:'Tebing Tinggi'},{l:'Kota Pematangsiantar',v:'Pematangsiantar'},{l:'Kota Tanjungbalai',v:'Tanjungbalai'},{l:'Kota Sibolga',v:'Sibolga'},{l:'Kota Padang Sidempuan',v:'Padang Sidempuan'},{l:'Kota Gunungsitoli',v:'Gunungsitoli'},
                                {l:'Kabupaten Asahan',v:'Asahan'},{l:'Kabupaten Batubara',v:'Batubara'},{l:'Kabupaten Dairi',v:'Dairi'},{l:'Kabupaten Deli Serdang',v:'Deli Serdang'},{l:'Kabupaten Humbang Hasundutan',v:'Humbang Hasundutan'},{l:'Kabupaten Karo',v:'Karo'},{l:'Kabupaten Labuhanbatu',v:'Labuhanbatu'},{l:'Kabupaten Labuhanbatu Selatan',v:'Labuhanbatu Selatan'},{l:'Kabupaten Labuhanbatu Utara',v:'Labuhanbatu Utara'},{l:'Kabupaten Langkat',v:'Langkat'},{l:'Kabupaten Mandailing Natal',v:'Mandailing Natal'},{l:'Kabupaten Nias',v:'Nias'},{l:'Kabupaten Nias Barat',v:'Nias Barat'},{l:'Kabupaten Nias Selatan',v:'Nias Selatan'},{l:'Kabupaten Nias Utara',v:'Nias Utara'},{l:'Kabupaten Padang Lawas',v:'Padang Lawas'},{l:'Kabupaten Padang Lawas Utara',v:'Padang Lawas Utara'},{l:'Kabupaten Pakpak Bharat',v:'Pakpak Bharat'},{l:'Kabupaten Samosir',v:'Samosir'},{l:'Kabupaten Serdang Bedagai',v:'Serdang Bedagai'},{l:'Kabupaten Simalungun',v:'Simalungun'},{l:'Kabupaten Tapanuli Selatan',v:'Tapanuli Selatan'},{l:'Kabupaten Tapanuli Tengah',v:'Tapanuli Tengah'},{l:'Kabupaten Tapanuli Utara',v:'Tapanuli Utara'},{l:'Kabupaten Toba',v:'Toba'},
                                {l:'Kota Padang',v:'Padang'},{l:'Kota Solok',v:'Solok'},{l:'Kota Sawahlunto',v:'Sawahlunto'},{l:'Kota Padang Panjang',v:'Padang Panjang'},{l:'Kota Bukittinggi',v:'Bukittinggi'},{l:'Kota Payakumbuh',v:'Payakumbuh'},{l:'Kota Pariaman',v:'Pariaman'},
                                {l:'Kabupaten Agam',v:'Agam'},{l:'Kabupaten Dharmasraya',v:'Dharmasraya'},{l:'Kabupaten Kepulauan Mentawai',v:'Kepulauan Mentawai'},{l:'Kabupaten Lima Puluh Kota',v:'Lima Puluh Kota'},{l:'Kabupaten Padang Pariaman',v:'Padang Pariaman'},{l:'Kabupaten Pasaman',v:'Pasaman'},{l:'Kabupaten Pasaman Barat',v:'Pasaman Barat'},{l:'Kabupaten Pesisir Selatan',v:'Pesisir Selatan'},{l:'Kabupaten Sijunjung',v:'Sijunjung'},{l:'Kabupaten Solok',v:'Solok'},{l:'Kabupaten Solok Selatan',v:'Solok Selatan'},{l:'Kabupaten Tanah Datar',v:'Tanah Datar'},
                                {l:'Kota Pekanbaru',v:'Pekanbaru'},{l:'Kota Dumai',v:'Dumai'},
                                {l:'Kabupaten Bengkalis',v:'Bengkalis'},{l:'Kabupaten Indragiri Hilir',v:'Indragiri Hilir'},{l:'Kabupaten Indragiri Hulu',v:'Indragiri Hulu'},{l:'Kabupaten Kampar',v:'Kampar'},{l:'Kabupaten Kepulauan Meranti',v:'Kepulauan Meranti'},{l:'Kabupaten Kuantan Singingi',v:'Kuantan Singingi'},{l:'Kabupaten Pelalawan',v:'Pelalawan'},{l:'Kabupaten Rokan Hilir',v:'Rokan Hilir'},{l:'Kabupaten Rokan Hulu',v:'Rokan Hulu'},{l:'Kabupaten Siak',v:'Siak'},
                                {l:'Kota Jambi',v:'Jambi'},{l:'Kota Sungai Penuh',v:'Sungai Penuh'},
                                {l:'Kabupaten Batanghari',v:'Batanghari'},{l:'Kabupaten Bungo',v:'Bungo'},{l:'Kabupaten Kerinci',v:'Kerinci'},{l:'Kabupaten Merangin',v:'Merangin'},{l:'Kabupaten Muaro Jambi',v:'Muaro Jambi'},{l:'Kabupaten Sarolangun',v:'Sarolangun'},{l:'Kabupaten Tanjung Jabung Barat',v:'Tanjung Jabung Barat'},{l:'Kabupaten Tanjung Jabung Timur',v:'Tanjung Jabung Timur'},{l:'Kabupaten Tebo',v:'Tebo'},
                                {l:'Kota Palembang',v:'Palembang'},{l:'Kota Prabumulih',v:'Prabumulih'},{l:'Kota Pagaralam',v:'Pagaralam'},{l:'Kota Lubuklinggau',v:'Lubuklinggau'},
                                {l:'Kabupaten Banyuasin',v:'Banyuasin'},{l:'Kabupaten Empat Lawang',v:'Empat Lawang'},{l:'Kabupaten Lahat',v:'Lahat'},{l:'Kabupaten Muara Enim',v:'Muara Enim'},{l:'Kabupaten Musi Banyuasin',v:'Musi Banyuasin'},{l:'Kabupaten Musi Rawas',v:'Musi Rawas'},{l:'Kabupaten Musi Rawas Utara',v:'Musi Rawas Utara'},{l:'Kabupaten Ogan Ilir',v:'Ogan Ilir'},{l:'Kabupaten Ogan Komering Ilir',v:'Ogan Komering Ilir'},{l:'Kabupaten Ogan Komering Ulu',v:'Ogan Komering Ulu'},{l:'Kabupaten Ogan Komering Ulu Selatan',v:'Ogan Komering Ulu Selatan'},{l:'Kabupaten Ogan Komering Ulu Timur',v:'Ogan Komering Ulu Timur'},{l:'Kabupaten Penukal Abab Lematang Ilir',v:'Penukal Abab Lematang Ilir'},
                                {l:'Kota Bengkulu',v:'Bengkulu'},
                                {l:'Kabupaten Bengkulu Selatan',v:'Bengkulu Selatan'},{l:'Kabupaten Bengkulu Tengah',v:'Bengkulu Tengah'},{l:'Kabupaten Bengkulu Utara',v:'Bengkulu Utara'},{l:'Kabupaten Kaur',v:'Kaur'},{l:'Kabupaten Kepahiang',v:'Kepahiang'},{l:'Kabupaten Lebong',v:'Lebong'},{l:'Kabupaten Mukomuko',v:'Mukomuko'},{l:'Kabupaten Rejang Lebong',v:'Rejang Lebong'},{l:'Kabupaten Seluma',v:'Seluma'},
                                {l:'Kota Bandar Lampung',v:'Bandar Lampung'},{l:'Kota Metro',v:'Metro'},
                                {l:'Kabupaten Lampung Barat',v:'Lampung Barat'},{l:'Kabupaten Lampung Selatan',v:'Lampung Selatan'},{l:'Kabupaten Lampung Tengah',v:'Lampung Tengah'},{l:'Kabupaten Lampung Timur',v:'Lampung Timur'},{l:'Kabupaten Lampung Utara',v:'Lampung Utara'},{l:'Kabupaten Mesuji',v:'Mesuji'},{l:'Kabupaten Pesawaran',v:'Pesawaran'},{l:'Kabupaten Pesisir Barat',v:'Pesisir Barat'},{l:'Kabupaten Pringsewu',v:'Pringsewu'},{l:'Kabupaten Tanggamus',v:'Tanggamus'},{l:'Kabupaten Tulang Bawang',v:'Tulang Bawang'},{l:'Kabupaten Tulang Bawang Barat',v:'Tulang Bawang Barat'},{l:'Kabupaten Way Kanan',v:'Way Kanan'},
                                {l:'Kota Pangkalpinang',v:'Pangkalpinang'},
                                {l:'Kabupaten Bangka',v:'Bangka'},{l:'Kabupaten Bangka Barat',v:'Bangka Barat'},{l:'Kabupaten Bangka Selatan',v:'Bangka Selatan'},{l:'Kabupaten Bangka Tengah',v:'Bangka Tengah'},{l:'Kabupaten Belitung',v:'Belitung'},{l:'Kabupaten Belitung Timur',v:'Belitung Timur'},
                                {l:'Kota Tanjungpinang',v:'Tanjungpinang'},{l:'Kota Batam',v:'Batam'},
                                {l:'Kabupaten Bintan',v:'Bintan'},{l:'Kabupaten Karimun',v:'Karimun'},{l:'Kabupaten Kepulauan Anambas',v:'Kepulauan Anambas'},{l:'Kabupaten Lingga',v:'Lingga'},{l:'Kabupaten Natuna',v:'Natuna'},
                                {l:'Kota Jakarta Pusat',v:'Jakarta Pusat'},{l:'Kota Jakarta Utara',v:'Jakarta Utara'},{l:'Kota Jakarta Barat',v:'Jakarta Barat'},{l:'Kota Jakarta Selatan',v:'Jakarta Selatan'},{l:'Kota Jakarta Timur',v:'Jakarta Timur'},{l:'Kabupaten Kepulauan Seribu',v:'Kepulauan Seribu'},
                                {l:'Kota Bogor',v:'Bogor'},{l:'Kota Sukabumi',v:'Sukabumi'},{l:'Kota Bandung',v:'Bandung'},{l:'Kota Cirebon',v:'Cirebon'},{l:'Kota Bekasi',v:'Bekasi'},{l:'Kota Depok',v:'Depok'},{l:'Kota Cimahi',v:'Cimahi'},{l:'Kota Tasikmalaya',v:'Tasikmalaya'},{l:'Kota Banjar',v:'Banjar'},
                                {l:'Kabupaten Bandung',v:'Bandung'},{l:'Kabupaten Bandung Barat',v:'Bandung Barat'},{l:'Kabupaten Bekasi',v:'Bekasi'},{l:'Kabupaten Bogor',v:'Bogor'},{l:'Kabupaten Ciamis',v:'Ciamis'},{l:'Kabupaten Cianjur',v:'Cianjur'},{l:'Kabupaten Cirebon',v:'Cirebon'},{l:'Kabupaten Garut',v:'Garut'},{l:'Kabupaten Indramayu',v:'Indramayu'},{l:'Kabupaten Karawang',v:'Karawang'},{l:'Kabupaten Kuningan',v:'Kuningan'},{l:'Kabupaten Majalengka',v:'Majalengka'},{l:'Kabupaten Pangandaran',v:'Pangandaran'},{l:'Kabupaten Purwakarta',v:'Purwakarta'},{l:'Kabupaten Subang',v:'Subang'},{l:'Kabupaten Sukabumi',v:'Sukabumi'},{l:'Kabupaten Sumedang',v:'Sumedang'},{l:'Kabupaten Tasikmalaya',v:'Tasikmalaya'},
                                {l:'Kota Serang',v:'Serang'},{l:'Kota Cilegon',v:'Cilegon'},{l:'Kota Tangerang',v:'Tangerang'},{l:'Kota Tangerang Selatan',v:'Tangerang Selatan'},
                                {l:'Kabupaten Lebak',v:'Lebak'},{l:'Kabupaten Pandeglang',v:'Pandeglang'},{l:'Kabupaten Serang',v:'Serang'},{l:'Kabupaten Tangerang',v:'Tangerang'},
                                {l:'Kota Semarang',v:'Semarang'},{l:'Kota Surakarta',v:'Surakarta'},{l:'Kota Salatiga',v:'Salatiga'},{l:'Kota Magelang',v:'Magelang'},{l:'Kota Pekalongan',v:'Pekalongan'},{l:'Kota Tegal',v:'Tegal'},
                                {l:'Kabupaten Banjarnegara',v:'Banjarnegara'},{l:'Kabupaten Banyumas',v:'Banyumas'},{l:'Kabupaten Batang',v:'Batang'},{l:'Kabupaten Blora',v:'Blora'},{l:'Kabupaten Boyolali',v:'Boyolali'},{l:'Kabupaten Brebes',v:'Brebes'},{l:'Kabupaten Cilacap',v:'Cilacap'},{l:'Kabupaten Demak',v:'Demak'},{l:'Kabupaten Grobogan',v:'Grobogan'},{l:'Kabupaten Jepara',v:'Jepara'},{l:'Kabupaten Karanganyar',v:'Karanganyar'},{l:'Kabupaten Kebumen',v:'Kebumen'},{l:'Kabupaten Kendal',v:'Kendal'},{l:'Kabupaten Klaten',v:'Klaten'},{l:'Kabupaten Kudus',v:'Kudus'},{l:'Kabupaten Magelang',v:'Magelang'},{l:'Kabupaten Pati',v:'Pati'},{l:'Kabupaten Pekalongan',v:'Pekalongan'},{l:'Kabupaten Pemalang',v:'Pemalang'},{l:'Kabupaten Purbalingga',v:'Purbalingga'},{l:'Kabupaten Purworejo',v:'Purworejo'},{l:'Kabupaten Rembang',v:'Rembang'},{l:'Kabupaten Semarang',v:'Semarang'},{l:'Kabupaten Sragen',v:'Sragen'},{l:'Kabupaten Sukoharjo',v:'Sukoharjo'},{l:'Kabupaten Tegal',v:'Tegal'},{l:'Kabupaten Temanggung',v:'Temanggung'},{l:'Kabupaten Wonogiri',v:'Wonogiri'},{l:'Kabupaten Wonosobo',v:'Wonosobo'},
                                {l:'Kota Yogyakarta',v:'Yogyakarta'},
                                {l:'Kabupaten Bantul',v:'Bantul'},{l:'Kabupaten Gunungkidul',v:'Gunungkidul'},{l:'Kabupaten Kulon Progo',v:'Kulon Progo'},{l:'Kabupaten Sleman',v:'Sleman'},
                                {l:'Kota Surabaya',v:'Surabaya'},{l:'Kota Malang',v:'Malang'},{l:'Kota Pasuruan',v:'Pasuruan'},{l:'Kota Probolinggo',v:'Probolinggo'},{l:'Kota Blitar',v:'Blitar'},{l:'Kota Kediri',v:'Kediri'},{l:'Kota Madiun',v:'Madiun'},{l:'Kota Mojokerto',v:'Mojokerto'},{l:'Kota Batu',v:'Batu'},
                                {l:'Kabupaten Bangkalan',v:'Bangkalan'},{l:'Kabupaten Banyuwangi',v:'Banyuwangi'},{l:'Kabupaten Blitar',v:'Blitar'},{l:'Kabupaten Bojonegoro',v:'Bojonegoro'},{l:'Kabupaten Bondowoso',v:'Bondowoso'},{l:'Kabupaten Gresik',v:'Gresik'},{l:'Kabupaten Jember',v:'Jember'},{l:'Kabupaten Jombang',v:'Jombang'},{l:'Kabupaten Kediri',v:'Kediri'},{l:'Kabupaten Lamongan',v:'Lamongan'},{l:'Kabupaten Lumajang',v:'Lumajang'},{l:'Kabupaten Madiun',v:'Madiun'},{l:'Kabupaten Magetan',v:'Magetan'},{l:'Kabupaten Malang',v:'Malang'},{l:'Kabupaten Mojokerto',v:'Mojokerto'},{l:'Kabupaten Nganjuk',v:'Nganjuk'},{l:'Kabupaten Ngawi',v:'Ngawi'},{l:'Kabupaten Pacitan',v:'Pacitan'},{l:'Kabupaten Pamekasan',v:'Pamekasan'},{l:'Kabupaten Pasuruan',v:'Pasuruan'},{l:'Kabupaten Ponorogo',v:'Ponorogo'},{l:'Kabupaten Probolinggo',v:'Probolinggo'},{l:'Kabupaten Sampang',v:'Sampang'},{l:'Kabupaten Sidoarjo',v:'Sidoarjo'},{l:'Kabupaten Situbondo',v:'Situbondo'},{l:'Kabupaten Sumenep',v:'Sumenep'},{l:'Kabupaten Trenggalek',v:'Trenggalek'},{l:'Kabupaten Tuban',v:'Tuban'},{l:'Kabupaten Tulungagung',v:'Tulungagung'},
                                {l:'Kota Denpasar',v:'Denpasar'},
                                {l:'Kabupaten Badung',v:'Badung'},{l:'Kabupaten Bangli',v:'Bangli'},{l:'Kabupaten Buleleng',v:'Buleleng'},{l:'Kabupaten Gianyar',v:'Gianyar'},{l:'Kabupaten Jembrana',v:'Jembrana'},{l:'Kabupaten Karangasem',v:'Karangasem'},{l:'Kabupaten Klungkung',v:'Klungkung'},{l:'Kabupaten Tabanan',v:'Tabanan'},
                                {l:'Kota Mataram',v:'Mataram'},{l:'Kota Bima',v:'Bima'},
                                {l:'Kabupaten Bima',v:'Bima'},{l:'Kabupaten Dompu',v:'Dompu'},{l:'Kabupaten Lombok Barat',v:'Lombok Barat'},{l:'Kabupaten Lombok tengah',v:'Lombok Tengah'},{l:'Kabupaten Lombok Timur',v:'Lombok Timur'},{l:'Kabupaten Lombok Utara',v:'Lombok Utara'},{l:'Kabupaten Sumbawa',v:'Sumbawa'},{l:'Kabupaten Sumbawa Barat',v:'Sumbawa Barat'},
                                {l:'Kota Kupang',v:'Kupang'},
                                {l:'Kabupaten Alor',v:'Alor'},{l:'Kabupaten Belu',v:'Belu'},{l:'Kabupaten Ende',v:'Ende'},{l:'Kabupaten Flores Timur',v:'Flores Timur'},{l:'Kabupaten Kupang',v:'Kupang'},{l:'Kabupaten Lembata',v:'Lembata'},{l:'Kabupaten Malaka',v:'Malaka'},{l:'Kabupaten Manggarai',v:'Manggarai'},{l:'Kabupaten Manggarai Barat',v:'Manggarai Barat'},{l:'Kabupaten Manggarai Timur',v:'Manggarai Timur'},{l:'Kabupaten Nagekeo',v:'Nagekeo'},{l:'Kabupaten Ngada',v:'Ngada'},{l:'Kabupaten Rote Ndao',v:'Rote Ndao'},{l:'Kabupaten Sabu Raijua',v:'Sabu Raijua'},{l:'Kabupaten Sikka',v:'Sikka'},{l:'Kabupaten Sumba Barat',v:'Sumba Barat'},{l:'Kabupaten Sumba Barat Daya',v:'Sumba Barat Daya'},{l:'Kabupaten Sumba Tengah',v:'Sumba Tengah'},{l:'Kabupaten Sumba Timur',v:'Sumba Timur'},{l:'Kabupaten Timor Tengah Selatan',v:'Timor Tengah Selatan'},{l:'Kabupaten Timor Tengah Utara',v:'Timor Tengah Utara'},
                                {l:'Kota Pontianak',v:'Pontianak'},{l:'Kota Singkawang',v:'Singkawang'},
                                {l:'Kabupaten Bengkayang',v:'Bengkayang'},{l:'Kabupaten Kapuas Hulu',v:'Kapuas Hulu'},{l:'Kabupaten Kayong Utara',v:'Kayong Utara'},{l:'Kabupaten Ketapang',v:'Ketapang'},{l:'Kabupaten Kubu Raya',v:'Kubu Raya'},{l:'Kabupaten Landak',v:'Landak'},{l:'Kabupaten Melawi',v:'Melawi'},{l:'Kabupaten Mempawah',v:'Mempawah'},{l:'Kabupaten Sambas',v:'Sambas'},{l:'Kabupaten Sanggau',v:'Sanggau'},{l:'Kabupaten Sekadau',v:'Sekadau'},{l:'Kabupaten Sintang',v:'Sintang'},
                                {l:'Kota Palangkaraya',v:'Palangkaraya'},
                                {l:'Kabupaten Barito Selatan',v:'Barito Selatan'},{l:'Kabupaten Barito Timur',v:'Barito Timur'},{l:'Kabupaten Barito Utara',v:'Barito Utara'},{l:'Kabupaten Gunung Mas',v:'Gunung Mas'},{l:'Kabupaten Kapuas',v:'Kapuas'},{l:'Kabupaten Katingan',v:'Katingan'},{l:'Kabupaten Kotawaringin Barat',v:'Kotawaringin Barat'},{l:'Kabupaten Kotawaringin Timur',v:'Kotawaringin Timur'},{l:'Kabupaten Lamandau',v:'Lamandau'},{l:'Kabupaten Murung Raya',v:'Murung Raya'},{l:'Kabupaten Pulang Pisau',v:'Pulang Pisau'},{l:'Kabupaten Seruyan',v:'Seruyan'},{l:'Kabupaten Sukamara',v:'Sukamara'},
                                {l:'Kota Banjarmasin',v:'Banjarmasin'},{l:'Kota Banjarbaru',v:'Banjarbaru'},
                                {l:'Kabupaten Balangan',v:'Balangan'},{l:'Kabupaten Banjar',v:'Banjar'},{l:'Kabupaten Barito Kuala',v:'Barito Kuala'},{l:'Kabupaten Hulu Sungai Selatan',v:'Hulu Sungai Selatan'},{l:'Kabupaten Hulu Sungai Tengah',v:'Hulu Sungai Tengah'},{l:'Kabupaten Hulu Sungai Utara',v:'Hulu Sungai Utara'},{l:'Kabupaten Kotabaru',v:'Kotabaru'},{l:'Kabupaten Tabalong',v:'Tabalong'},{l:'Kabupaten Tanah Bumbu',v:'Tanah Bumbu'},{l:'Kabupaten Tanah Laut',v:'Tanah Laut'},{l:'Kabupaten Tapin',v:'Tapin'},
                                {l:'Kota Samarinda',v:'Samarinda'},{l:'Kota Balikpapan',v:'Balikpapan'},{l:'Kota Bontang',v:'Bontang'},
                                {l:'Kabupaten Berau',v:'Berau'},{l:'Kabupaten Kutai Barat',v:'Kutai Barat'},{l:'Kabupaten Kutai Kartanegara',v:'Kutai Kartanegara'},{l:'Kabupaten Kutai Timur',v:'Kutai Timur'},{l:'Kabupaten Mahakam Ulu',v:'Mahakam Ulu'},{l:'Kabupaten Paser',v:'Paser'},{l:'Kabupaten Penajam Paser Utara',v:'Penajam Paser Utara'},
                                {l:'Kota Tarakan',v:'Tarakan'},
                                {l:'Kabupaten Bulungan',v:'Bulungan'},{l:'Kabupaten Malinau',v:'Malinau'},{l:'Kabupaten Nunukan',v:'Nunukan'},{l:'Kabupaten Tana Tidung',v:'Tana Tidung'},
                                {l:'Kota Manado',v:'Manado'},{l:'Kota Bitung',v:'Bitung'},{l:'Kota Tomohon',v:'Tomohon'},{l:'Kota Kotamobagu',v:'Kotamobagu'},
                                {l:'Kabupaten Bolaang Mongondow',v:'Bolaang Mongondow'},{l:'Kabupaten Bolaang Mongondow Selatan',v:'Bolaang Mongondow Selatan'},{l:'Kabupaten Bolaang Mongondow Timur',v:'Bolaang Mongondow Timur'},{l:'Kabupaten Bolaang Mongondow Utara',v:'Bolaang Mongondow Utara'},{l:'Kabupaten Kepulauan Sangihe',v:'Kepulauan Sangihe'},{l:'Kabupaten Kepulauan Siau Tagulandang Biaro',v:'Kepulauan Siau Tagulandang Biaro'},{l:'Kabupaten Kepulauan Talaud',v:'Kepulauan Talaud'},{l:'Kabupaten Minahasa',v:'Minahasa'},{l:'Kabupaten Minahasa Selatan',v:'Minahasa Selatan'},{l:'Kabupaten Minahasa Tenggara',v:'Minahasa Tenggara'},{l:'Kabupaten Minahasa Utara',v:'Minahasa Utara'},
                                {l:'Kota Gorontalo',v:'Gorontalo'},
                                {l:'Kabupaten Bone Bolango',v:'Bone Bolango'},{l:'Kabupaten Gorontalo',v:'Gorontalo'},{l:'Kabupaten Gorontalo Utara',v:'Gorontalo Utara'},{l:'Kabupaten Pohuwato',v:'Pohuwato'},
                                {l:'Kota Palu',v:'Palu'},
                                {l:'Kabupaten Banggai',v:'Banggai'},{l:'Kabupaten Banggai Kepulauan',v:'Banggai Kepulauan'},{l:'Kabupaten Banggai Laut',v:'Banggai Laut'},{l:'Kabupaten Buol',v:'Buol'},{l:'Kabupaten Donggala',v:'Donggala'},{l:'Kabupaten Morowali',v:'Morowali'},{l:'Kabupaten Morowali Utara',v:'Morowali Utara'},{l:'Kabupaten Parigi Moutong',v:'Parigi Moutong'},{l:'Kabupaten Poso',v:'Poso'},{l:'Kabupaten Sigi',v:'Sigi'},{l:'Kabupaten Tojo Una-Una',v:'Tojo Una-Una'},{l:'Kabupaten Toli-Toli',v:'Toli-Toli'},
                                {l:'Kota Makassar',v:'Makassar'},{l:'Kota Parepare',v:'Parepare'},{l:'Kota Palopo',v:'Palopo'},
                                {l:'Kabupaten Bantaeng',v:'Bantaeng'},{l:'Kabupaten Barru',v:'Barru'},{l:'Kabupaten Bone',v:'Bone'},{l:'Kabupaten Bulukumba',v:'Bulukumba'},{l:'Kabupaten Enrekang',v:'Enrekang'},{l:'Kabupaten Gowa',v:'Gowa'},{l:'Kabupaten Jeneponto',v:'Jeneponto'},{l:'Kabupaten Kepulauan Selayar',v:'Kepulauan Selayar'},{l:'Kabupaten Luwu',v:'Luwu'},{l:'Kabupaten Luwu Timur',v:'Luwu Timur'},{l:'Kabupaten Luwu Utara',v:'Luwu Utara'},{l:'Kabupaten Maros',v:'Maros'},{l:'Kabupaten Pangkajene dan Kepulauan',v:'Pangkajene dan Kepulauan'},{l:'Kabupaten Pinrang',v:'Pinrang'},{l:'Kabupaten Sidenreng Rappang',v:'Sidenreng Rappang'},{l:'Kabupaten Sinjai',v:'Sinjai'},{l:'Kabupaten Soppeng',v:'Soppeng'},{l:'Kabupaten Takalar',v:'Takalar'},{l:'Kabupaten Tana Toraja',v:'Tana Toraja'},{l:'Kabupaten Toraja Utara',v:'Toraja Utara'},{l:'Kabupaten Wajo',v:'Wajo'},
                                {l:'Kota Kendari',v:'Kendari'},{l:'Kota Baubau',v:'Baubau'},
                                {l:'Kabupaten Bombana',v:'Bombana'},{l:'Kabupaten Buton',v:'Buton'},{l:'Kabupaten Buton Selatan',v:'Buton Selatan'},{l:'Kabupaten Buton Tengah',v:'Buton Tengah'},{l:'Kabupaten Buton Utara',v:'Buton Utara'},{l:'Kabupaten Kolaka',v:'Kolaka'},{l:'Kabupaten Kolaka Timur',v:'Kolaka Timur'},{l:'Kabupaten Kolaka Utara',v:'Kolaka Utara'},{l:'Kabupaten Konawe',v:'Konawe'},{l:'Kabupaten Konawe Kepulauan',v:'Konawe Kepulauan'},{l:'Kabupaten Konawe Selatan',v:'Konawe Selatan'},{l:'Kabupaten Konawe Utara',v:'Konawe Utara'},{l:'Kabupaten Muna',v:'Muna'},{l:'Kabupaten Muna Barat',v:'Muna Barat'},{l:'Kabupaten Wakatobi',v:'Wakatobi'},
                                {l:'Kota Ambon',v:'Ambon'},{l:'Kota Tual',v:'Tual'},
                                {l:'Kabupaten Buru',v:'Buru'},{l:'Kabupaten Buru Selatan',v:'Buru Selatan'},{l:'Kabupaten Kepulauan Aru',v:'Kepulauan Aru'},{l:'Kabupaten Maluku Barat Daya',v:'Maluku Barat Daya'},{l:'Kabupaten Maluku Tengah',v:'Maluku Tengah'},{l:'Kabupaten Maluku Tenggara',v:'Maluku Tenggara'},{l:'Kabupaten Maluku Tenggara Barat',v:'Maluku Tenggara Barat'},{l:'Kabupaten Seram Bagian Barat',v:'Seram Bagian Barat'},{l:'Kabupaten Seram Bagian Timur',v:'Seram Bagian Timur'},
                                {l:'Kota Ternate',v:'Ternate'},{l:'Kota Tidore Kepulauan',v:'Tidore Kepulauan'},
                                {l:'Kabupaten Halmahera Barat',v:'Halmahera Barat'},{l:'Kabupaten Halmahera Selatan',v:'Halmahera Selatan'},{l:'Kabupaten Halmahera Tengah',v:'Halmahera Tengah'},{l:'Kabupaten Halmahera Timur',v:'Halmahera Timur'},{l:'Kabupaten Halmahera Utara',v:'Halmahera Utara'},{l:'Kabupaten Kepulauan Sula',v:'Kepulauan Sula'},{l:'Kabupaten Pulau Morotai',v:'Pulau Morotai'},{l:'Kabupaten Pulau Taliabu',v:'Pulau Taliabu'},
                                {l:'Kota Jayapura',v:'Jayapura'},
                                {l:'Kabupaten Asmat',v:'Asmat'},{l:'Kabupaten Biak Numfor',v:'Biak Numfor'},{l:'Kabupaten Boven Digoel',v:'Boven Digoel'},{l:'Kabupaten Deiyai',v:'Deiyai'},{l:'Kabupaten Dogiyai',v:'Dogiyai'},{l:'Kabupaten Intan Jaya',v:'Intan Jaya'},{l:'Kabupaten Jayapura',v:'Jayapura'},{l:'Kabupaten Jayawijaya',v:'Jayawijaya'},{l:'Kabupaten Keerom',v:'Keerom'},{l:'Kabupaten Kepulauan Yapen',v:'Kepulauan Yapen'},{l:'Kabupaten Lanny Jaya',v:'Lanny Jaya'},{l:'Kabupaten Mamberamo Raya',v:'Mamberamo Raya'},{l:'Kabupaten Mamberamo Tengah',v:'Mamberamo Tengah'},{l:'Kabupaten Mappi',v:'Mappi'},{l:'Kabupaten Merauke',v:'Merauke'},{l:'Kabupaten Mimika',v:'Mimika'},{l:'Kabupaten Nabire',v:'Nabire'},{l:'Kabupaten Nduga',v:'Nduga'},{l:'Kabupaten Paniai',v:'Paniai'},{l:'Kabupaten Pegunungan Bintang',v:'Pegunungan Bintang'},{l:'Kabupaten Puncak',v:'Puncak'},{l:'Kabupaten Puncak Jaya',v:'Puncak Jaya'},{l:'Kabupaten Sarmi',v:'Sarmi'},{l:'Kabupaten Supiori',v:'Supiori'},{l:'Kabupaten Tolikara',v:'Tolikara'},{l:'Kabupaten Waropen',v:'Waropen'},{l:'Kabupaten Yahukimo',v:'Yahukimo'},{l:'Kabupaten Yalimo',v:'Yalimo'},
                                {l:'Kota Sorong',v:'Sorong'},
                                {l:'Kabupaten Fakfak',v:'Fakfak'},{l:'Kabupaten Kaimana',v:'Kaimana'},{l:'Kabupaten Manokwari',v:'Manokwari'},{l:'Kabupaten Manokwari Selatan',v:'Manokwari Selatan'},{l:'Kabupaten Maybrat',v:'Maybrat'},{l:'Kabupaten Pegunungan Arfak',v:'Pegunungan Arfak'},{l:'Kabupaten Raja Ampat',v:'Raja Ampat'},{l:'Kabupaten Sorong',v:'Sorong'},{l:'Kabupaten Sorong Selatan',v:'Sorong Selatan'},{l:'Kabupaten Tambrauw',v:'Tambrauw'},{l:'Kabupaten Teluk Bintuni',v:'Teluk Bintuni'},{l:'Kabupaten Teluk Wondama',v:'Teluk Wondama'}
                            ],
                            get filtered() {
                                if (this.query.length < 1) return [];
                                return this.places.filter(p => p.l.toLowerCase().includes(this.query.toLowerCase())).slice(0, 8);
                            }
                         }"
                         @click.outside="open = false">
                        <label for="tempat_lahir" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Tempat Lahir <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="tempat_lahir" name="tempat_lahir" autocomplete="off"
                                class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                                placeholder="Ketik nama kota / kabupaten..."
                                x-model="query"
                                @focus="open = filtered.length > 0"
                                @input="open = filtered.length > 0">
                            <div x-show="open && filtered.length > 0" x-transition
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
                                 style="display:none;">
                                <div class="p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                    <template x-for="place in filtered" :key="place.l">
                                        <button type="button"
                                            class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                                            @click="query = place.v; open = false;">
                                            <span class="font-medium" x-text="place.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
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
                        <input type="tel" id="no_telepon" name="no_telepon" maxlength="15"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="08xxx (maks. 15 digit)" value="{{ old('no_telepon') }}"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="jenis_kelamin" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jenis Kelamin <span class="text-red-600">*</span>
                        </label>
                        <div x-data="{ open: false, val: '{{ old('jenis_kelamin') }}', opts: [{v:'L',l:'Laki-laki'},{v:'P',l:'Perempuan'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" id="jenis_kelamin" name="jenis_kelamin" :value="val">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kewarganegaraan & Status Pernikahan --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Kewarganegaraan <span class="text-red-600">*</span>
                        </label>
                        <div x-data="{ open: false, val: '{{ old('kewarganegaraan') }}', opts: [{v:'WNI',l:'WNI (Warga Negara Indonesia)'},{v:'WNA',l:'WNA (Warga Negara Asing)'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" id="kewarganegaraan" name="kewarganegaraan" :value="val">
                            <button type="button" id="kewarganegaraan_btn" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Status Pernikahan <span class="text-red-600">*</span>
                        </label>
                        <div x-data="{ open: false, val: '{{ old('status_pernikahan') }}', opts: [{v:'Belum Kawin',l:'Belum Kawin'},{v:'Kawin',l:'Kawin'},{v:'Cerai Hidup',l:'Cerai Hidup'},{v:'Cerai Mati',l:'Cerai Mati'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" id="status_pernikahan" name="status_pernikahan" :value="val">
                            <button type="button" id="status_pernikahan_btn" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? val : '— Pilih —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Alamat Domisili --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="alamat_domisili" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Alamat Domisili <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alamat_domisili" name="alamat_domisili" rows="2"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 resize-y transition"
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ old('alamat_domisili') }}</textarea>
                    </div>
                    {{-- Alamat KTP --}}
                    <div class="col-span-2 flex flex-col gap-1.5">
                        <label for="alamat_ktp" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Alamat Sesuai KTP <span class="text-red-600">*</span>
                        </label>
                        <textarea id="alamat_ktp" name="alamat_ktp" rows="2"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 resize-y transition"
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ old('alamat_ktp') }}</textarea>
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
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jenjang Pendidikan <span class="text-red-600">*</span>
                        </label>
                        <div x-data="{ open: false, val: '{{ old('jenjang') }}', opts: [{v:'S1',l:'S1 (Sarjana)'},{v:'S2',l:'S2 (Magister)'},{v:'S3',l:'S3 (Doktor)'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" id="jenjang" name="jenjang" :value="val">
                            <button type="button" id="jenjang_btn" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih jenjang —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="ipk" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            IPK <span class="text-red-600">*</span>
                        </label>
                        <input type="number" id="ipk" name="ipk" min="0" max="4" step="0.01"
                            class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                            placeholder="Contoh: 3.75" value="{{ old('ipk') }}"
                            oninput="if(parseFloat(this.value)>4){this.value='4'}">
                    </div>
                    {{-- Institusi --}}
                    <div class="col-span-2 flex flex-col gap-1.5"
                         x-data="{
                            open: false,
                            query: '{{ old('institusi') }}',
                            suggestions: ['Universitas Indonesia','Universitas Gadjah Mada','Institut Teknologi Bandung','Institut Pertanian Bogor','Universitas Airlangga','Universitas Diponegoro','Universitas Brawijaya','Universitas Padjadjaran','Universitas Hasanuddin','Institut Teknologi Sepuluh Nopember','Universitas Andalas','Universitas Sumatera Utara','Universitas Sebelas Maret','Universitas Negeri Yogyakarta','Universitas Pendidikan Indonesia','Universitas Lampung','Universitas Sriwijaya','Universitas Mataram','Universitas Sam Ratulangi','Universitas Udayana','Universitas Telkom','Universitas Bina Nusantara','Universitas Trisakti','Universitas Tarumanagara','Universitas Mercu Buana','Universitas Gunadarma','Universitas Atma Jaya','Universitas Sanata Dharma','Universitas Islam Indonesia','Universitas Muhammadiyah Malang','Universitas Negeri Malang','Universitas Jember','Universitas Syiah Kuala','Universitas Tanjungpura','Institut Agama Islam Negeri'],
                            get filtered() { return this.query.length < 1 ? [] : this.suggestions.filter(s => s.toLowerCase().includes(this.query.toLowerCase())).slice(0,8); }
                         }"
                         @click.outside="open = false">
                        <label for="institusi" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Institusi Pendidikan <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="institusi" name="institusi" autocomplete="off"
                                class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                                placeholder="Ketik nama universitas / perguruan tinggi..."
                                x-model="query"
                                @focus="open = filtered.length > 0"
                                @input="open = filtered.length > 0">
                            <div x-show="open && filtered.length > 0" x-transition
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
                                 style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="item in filtered" :key="item">
                                        <button type="button"
                                            class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                                            @click="query = item; open = false; document.getElementById('institusi').value = item;">
                                            <span x-text="item"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Prodi --}}
                    <div class="col-span-2 flex flex-col gap-1.5"
                         x-data="{
                            open: false,
                            query: '{{ old('prodi_pendidikan') }}',
                            suggestions: ['Teknik Informatika','Sistem Informasi','Ilmu Komputer','Teknik Elektro','Teknik Mesin','Teknik Sipil','Teknik Kimia','Teknik Industri','Teknik Lingkungan','Matematika','Fisika','Kimia','Biologi','Statistika','Akuntansi','Manajemen','Ekonomi Pembangunan','Ilmu Komunikasi','Hubungan Internasional','Hukum','Kedokteran','Keperawatan','Farmasi','Psikologi','Pendidikan Matematika','Pendidikan Bahasa Inggris','Pendidikan Bahasa Indonesia','Arsitektur','Agribisnis','Agroteknologi','Peternakan','Kehutanan','Ilmu Administrasi Negara','Sosiologi','Antropologi','Pendidikan Dokter Gigi','Rekayasa Perangkat Lunak','Kecerdasan Buatan','Teknologi Informasi','Bisnis Digital','Desain Komunikasi Visual','Desain Produk Industri'],
                            get filtered() { return this.query.length < 1 ? [] : this.suggestions.filter(s => s.toLowerCase().includes(this.query.toLowerCase())).slice(0,8); }
                         }"
                         @click.outside="open = false">
                        <label for="prodi_pendidikan" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Program Studi <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="prodi_pendidikan" name="prodi_pendidikan" autocomplete="off"
                                class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm placeholder-gray-400 transition"
                                placeholder="Ketik nama program studi..."
                                x-model="query"
                                @focus="open = filtered.length > 0"
                                @input="open = filtered.length > 0">
                            <div x-show="open && filtered.length > 0" x-transition
                                 class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
                                 style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="item in filtered" :key="item">
                                        <button type="button"
                                            class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                                            @click="query = item; open = false; document.getElementById('prodi_pendidikan').value = item;">
                                            <span x-text="item"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Akreditas & No Ijazah --}}
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Akreditasi Prodi <span class="text-gray-400 normal-case ml-1 px-1.5 py-0.5 bg-gray-100 rounded text-[0.65rem] font-medium">Opsional</span>
                        </label>
                        <div x-data="{ open: false, val: '{{ old('akreditas') }}', opts: [{v:'A',l:'A'},{v:'B',l:'B'},{v:'C',l:'C'},{v:'Unggul',l:'Unggul'},{v:'Baik Sekali',l:'Baik Sekali'},{v:'Baik',l:'Baik'},{v:'Tidak Terakreditasi',l:'Tidak Terakreditasi'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" name="akreditas" :value="val">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih Akreditasi —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="no_ijazah" class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            No. Ijazah <span class="text-gray-400 normal-case ml-1 px-1.5 py-0.5 bg-gray-100 rounded text-[0.65rem] font-medium">Opsional</span>
                        </label>
                        <input type="text" id="no_ijazah" name="no_ijazah" maxlength="15"
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
                        <div x-data="{ open: false, val: '{{ old('kategori_sertifikat') }}', opts: [{v:'kompetensi',l:'Kompetensi'},{v:'keahlian_khusus',l:'Keahlian Khusus'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" name="kategori_sertifikat" :value="val">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih kategori —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
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
                        <div x-data="{ open: false, val: '{{ old('jenis_tes_bahasa') }}', opts: [{v:'PBT',l:'PBT (Paper-Based TOEFL)'},{v:'TOEFL_ITP',l:'TOEFL (ITP/Institutional)'},{v:'EPrT',l:'EPrT (English Proficiency Test)'},{v:'CBT',l:'CBT (Computer-Based TOEFL)'},{v:'IBT',l:'IBT (Internet-Based TOEFL)'},{v:'IELTS',l:'IELTS'},{v:'AcEPT',l:'AcEPT (Academic English Proficiency Test)'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" name="jenis_tes_bahasa" :value="val">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih jenis tes —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
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
        <div class="step-content bg-white rounded-xl shadow-md overflow-hidden" id="step-5" x-data="{ nidnValue: '{{ old('nidn', '') }}' }">
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
                            x-model="nidnValue"
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
                        <label class="text-[0.72rem] font-bold text-gray-600 uppercase tracking-wide">
                            Jabatan Fungsional Akademik
                            <span class="ml-1 normal-case font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded text-[0.65rem]">Jika ada</span>
                        </label>
                        <div x-data="{ open: false, val: '{{ old('jabatan_akademik', 'non_jabatan') }}', opts: [{v:'non_jabatan',l:'Non Jabatan (NJAD)'},{v:'asisten_ahli',l:'Asisten Ahli (AA)'},{v:'lektor',l:'Lektor (L)'},{v:'lektor_kepala',l:'Lektor Kepala (LK)'},{v:'guru_besar',l:'Guru Besar (GB)'}] }" @click.outside="open = false" class="relative">
                            <input type="hidden" id="jabatan_akademik" name="jabatan_akademik" :value="val">
                            <button type="button" id="jabatan_akademik_btn" @click="open = !open"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-lg border bg-gray-50 text-sm transition-all"
                                :class="val ? 'border-gray-200 text-gray-800' : 'border-gray-200 text-gray-400'">
                                <span x-text="val ? opts.find(o=>o.v===val)?.l : '— Pilih jabatan —'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden" style="display:none;">
                                <div class="p-1.5 space-y-0.5">
                                    <template x-for="opt in opts" :key="opt.v">
                                        <button type="button" @click="val = opt.v; open = false"
                                            class="w-full text-left px-3 py-2.5 text-sm rounded-lg transition-colors"
                                            :class="val === opt.v ? 'bg-gray-100 text-gray-900 font-semibold' : 'hover:bg-gray-100 text-gray-700'">
                                            <span x-text="opt.l"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
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
                            placeholder="Tuliskan bidang riset atau topik penelitian yang Anda minati...">{{ old('minat_riset') }}</textarea>
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
                        <input type="file" id="kartu_dosen" name="kartu_dosen" class="hidden" accept=".jpg,.jpeg,.png" onchange="showFileName(this,'kartu-name')">
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

    // Field dropdown Alpine (hidden input) — dibaca via name selector
    const dropdownFields = ['jenis_kelamin', 'kewarganegaraan', 'status_pernikahan', 'jenjang'];

    // Batas ukuran file per input (dalam KB) — harus sama dengan validasi server
    const fileMaxSizes = {
        ijazah: 5120, transkrip: 5120,
        cv: 5120, pas_foto: 2048, ktp: 2048,
        sertifikat_kompetensi: 5120, sertifikat_bahasa: 5120,
        kartu_dosen: 2048,
    };
    // File input yang ada di tiap step
    const stepFileFields = {
        3: ['ijazah', 'transkrip'],
        4: ['cv', 'pas_foto', 'ktp', 'sertifikat_kompetensi', 'sertifikat_bahasa'],
        5: ['kartu_dosen'],
    };

    function validateStepFiles(step) {
        const fields = stepFileFields[step] || [];
        for (const id of fields) {
            const el = document.getElementById(id);
            if (!el || !el.files || el.files.length === 0) continue;
            const file = el.files[0];
            const maxKB = fileMaxSizes[id] || 5120;
            if (file.size > maxKB * 1024) {
                showToast('Ukuran File Terlalu Besar', `File ${id.replace(/_/g,' ')} maksimal ${(maxKB/1024)}MB. Ukuran file Anda ${(file.size/1024/1024).toFixed(1)}MB.`, 'error');
                return false;
            }
        }
        return true;
    }

    function validateStep(step) {
        const fields = requiredFields[step] || [];
        let valid = true;
        let firstErrorBtn = null;

        fields.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;

            // Untuk hidden input Alpine dropdown, cek value DOM property
            const val = el.value ? el.value.trim() : '';
            const btnId = id + '_btn';
            const btn   = document.getElementById(btnId);

            // Reset styling
            if (btn) btn.classList.remove('border-red-400', 'ring-1', 'ring-red-300');
            else el.classList.remove('error');

            if (!val) {
                valid = false;
                if (btn) {
                    btn.classList.add('border-red-400', 'ring-1', 'ring-red-300');
                    if (!firstErrorBtn) firstErrorBtn = btn;
                } else {
                    el.classList.add('error');
                    if (!firstErrorBtn) firstErrorBtn = el;
                }
            }
        });

        if (!valid) {
            showToast('Error', 'Harap lengkapi semua kolom yang wajib diisi (ditandai *).', 'error');
            if (firstErrorBtn) firstErrorBtn.focus();
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

            const no_telp = document.getElementById('no_telepon').value;
            if (!/^08[0-9]{8,13}$/.test(no_telp)) {
                document.getElementById('no_telepon').classList.add('error');
                showToast('Peringatan', 'Format No. Telepon harus diawali "08" dan berisi angka (10-15 digit).', 'warning');
                return false;
            }
        }

        // Validasi ukuran file untuk step yang punya input file
        if (!validateStepFiles(step)) return false;

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

    async function nextStep(from) {
        if (!validateStep(from)) return;

        if (from === 1) {
            const email = document.getElementById('email').value;
            const token = document.querySelector('input[name="_token"]').value;
            const btn = document.querySelector('#step-1 button');
            
            try {
                // Disable button & show loading state
                btn.disabled = true;
                btn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memeriksa...</span>';

                const response = await fetch("{{ route('register.check-email') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();

                if (!response.ok || !data.valid) {
                    document.getElementById('email').classList.add('error');
                    showToast('Validasi Gagal', data.message || 'Email tidak valid atau sudah digunakan.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Lanjut <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                    return;
                }
            } catch (error) {
                showToast('Error', 'Terjadi kesalahan jaringan saat mengecek email.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Lanjut <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                return;
            }

            // Restore button
            btn.disabled = false;
            btn.innerHTML = 'Lanjut <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
        }

        // ── Step 2: Cek NIK & No. Telepon sebelum lanjut ke Step 3 ──
        if (from === 2) {
            const nik        = document.getElementById('nik').value;
            const noTelepon  = document.getElementById('no_telepon').value;
            const token      = document.querySelector('input[name="_token"]').value;
            const btn2       = document.querySelector('#step-2 button[onclick="nextStep(2)"]');

            try {
                btn2.disabled = true;
                btn2.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memeriksa...</span>';

                const response = await fetch("{{ route('register.check-identity') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ nik, no_telepon: noTelepon })
                });
                const data = await response.json();

                btn2.disabled = false;
                btn2.innerHTML = 'Lanjut <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';

                if (!response.ok || !data.valid) {
                    const fieldEl = document.getElementById(data.field === 'no_telepon' ? 'no_telepon' : 'nik');
                    if (fieldEl) fieldEl.classList.add('error');
                    showToast('Validasi Gagal', data.message || 'Data sudah terdaftar.', 'error');
                    return;
                }
            } catch (err) {
                btn2.disabled = false;
                btn2.innerHTML = 'Lanjut <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
                showToast('Error', 'Terjadi kesalahan jaringan.', 'error');
                return;
            }
        }

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
