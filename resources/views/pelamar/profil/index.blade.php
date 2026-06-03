@extends('layouts.admin')

@section('title', 'Profil Lengkap Pelamar')

@section('content')

<div class="space-y-6" x-data="{
    isEditing: {{ ($errors->any() || empty($pelamar->nik)) ? 'true' : 'false' }},
    showEdu2: {{ old('jenjang_2', $pelamar->jenjang_2) ? 'true' : 'false' }},
    showEdu3: {{ old('jenjang_3', $pelamar->jenjang_3) ? 'true' : 'false' }},
    jenjang1: '{{ old('jenjang', $pelamar->jenjang) }}',
    jenjang2: '{{ old('jenjang_2', $pelamar->jenjang_2) }}',
    jenjang3: '{{ old('jenjang_3', $pelamar->jenjang_3) }}',
    nidn: '{{ old('nidn', $pelamar->nidn) }}',
    homebase: '{{ old('homebase', $pelamar->homebase) }}',
    otpMode: false,
    otpCode: '',
    isLoadingOtp: false,
    otpMessage: '',
    otpCountdown: 60,
    otpExpired: false,
    otpTimer: null,
    startCountdown() {
        this.otpCountdown = 60;
        this.otpExpired = false;
        if (this.otpTimer) clearInterval(this.otpTimer);
        this.otpTimer = setInterval(() => {
            this.otpCountdown--;
            if (this.otpCountdown <= 0) {
                this.otpExpired = true;
                clearInterval(this.otpTimer);
            }
        }, 1000);
    },
    get otpTimeDisplay() {
        const s = Math.max(0, this.otpCountdown);
        return String(Math.floor(s/60)).padStart(2,'0') + ':' + String(s%60).padStart(2,'0');
    },
    get otpArcOffset() {
        const circ = 100.53;
        return circ * (1 - Math.max(0, this.otpCountdown) / 60);
    },
    sendOtp() {
        this.isLoadingOtp = true;
        fetch('{{ route('email.otp.send') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            this.isLoadingOtp = false;
            if (res.status === 200) {
                this.otpMode = true;
                this.otpCode = '';
                if(window.showProfilToast) window.showProfilToast('OTP Terkirim', res.body.message, 'success');
                this.startCountdown();
            } else {
                if(window.showProfilToast) window.showProfilToast('Gagal', res.body.message, 'error');
            }
        })
        .catch(err => {
            this.isLoadingOtp = false;
            if(window.showProfilToast) window.showProfilToast('Kesalahan Jaringan', 'Terjadi kesalahan saat menghubungi server.', 'error');
        });
    },
    verifyOtp() {
        if(this.otpCode.length !== 6) return;
        this.isLoadingOtp = true;
        fetch('{{ route('email.otp.verify') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ otp: this.otpCode })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                if(window.showProfilToast) window.showProfilToast('Verifikasi Berhasil', 'Memuat ulang halaman...', 'success');
                if (this.otpTimer) clearInterval(this.otpTimer);
                this.otpMode = false;
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                this.isLoadingOtp = false;
                if(window.showProfilToast) window.showProfilToast('Verifikasi Gagal', res.body.message, 'error');
            }
        })
        .catch(err => {
            this.isLoadingOtp = false;
            if(window.showProfilToast) window.showProfilToast('Kesalahan Jaringan', 'Terjadi kesalahan saat memverifikasi.', 'error');
        });
    },
    // ── Phone OTP (WhatsApp) ─────────────────
    phoneOtpModal: false,
    phoneOtpCode: '',
    isLoadingPhoneOtp: false,
    phoneOtpCountdown: 60,
    phoneOtpExpired: false,
    phoneOtpTimer: null,
    phoneOtpSent: false,
    startPhoneCountdown() {
        this.phoneOtpCountdown = 300; // 5 minutes
        this.phoneOtpExpired = false;
        if (this.phoneOtpTimer) clearInterval(this.phoneOtpTimer);
        this.phoneOtpTimer = setInterval(() => {
            this.phoneOtpCountdown--;
            if (this.phoneOtpCountdown <= 0) {
                this.phoneOtpExpired = true;
                clearInterval(this.phoneOtpTimer);
            }
        }, 1000);
    },
    get phoneOtpTimeDisplay() {
        const s = Math.max(0, this.phoneOtpCountdown);
        return String(Math.floor(s/60)).padStart(2,'0') + ':' + String(s%60).padStart(2,'0');
    },
    get phoneOtpArcOffset() {
        const circ = 100.53;
        return circ * (1 - Math.max(0, this.phoneOtpCountdown) / 300);
    },
    sendPhoneOtp() {
        this.isLoadingPhoneOtp = true;
        fetch('{{ route('phone.otp.send') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            this.isLoadingPhoneOtp = false;
            if (res.status === 200) {
                this.phoneOtpSent = true;
                this.phoneOtpCode = '';
                this.phoneOtpModal = true;
                this.startPhoneCountdown();
            } else {
                if(window.showProfilToast) window.showProfilToast('Gagal', res.body.message, 'error');
            }
        })
        .catch(err => {
            this.isLoadingPhoneOtp = false;
            if(window.showProfilToast) window.showProfilToast('Kesalahan Jaringan', 'Terjadi kesalahan saat menghubungi server.', 'error');
        });
    },
    verifyPhoneOtp() {
        if(this.phoneOtpCode.length !== 6) return;
        this.isLoadingPhoneOtp = true;
        fetch('{{ route('phone.otp.verify') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ otp: this.phoneOtpCode })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                if(window.showProfilToast) window.showProfilToast('Verifikasi Berhasil', 'Memuat ulang halaman...', 'success');
                if (this.phoneOtpTimer) clearInterval(this.phoneOtpTimer);
                this.phoneOtpModal = false;
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                this.isLoadingPhoneOtp = false;
                if(window.showProfilToast) window.showProfilToast('Verifikasi Gagal', res.body.message, 'error');
            }
        })
        .catch(err => {
            this.isLoadingPhoneOtp = false;
            if(window.showProfilToast) window.showProfilToast('Kesalahan Jaringan', 'Terjadi kesalahan saat memverifikasi.', 'error');
        });
    }
}">

    <!-- Single Card -->
    <form id="mainProfilForm" action="{{ route('pelamar.profil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- RED HEADER -->
        <div class="bg-gradient-to-r from-[#7a1111] via-[#8b1515] to-[#6e1010] p-6 md:p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm ring-2 ring-white/30">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($pelamar->nama ?? 'P', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">{{ $pelamar->nama ?? '-' }}</h1>
                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-red-200 text-xs">Terdaftar: {{ $pelamar->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-show="!isEditing" @click="isEditing = true"
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-all shadow-sm" title="Edit Profil">
                        Edit Profil
                    </button>
                    <button type="button" x-show="isEditing" @click="isEditing = false"
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <!-- CONTENT: Full Profile -->
        <div class="p-6 md:p-8 space-y-8">

            {{-- Flash success setelah kirim ulang --}}
            @if(session('status') === 'verification-link-sent')
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-semibold text-green-700">Email verifikasi berhasil dikirim! Silakan cek kotak masuk Anda.</p>
            </div>
            @endif

            {{-- 1. DATA DIRI --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Data Diri
                </h3>
                {{-- 3-col grid: Nama(2) + Telp(1) | Email(3) | NIK(1)+TempatLahir(1)+TglLahir(1) | JK(1)+WN(1)+Status(1) | Alamat full --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-5">

                    {{-- Baris 1: Nama | NIK | Jenis Kelamin --}}
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Nama Lengkap</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->nama ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="nama" value="{{ old('nama',$pelamar->nama) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        @error('nama')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">NIK (KTP)</p>
                        <p x-show="!isEditing" class="text-sm font-semibold font-mono text-gray-700 mt-0.5">{{ $pelamar->nik ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="nik" value="{{ old('nik',$pelamar->nik) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        @error('nik')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenis Kelamin</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelamar->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</p>
                        <div x-show="isEditing" x-cloak class="flex gap-4 mt-2">
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin',$pelamar->jenis_kelamin)=='L'?'checked':'' }} class="text-[#8b1515] focus:ring-[#8b1515]"> Laki-laki</label>
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin',$pelamar->jenis_kelamin)=='P'?'checked':'' }} class="text-[#8b1515] focus:ring-[#8b1515]"> Perempuan</label>
                        </div>
                        @error('jenis_kelamin')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>

                    {{-- Baris 2: Tempat Lahir | Tanggal Lahir | Kewarganegaraan --}}
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tempat Lahir</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->tempat_lahir ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="tempat_lahir" value="{{ old('tempat_lahir',$pelamar->tempat_lahir) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        @error('tempat_lahir')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tanggal Lahir</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->tanggal_lahir ? $pelamar->tanggal_lahir->format('d M Y') : '-' }}</p>
                        <input x-show="isEditing" x-cloak type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir',$pelamar->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        @error('tanggal_lahir')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Kewarganegaraan</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->kewarganegaraan ?: '-' }}</p>
                        <select x-show="isEditing" x-cloak name="kewarganegaraan" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            <option value="">-</option>
                            <option value="WNI" {{ old('kewarganegaraan',$pelamar->kewarganegaraan)=='WNI'?'selected':'' }}>WNI</option>
                            <option value="WNA" {{ old('kewarganegaraan',$pelamar->kewarganegaraan)=='WNA'?'selected':'' }}>WNA</option>
                        </select>
                        @error('kewarganegaraan')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>

                    {{-- Baris 3: Status Pernikahan | No Telepon | Email --}}
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Status Pernikahan</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->status_pernikahan ?: '-' }}</p>
                        <select x-show="isEditing" x-cloak name="status_pernikahan" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            <option value="">-</option>
                            <option value="Belum Kawin" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Belum Kawin'?'selected':'' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Kawin'?'selected':'' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Cerai Hidup'?'selected':'' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_pernikahan',$pelamar->status_pernikahan)=='Cerai Mati'?'selected':'' }}>Cerai Mati</option>
                        </select>
                        @error('status_pernikahan')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Telepon / WA</p>
                        <div x-show="!isEditing" class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <p class="text-sm font-semibold text-gray-700">{{ $pelamar->no_telepon ?: '-' }}</p>
                            @if($pelamar->no_telepon)
                                @if($pelamar->phone_verified_at)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-green-50 border border-green-200 text-[0.6rem] font-bold text-green-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Terverifikasi
                                    </span>
                                @else
                                    <button type="button" @click="sendPhoneOtp()" :disabled="isLoadingPhoneOtp"
                                        class="text-[0.65rem] font-bold text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md transition-colors shadow-sm whitespace-nowrap flex-shrink-0 inline-flex items-center gap-1 disabled:opacity-50">
                                        <span x-text="isLoadingPhoneOtp ? 'Mengirim...' : 'Verifikasi by WA'"></span>
                                    </button>
                                @endif
                            @endif
                        </div>
                        <input x-show="isEditing" x-cloak type="text" name="no_telepon" value="{{ old('no_telepon',$pelamar->no_telepon) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        @error('no_telepon')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="md:row-span-3 flex flex-col justify-start">
                        <div x-show="!isEditing" class="w-full">
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Email</p>
                            
                            <!-- Normal Mode -->
                            <div x-show="!otpMode" class="flex items-center gap-2 mt-0.5">
                                <p class="text-sm font-medium text-gray-800">{{ $pelamar->user?->email ?: '-' }}</p>
                                @if(!auth()->user()->hasVerifiedEmail())
                                    <button type="button" @click="sendOtp" :disabled="isLoadingOtp"
                                        class="text-[0.65rem] font-bold text-white bg-amber-600 hover:bg-amber-700 px-3 py-1 rounded-md transition-colors shadow-sm whitespace-nowrap disabled:opacity-50 flex-shrink-0">
                                        <span x-text="isLoadingOtp ? 'Mengirim...' : 'Verifikasi Sekarang'"></span>
                                    </button>
                                @endif
                            </div>

                            <!-- OTP Mode dipindah ke modal -->
                        </div>
                        <div x-show="isEditing" x-cloak>
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Email</p>
                            <input type="email" name="email" value="{{ old('email', $pelamar->user?->email) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            @error('email')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Baris 4: Alamat Domisili & KTP --}}
                    <div class="col-span-1 md:col-span-2"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Domisili</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_domisili ?: '-' }}</p>
                        <textarea x-show="isEditing" x-cloak name="alamat_domisili" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">{{ old('alamat_domisili',$pelamar->alamat_domisili) }}</textarea>
                        @error('alamat_domisili')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-span-1 md:col-span-2"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Alamat Sesuai KTP</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->alamat_ktp ?: '-' }}</p>
                        <textarea x-show="isEditing" x-cloak name="alamat_ktp" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">{{ old('alamat_ktp',$pelamar->alamat_ktp) }}</textarea>
                        @error('alamat_ktp')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>

                </div>
            </div>

            {{-- 2. RIWAYAT PENDIDIKAN --}}
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Riwayat Pendidikan</h3>
                    <button type="button" x-show="isEditing && (!showEdu2 || !showEdu3)" x-cloak @click="if(!showEdu2){showEdu2=true}else{showEdu3=true}"
                        class="text-[0.65rem] font-black text-[#8b1515] px-3 py-1.5 rounded-lg bg-[#8b1515]/5 hover:bg-[#8b1515]/10 uppercase tracking-widest transition-colors">
                        + Tambah Jenjang
                    </button>
                </div>
                
                <div class="space-y-4">
                    {{-- Jenjang 1 --}}
                    <div class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-[#8b1515]/40 py-2 relative">
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenjang</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="jenjang" x-model="jenjang1" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="S1" :hidden="jenjang2 === 'S1' || jenjang3 === 'S1'" :disabled="jenjang2 === 'S1' || jenjang3 === 'S1'">S1</option>
                                <option value="S2" :hidden="jenjang2 === 'S2' || jenjang3 === 'S2'" :disabled="jenjang2 === 'S2' || jenjang3 === 'S2'">S2</option>
                                <option value="S3" :hidden="jenjang2 === 'S3' || jenjang3 === 'S3'" :disabled="jenjang2 === 'S3' || jenjang3 === 'S3'">S3</option>
                            </select>
                            @error('jenjang')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Institusi</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->institusi ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="institusi" value="{{ old('institusi',$pelamar->institusi) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            @error('institusi')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Prodi</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan" value="{{ old('prodi_pendidikan',$pelamar->prodi_pendidikan) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Akreditas</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->akreditas ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="akreditas" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['A','B','C','Unggul','Baik Sekali','Baik','Tidak Terakreditasi'] as $akr)
                                <option value="{{ $akr }}" {{ old('akreditas',$pelamar->akreditas)==$akr?'selected':'' }}>{{ $akr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Ijazah</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->no_ijazah ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="no_ijazah" value="{{ old('no_ijazah',$pelamar->no_ijazah) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">IPK</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk" value="{{ old('ipk',$pelamar->ipk) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Ijazah</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">@if($pelamar->file_ijazah)<a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_ijazah" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_ijazah)<a href="{{ asset('storage/'.$pelamar->file_ijazah) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                            @error('file_ijazah')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-span-2 md:col-span-1"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Transkrip</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">@if($pelamar->file_transkrip)<a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_transkrip" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_transkrip)<a href="{{ asset('storage/'.$pelamar->file_transkrip) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                            @error('file_transkrip')<span class="text-xs text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Jenjang 2 --}}
                    <div x-show="showEdu2 || (!isEditing && {{ $pelamar->jenjang_2 ? 'true' : 'false' }})" class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2 relative" x-cloak>
                        <button type="button" x-show="isEditing" @click="showEdu2=false; jenjang2=''" class="absolute -left-2 -top-1 w-5 h-5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center text-[10px] font-bold" title="Hapus Jenjang">✕</button>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenjang</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_2 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="jenjang_2" x-model="jenjang2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="S1" :hidden="jenjang1 === 'S1' || jenjang3 === 'S1'" :disabled="jenjang1 === 'S1' || jenjang3 === 'S1'">S1</option>
                                <option value="S2" :hidden="jenjang1 === 'S2' || jenjang3 === 'S2'" :disabled="jenjang1 === 'S2' || jenjang3 === 'S2'">S2</option>
                                <option value="S3" :hidden="jenjang1 === 'S3' || jenjang3 === 'S3'" :disabled="jenjang1 === 'S3' || jenjang3 === 'S3'">S3</option>
                            </select>
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Institusi</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->institusi_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="institusi_2" value="{{ old('institusi_2',$pelamar->institusi_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Prodi</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan_2" value="{{ old('prodi_pendidikan_2',$pelamar->prodi_pendidikan_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Akreditas</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->akreditas_2 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="akreditas_2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['A','B','C','Unggul','Baik Sekali','Baik','Tidak Terakreditasi'] as $akr)
                                <option value="{{ $akr }}" {{ old('akreditas_2',$pelamar->akreditas_2)==$akr?'selected':'' }}>{{ $akr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Ijazah</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="no_ijazah_2" value="{{ old('no_ijazah_2',$pelamar->no_ijazah_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">IPK</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_2 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk_2" value="{{ old('ipk_2',$pelamar->ipk_2) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Ijazah</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_ijazah_2" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_ijazah_2)<a href="{{ asset('storage/'.$pelamar->file_ijazah_2) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                        <div class="col-span-2 md:col-span-1"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Transkrip</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_transkrip_2" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_transkrip_2)<a href="{{ asset('storage/'.$pelamar->file_transkrip_2) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                    </div>

                    {{-- Jenjang 3 --}}
                    <div x-show="showEdu3 || (!isEditing && {{ $pelamar->jenjang_3 ? 'true' : 'false' }})" class="grid grid-cols-2 md:grid-cols-8 gap-x-6 gap-y-3 pl-4 border-l-[3px] border-gray-200 py-2 relative" x-cloak>
                        <button type="button" x-show="isEditing" @click="showEdu3=false; jenjang3=''" class="absolute -left-2 -top-1 w-5 h-5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center text-[10px] font-bold" title="Hapus Jenjang">✕</button>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenjang</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-[#8b1515] mt-0.5">{{ $pelamar->jenjang_3 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="jenjang_3" x-model="jenjang3" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="S1" :hidden="jenjang1 === 'S1' || jenjang2 === 'S1'" :disabled="jenjang1 === 'S1' || jenjang2 === 'S1'">S1</option>
                                <option value="S2" :hidden="jenjang1 === 'S2' || jenjang2 === 'S2'" :disabled="jenjang1 === 'S2' || jenjang2 === 'S2'">S2</option>
                                <option value="S3" :hidden="jenjang1 === 'S3' || jenjang2 === 'S3'" :disabled="jenjang1 === 'S3' || jenjang2 === 'S3'">S3</option>
                            </select>
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Institusi</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->institusi_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="institusi_3" value="{{ old('institusi_3',$pelamar->institusi_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Prodi</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->prodi_pendidikan_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="prodi_pendidikan_3" value="{{ old('prodi_pendidikan_3',$pelamar->prodi_pendidikan_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Akreditas</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->akreditas_3 ?: '-' }}</p>
                            <select x-show="isEditing" x-cloak name="akreditas_3" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['A','B','C','Unggul','Baik Sekali','Baik','Tidak Terakreditasi'] as $akr)
                                <option value="{{ $akr }}" {{ old('akreditas_3',$pelamar->akreditas_3)==$akr?'selected':'' }}>{{ $akr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">No. Ijazah</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->no_ijazah_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="text" name="no_ijazah_3" value="{{ old('no_ijazah_3',$pelamar->no_ijazah_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">IPK</p>
                            <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->ipk_3 ?: '-' }}</p>
                            <input x-show="isEditing" x-cloak type="number" step="0.01" name="ipk_3" value="{{ old('ipk_3',$pelamar->ipk_3) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Ijazah</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_ijazah_3" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_ijazah_3)<a href="{{ asset('storage/'.$pelamar->file_ijazah_3) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                        <div class="col-span-2 md:col-span-1"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Transkrip</p>
                            <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank" class="text-[#8b1515] hover:text-red-900 underline text-xs font-bold">Preview</a>@else-@endif</p>
                            <div x-show="isEditing" x-cloak class="mt-1"><input type="file" name="file_transkrip_3" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_transkrip_3)<a href="{{ asset('storage/'.$pelamar->file_transkrip_3) }}" target="_blank" class="text-[#8b1515] underline text-xs">Preview</a>@endif</div>
                        </div>
                    </div>
                    
                    <p x-show="!isEditing && !{{ $pelamar->jenjang ? 'true' : 'false' }}" class="text-sm text-gray-400 italic">-</p>
                </div>
            </div>

            {{-- 3. DOKUMEN PENDUKUNG --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Dokumen Pendukung
                </h3>
                
                {{-- View Mode --}}
                <div x-show="!isEditing" class="space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">CV (Resume)</p>
                            @if($pelamar->file_cv)
                                <a href="{{ asset('storage/' . $pelamar->file_cv) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">Pas Foto Formal</p>
                            @if($pelamar->file_pas_foto)
                                <a href="{{ asset('storage/' . $pelamar->file_pas_foto) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">Scan KTP</p>
                            @if($pelamar->file_ktp)
                                <a href="{{ asset('storage/' . $pelamar->file_ktp) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">{{ $pelamar->kategori_sertifikat ?: 'Sertifikat' }}</p>
                            @if($pelamar->file_sertifikat)
                                <a href="{{ asset('storage/' . $pelamar->file_sertifikat) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>
                            @else
                                <p class="text-sm font-semibold text-gray-700 mt-0.5">-</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div x-show="isEditing" x-cloak class="space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4">
                        <div>
                            <label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">CV (Resume)</label>
                            <div class="mt-1">
                                <input type="file" name="file_cv" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_cv)<a href="{{ asset('storage/'.$pelamar->file_cv) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                        <div>
                            <label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Pas Foto Formal</label>
                            <div class="mt-1">
                                <input type="file" name="file_pas_foto" accept=".jpg,.jpeg" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_pas_foto)<a href="{{ asset('storage/'.$pelamar->file_pas_foto) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                        <div>
                            <label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Scan KTP</label>
                            <div class="mt-1">
                                <input type="file" name="file_ktp" accept=".jpg,.jpeg" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_ktp)<a href="{{ asset('storage/'.$pelamar->file_ktp) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[0.55rem] font-black text-gray-400 uppercase">Kategori Sertifikat</p>
                            <select name="kategori_sertifikat" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                <option value="kompetensi" {{ old('kategori_sertifikat',$pelamar->kategori_sertifikat)=='kompetensi'?'selected':'' }}>Kompetensi</option>
                                <option value="keahlian_khusus" {{ old('kategori_sertifikat',$pelamar->kategori_sertifikat)=='keahlian_khusus'?'selected':'' }}>Keahlian Khusus</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Profesi</label>
                            <div class="mt-1">
                                <input type="file" name="file_sertifikat" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">
                                @if($pelamar->file_sertifikat)<a href="{{ asset('storage/'.$pelamar->file_sertifikat) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. SERTIFIKAT BAHASA INGGRIS --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Sertifikat Bahasa Inggris
                </h3>
                
                {{-- View Mode --}}
                <div x-show="!isEditing">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p><p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->jenis_tes_bahasa ?: '-' }}</p></div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Skor Bahasa</p><p class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->skor_bahasa ?: '-' }}</p></div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tanggal Tes</p><p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->tanggal_tes_bahasa ? $pelamar->tanggal_tes_bahasa->format('d M Y') : '-' }}</p></div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Sertifikat Bahasa</p>
                            @if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>@else<p class="text-sm font-semibold text-gray-700 mt-0.5">-</p>@endif
                        </div>
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div x-show="isEditing" x-cloak>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Jenis Tes Bahasa</p>
                            <select name="jenis_tes_bahasa" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                                <option value="">-</option>
                                @foreach(['PBT','TOEFL_ITP','EPrT','CBT','IBT','IELTS','AcEPT'] as $tes)
                                <option value="{{ $tes }}" {{ old('jenis_tes_bahasa',$pelamar->jenis_tes_bahasa)==$tes?'selected':'' }}>{{ $tes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Skor Bahasa</p>
                            <input type="number" step="0.01" name="skor_bahasa" value="{{ old('skor_bahasa',$pelamar->skor_bahasa) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Tanggal Tes</p>
                            <input type="date" name="tanggal_tes_bahasa" value="{{ old('tanggal_tes_bahasa',$pelamar->tanggal_tes_bahasa?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                        </div>
                        <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Bahasa</label>
                            <div class="mt-1"><input type="file" name="file_sertifikat_bahasa" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_sertifikat_bahasa)<a href="{{ asset('storage/'.$pelamar->file_sertifikat_bahasa) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. DATA AKADEMIK (DOSEN) --}}
            <div>
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Data Akademik (Dosen)
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">NIDN</p>
                        <p x-show="!isEditing" class="text-sm font-semibold font-mono text-gray-700 mt-0.5">{{ $pelamar->nidn ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="nidn" x-model="nidn" value="{{ old('nidn',$pelamar->nidn) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">Homebase</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->homebase ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="text" name="homebase" x-model="homebase" value="{{ old('homebase',$pelamar->homebase) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                    </div>
                    <div>
                        @php
                            $jfaLabels = ['guru_besar' => 'Guru Besar (GB)', 'lektor_kepala' => 'Lektor Kepala (LK)', 'lektor' => 'Lektor (L)', 'asisten_ahli' => 'Asisten Ahli (AA)', 'non_jabatan' => 'Non Jabatan (NJAD)'];
                        @endphp
                        <p class="text-[0.55rem] font-black text-gray-400 uppercase">Jabatan Fungsional Akademik</p>
                        <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5">{{ $pelamar->jabatan_akademik && $pelamar->jabatan_akademik !== 'non_jabatan' ? ($jfaLabels[$pelamar->jabatan_akademik] ?? ucwords(str_replace('_', ' ', $pelamar->jabatan_akademik))) : 'Non Jabatan (NJAD)' }}</p>
                        <select x-show="isEditing" x-cloak name="jabatan_akademik" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">
                            <option value="non_jabatan" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)==='non_jabatan' || !$pelamar->jabatan_akademik ?'selected':'' }}>Non Jabatan (NJAD)</option>
                            <option value="guru_besar" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='guru_besar'?'selected':'' }}>Guru Besar (GB)</option>
                            <option value="lektor_kepala" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='lektor_kepala'?'selected':'' }}>Lektor Kepala (LK)</option>
                            <option value="lektor" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='lektor'?'selected':'' }}>Lektor (L)</option>
                            <option value="asisten_ahli" {{ old('jabatan_akademik',$pelamar->jabatan_akademik)=='asisten_ahli'?'selected':'' }}>Asisten Ahli (AA)</option>
                        </select>
                    </div>
                    <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">H-Index</p>
                        <p x-show="!isEditing" class="text-sm font-bold text-gray-800 mt-0.5">{{ $pelamar->h_index ?: '-' }}</p>
                        <input x-show="isEditing" x-cloak type="number" name="h_index" value="{{ old('h_index',$pelamar->h_index) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1" >
                    </div>
                </div>
                <div class="mt-4"><p class="text-[0.55rem] font-black text-gray-400 uppercase">Minat Riset & Keahlian</p>
                    <p x-show="!isEditing" class="text-sm font-semibold text-gray-700 mt-0.5 leading-relaxed">{{ $pelamar->minat_riset ?: '-' }}</p>
                    <textarea x-show="isEditing" x-cloak name="minat_riset" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition-all mt-1">{{ old('minat_riset',$pelamar->minat_riset) }}</textarea>
                </div>
            </div>

            {{-- 6. DOKUMEN PELAMAR BER-HOMEBASE --}}
            <div x-show="(!isEditing && '{{ $pelamar->nidn }}' !== '') || (isEditing && nidn && homebase)" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 pb-2 border-b border-gray-100">
                    Dokumen Pelamar Ber-Homebase
                </h3>
                
                {{-- View Mode --}}
                <div x-show="!isEditing">
                    @php
                        $homebaseDocs = [
                            ['label' => 'SK Jabatan Akademik (JAD)', 'file' => $pelamar->file_jad],
                            ['label' => 'SK Penetapan Angka Kredit (PAK)', 'file' => $pelamar->file_pak],
                            ['label' => 'Kartu Dosen', 'file' => $pelamar->file_kartu_dosen],
                            ['label' => 'Bukti Registrasi Dosen', 'file' => $pelamar->file_registrasi_dosen],
                            ['label' => 'SK Inpassing', 'file' => $pelamar->file_inpassing],
                            ['label' => 'Sertifikat Pendidik (Serdik)', 'file' => $pelamar->file_serdik],
                            ['label' => 'SKPP Serdos', 'file' => $pelamar->file_skpp_serdos],
                            ['label' => 'Surat Pernyataan Lolos Butuh', 'file' => $pelamar->file_pernyataan_lolos_butuh],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        @foreach($homebaseDocs as $doc)
                        <div><p class="text-[0.55rem] font-black text-gray-400 uppercase">{{ $doc['label'] }}</p>
                            @if($doc['file'])<a href="{{ asset('storage/'.$doc['file']) }}" target="_blank" class="text-sm font-bold text-[#8b1515] hover:underline mt-0.5 inline-block">Preview</a>@else<p class="text-sm font-semibold text-gray-700 mt-0.5">-</p>@endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Edit Mode --}}
                <div x-show="isEditing" x-cloak class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4">
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">SK Jabatan Akademik (JAD)</label>
                        <div class="mt-1"><input type="file" name="file_jad" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_jad)<a href="{{ asset('storage/'.$pelamar->file_jad) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">SK Angka Kredit (PAK)</label>
                        <div class="mt-1"><input type="file" name="file_pak" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_pak)<a href="{{ asset('storage/'.$pelamar->file_pak) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Kartu Dosen</label>
                        <div class="mt-1"><input type="file" name="file_kartu_dosen" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_kartu_dosen)<a href="{{ asset('storage/'.$pelamar->file_kartu_dosen) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Bukti Registrasi Dosen</label>
                        <div class="mt-1"><input type="file" name="file_registrasi_dosen" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_registrasi_dosen)<a href="{{ asset('storage/'.$pelamar->file_registrasi_dosen) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">SK Inpassing</label>
                        <div class="mt-1"><input type="file" name="file_inpassing" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_inpassing)<a href="{{ asset('storage/'.$pelamar->file_inpassing) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Sertifikat Pendidik (Serdik)</label>
                        <div class="mt-1"><input type="file" name="file_serdik" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_serdik)<a href="{{ asset('storage/'.$pelamar->file_serdik) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">SKPP Serdos</label>
                        <div class="mt-1"><input type="file" name="file_skpp_serdos" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_skpp_serdos)<a href="{{ asset('storage/'.$pelamar->file_skpp_serdos) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                    <div><label class="text-[0.55rem] font-black text-gray-400 uppercase mb-0.5 block">Surat Pernyataan Lolos Butuh</label>
                        <div class="mt-1"><input type="file" name="file_pernyataan_lolos_butuh" accept=".pdf" class="text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[0.6rem] file:font-black file:bg-[#8b1515]/10 file:text-[#8b1515] file:cursor-pointer cursor-pointer">@if($pelamar->file_pernyataan_lolos_butuh)<a href="{{ asset('storage/'.$pelamar->file_pernyataan_lolos_butuh) }}" target="_blank" class="text-[#8b1515] underline text-xs ml-1">Preview</a>@endif</div>
                    </div>
                </div>
            </div>

            <div x-show="isEditing" x-cloak class="pt-6 border-t border-gray-100 flex items-center justify-center gap-3">
                
                <button type="submit" class="px-8 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-lg shadow-md shadow-[#8b1515]/20 hover:bg-red-900 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
    </form>

    {{-- ── WhatsApp Phone OTP Modal ── --}}
    <template x-teleport="body">
        <div x-show="phoneOtpModal" x-transition.opacity
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="phoneOtpModal = false; if(phoneOtpTimer) clearInterval(phoneOtpTimer);" style="display: none;">
            <div x-show="phoneOtpModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-[24px] shadow-2xl w-full max-w-[380px] overflow-hidden text-center relative">

            {{-- Close --}}
            <button type="button" @click="phoneOtpModal = false; if(phoneOtpTimer) clearInterval(phoneOtpTimer);"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="p-8 pb-6">
                {{-- WhatsApp Icon --}}
                <div class="mx-auto mb-4 w-16 h-16 bg-green-500 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>

                <h2 class="text-xl font-extrabold text-gray-800 mb-1">Input Kode OTP</h2>
                <p class="text-sm text-gray-500 mb-6">Kami telah mengirim kode OTP 6 digit ke WhatsApp <span class="font-bold text-gray-700">{{ $pelamar->no_telepon }}</span></p>

                {{-- Step 2: Enter OTP --}}
                <div>
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="relative flex items-center gap-1 flex-shrink-0">
                            <input type="text" x-model="phoneOtpCode" @input="verifyPhoneOtp()" maxlength="6"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-text z-10"
                                :disabled="isLoadingPhoneOtp" autocomplete="one-time-code" inputmode="numeric">
                            <template x-for="i in 6">
                                <div class="w-9 h-11 border-[1.5px] rounded-lg flex items-center justify-center text-base font-bold transition-all duration-150 select-none"
                                     :class="{
                                        'border-green-500 ring-1 ring-green-500/20 bg-white text-green-700': phoneOtpCode.length === i-1 || (i===6 && phoneOtpCode.length===6),
                                        'border-gray-300 bg-white text-gray-800': phoneOtpCode.length !== i-1 && !(i===6 && phoneOtpCode.length===6)
                                     }">
                                    <span x-text="phoneOtpCode[i-1] || ''"></span>
                                </div>
                            </template>
                        </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center gap-2 mt-2">
                        <div x-show="!phoneOtpExpired" class="text-sm font-semibold text-gray-500">
                            Sisa waktu <span :class="phoneOtpCountdown <= 30 ? 'text-red-500' : 'text-green-600'" x-text="phoneOtpTimeDisplay"></span>
                        </div>
                        <button type="button" x-show="phoneOtpExpired" x-cloak @click="sendPhoneOtp()" :disabled="isLoadingPhoneOtp"
                                class="text-sm font-bold text-green-600 hover:text-green-700 underline underline-offset-2 disabled:opacity-50 transition-colors">
                            Kirim Ulang Kode OTP
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>

    {{-- ── Email OTP Modal ── --}}
    <template x-teleport="body">
        <div x-show="otpMode" x-transition.opacity
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
             @click.self="otpMode = false; if(otpTimer) clearInterval(otpTimer);" style="display: none;">
            <div x-show="otpMode"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-[24px] shadow-2xl w-full max-w-[380px] overflow-hidden text-center relative">

            {{-- Close --}}
            <button type="button" @click="otpMode = false; if(otpTimer) clearInterval(otpTimer);"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="p-8 pb-6">
                {{-- Email Icon --}}
                <div class="mx-auto mb-4 w-16 h-16 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>

                <h2 class="text-xl font-extrabold text-gray-800 mb-1">Input Kode OTP</h2>
                <p class="text-sm text-gray-500 mb-6">Kami telah mengirim kode OTP 6 digit ke email <span class="font-bold text-gray-700">{{ $pelamar->user?->email }}</span></p>

                {{-- Enter OTP --}}
                <div>
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="relative flex items-center gap-1 flex-shrink-0">
                            <input type="text" x-model="otpCode" @input="verifyOtp()" maxlength="6"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-text z-10"
                                :disabled="isLoadingOtp" autocomplete="one-time-code" inputmode="numeric">
                            <template x-for="i in 6">
                                <div class="w-9 h-11 border-[1.5px] rounded-lg flex items-center justify-center text-base font-bold transition-all duration-150 select-none"
                                     :class="{
                                        'border-amber-500 ring-1 ring-amber-500/20 bg-white text-amber-700': otpCode.length === i-1 || (i===6 && otpCode.length===6),
                                        'border-gray-300 bg-white text-gray-800': otpCode.length !== i-1 && !(i===6 && otpCode.length===6)
                                     }">
                                    <span x-text="otpCode[i-1] || ''"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center gap-2 mt-2">
                        <div x-show="!otpExpired" class="text-sm font-semibold text-gray-500">
                            Sisa waktu <span :class="otpCountdown <= 10 ? 'text-red-500' : 'text-amber-600'" x-text="otpTimeDisplay"></span>
                        </div>
                        <button type="button" x-show="otpExpired" x-cloak @click="sendOtp()" :disabled="isLoadingOtp"
                                class="text-sm font-bold text-amber-600 hover:text-amber-700 underline underline-offset-2 disabled:opacity-50 transition-colors">
                            Kirim Ulang Kode OTP
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>

   
</div>

