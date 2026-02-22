<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Items Per Page & Info -->
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-sm text-gray-600 dark:text-gray-400">Items per page:</span>
        <form method="GET" action="{{ route('produk.index') }}" class="inline-block" onchange="this.submit()">
            @if($searchQuery ?? null)
                <input type="hidden" name="search" value="{{ $searchQuery }}">
            @endif
            <select name="per_page" 
                    class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
            </select>
        </form>
        <span class="text-sm text-gray-600 dark:text-gray-400">
            Showing {{ $produk->firstItem() ?? 0 }} to {{ $produk->lastItem() ?? 0 }} of {{ $produk->total() }}
        </span>
    </div>

    <!-- Pagination Buttons -->
    <div class="flex items-center gap-1">
        @if ($produk->onFirstPage())
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
        @else
            <a href="{{ $produk->previousPageUrl() }}{{ $searchQuery ? '&search=' . urlencode($searchQuery) : '' }}&per_page={{ request('per_page', 10) }}" 
               class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white pagination-link">◀</a>
        @endif

        @for ($page = max(1, $produk->currentPage() - 2); $page <= min($produk->lastPage(), $produk->currentPage() + 2); $page++)
            @if ($page == $produk->currentPage())
                <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $produk->url($page) }}{{ $searchQuery ? '&search=' . urlencode($searchQuery) : '' }}&per_page={{ request('per_page', 10) }}" 
                   class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white pagination-link">
                    {{ $page }}
                </a>
            @endif
        @endfor

        @if ($produk->hasMorePages())
            <a href="{{ $produk->nextPageUrl() }}{{ $searchQuery ? '&search=' . urlencode($searchQuery) : '' }}&per_page={{ request('per_page', 10) }}" 
               class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white pagination-link">▶</a>
        @else
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">▶</span>
        @endif
    </div>
</div>
