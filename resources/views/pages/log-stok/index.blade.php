@extends('layouts.app')

@section('title', 'Stock History')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Stock History Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Stock History</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $logs->total() }} stock movement records
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 pb-4">
            <form method="GET" action="{{ route('log-stok.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <!-- Type Filter -->
                <select name="tipe" onchange="this.form.submit()" 
                        class="rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                    <option value="">All Types</option>
                    <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Incoming</option>
                    <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Outgoing</option>
                    <option value="retur" {{ request('tipe') == 'retur' ? 'selected' : '' }}>Return</option>
                    <option value="adjustment" {{ request('tipe') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>

                <!-- Reference Search -->
                <input type="text" name="referensi" value="{{ request('referensi') }}" placeholder="Search reference..." 
                       class="rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 placeholder:text-gray-400 dark:placeholder:text-gray-500" />

                <div class="flex gap-2">
                    <button type="submit" 
                            class="flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-2 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['tipe', 'referensi', 'pos_produk_id', 'pos_toko_id']))
                        <a href="{{ route('log-stok.index') }}" 
                           class="flex items-center justify-center rounded-xl bg-gray-100 px-5 py-2 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Date</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Product</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Store</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Type</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Before</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Change</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">After</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Reference</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">User</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <!-- Date -->
                        <td class="py-4">
                            <div>
                                <p class="text-sm font-medium text-navy-700 dark:text-white">{{ $log->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i') }}</p>
                            </div>
                        </td>
                        
                        <!-- Product -->
                        <td class="py-4">
                            <div>
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $log->produk->nama ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->produk->kode ?? '-' }}</p>
                            </div>
                        </td>
                        
                        <!-- Store -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $log->toko->nama ?? '-' }}</p>
                        </td>
                        
                        <!-- Type -->
                        <td class="py-4 text-center">
                            @php
                                $colors = [
                                    'masuk' => 'green',
                                    'keluar' => 'red',
                                    'retur' => 'blue',
                                    'adjustment' => 'yellow',
                                ];
                                $color = $colors[$log->tipe] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center rounded-lg bg-{{ $color }}-100 dark:bg-{{ $color }}-500/20 px-3 py-1 text-xs font-bold text-{{ $color }}-600 dark:text-{{ $color }}-300">
                                {{ ucfirst($log->tipe) }}
                            </span>
                        </td>
                        
                        <!-- Stock Before -->
                        <td class="py-4 text-right">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ number_format($log->stok_sebelum, 0, ',', '.') }}</p>
                        </td>
                        
                        <!-- Change -->
                        <td class="py-4 text-center">
                            @if($log->perubahan > 0)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    +{{ number_format($log->perubahan, 0, ',', '.') }}
                                </span>
                            @elseif($log->perubahan < 0)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ number_format($log->perubahan, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">0</span>
                            @endif
                        </td>
                        
                        <!-- Stock After -->
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ number_format($log->stok_sesudah, 0, ',', '.') }}</p>
                        </td>
                        
                        <!-- Reference -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $log->referensi ?? '-' }}</p>
                            @if($log->keterangan)
                                <p class="text-xs text-gray-500 dark:text-gray-500">{{ $log->keterangan }}</p>
                            @endif
                        </td>
                        
                        <!-- User -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $log->user->nama ?? $log->pengguna->nama ?? 'System' }}
                            </p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No stock history found</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Stock movements will appear here</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 dark:border-white/10 px-6 py-4 gap-4">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm text-gray-600 dark:text-gray-400">Items per page:</span>
                <form method="GET" action="{{ route('log-stok.index') }}" id="perPageForm" class="inline-block">
                    <input type="hidden" name="tipe" value="{{ request('tipe') }}">
                    <input type="hidden" name="referensi" value="{{ request('referensi') }}">
                    <select name="per_page" onchange="this.form.submit()" 
                            class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 [&>option]:!bg-white [&>option]:dark:!bg-navy-800 [&>option]:!text-navy-700 [&>option]:dark:!text-white">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                </span>
            </div>
            <div class="flex items-center gap-1">
                {{-- Previous Button --}}
                @if ($logs->onFirstPage())
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                        </svg>
                    </span>
                @else
                    <a href="{{ $logs->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                    @if ($page == $logs->currentPage())
                        <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $logs->appends(request()->except('page'))->url($page) }}" 
                           class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($logs->hasMorePages())
                    <a href="{{ $logs->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                        </svg>
                    </a>
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
