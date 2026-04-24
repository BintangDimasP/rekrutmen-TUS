@extends('layouts.admin')

@section('title', 'Penjadwalan Seleksi')

@section('content')
    <div class="max-w-7xl mx-auto" x-data="jadwalForm">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.jadwal.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-800 hover:border-red-800 transition-all flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Penjadwalan Seleksi</h1>
                <p class="text-xs text-gray-500 mt-0.5">Jadwalkan wawancara & micro teaching — sistem mendeteksi bentrok
                    otomatis.</p>
            </div>
        </div>

        {{-- Error Laravel --}}
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-sm font-semibold text-red-800 mb-1">Gagal menyimpan jadwal:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li class="text-xs text-red-700">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Error Flash Alpine --}}
        <div x-show="error" x-transition x-cloak
            class="mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center gap-3">
            <p class="text-xs text-amber-800" x-text="error"></p>
            <button @click="error = ''" class="ml-auto text-amber-400 hover:text-amber-600 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.jadwal.store') }}" @submit.prevent="submitForm($event)">
            @csrf

            {{-- ══ INFORMASI DASAR ══ --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
                <p class="text-[0.65rem] font-semibold text-gray-400 uppercase tracking-widest mb-3">Informasi Dasar</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- Prodi --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Program Studi</label>
                        <select name="prodi_id" x-model="prodiId" @change="onProdiChange()"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white">
                            <option value="">— Pilih —</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lowongan --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Lowongan</label>
                        <div x-show="loadingLowongan"
                            class="flex items-center gap-2 text-xs text-gray-400 px-3 py-2 border border-gray-200 rounded-lg h-[38px]">
                            <svg class="w-3.5 h-3.5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                                <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                            Memuat...
                        </div>
                        <select name="lowongan_id" x-model="lowonganId" :disabled="!prodiId" x-show="!loadingLowongan"
                            @change="onLowonganChange()"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">— Pilih —</option>
                            <template x-for="l in lowongans" :key="l.id">
                                <option :value="l.id" x-text="l.nama_posisi"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Tanggal Seleksi</label>
                        <input type="date" name="tanggal" x-model="tanggal" @change="onTanggalChange()"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition">
                    </div>
                </div>
            </div>

            {{-- ══ TABEL PENJADWALAN ══ --}}

            {{-- Toolbar tabel --}}
            <div class="flex items-center justify-between gap-3 mb-3"
                x-show="lowonganId && !loadingPelamar && pelamars.length > 0">
                <div class="flex items-center gap-3">
                    <p class="text-sm font-semibold text-gray-700">Jadwal Per Pelamar</p>
                    <span x-show="readyCount > 0"
                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                        x-text="`${readyCount} siap`"></span>
                    <span x-show="!tanggal"
                        class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1">
                        Pilih tanggal agar cek bentrok aktif.
                    </span>
                </div>
                <input type="text" x-model="search" placeholder="Cari pelamar..."
                    class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white w-48">
            </div>

            {{-- State: belum pilih lowongan --}}
            <div x-show="!lowonganId"
                class="flex items-center justify-center h-36 rounded-xl border border-dashed border-gray-200 bg-gray-50 mb-5">
                <p class="text-xs text-gray-400">Isi informasi dasar di atas terlebih dahulu.</p>
            </div>

            {{-- State: loading --}}
            <div x-show="loadingPelamar" class="flex items-center justify-center h-36 mb-5">
                <svg class="w-6 h-6 text-red-800 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
            </div>

            {{-- State: kosong --}}
            <div x-show="!loadingPelamar && lowonganId && pelamars.length === 0"
                class="flex items-center justify-center h-36 rounded-xl border border-dashed border-gray-200 bg-gray-50 mb-5">
                <p class="text-xs text-gray-400">Tidak ada pelamar siap dijadwalkan di lowongan ini.</p>
            </div>

            {{-- TABEL UTAMA --}}
            <div x-show="!loadingPelamar && pelamars.length > 0"
                class="rounded-xl border border-gray-200 overflow-hidden mb-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" style="min-width: 900px;">
                        <thead>
                            <tr>
                                <th rowspan="2"
                                    class="bg-red-800 text-white text-[0.6rem] font-medium px-3 py-2.5 w-8 align-middle">#
                                </th>
                                <th rowspan="2"
                                    class="bg-red-800 text-white text-[0.6rem] font-medium px-3 py-2.5 w-44 align-middle">
                                    Pelamar</th>

                                {{-- Wawancara header --}}
                                <th colspan="3"
                                    class="bg-red-800 text-white text-[0.6rem] font-semibold px-3 py-2 border-l border-red-700">
                                    Wawancara
                                    <span class="font-normal text-red-300 ml-1">08.00–14.00</span>
                                </th>

                                {{-- Micro Teaching header --}}
                                <th colspan="2"
                                    class="bg-red-800 text-white text-[0.6rem] font-semibold px-3 py-2 border-l border-red-700">
                                    Micro Teaching
                                    <span class="font-normal text-red-300 ml-1">13.00–16.00</span>
                                </th>

                                <th rowspan="2"
                                    class="bg-red-800 text-white text-[0.6rem] font-medium px-3 py-2.5 w-16 text-center border-l border-red-700 align-middle">
                                    Status</th>
                            </tr>
                            <tr>
                                {{-- Wawancara sub-headers --}}
                                <th
                                    class="bg-red-900 text-red-200 text-[0.6rem] font-medium px-3 py-1.5 border-l border-red-700 w-52">
                                    Penguji
                                </th>
                                <th class="bg-red-900 text-red-200 text-[0.6rem] font-medium px-3 py-1.5 w-36">Sesi</th>
                                <th class="bg-red-900 text-red-200 text-[0.6rem] font-medium px-3 py-1.5 w-44">Link Meeting
                                </th>

                                {{-- Micro sub-headers --}}
                                <th
                                    class="bg-red-900 text-red-200 text-[0.6rem] font-medium px-3 py-1.5 border-l border-red-700 w-52">
                                    Penguji
                                </th>
                                <th class="bg-red-900 text-red-200 text-[0.6rem] font-medium px-3 py-1.5 w-36">Sesi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(p, idx) in filteredPelamars" :key="p.id">
                                <tr class="border-t border-gray-100 align-top transition-colors" :class="{
                                        'bg-green-50': isRowComplete(p.id) && !getConflictWarning(p.id),
                                        'bg-red-50':   getConflictWarning(p.id),
                                        'hover:bg-gray-50': !isRowComplete(p.id) && !getConflictWarning(p.id)
                                    }">

                                    {{-- No --}}
                                    <td class="py-3 px-3 text-xs text-gray-400 font-mono" x-text="idx + 1"></td>

                                    {{-- Pelamar --}}
                                    <td class="py-3 px-3">
                                        <div class="flex items-start gap-2">
                                            <span class="mt-1.5 flex-shrink-0 w-1.5 h-1.5 rounded-full"
                                                :class="getConflictWarning(p.id) ? 'bg-red-500' : isRowComplete(p.id) ? 'bg-green-500' : 'bg-gray-300'"></span>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800" x-text="p.nama"></p>
                                                <p class="text-[0.65rem] text-gray-400 font-mono" x-text="p.email"></p>
                                                <p x-show="getConflictWarning(p.id)" x-text="getConflictWarning(p.id)"
                                                    class="text-[0.62rem] text-red-600 mt-1 leading-snug"></p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ═══ WAWANCARA: Penguji (multi, tag style) ═══ --}}
                                    <td class="py-3 px-3 border-l border-gray-100">
                                        <div x-show="loadingPenguji" class="text-[0.65rem] text-gray-300 italic">Memuat...
                                        </div>
                                        <template x-if="!loadingPenguji">
                                            <div>
                                                {{-- Tags penguji terpilih --}}
                                                <div class="flex flex-wrap gap-1 mb-1.5"
                                                    x-show="assignments[p.id]?.wawancara?.pengujiIds?.length > 0">
                                                    <template x-for="pgId in assignments[p.id]?.wawancara?.pengujiIds ?? []"
                                                        :key="pgId">
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 border border-red-100 text-[0.62rem] text-red-800">
                                                            <span x-text="getPengujiNama(pgId)"></span>
                                                            <button type="button" @click="removePengujiW(p.id, pgId)"
                                                                class="text-red-300 hover:text-red-600 transition-colors ml-0.5">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    stroke-width="2.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                            {{-- hidden input untuk submit --}}
                                                            <input type="hidden"
                                                                :name="`schedule[${p.id}][wawancara][penguji_ids][]`"
                                                                :value="pgId">
                                                        </span>
                                                    </template>
                                                </div>
                                                {{-- Dropdown tambah penguji --}}
                                                <select @change="addPengujiW(p.id, $event)"
                                                    class="w-full px-2 py-1.5 rounded-md border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white">
                                                    <option value="">+ Tambah penguji</option>
                                                    <template x-for="pg in availablePengujisW(p.id)" :key="pg.id">
                                                        <option :value="pg.id" x-text="`${pg.nama} (${pg.kode})`"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </template>
                                    </td>

                                    {{-- ═══ WAWANCARA: Sesi ═══ --}}
                                    <td class="py-3 px-3">
                                        <template x-if="!(assignments[p.id]?.wawancara?.pengujiIds?.length > 0)">
                                            <span class="text-[0.65rem] text-gray-300 italic">Pilih penguji dulu</span>
                                        </template>
                                        <template x-if="assignments[p.id]?.wawancara?.pengujiIds?.length > 0">
                                            <select :name="`schedule[${p.id}][wawancara][sesi]`"
                                                x-model="assignments[p.id].wawancara.sesi" @change="onSesiChangeW(p.id)"
                                                class="w-full px-2 py-1.5 rounded-md border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white"
                                                :class="{ 'border-red-400 bg-red-50': isSesiConflictW(p.id) }">
                                                <option value="">— Pilih Sesi —</option>
                                                <template x-for="(info, sesiKey) in sessions.tahap1" :key="sesiKey">
                                                    <option :value="sesiKey"
                                                        :disabled="isSesiDisabledW(p.id, parseInt(sesiKey))"
                                                        x-text="sesiLabel('tahap1', sesiKey, isSesiDisabledW(p.id, parseInt(sesiKey)))">
                                                    </option>
                                                </template>
                                            </select>
                                        </template>
                                    </td>

                                    {{-- ═══ WAWANCARA: Link Meeting ═══ --}}
                                    <td class="py-3 px-3">
                                        <template x-if="!assignments[p.id]?.wawancara?.sesi">
                                            <span class="text-[0.65rem] text-gray-300 italic">Isi sesi dulu</span>
                                        </template>
                                        <template x-if="assignments[p.id]?.wawancara?.sesi">
                                            <input type="url" :name="`schedule[${p.id}][wawancara][link]`"
                                                x-model="assignments[p.id].wawancara.link"
                                                placeholder="https://meet.google.com/..."
                                                class="w-full px-2 py-1.5 rounded-md border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white placeholder-gray-300">
                                        </template>
                                    </td>

                                    {{-- ═══ MICRO: Penguji (multi, tag style) ═══ --}}
                                    <td class="py-3 px-3 border-l border-gray-100">
                                        <div x-show="loadingPenguji" class="text-[0.65rem] text-gray-300 italic">Memuat...
                                        </div>
                                        <template x-if="!loadingPenguji">
                                            <div>
                                                <div class="flex flex-wrap gap-1 mb-1.5"
                                                    x-show="assignments[p.id]?.micro?.pengujiIds?.length > 0">
                                                    <template x-for="pgId in assignments[p.id]?.micro?.pengujiIds ?? []"
                                                        :key="pgId">
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 border border-red-100 text-[0.62rem] text-red-800">
                                                            <span x-text="getPengujiNama(pgId)"></span>
                                                            <button type="button" @click="removePengujiM(p.id, pgId)"
                                                                class="text-red-300 hover:text-red-600 transition-colors ml-0.5">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    stroke-width="2.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                            <input type="hidden"
                                                                :name="`schedule[${p.id}][micro][penguji_ids][]`"
                                                                :value="pgId">
                                                        </span>
                                                    </template>
                                                </div>
                                                <select @change="addPengujiM(p.id, $event)"
                                                    class="w-full px-2 py-1.5 rounded-md border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white">
                                                    <option value="">+ Tambah penguji</option>
                                                    <template x-for="pg in availablePengujisM(p.id)" :key="pg.id">
                                                        <option :value="pg.id" x-text="`${pg.nama} (${pg.kode})`"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </template>
                                    </td>

                                    {{-- ═══ MICRO: Sesi ═══ --}}
                                    <td class="py-3 px-3">
                                        <template x-if="!(assignments[p.id]?.micro?.pengujiIds?.length > 0)">
                                            <span class="text-[0.65rem] text-gray-300 italic">Pilih penguji dulu</span>
                                        </template>
                                        <template x-if="assignments[p.id]?.micro?.pengujiIds?.length > 0">
                                            <select :name="`schedule[${p.id}][micro][sesi]`"
                                                x-model="assignments[p.id].micro.sesi"
                                                class="w-full px-2 py-1.5 rounded-md border border-gray-200 text-xs text-gray-800 focus:outline-none focus:border-red-800 focus:ring-1 focus:ring-red-800 transition bg-white"
                                                :class="{ 'border-red-400 bg-red-50': isSesiConflictM(p.id) }">
                                                <option value="">— Pilih Sesi —</option>
                                                <template x-for="(info, sesiKey) in sessions.tahap2" :key="sesiKey">
                                                    <option :value="sesiKey"
                                                        :disabled="isSesiDisabledM(p.id, parseInt(sesiKey))"
                                                        x-text="sesiLabel('tahap2', sesiKey, isSesiDisabledM(p.id, parseInt(sesiKey)))">
                                                    </option>
                                                </template>
                                            </select>
                                        </template>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3 px-3 text-center border-l border-gray-100">
                                        <template x-if="isRowComplete(p.id) && !getConflictWarning(p.id)">
                                            <span
                                                class="inline-block text-[0.62rem] font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-700">Siap</span>
                                        </template>
                                        <template x-if="getConflictWarning(p.id)">
                                            <span
                                                class="inline-block text-[0.62rem] font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-700">Bentrok</span>
                                        </template>
                                        <template x-if="!isRowComplete(p.id) && !getConflictWarning(p.id)">
                                            <span
                                                class="inline-block text-[0.62rem] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Belum</span>
                                        </template>
                                    </td>

                                </tr>
                            </template>
                            <tr x-show="filteredPelamars.length === 0 && pelamars.length > 0">
                                <td colspan="8" class="py-8 text-center text-xs text-gray-400">Tidak ada hasil pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Ringkasan bawah tabel --}}
                <div x-show="readyCount > 0"
                    class="px-4 py-2.5 border-t border-gray-100 bg-green-50 text-xs text-green-800">
                    <span class="font-semibold" x-text="readyCount"></span> pelamar siap (wawancara + micro) —
                    <span x-text="formatDate(tanggal)"></span>
                </div>
            </div>

            {{-- ══ FOOTER SUBMIT ══ --}}
            <div class="bg-white rounded-xl border border-gray-200 px-5 py-4 flex items-center justify-between gap-4">
                <p x-show="!canSubmit()" class="text-xs text-gray-400">
                    Lengkapi informasi dasar dan jadwal minimal 1 pelamar.
                </p>
                <p x-show="canSubmit()" class="text-xs text-green-700 font-medium">
                    Siap disimpan — <span x-text="readyCount + ' pelamar'"></span>
                </p>
                <button type="submit" :disabled="!canSubmit()" :class="canSubmit()
                            ? 'bg-red-800 hover:bg-red-900 text-white cursor-pointer'
                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                    class="flex-shrink-0 px-6 py-2 text-xs font-semibold rounded-lg transition-colors">
                    Simpan Jadwal
                </button>
            </div>

        </form>
    </div>

    <script>
        /**
         * Struktur assignments (BARU — multi-penguji sebagai array ID, sesi tunggal):
         * {
         *   [pelamar_id]: {
         *     wawancara: { pengujiIds: [1, 3], sesi: '2', link: 'https://...' },
         *     micro:     { pengujiIds: [2],    sesi: '1' }
         *   }
         * }
         *
         * Form name attributes yang dikirim:
         *   schedule[101][wawancara][penguji_ids][]  → array penguji
         *   schedule[101][wawancara][sesi]
         *   schedule[101][wawancara][link]
         *   schedule[101][micro][penguji_ids][]
         *   schedule[101][micro][sesi]
         */
        const _jadwalSessions = @json($sessions);
        const _jadwalBaseUrl = '{{ url("/") }}';

        document.addEventListener('alpine:init', () => {
            Alpine.data('jadwalForm', () => ({

                tanggal: '',
                prodiId: '',
                lowonganId: '',
                search: '',
                error: '',

                lowongans: [],
                pengujis: [],
                pelamars: [],

                loadingLowongan: false,
                loadingPenguji: false,
                loadingPelamar: false,

                sessions: _jadwalSessions,
                takenMap: {},
                assignments: {},

                // ─── COMPUTED ──────────────────────────────────────

                get filteredPelamars() {
                    if (!this.search.trim()) return this.pelamars;
                    const q = this.search.toLowerCase();
                    return this.pelamars.filter(p =>
                        p.nama.toLowerCase().includes(q) ||
                        (p.email && p.email.toLowerCase().includes(q))
                    );
                },

                get readyCount() {
                    return this.pelamars.filter(p =>
                        this.isRowComplete(p.id) && !this.getConflictWarning(p.id)
                    ).length;
                },

                // ─── INIT ──────────────────────────────────────────

                initAssignment(pelamarId) {
                    if (!this.assignments[pelamarId]) {
                        this.assignments[pelamarId] = {
                            wawancara: { pengujiIds: [], sesi: '', link: '' },
                            micro: { pengujiIds: [], sesi: '' },
                        };
                    }
                },

                // ─── HELPERS ───────────────────────────────────────

                getPengujiNama(pgId) {
                    const pg = this.pengujis.find(x => Number(x.id) === Number(pgId));
                    return pg ? `${pg.nama} (${pg.kode})` : `#${pgId}`;
                },

                /** Penguji yang belum dipilih untuk wawancara pelamar ini */
                availablePengujisW(pelamarId) {
                    const selected = this.assignments[pelamarId]?.wawancara?.pengujiIds ?? [];
                    return this.pengujis.filter(pg => !selected.map(Number).includes(Number(pg.id)));
                },

                /** Penguji yang belum dipilih untuk micro pelamar ini */
                availablePengujisM(pelamarId) {
                    const selected = this.assignments[pelamarId]?.micro?.pengujiIds ?? [];
                    return this.pengujis.filter(pg => !selected.map(Number).includes(Number(pg.id)));
                },

                addPengujiW(pelamarId, event) {
                    const val = event.target.value;
                    if (!val) return;
                    const ids = this.assignments[pelamarId].wawancara.pengujiIds;
                    if (!ids.map(Number).includes(Number(val))) ids.push(Number(val));
                    event.target.value = ''; // reset dropdown
                },

                removePengujiW(pelamarId, pgId) {
                    const a = this.assignments[pelamarId].wawancara;
                    a.pengujiIds = a.pengujiIds.filter(id => Number(id) !== Number(pgId));
                    // Jika semua penguji dihapus, kosongkan sesi & link
                    if (!a.pengujiIds.length) { a.sesi = ''; a.link = ''; }
                },

                addPengujiM(pelamarId, event) {
                    const val = event.target.value;
                    if (!val) return;
                    const ids = this.assignments[pelamarId].micro.pengujiIds;
                    if (!ids.map(Number).includes(Number(val))) ids.push(Number(val));
                    event.target.value = '';
                },

                removePengujiM(pelamarId, pgId) {
                    const a = this.assignments[pelamarId].micro;
                    a.pengujiIds = a.pengujiIds.filter(id => Number(id) !== Number(pgId));
                    if (!a.pengujiIds.length) a.sesi = '';
                },

                onSesiChangeW(pelamarId) {
                    const a = this.assignments[pelamarId]?.wawancara;
                    if (a && !a.sesi) a.link = '';
                },

                /**
                 * Apakah sesi wawancara disabled untuk pelamar ini?
                 * Cek semua penguji yang dipilih — jika salah satu bentrok, sesi disabled.
                 */
                isSesiDisabledW(pelamarId, sesiNum) {
                    const ids = this.assignments[pelamarId]?.wawancara?.pengujiIds ?? [];
                    for (const pgId of ids) {
                        if (this._isTakenFor(pgId, 'tahap1', sesiNum)) return true;
                        // cross-conflict: tahap1 S4 ↔ tahap2 S1 (jam 13.00)
                        if (sesiNum === 4 && this._isTakenFor(pgId, 'tahap2', 1)) return true;
                        // in-form: penguji ini sudah dipakai di wawancara pelamar lain sesi sama
                        if (this._inFormConflict(pelamarId, pgId, 'wawancara', sesiNum)) return true;
                        // in-form cross: penguji ini micro S1 di pelamar lain
                        if (sesiNum === 4 && this._inFormConflict(pelamarId, pgId, 'micro', 1)) return true;
                    }
                    return false;
                },

                isSesiConflictW(pelamarId) {
                    const sesi = parseInt(this.assignments[pelamarId]?.wawancara?.sesi ?? '0');
                    if (!sesi) return false;
                    return this.isSesiDisabledW(pelamarId, sesi);
                },

                isSesiDisabledM(pelamarId, sesiNum) {
                    const ids = this.assignments[pelamarId]?.micro?.pengujiIds ?? [];
                    for (const pgId of ids) {
                        if (this._isTakenFor(pgId, 'tahap2', sesiNum)) return true;
                        if (sesiNum === 1 && this._isTakenFor(pgId, 'tahap1', 4)) return true;
                        if (this._inFormConflict(pelamarId, pgId, 'micro', sesiNum)) return true;
                        if (sesiNum === 1 && this._inFormConflict(pelamarId, pgId, 'wawancara', 4)) return true;
                    }
                    return false;
                },

                isSesiConflictM(pelamarId) {
                    const sesi = parseInt(this.assignments[pelamarId]?.micro?.sesi ?? '0');
                    if (!sesi) return false;
                    return this.isSesiDisabledM(pelamarId, sesi);
                },

                /** Cek takenMap dari DB */
                _isTakenFor(pgId, tipe, sesiNum) {
                    const taken = (this.takenMap[pgId] ?? this.takenMap[String(pgId)])?.[tipe] ?? [];
                    return taken.map(Number).includes(sesiNum);
                },

                /** Cek in-form: apakah pgId sudah dipakai di pelamar lain untuk tipe+sesi tsb */
                _inFormConflict(pelamarId, pgId, tipe, sesiNum) {
                    for (const [otherPid, otherA] of Object.entries(this.assignments)) {
                        if (parseInt(otherPid) === parseInt(pelamarId)) continue;
                        const slot = tipe === 'wawancara' ? otherA.wawancara : otherA.micro;
                        if (
                            slot.pengujiIds.map(Number).includes(Number(pgId)) &&
                            parseInt(slot.sesi) === sesiNum
                        ) return true;
                    }
                    return false;
                },

                /**
                 * Bentrok 13.00: penguji yang sama ada di wawancara S4 DAN micro S1
                 * milik pelamar yang sama.
                 */
                getConflictWarning(pelamarId) {
                    const a = this.assignments[pelamarId];
                    if (!a) return '';
                    const wSesi = parseInt(a.wawancara.sesi ?? '0');
                    const mSesi = parseInt(a.micro.sesi ?? '0');
                    if (wSesi !== 4 || mSesi !== 1) return '';
                    const overlap = a.wawancara.pengujiIds.filter(id =>
                        a.micro.pengujiIds.map(Number).includes(Number(id))
                    );
                    if (!overlap.length) return '';
                    const names = overlap.map(id => {
                        const pg = this.pengujis.find(x => Number(x.id) === Number(id));
                        return pg ? pg.nama : `#${id}`;
                    }).join(', ');
                    return `Bentrok 13.00 — ${names} di Wawancara S4 & Micro S1.`;
                },

                isRowComplete(pelamarId) {
                    const a = this.assignments[pelamarId];
                    if (!a) return false;
                    return (
                        a.wawancara.pengujiIds.length > 0 && a.wawancara.sesi !== '' &&
                        a.micro.pengujiIds.length > 0 && a.micro.sesi !== ''
                    );
                },

                canSubmit() {
                    return (
                        this.tanggal &&
                        this.lowonganId &&
                        this.readyCount > 0 &&
                        this.pelamars.every(p => !this.getConflictWarning(p.id))
                    );
                },

                sesiLabel(tipe, sesiKey, isDisabled) {
                    const info = this.sessions[tipe]?.[sesiKey];
                    if (!info) return `Sesi ${sesiKey}${isDisabled ? ' ✗' : ''}`;
                    let time = info.time ?? '';
                    if (!time && info.label) {
                        const m = info.label.match(/(\d{2}[.:]\d{2}\s*[–\-]\s*\d{2}[.:]\d{2})/);
                        time = m ? m[1] : '';
                    }
                    const base = time ? `Sesi ${sesiKey} (${time})` : `Sesi ${sesiKey}`;
                    return isDisabled ? base + ' ✗' : base;
                },

                formatDate(d) {
                    if (!d) return '-';
                    return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    });
                },

                // ─── EVENT HANDLERS ────────────────────────────────

                async onProdiChange() {
                    this.lowonganId = '';
                    this.lowongans = [];
                    this.pengujis = [];
                    this.pelamars = [];
                    this.assignments = {};
                    this.takenMap = {};
                    this.error = '';
                    if (!this.prodiId) return;

                    this.loadingLowongan = this.loadingPenguji = true;
                    try {
                        const [resL, resP] = await Promise.all([
                            fetch(`${_jadwalBaseUrl}/admin/api/lowongan-by-prodi?prodi_id=${this.prodiId}`),
                            fetch(`${_jadwalBaseUrl}/admin/api/penguji-by-prodi?prodi_id=${this.prodiId}`)
                        ]);
                        if (!resL.ok || !resP.ok) throw new Error();
                        this.lowongans = await resL.json();
                        this.pengujis = await resP.json();
                        if (!this.lowongans.length) this.error = 'Tidak ada lowongan aktif untuk prodi ini.';
                        if (!this.pengujis.length) this.error = (this.error ? this.error + ' & ' : '') + 'Tidak ada penguji untuk prodi ini.';
                    } catch {
                        this.error = 'Terjadi kesalahan saat memuat data.';
                    } finally {
                        this.loadingLowongan = this.loadingPenguji = false;
                    }
                },

                async onLowonganChange() {
                    this.pelamars = [];
                    this.assignments = {};
                    this.error = '';
                    if (!this.lowonganId) return;

                    this.loadingPelamar = true;
                    try {
                        const res = await fetch(`${_jadwalBaseUrl}/admin/api/pelamar-by-lowongan?lowongan_id=${this.lowonganId}`);
                        if (!res.ok) throw new Error();
                        this.pelamars = await res.json();
                        this.pelamars.forEach(p => this.initAssignment(p.id));
                        if (!this.pelamars.length) this.error = 'Tidak ada pelamar siap dijadwalkan di lowongan ini.';
                    } catch {
                        this.error = 'Gagal memuat daftar pelamar.';
                    } finally {
                        this.loadingPelamar = false;
                    }
                    if (this.tanggal) await this.loadTakenSessions();
                },

                async onTanggalChange() {
                    Object.values(this.assignments).forEach(a => {
                        a.wawancara.sesi = '';
                        a.wawancara.link = '';
                        a.micro.sesi = '';
                    });
                    this.takenMap = {};
                    if (this.tanggal) await this.loadTakenSessions();
                },

                async loadTakenSessions() {
                    if (!this.tanggal || !this.pengujis.length) return;
                    try {
                        const ids = this.pengujis.map(p => p.id).join(',');
                        const res = await fetch(
                            `${_jadwalBaseUrl}/admin/api/sesi-taken-all?tanggal=${this.tanggal}&penguji_ids=${ids}`
                        );
                        if (!res.ok) throw new Error();
                        this.takenMap = await res.json();
                    } catch (e) {
                        console.error('Gagal memuat sesi terpakai:', e);
                    }
                },

                submitForm(e) {
                    if (!this.canSubmit()) {
                        alert('Harap lengkapi jadwal minimal 1 pelamar (wawancara + micro) dan pastikan tidak ada bentrok waktu.');
                        return;
                    }
                    if (!confirm(`Simpan jadwal untuk ${this.readyCount} pelamar pada ${this.formatDate(this.tanggal)}?`)) return;
                    e.target.submit();
                },
            }));
        });
    </script>

@endsection