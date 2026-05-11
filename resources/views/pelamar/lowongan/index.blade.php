@extends('layouts.admin')

@section('title', 'Cari Lowongan')

@section('content')
    <div class="max-w-5xl mx-auto space-y-8" x-data="lowonganApp()">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('pelamar.dashboard') }}" class="hover:text-[#8b1515] transition-colors font-medium">Dashboard</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="font-semibold text-gray-800">Cari Lowongan</span>
        </div>

        {{-- Filter Panel --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form id="filterForm" method="GET" action="{{ route('pelamar.lowongan.index') }}"
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                    {{-- Filter Prodi --}}
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                    </div>
                        <select name="prodi_id" onchange="document.getElementById('filterForm').submit()"
                            class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm appearance-none cursor-pointer">
                            <option value="">Semua Prodi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(request()->filled('prodi_id'))
                        <a href="{{ route('pelamar.lowongan.index') }}" class="text-xs text-red-600 hover:underline font-medium">Reset</a>
                    @endif
                </div>

            </form>
        </div>

        {{-- Lowongan Cards Grid (Available) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availableLowongans as $lowongan)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col p-6 relative">

                    {{-- Header: Title & Logo --}}
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="flex-1 pt-1">
                            <h3 class="text-base font-bold text-gray-800 leading-tight">{{ $lowongan->nama_posisi }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</p>
                        </div>
                        @if($lowongan->prodi && $lowongan->prodi->logo)
                            <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}"
                                class="w-[60px] h-[60px] rounded-full object-contain bg-white border border-gray-100 p-1 flex-shrink-0">
                        @else
                            <div
                                class="w-[60px] h-[60px] rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-2xl">
                                {{ substr($lowongan->nama_posisi, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    {{-- Info Rows (Location & Time/Quota) --}}
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3 text-black font-medium text-sm">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>Surabaya</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-400 text-xs font-medium">
                            <svg class="w-6 h-6 flex-shrink-0 text-black" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $lowongan->tanggal_tutup->format('j F Y') }} . {{ $lowongan->kuota }} Kuota</span>
                        </div>
                    </div>

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-3 mb-8">
                        <span
                            class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">{{ $lowongan->jenjang_minimal }}</span>
                        <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">>
                            {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                        <span class="px-4 py-2 bg-gray-100 text-black text-sm font-medium rounded-xl">Full-Time</span>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-auto">
                        <a href="{{ route('pelamar.lowongan.show', $lowongan) }}"
                            class="flex-1 py-2.5 bg-[#8b1515] text-white text-center text-sm font-bold rounded-xl shadow-sm hover:bg-red-900 transition-colors duration-300">
                            Details
                        </a>

                        <button type="button" @click="toggleSave({{ $lowongan->id }})" class=""
                            :class="{ 'scale-130': isSaved({{ $lowongan->id }}) }">
                            <svg class="w-5 h-5 transition-colors duration-300"
                                :fill="isSaved({{ $lowongan->id }}) ? '#8b1515' : 'none'" stroke="#8b1515" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-100 rounded-3xl p-16 text-center">
                    <h2 class="text-xl font-bold text-gray-800">Tidak ada lowongan tersedia</h2>
                    <p class="text-sm text-gray-500 mt-2">Coba sesuaikan filter Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- Applied Lowongan Section --}}
        @if($appliedLowongans->count() > 0)
            <div class="pt-8 mt-12 mb-12">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Sudah Dilamar</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-60">
                    @foreach($appliedLowongans as $lowongan)
                        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 flex flex-col cursor-not-allowed">

                            {{-- Header: Title & Logo --}}
                            <div class="flex items-start justify-between gap-4 mb-5">
                                <div class="flex-1 pt-1">
                                    <h3 class="text-base font-bold text-gray-600 leading-tight">{{ $lowongan->nama_posisi }}</h3>
                                    <p class="text-sm text-gray-400 mt-1">{{ $lowongan->prodi->nama ?? 'Semua Prodi' }}</p>
                                </div>
                                @if($lowongan->prodi && $lowongan->prodi->logo)
                                    <img src="{{ asset('storage/' . $lowongan->prodi->logo) }}" alt="Logo {{ $lowongan->prodi->nama }}"
                                        class="w-[60px] h-[60px] rounded-full object-contain bg-white border border-gray-200 p-1 flex-shrink-0 grayscale">
                                @else
                                    <div
                                        class="w-[60px] h-[60px] rounded-full bg-gray-300 flex-shrink-0 flex items-center justify-center text-gray-500 font-bold text-2xl">
                                        {{ substr($lowongan->nama_posisi, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Info Rows (Location & Time/Quota) --}}
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-3 text-gray-500 font-medium text-sm">
                                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span>Surabaya</span>
                                </div>
                                <div class="flex items-center gap-3 text-gray-400 text-xs font-medium">
                                    <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $lowongan->tanggal_tutup->format('j F Y') }} . {{ $lowongan->kuota }} Kuota</span>
                                </div>
                            </div>

                            {{-- Badges --}}
                            <div class="flex flex-wrap gap-3 mb-8">
                                <span
                                    class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl">{{ $lowongan->jenjang_minimal }}</span>
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl">>
                                    {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl">Full-Time</span>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 mt-auto">
                                <button disabled
                                    class="flex-1 py-2.5 bg-gray-300 text-gray-500 text-sm font-bold rounded-xl cursor-not-allowed">
                                    Details
                                </button>

                                <button disabled
                                    class="w-11 h-11 flex items-center justify-center border border-gray-300 rounded-xl bg-transparent text-gray-400 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('lowonganApp', () => ({
                    savedIds: @json($savedLowonganIds),

                    isSaved(id) {
                        return this.savedIds.includes(id);
                    },

                    async toggleSave(id) {
                        try {
                            const response = await fetch(`/pelamar/lowongan/${id}/save`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await response.json();

                            if (data.saved) {
                                if (!this.savedIds.includes(id)) this.savedIds.push(id);
                            } else {
                                this.savedIds = this.savedIds.filter(savedId => savedId !== id);
                            }
                        } catch (error) {
                            console.error('Error toggling save status:', error);
                        }
                    }
                }));
            });
        </script>
    @endpush
@endsection