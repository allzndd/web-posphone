@extends('layouts.app')

@section('title', 'Laporan')

@section('main')
<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3">
    <!-- Reports Grid -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        
        <!-- Sales Report -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-brand-600">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white">Laporan Penjualan</h5>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Analisis transaksi penjualan dan pendapatan</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('reports.sales') }}" class="linear w-full rounded-xl bg-brand-500 py-3 text-center text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200 flex items-center justify-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Trade-In Report -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white">Laporan Tukar Tambah</h5>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Riwayat dan nilai tukar tambah produk</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('reports.trade-in') }}" class="linear w-full rounded-xl bg-blue-500 py-3 text-center text-base font-medium text-white transition duration-200 hover:bg-blue-600 active:bg-blue-700 flex items-center justify-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Products Report -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white">Laporan Produk</h5>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Daftar produk dan performa penjualan</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('reports.products') }}" class="linear w-full rounded-xl bg-green-500 py-3 text-center text-base font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center justify-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Stock Report -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-orange-400 to-orange-600">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white">Laporan Stok</h5>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Monitoring stok dan ketersediaan produk</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('reports.stock') }}" class="linear w-full rounded-xl bg-orange-500 py-3 text-center text-base font-medium text-white transition duration-200 hover:bg-orange-600 active:bg-orange-700 flex items-center justify-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Customers Report -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white">Laporan Pelanggan</h5>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Analisis data dan aktivitas pelanggan</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('reports.customers') }}" class="linear w-full rounded-xl bg-yellow-500 py-3 text-center text-base font-medium text-white transition duration-200 hover:bg-yellow-600 active:bg-yellow-700 flex items-center justify-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-600">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white">Ringkasan Keuangan</h5>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Analisis keuangan dan profitabilitas</p>
                </div>
                <div class="mt-6">
                    <a href="{{ route('reports.financial') }}" class="linear w-full rounded-xl bg-purple-500 py-3 text-center text-base font-medium text-white transition duration-200 hover:bg-purple-600 active:bg-purple-700 flex items-center justify-center">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
