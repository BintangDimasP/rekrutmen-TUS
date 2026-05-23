@extends('layouts.admin')

@section('title', 'Manajemen Lowongan')

@section('content')

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Filter & Action --}}
        <div
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full lg:w-auto">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Cari nama posisi..."
                        class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:border-[#8b1515] focus:ring-1 focus:ring-[#8b1515] transition shadow-sm">
                </div>
            </div>

            <a href="{{ route('admin.lowongan.create') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-[#8b1515] text-white text-sm font-bold rounded-xl shadow-md hover:bg-red-900 transition-colors shrink-0 w-full lg:w-auto">
                Tambah Lowongan
            </a>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-[#8b1515] text-white">
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Posisi / Prodi</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[20%]">Persyaratan</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Kuota</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[10%]">Pelamar</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[14%]">Ditutup</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap w-[12%]">Status</th>
                            <th class="py-3 px-5 text-sm font-bold whitespace-nowrap text-center w-[14%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lowongans as $lowongan)
                            <tr x-data="{ showToggleModal: false }" class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-5">
                                    <div class="text-sm font-semibold text-gray-800">{{ $lowongan->nama_posisi }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $lowongan->prodi->nama ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-5">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-indigo-50 text-indigo-700">{{ $lowongan->jenjang_minimal }}</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[0.7rem] font-medium bg-gray-100 text-gray-600">IPK
                                            ≥ {{ number_format($lowongan->minimal_ipk, 2) }}</span>
                                    </div>
                                    @if($lowongan->skill_dibutuhkan)
                                        <div class="text-xs text-gray-400 mt-1">{{ $lowongan->skill_dibutuhkan }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <div class="text-sm font-bold text-gray-800">{{ $lowongan->sisa_kuota }}</div>
                                    <div class="text-[0.65rem] text-gray-400">dari {{ $lowongan->kuota }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#8b1515]/10 text-[#8b1515] text-sm font-bold">
                                        {{ $lowongan->lamarans->count() }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $lowongan->tanggal_tutup->format('d M Y') }}
                                    @if($lowongan->tanggal_tutup->isPast())
                                        <div class="text-[0.65rem] text-red-500 font-medium">Sudah lewat</div>
                                    @else
                                        <div class="text-[0.65rem] text-green-600 font-medium">
                                            {{ $lowongan->tanggal_tutup->diffForHumans() }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-5">
                                    @if($lowongan->status === 'aktif')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-green-100 text-green-700">
                                            Aktif
                                        </span>
                                    @elseif($lowongan->status === 'draft')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-amber-100 text-amber-700">
                                            Draft
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-semibold bg-gray-100 text-gray-600">
                                            Ditutup
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-5">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.lowongan.show', $lowongan) }}" title="Detail"
                                            class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.lowongan.edit', $lowongan) }}" title="Edit"
                                            class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($lowongan->status === 'aktif')
                                            <button type="button" @click="showToggleModal = true" title="Tutup Lowongan"
                                                class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" @click="showToggleModal = true" title="Publish Lowongan"
                                                class="flex items-center justify-center p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 10a6 6 0 0112 0m-6 11V10m-4 4l4-4 4 4" />
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Toggle Modal --}}
                                        <div x-show="showToggleModal" x-transition.opacity
                                            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                            @click.self="showToggleModal = false" style="display: none;">
                                            <div x-show="showToggleModal" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                class="bg-white rounded-[24px] shadow-2xl w-full max-w-[340px] overflow-hidden text-center p-8 relative whitespace-normal">

                                                {{-- Close Button --}}
                                                <button type="button" @click="showToggleModal = false"
                                                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                                {{-- Question Icon --}}
                                                <div class="mx-auto mb-5 flex justify-center">
                                                    <svg width="68" height="68" viewBox="0 0 24 24" fill="#8b1515"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="drop-shadow-[0_8px_12px_rgba(59,130,246,0.3)]">
                                                        <path fill-rule="evenodd"
                                                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 01-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 01-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 01-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.89-.777.89-2.038 0-2.815zM12 18.75a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>

                                                <h2 class="text-xl font-extrabold text-gray-800 mb-2 leading-tight">
                                                    {{ $lowongan->status === 'aktif' ? 'Tutup lowongan?' : 'Publish lowongan?' }}
                                                </h2>
                                                <p class="text-[0.85rem] font-medium text-gray-500 mb-8">
                                                    {{ $lowongan->status === 'aktif' ? 'Lowongan akan ditutup dan tidak dapat menerima pelamar baru.' : 'Lowongan akan dipublish dan dapat dilamar oleh kandidat.' }}
                                                </p>

                                                <div class="flex justify-center gap-3">
                                                    <!-- Wrapper untuk tombol No disamakan dengan Form -->
                                                    <div class="flex-1 m-0">
                                                        <button type="button" @click="showToggleModal = false"
                                                            class="w-full px-5 py-3 text-sm font-bold text-gray-600 border-2 border-gray-600 bg-transparent hover:bg-gray-800 hover:text-white active:scale-95 rounded-xl transition-all">
                                                            No
                                                        </button>
                                                    </div>

                                                    <!-- Wrapper form untuk tombol Yes -->
                                                    <form method="POST"
                                                        action="{{ route('admin.lowongan.toggleStatus', $lowongan) }}"
                                                        class="flex-1 m-0">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="w-full px-5 py-3 text-sm font-bold text-white bg-[#8b1515] hover:bg-red-800 border-2 border-transparent active:scale-95 rounded-xl shadow-md transition-all">
                                                            Yes
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-700 font-semibold text-sm">Belum ada lowongan</h3>
                                        <p class="text-gray-400 text-xs">Buat lowongan pertama dengan menekan tombol "Tambah
                                            Lowongan".</p>
                                        <a href="{{ route('admin.lowongan.create') }}"
                                            class="mt-1 px-4 py-2 bg-[#8b1515] text-white text-xs font-semibold rounded-lg hover:bg-red-900 transition-colors">
                                            Tambah Lowongan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            @include('components.pagination', ['paginator' => $lowongans])
        </div>
    </div>

@endsection