<form id="verifyEmailForm" action="{{ route('verification.send') }}" method="POST" class="hidden">
    @csrf
</form>

@push('scripts')
<script>
    window.showProfilToast = function(title, message, type) {
        var existing = document.getElementById('profil-toast');
        if (existing) existing.remove();

        var colors = {
            error:   { bar: '#ef4444', icon: '#ef4444' },
            warning: { bar: '#f59e0b', icon: '#f59e0b' },
            success: { bar: '#22c55e', icon: '#22c55e' },
            info:    { bar: '#3b82f6', icon: '#3b82f6' }
        };
        var c = colors[type] || colors.info;
        var icons = {
            error:   '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
            warning: '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
            success: '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
            info:    '<svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };

        var toast = document.createElement('div');
        toast.id = 'profil-toast';
        toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:9999;display:flex;align-items:center;gap:12px;min-width:320px;max-width:420px;background:white;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.12);border:1px solid #e5e7eb;overflow:hidden;animation:toastSlideIn 0.3s ease forwards;';
        toast.innerHTML =
            '<div style="width:5px;align-self:stretch;background:' + c.bar + ';flex-shrink:0;border-radius:8px 0 0 8px;"></div>' +
            '<div style="width:36px;height:36px;border-radius:50%;background:' + c.icon + ';display:flex;align-items:center;justify-content:center;flex-shrink:0;margin:12px 0 12px 10px;">' + (icons[type] || icons.info) + '</div>' +
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
        setTimeout(function() { if (toast.parentElement) toast.remove(); }, 5000);
    }
</script>
@endpush

@endsection


