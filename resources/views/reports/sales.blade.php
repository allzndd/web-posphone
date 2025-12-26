@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('main')
<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-5">
        <!-- Total Transactions -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Transaksi</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalTransactions, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Penjualan</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Items -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Item Terjual</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalItems, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Transaction -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Rata-rata Transaksi</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</h4>
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

    <!-- Transactions Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h5 class="text-xl font-bold text-navy-700 dark:text-white">Detail Transaksi</h5>
                <div class="flex items-center gap-3">
                    <form id="salesFilterForm" method="GET" action="{{ route('reports.sales') }}" class="flex items-center gap-2">
                        <select name="period" id="periodSelect" onchange="handleSalesPeriodChange(this.value)" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
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
                        <button id="salesFilterButton" type="submit" class="linear rounded-xl bg-brand-500 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('reports.sales.export', request()->query()) }}" class="linear rounded-xl bg-green-500 px-4 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
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
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Tanggal</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">No. Transaksi</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Toko</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Pelanggan</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Total</th>
                            <th class="pb-3 text-center text-sm font-bold text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $index + 1 }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y') }}</td>
                            <td class="py-3 text-sm font-medium text-navy-700 dark:text-white">{{ $transaction->invoice }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $transaction->toko->nama ?? '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $transaction->pelanggan->nama ?? 'Umum' }}</td>
                            <td class="py-3 text-right text-sm font-medium text-navy-700 dark:text-white">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
                            <td class="py-3 text-center">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $transaction->status == 'LUNAS' ? 'bg-green-100 text-green-700 dark:bg-green-500 dark:text-white' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500 dark:text-white' }}">
                                    {{ $transaction->status ?? 'PENDING' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-600 dark:text-gray-400">
                                Tidak ada data transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleSalesPeriodChange(value) {
        const customDates = document.querySelectorAll('.custom-date');
        const filterButton = document.getElementById('salesFilterButton');
        
        if (value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
            filterButton.style.display = 'flex';
        } else {
            customDates.forEach(el => el.style.display = 'none');
            filterButton.style.display = 'none';
            document.getElementById('salesFilterForm').submit();
        }
    }
</script>
@endpush
@endsection
