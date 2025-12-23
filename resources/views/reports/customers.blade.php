@extends('layouts.app')

@section('title', 'Laporan Pelanggan')

@section('main')
<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3">
    <!-- Header with Breadcrumb -->
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h4 class="text-2xl font-bold text-navy-700 dark:text-white">Laporan Pelanggan</h4>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-500">Laporan</a> 
                <span class="mx-1">/</span> Pelanggan
            </p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="!z-5 relative mb-5 flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <form method="GET" action="{{ route('reports.customers') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Urutkan Berdasarkan</label>
                    <select name="sort_by" class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                        <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Nama</option>
                        <option value="purchases" {{ $sortBy == 'purchases' ? 'selected' : '' }}>Jumlah Pembelian</option>
                        <option value="value" {{ $sortBy == 'value' ? 'selected' : '' }}>Total Nilai Pembelian</option>
                    </select>
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="submit" class="linear w-full rounded-xl bg-brand-500 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <i class="fas fa-sort mr-2"></i>Urutkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-4 mb-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Pelanggan</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalCustomers, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Pembelian</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalPurchases, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Nilai</p>
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

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Rata-rata Nilai</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($averageValue, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <h5 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Daftar Pelanggan</h5>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">No</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Nama</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">No. Telepon</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Email</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Jml. Pembelian</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                        @php
                            $purchaseCount = $customer->transaksi->count();
                            $purchaseValue = $customer->transaksi->sum('total_harga');
                        @endphp
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $index + 1 }}</td>
                            <td class="py-3 text-sm font-medium text-navy-700 dark:text-white">{{ $customer->nama }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $customer->nomor_hp ?? '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $customer->email ?? '-' }}</td>
                            <td class="py-3 text-right text-sm text-navy-700 dark:text-white">{{ number_format($purchaseCount, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm font-medium text-navy-700 dark:text-white">Rp {{ number_format($purchaseValue, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-gray-600 dark:text-gray-400">
                                Tidak ada data pelanggan
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
