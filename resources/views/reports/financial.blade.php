@extends('layouts.app')

@section('title', 'Ringkasan Keuangan')

@section('main')
@include('components.access-denied-overlay', ['module' => 'Laporan Keuangan', 'hasAccessRead' => $hasAccessRead])

<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3 @if(!$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Core Metrics Summary -->
    <div class="mb-5">
        <h5 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Core Metrics</h5>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            <!-- Revenue -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-blue-400 to-blue-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
                <div>
                    <p class="text-sm font-medium text-white/80">Revenue (Pendapatan)</p>
                    <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-white/70">{{ $totalSalesCount }} transaksi</p>
                </div>
            </div>

            <!-- HPP / COGS -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-orange-400 to-orange-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
                <div>
                    <p class="text-sm font-medium text-white/80">HPP / COGS</p>
                    <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($totalHPP, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-white/70">Harga Pokok Penjualan</p>
                </div>
            </div>

            <!-- Gross Profit -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-green-400 to-green-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
                <div>
                    <p class="text-sm font-medium text-white/80">Gross Profit (Laba Kotor)</p>
                    <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($grossProfit, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-white/70">Margin: {{ number_format($grossMargin, 2) }}%</p>
                </div>
            </div>

            <!-- Operating Expenses -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-red-400 to-red-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
                <div>
                    <p class="text-sm font-medium text-white/80">Operating Expenses</p>
                    <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($totalOperatingExpenses, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-white/70">Biaya Operasional</p>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-purple-400 to-purple-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
                <div>
                    <p class="text-sm font-medium text-white/80">Net Profit (Laba Bersih)</p>
                    <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($netProfit, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-white/70">Margin: {{ number_format($netMargin, 2) }}%</p>
                </div>
            </div>

            <!-- Profit Margin -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-indigo-400 to-indigo-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
                <div>
                    <p class="text-sm font-medium text-white/80">Net Profit Margin</p>
                    <h4 class="mt-2 text-3xl font-bold text-white">{{ number_format($netMargin, 2) }}%</h4>
                    <p class="mt-1 text-xs text-white/70">Efisiensi Usaha</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending & Cancelled Info -->
    <div class="mb-5">
        <h5 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Status Transaksi Lainnya</h5>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
            <!-- Pending Sales -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6 border-l-4 border-orange-400">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Penjualan Pending</p>
                    <h4 class="mt-2 text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($pendingSalesAmount ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ $pendingSalesCount ?? 0 }} transaksi</p>
                </div>
            </div>
            <!-- Cancelled Sales -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6 border-l-4 border-red-400">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Penjualan Cancelled</p>
                    <h4 class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($cancelledSalesAmount ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ $cancelledSalesCount ?? 0 }} transaksi</p>
                </div>
            </div>
            <!-- Pending Expenses -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6 border-l-4 border-orange-400">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Expense Pending</p>
                    <h4 class="mt-2 text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($pendingExpenseAmount ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ $pendingExpenseCount ?? 0 }} transaksi</p>
                </div>
            </div>
            <!-- Cancelled Expenses -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6 border-l-4 border-red-400">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Expense Cancelled</p>
                    <h4 class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($cancelledExpenseAmount ?? 0, 0, ',', '.') }}</h4>
                    <p class="mt-1 text-xs text-gray-500">{{ $cancelledExpenseCount ?? 0 }} transaksi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Section -->
    <div class="mb-5">
        <h5 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Cash Flow</h5>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-5">
            <!-- Cash In -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Cash In</p>
                        <h4 class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($cashIn, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Cash Out -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Cash Out</p>
                        <h4 class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($cashOut, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Free Cash Flow -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Free Cash Flow</p>
                        <h4 class="mt-2 text-2xl font-bold {{ $freeCashFlow >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($freeCashFlow, 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full {{ $freeCashFlow >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                        <svg class="h-6 w-6 {{ $freeCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Receivable -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Piutang</p>
                        <h4 class="mt-2 text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($receivable, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6 mb-5">
            <h6 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Payment Method Breakdown</h6>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($paymentBreakdown as $method => $amount)
                <div class="p-4 rounded-lg bg-lightPrimary dark:bg-navy-900">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ strtoupper($method) }}</p>
                    <h5 class="mt-1 text-xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($amount, 0, ',', '.') }}</h5>
                    <p class="mt-1 text-xs text-gray-500">{{ $totalRevenue > 0 ? number_format(($amount / $totalRevenue) * 100, 1) : 0 }}%</p>
                </div>
                @empty
                <div class="col-span-3 text-center py-4 text-gray-500">Tidak ada data metode pembayaran</div>
                @endforelse
            </div>
        </div>

        <!-- Cash Balance Per Outlet -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h6 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Cash Balance per Outlet</h6>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Toko</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Cash In</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Cash Out</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashBalancePerOutlet as $outlet)
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $outlet['store_name'] }}</td>
                            <td class="py-3 text-right text-sm text-green-600 dark:text-green-400">Rp {{ number_format($outlet['cash_in'], 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm text-red-600 dark:text-red-400">Rp {{ number_format($outlet['cash_out'], 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm font-bold {{ $outlet['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                Rp {{ number_format($outlet['balance'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Per Item -->
    <div class="mb-5">
        <div class="flex items-center justify-between gap-4 mb-4">
            <h5 class="text-xl font-bold text-navy-700 dark:text-white">Detail Per Item</h5>
            <div class="flex items-center gap-3">
                <form id="filterForm" method="GET" action="{{ route('reports.financial') }}" class="flex items-center gap-2">
                    <select name="period" id="periodSelect" onchange="handlePeriodChange(this.value)" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="custom-date rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="custom-date rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                    <select name="store_id" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                        <option value="">Semua Toko</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->nama }}</option>
                        @endforeach
                    </select>
                    <select name="merk_id" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                        <option value="">Semua Product Name</option>
                        @foreach($merks as $merk)
                            <option value="{{ $merk->id }}" {{ $merkId == $merk->id ? 'selected' : '' }}>{{ $merk->nama }}</option>
                        @endforeach
                    </select>
                    <button id="filterButton" type="submit" class="linear rounded-xl bg-brand-500 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                        Filter
                    </button>
                </form>
                <a href="{{ route('reports.financial.export', request()->query()) }}" class="linear rounded-xl bg-green-500 px-4 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Excel
                </a>
            </div>
        </div>
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="pb-3 text-left text-xs font-bold text-gray-600 dark:text-gray-400">Invoice</th>
                            <th class="pb-3 text-left text-xs font-bold text-gray-600 dark:text-gray-400">Produk</th>
                            <th class="pb-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400">Type</th>
                            <th class="pb-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400">Qty</th>
                            <th class="pb-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">Revenue</th>
                            <th class="pb-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">HPP</th>
                            <th class="pb-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">Gross Profit</th>
                            <th class="pb-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemDetails as $item)
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-xs text-navy-700 dark:text-white">{{ $item['invoice'] }}</td>
                            <td class="py-3 text-xs text-navy-700 dark:text-white">{{ $item['product_name'] }}</td>
                            <td class="py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $item['product_type'] == 'electronic' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                    {{ $item['product_type'] == 'accessory' ? 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300' : '' }}
                                    {{ $item['product_type'] == 'service' ? 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300' : '' }}">
                                    {{ ucfirst($item['product_type']) }}
                                </span>
                            </td>
                            <td class="py-3 text-center text-xs text-navy-700 dark:text-white">{{ $item['quantity'] }}</td>
                            <td class="py-3 text-right text-xs text-navy-700 dark:text-white">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-xs text-orange-600 dark:text-orange-400">Rp {{ number_format($item['hpp'], 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-xs font-semibold {{ $item['gross_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                Rp {{ number_format($item['gross_profit'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right text-xs font-semibold {{ $item['gross_margin'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($item['gross_margin'], 2) }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center text-gray-500">Tidak ada data transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Operating Expenses Detail -->
    <div class="mb-5">
        <h5 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Operating Expenses Detail</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Expenses by Type -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
                <h6 class="text-base font-bold text-navy-700 dark:text-white mb-4">Breakdown by Type</h6>
                @forelse($expensesByType as $type => $amount)
                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-white/10">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($type) }}</span>
                    <span class="text-sm font-bold text-navy-700 dark:text-white">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500">Belum ada data expenses</p>
                @endforelse
            </div>

            <!-- Recent Expenses -->
            <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
                <h6 class="text-base font-bold text-navy-700 dark:text-white mb-4">Recent Expenses</h6>
                @forelse($expenses->take(5) as $expense)
                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-white/10">
                    <div class="flex-1">
                        <p class="text-sm text-navy-700 dark:text-white">{{ $expense->keterangan ?? ($expense->kategoriExpense ? $expense->kategoriExpense->nama : 'Expense') }}</p>
                        <p class="text-xs text-gray-500">{{ $expense->created_at->format('d M Y') }}</p>
                    </div>
                    <span class="text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($expense->total_harga, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500">Belum ada data expenses</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handlePeriodChange(value) {
        const customDates = document.querySelectorAll('.custom-date');
        const filterButton = document.getElementById('filterButton');
        
        if (value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
            filterButton.style.display = 'flex';
        } else {
            customDates.forEach(el => el.style.display = 'none');
            filterButton.style.display = 'none';
            // Auto submit if not custom
            document.getElementById('filterForm').submit();
        }
    }
</script>
@endpush
@endsection
