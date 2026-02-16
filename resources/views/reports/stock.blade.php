@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('main')
@include('components.access-denied-overlay', ['module' => 'Laporan Stok', 'hasAccessRead' => false])

<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3 @if(!isset($hasAccessRead) || !$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-4 mb-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Item</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalItems, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Stok</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalStock, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stok Menipis</p>
                        <h4 class="mt-2 text-2xl font-bold text-orange-500">{{ number_format($lowStockItems, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900">
                        <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stok Habis</p>
                        <h4 class="mt-2 text-2xl font-bold text-red-500">{{ number_format($outOfStock, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h5 class="text-xl font-bold text-navy-700 dark:text-white">Detail Stok</h5>
                <div class="flex items-center gap-3">
                    <form id="stockFilterForm" method="GET" action="{{ route('reports.stock') }}" class="flex items-center gap-2">
                        <select name="store_id" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                            <option value="">Semua Toko</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->nama }}</option>
                            @endforeach
                        </select>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="low_stock" value="1" {{ $lowStockOnly ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-gray-300 text-brand-500 shadow-sm focus:border-brand-500 focus:ring focus:ring-brand-500 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-navy-700 dark:text-white whitespace-nowrap">Stok Menipis</span>
                        </label>
                    </form>
                    <a href="{{ route('reports.stock.export', request()->query()) }}" class="linear rounded-xl bg-green-500 px-4 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Excel
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">No</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Produk</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Toko</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Stok</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Min. Stok</th>
                            <th class="pb-3 text-center text-sm font-bold text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $index => $stock)
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $index + 1 }}</td>
                            <td class="py-3 text-sm font-medium text-navy-700 dark:text-white">{{ $stock->produk->nama ?? '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $stock->toko->nama ?? '-' }}</td>
                            <td class="py-3 text-right text-sm font-medium text-navy-700 dark:text-white">{{ number_format($stock->stok, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm text-navy-700 dark:text-white">5</td>
                            <td class="py-3 text-center">
                                @if($stock->stok == 0)
                                    <span class="rounded-full px-3 py-1 text-xs font-bold bg-red-100 text-red-700 dark:bg-red-500 dark:text-white">
                                        Habis
                                    </span>
                                @elseif($stock->stok <= 5)
                                    <span class="rounded-full px-3 py-1 text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-500 dark:text-white">
                                        Menipis
                                    </span>
                                @else
                                    <span class="rounded-full px-3 py-1 text-xs font-bold bg-green-100 text-green-700 dark:bg-green-500 dark:text-white">
                                        Aman
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-600 dark:text-gray-400">
                                Tidak ada data stok
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
