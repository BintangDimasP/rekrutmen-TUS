@extends('layouts.admin')

@section('title', 'Jadwal Wawancara & Micro Teaching')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.jadwal.index') }}"
           class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-[#8b1515] hover:border-[#8b1515] transition-all shadow-sm flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Jadwal Wawancara & Micro Teaching</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pilih sesi secara manual untuk setiap pelamar — sistem mendeteksi bentrok secara otomatis.</p>
        </div>
    </div>

    {{-- Error --}}
    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-bold text-red-700 mb-2">⚠ Gagal menyimpan jadwal:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li class="text-sm text-red-600">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Error Flash (Alpine) --}}
    <div x-show="error" x-transition x-cloak class="mb-5 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm text-amber-700 font-medium" x-text="error"></p>
        <button @click="error = ''" class="ml-auto text-amber-400 hover:text-amber-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Alpine.js Form --}}
    <div x-data="jadwalForm(@json($sessions))">
        <form method="POST" action="{{ route('admin.jadwal.store') }}" @submit.prevent="submitForm($event)">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ══ Kolom Kiri: Info Dasar ══════════════════════════════ --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Card Info Dasar --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-[#8b1515] px-5 py-3.5">
                            <h2 class="text-sm font-bold text-white uppercase tracking-wider">① Informasi Dasar</h2>
                        </div>
                        <div class="p-5 space-y-4">

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Tanggal Seleksi</label>
                                <input type="date" name="tanggal" x-model="tanggal" @change="onContextChange()"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            </div>

                            {{-- Prodi --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Program Studi</label>
                                <select name="prodi_id" x-model="prodiId" @change="onProdiChange()"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white">
                                    <option value="">— Pilih Prodi —</option>
                                    @foreach($prodis as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lowongan --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Lowongan</label>
                                <div x-show="loadingLowongan" class="flex items-center gap-2 text-sm text-gray-400 px-4 py-2.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>Memuat...
                                </div>
                                <select name="lowongan_id" x-model="lowonganId" :disabled="!prodiId"
                                        x-show="!loadingLowongan" @change="loadPelamar()"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white disabled:opacity-50">
                                    <option value="">— Pilih Lowongan —</option>
                                    <template x-for="l in lowongans" :key="l.id">
                                        <option :value="l.id" x-text="l.nama_posisi"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Jenis Seleksi --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Jenis Seleksi</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="tipe_seleksi" value="tahap1" x-model="tipeSeleksi" @change="onContextChange()" class="sr-only peer">
                                        <div class="p-3 rounded-xl border-2 text-center transition-all peer-checked:border-[#8b1515] peer-checked:bg-[#8b1515]/5 border-gray-200 hover:border-gray-300">
                                            <div class="text-lg mb-1">🎙</div>
                                            <div class="text-xs font-bold text-gray-700">Wawancara</div>
                                            <div class="text-[0.62rem] text-gray-400">Pagi — 08.00–14.00</div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="tipe_seleksi" value="tahap2" x-model="tipeSeleksi" @change="onContextChange()" class="sr-only peer">
                                        <div class="p-3 rounded-xl border-2 text-center transition-all peer-checked:border-[#8b1515] peer-checked:bg-[#8b1515]/5 border-gray-200 hover:border-gray-300">
                                            <div class="text-lg mb-1">🏫</div>
                                            <div class="text-xs font-bold text-gray-700">Micro Teaching</div>
                                            <div class="text-[0.62rem] text-gray-400">Siang — 13.00–16.00</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Penguji --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">② Pilih Penguji</label>
                                <div x-show="loadingPenguji" class="flex items-center gap-2 text-sm text-gray-400 px-4 py-2.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>Memuat...
                                </div>
                                <select name="penguji_id" x-model="pengujiId" :disabled="!prodiId"
                                        x-show="!loadingPenguji" @change="onContextChange()"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition bg-white disabled:opacity-50">
                                    <option value="">— Pilih Penguji —</option>
                                    <template x-for="p in pengujis" :key="p.id">
                                        <option :value="p.id" x-text="`${p.nama} (${p.kode})`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Slot waktu info box --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700 space-y-1">
                        <p class="font-bold text-blue-800 text-[0.7rem] uppercase mb-2">ℹ Slot Waktu</p>
                        <p class="font-semibold">🎙 Wawancara:</p>
                        <p>S1: 08.00–09.00 &nbsp;|&nbsp; S2: 09.00–10.00</p>
                        <p>S3: 10.00–11.00 &nbsp;|&nbsp; S4: 13.00–14.00</p>
                        <p class="font-semibold mt-1">🏫 Micro Teaching:</p>
                        <p>S1: 13.00–14.00 &nbsp;|&nbsp; S2: 14.00–15.00 &nbsp;|&nbsp; S3: 15.00–16.00</p>
                        <div class="mt-2 bg-amber-100 text-amber-800 p-2 rounded-lg">
                            ⚠ <strong>Bentrok jam:</strong> Wawancara S4 = Micro Teaching S1 (13.00–14.00)
                        </div>
                    </div>

                    {{-- Ringkasan --}}
                    <div x-show="assignedCount > 0" class="bg-[#8b1515]/5 border border-[#8b1515]/20 rounded-xl p-4 text-xs space-y-1">
                        <p class="font-bold text-[#8b1515] uppercase text-[0.7rem] mb-2">③ Ringkasan</p>
                        <p><span class="font-semibold">Tanggal:</span> <span x-text="formatDate(tanggal)"></span></p>
                        <p><span class="font-semibold">Jenis:</span> <span x-text="tipeSeleksi === 'tahap1' ? '🎙 Wawancara' : '🏫 Micro Teaching'"></span></p>
                        <p><span class="font-semibold">Penguji:</span> <span x-text="pengujiName"></span></p>
                        <p><span class="font-semibold text-[#8b1515]">Pelamar diisi sesinya:</span> <span x-text="assignedCount + ' orang'" class="font-bold text-[#8b1515]"></span></p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" :disabled="!canSubmit()"
                            :class="canSubmit() ? 'bg-[#8b1515] hover:bg-red-900 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="w-full py-3 text-white text-sm font-bold rounded-xl shadow transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Simpan Jadwal
                    </button>
                    <p x-show="!canSubmit()" class="text-center text-xs text-gray-400">Lengkapi semua field dan isi sesi minimal 1 pelamar.</p>

                </div>

                {{-- ══ Kolom Kanan: Tabel Pelamar + Pilih Sesi ═════════════ --}}
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

                        {{-- Header Tabel --}}
                        <div class="bg-gray-700 px-5 py-3.5 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-white uppercase tracking-wider">③ Pelamar & Pilih Sesi</h2>
                            <span x-show="assignedCount > 0"
                                  class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 text-white"
                                  x-text="`${assignedCount} pelamar dipilih`"></span>
                        </div>

                        {{-- Search --}}
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" x-model="search" placeholder="Cari nama atau email pelamar..."
                                       class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Hanya pelamar berstatus <span class="font-bold text-blue-600">Seleksi Berkas (Tahap 1)</span> yang tampil.
                                <span x-show="tipeSeleksi && pengujiId && tanggal" class="text-[#8b1515] font-semibold">
                                    Sesi berwarna <span class="bg-red-100 px-1 rounded">merah</span> = sudah terpakai oleh penguji ini atau oleh pelamar lain di form ini.
                                </span>
                            </p>
                        </div>

                        {{-- State: belum pilih --}}
                        <div x-show="!lowonganId" class="flex-1 flex items-center justify-center py-16">
                            <div class="text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm text-gray-400">Pilih <strong>lowongan</strong>, <strong>jenis seleksi</strong>, dan <strong>penguji</strong> terlebih dahulu.</p>
                            </div>
                        </div>

                        {{-- State: loading --}}
                        <div x-show="loadingPelamar" class="flex-1 flex items-center justify-center py-16">
                            <svg class="w-8 h-8 text-[#8b1515] animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        </div>

                        {{-- State: kosong --}}
                        <div x-show="!loadingPelamar && lowonganId && pelamars.length === 0" class="flex-1 flex items-center justify-center py-16">
                            <div class="text-center">
                                <p class="text-sm text-gray-500 font-semibold">Tidak ada pelamar yang siap dijadwalkan</p>
                                <p class="text-xs text-gray-400 mt-1">Belum ada pelamar status <strong>Seleksi Berkas</strong> di lowongan ini.</p>
                            </div>
                        </div>

                        {{-- Tabel Pelamar --}}
                        <div x-show="!loadingPelamar && pelamars.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="py-2.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-8">#</th>
                                        <th class="py-2.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pelamar</th>
                                        <th class="py-2.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Pilih Sesi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <template x-for="(p, idx) in filteredPelamars" :key="p.id">
                                        <tr :class="selectedSessions[p.id] ? 'bg-[#8b1515]/3' : 'hover:bg-gray-50'" class="transition-colors">
                                            {{-- No --}}
                                            <td class="py-3 px-4">
                                                <span class="text-xs text-gray-400 font-mono" x-text="idx + 1"></span>
                                            </td>

                                            {{-- Nama & Email --}}
                                            <td class="py-3 px-4">
                                                <div class="flex items-center gap-2">
                                                    {{-- Indicator terpilih --}}
                                                    <div :class="selectedSessions[p.id] ? 'bg-green-500' : 'bg-gray-200'"
                                                         class="w-2 h-2 rounded-full flex-shrink-0 transition-colors"></div>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-800" x-text="p.nama"></p>
                                                        <p class="text-xs text-gray-400 font-mono" x-text="p.email"></p>
                                                    </div>
                                                </div>
                                                <div x-show="selectedSessions[p.id]" class="mt-1 ml-4">
                                                    <span class="text-[0.65rem] font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded">
                                                        ✓ Sesi dipilih
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Dropdown Sesi --}}
                                            <td class="py-3 px-4">
                                                <template x-if="!tipeSeleksi || !pengujiId || !tanggal">
                                                    <p class="text-xs text-gray-400 italic">Pilih jenis & penguji dulu</p>
                                                </template>
                                                <template x-if="tipeSeleksi && pengujiId && tanggal">
                                                    <select :name="`pelamar_sessions[${p.id}]`"
                                                            x-model="selectedSessions[p.id]"
                                                            class="w-full px-3 py-2 rounded-lg border text-xs focus:outline-none focus:ring-1 transition"
                                                            :class="selectedSessions[p.id] ? 'border-green-300 focus:border-green-400 focus:ring-green-300 bg-green-50' : 'border-gray-200 focus:border-[#8b1515] focus:ring-[#8b1515] bg-white'">
                                                        <option value="">— Belum dipilih —</option>
                                                        <template x-for="(info, sesiKey) in currentSessions" :key="sesiKey">
                                                            <option :value="sesiKey"
                                                                    :disabled="isSessionDisabled(p.id, parseInt(sesiKey))"
                                                                    x-text="info.label + (isSessionDisabled(p.id, parseInt(sesiKey)) ? ' ✗ terpakai' : '')">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <p x-show="filteredPelamars.length === 0 && pelamars.length > 0"
                               class="text-xs text-gray-400 text-center py-6">Tidak ada hasil pencarian.</p>
                        </div>

                    </div>
                </div>

            </div>

        </form>
    </div>

</div>

<script>
function jadwalForm(sessionsData) {
    const baseUrl = '{{ url("/") }}';
    
    return {
        tanggal: '',
        prodiId: '',
        lowonganId: '',
        tipeSeleksi: '',
        pengujiId: '',
        search: '',

        lowongans: [],
        pengujis: [],
        pelamars: [],
        dbTakenSessions: [],
        selectedSessions: {},

        loadingLowongan: false,
        loadingPenguji: false,
        loadingPelamar: false,
        loadingSessions: false,
        error: '',

        sessions: sessionsData,

        get currentSessions() {
            return this.tipeSeleksi ? (this.sessions[this.tipeSeleksi] ?? {}) : {};
        },

        get filteredPelamars() {
            if (!this.search.trim()) return this.pelamars;
            const q = this.search.toLowerCase();
            return this.pelamars.filter(p =>
                p.nama.toLowerCase().includes(q) ||
                (p.email && p.email.toLowerCase().includes(q))
            );
        },

        get assignedCount() {
            return Object.values(this.selectedSessions).filter(s => s && s !== '').length;
        },

        get pengujiName() {
            const p = this.pengujis.find(x => x.id == this.pengujiId);
            return p ? `${p.nama} (${p.kode})` : '-';
        },

        async onProdiChange() {
            this.lowonganId = '';
            this.pengujiId  = '';
            this.lowongans  = [];
            this.pengujis   = [];
            this.pelamars   = [];
            this.selectedSessions = {};
            this.dbTakenSessions  = [];
            this.error = '';

            if (!this.prodiId) return;

            this.loadingLowongan = this.loadingPenguji = true;
            try {
                const [resL, resP] = await Promise.all([
                    fetch(`${baseUrl}/admin/api/lowongan-by-prodi?prodi_id=${this.prodiId}`),
                    fetch(`${baseUrl}/admin/api/penguji-by-prodi?prodi_id=${this.prodiId}`)
                ]);

                if (!resL.ok || !resP.ok) throw new Error('Gagal mengambil data dari server.');

                this.lowongans = await resL.json();
                this.pengujis  = await resP.json();
                
                if (this.lowongans.length === 0) this.error = 'Tidak ada lowongan aktif untuk prodi ini.';
                if (this.pengujis.length === 0) this.error = this.error ? this.error + ' & tidak ada penguji.' : 'Tidak ada penguji untuk prodi ini.';

            } catch(e) { 
                console.error(e); 
                this.error = 'Terjadi kesalahan saat memuat data.';
            } finally {
                this.loadingLowongan = this.loadingPenguji = false;
            }
        },

        async loadPelamar() {
            this.pelamars = [];
            this.selectedSessions = {};
            this.error = '';
            if (!this.lowonganId) return;
            
            this.loadingPelamar = true;
            try {
                const res = await fetch(`${baseUrl}/admin/api/pelamar-by-lowongan?lowongan_id=${this.lowonganId}`);
                if (!res.ok) throw new Error('Gagal mengambil data pelamar.');
                
                this.pelamars = await res.json();
                if (this.pelamars.length === 0) this.error = 'Tidak ada pelamar dengan status Seleksi Berkas (Tahap 1) di lowongan ini.';
            } catch(e) { 
                console.error(e); 
                this.error = 'Gagal memuat daftar pelamar.';
            } finally {
                this.loadingPelamar = false;
            }
        },

        async onContextChange() {
            this.dbTakenSessions = [];
            this.selectedSessions = {};

            if (!this.pengujiId || !this.tanggal || !this.tipeSeleksi) return;
            
            this.loadingSessions = true;
            try {
                const res = await fetch(`${baseUrl}/admin/api/sesi-tersedia?penguji_id=${this.pengujiId}&tanggal=${this.tanggal}&tipe=${this.tipeSeleksi}`);
                if (!res.ok) throw new Error('Gagal mengecek ketersediaan penguji.');
                
                const data = await res.json();
                this.dbTakenSessions = (data.taken ?? []).map(s => s.sesi);
            } catch(e) { 
                console.error(e); 
            } finally {
                this.loadingSessions = false;
            }
        },

        isSessionDisabled(pelamarId, sesiNum) {
            if (this.dbTakenSessions.includes(sesiNum)) return true;
            for (const [pid, s] of Object.entries(this.selectedSessions)) {
                if (parseInt(pid) !== parseInt(pelamarId) && parseInt(s) === sesiNum) return true;
            }
            return false;
        },

        canSubmit() {
            return this.tanggal && this.lowonganId && this.tipeSeleksi && this.pengujiId && this.assignedCount > 0;
        },

        formatDate(d) {
            if (!d) return '-';
            return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },

        submitForm(e) {
            if (!this.canSubmit()) {
                alert('Harap lengkapi semua field dan pilih sesi untuk minimal 1 pelamar.');
                return;
            }
            const jenis = this.tipeSeleksi === 'tahap1' ? 'Wawancara' : 'Micro Teaching';
            if (!confirm(`Simpan ${this.assignedCount} jadwal ${jenis} pada ${this.formatDate(this.tanggal)}?\n\nStatus pelamar akan otomatis diperbarui ke Seleksi Tahap 2.`)) return;
            e.target.submit();
        },
    };
}
</script>

@endsection
