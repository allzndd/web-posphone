@extends('layouts.app')

@section('title', 'Laporan Produk')

@section('main')
@include('components.access-denied-overlay', ['module' => 'Laporan Produk', 'hasAccessRead' => false])

<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3 @if(!isset($hasAccessRead) || !$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-3 mb-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Produk</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalProducts, 0, ',', '.') }}</h4>
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
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Nilai Stok</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalValue, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h5 class="text-xl font-bold text-navy-700 dark:text-white">Daftar Produk</h5>
                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('reports.products') }}" class="flex items-center gap-2">
                        <select name="category" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                            <option value="">Semua Kategori</option>
                            @if(!empty($categories) && is_iterable($categories))
                                @foreach($categories as $category)
                                    @if(is_object($category) && isset($category->id))
                                        <option value="{{ $category->id }}" {{ (isset($categoryFilter) && $categoryFilter == $category->id) ? 'selected' : '' }}>
                                            {{ $category->nama_kategori ?? 'Tanpa Nama' }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <select name="brand" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                            <option value="">Semua Merk</option>
                            @if(!empty($brands) && is_iterable($brands))
                                @foreach($brands as $brand)
                                    @if(is_object($brand) && isset($brand->id))
                                        <option value="{{ $brand->id }}" {{ (isset($brandFilter) && $brandFilter == $brand->id) ? 'selected' : '' }}>
                                            {{ $brand->nama_merk ?? 'Tanpa Nama' }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </form>
                    <a href="{{ route('reports.products.export', request()->query()) }}" class="linear rounded-xl bg-green-500 px-4 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
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
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Nama Produk</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Kategori</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Merk</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Stok</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Harga Jual</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                        @php
                            $totalStok = 0;
                            if(isset($product->stok) && is_iterable($product->stok)) {
                                foreach($product->stok as $stok) {
                                    if(is_object($stok) && isset($stok->stok)) {
                                        $totalStok += $stok->stok;
                                    }
                                }
                            }
                            $hargaJual = is_object($product) && isset($product->harga_jual) ? $product->harga_jual : 0;
                            $nilaiTotal = $totalStok * $hargaJual;
                        @endphp
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $index + 1 }}</td>
                            <td class="py-3 text-sm font-medium text-navy-700 dark:text-white">{{ $product->nama ?? '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">-</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $product->merk->nama ?? '-' }}</td>
                            <td class="py-3 text-right text-sm text-navy-700 dark:text-white">{{ number_format($totalStok, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm text-navy-700 dark:text-white">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm font-medium text-navy-700 dark:text-white">Rp {{ number_format($nilaiTotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-600 dark:text-gray-400">
                                Tidak ada data produk
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
