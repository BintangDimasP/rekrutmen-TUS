@if ($paginator->hasPages())
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
    <span>
        Menampilkan <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
    </span>
    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed font-medium">Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] transition font-medium">Prev</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="px-3 py-1.5 rounded-lg bg-[#8b1515] text-white font-bold">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] transition font-medium">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:border-[#8b1515] hover:text-[#8b1515] transition font-medium">Next</a>
        @else
            <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed font-medium">Next</span>
        @endif
    </div>
</div>
@else
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
    <span>
        Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}</strong>–<strong>{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
    </span>
    <div class="flex items-center gap-1">
        <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed font-medium">Prev</span>
        <span class="px-3 py-1.5 rounded-lg bg-[#8b1515] text-white font-bold">1</span>
        <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed font-medium">Next</span>
    </div>
</div>
@endif
