@extends('layouts.app')

@section('title', 'Ringkasan Keuangan')

@section('main')
<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3">
    <!-- Header with Breadcrumb -->
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h4 class="text-2xl font-bold text-navy-700 dark:text-white">Ringkasan Keuangan</h4>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-1">
                <a href="{{ route('reports.index') }}" class="hover:text-brand-500">Laporan</a> 
                <span class="mx-1">/</span> Keuangan
            </p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="!z-5 relative mb-5 flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <form method="GET" action="{{ route('reports.financial') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Periode</label>
                    <select name="period" id="periodSelect" class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Kustom</option>
                    </select>
                </div>
                <div class="md:col-span-1 custom-date" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                <div class="md:col-span-1 custom-date" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="submit" class="linear w-full rounded-xl bg-brand-500 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3 mb-5">
        <!-- Total Sales -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-green-400 to-green-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-white/80">Penjualan ({{ $totalSalesCount }} transaksi)</p>
                <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Total Purchases -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-red-400 to-red-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-white/80">Pembelian ({{ $totalPurchasesCount }} transaksi)</p>
                <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($totalPurchases, 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Trade-Ins -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-gradient-to-br from-blue-400 to-blue-600 bg-clip-border shadow-3xl shadow-shadow-500 dark:shadow-none p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-white/80">Tukar Tambah</p>
                <h4 class="mt-2 text-3xl font-bold text-white">Rp {{ number_format($totalTradeIns, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- Profit Summary -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mb-5">
        <!-- Gross Profit -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Laba Kotor</p>
                        <h4 class="mt-2 text-3xl font-bold {{ $grossProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </h4>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">Penjualan - Pembelian</p>
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-full {{ $grossProfit >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                        <svg class="h-8 w-8 {{ $grossProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Laba Bersih</p>
                        <h4 class="mt-2 text-3xl font-bold {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($netProfit, 0, ',', '.') }}
                        </h4>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">Setelah Pengeluaran</p>
                    </div>
                    <div class="flex h-16 w-16 items-center justify-center rounded-full {{ $netProfit >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                        <svg class="h-8 w-8 {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-lightPrimary dark:!bg-navy-900 bg-clip-border p-6">
        <div class="flex items-start">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-500 mr-4">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h6 class="text-base font-bold text-navy-700 dark:text-white mb-2">Informasi Perhitungan</h6>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <li>• Laba Kotor = Total Penjualan - Total Pembelian</li>
                    <li>• Laba Bersih = Laba Kotor - Pengeluaran Operasional</li>
                    <li>• Nilai Tukar Tambah dihitung terpisah dari transaksi penjualan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('periodSelect').addEventListener('change', function() {
        const customDates = document.querySelectorAll('.custom-date');
        if (this.value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
        } else {
            customDates.forEach(el => el.style.display = 'none');
        }
    });
</script>
@endpush
@endsection
