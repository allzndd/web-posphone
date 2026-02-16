@extends('layouts.app')

@section('title', 'Laporan Operasional Expense')

@section('main')
@include('components.access-denied-overlay', ['module' => 'Laporan Operasional Expense', 'hasAccessRead' => false])

<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3 @if(!isset($hasAccessRead) || !$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-5">
        <!-- Total Expenses -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Expense</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Count -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Jumlah Transaksi</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalExpenseCount, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Expense -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Rata-rata Expense</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($averageExpense, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Info -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Periode</p>
                        <h4 class="mt-2 text-sm font-bold text-navy-700 dark:text-white">
                            {{ $startDate }} s/d {{ $endDate }}
                        </h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m7 8a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v12z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Expense Types -->
    @if(count($topExpenseTypes) > 0)
    <div class="mb-5 grid grid-cols-1 gap-5 md:grid-cols-2">
        <!-- Breakdown by Category -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <h6 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Top Expense Categories</h6>
                <div class="space-y-3">
                    @foreach($topExpenseTypes as $categoryName => $amount)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $categoryName }}</span>
                        <span class="text-sm font-bold text-navy-700 dark:text-white">
                            Rp {{ number_format($amount, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Breakdown by Store -->
        @if(count($expensesByStore) > 0)
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <h6 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Expense per Toko</h6>
                <div class="space-y-3">
                    @foreach($expensesByStore as $storeName => $amount)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $storeName }}</span>
                        <span class="text-sm font-bold text-navy-700 dark:text-white">
                            Rp {{ number_format($amount, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Detail Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h5 class="text-xl font-bold text-navy-700 dark:text-white">Detail Expense</h5>
                <div class="flex items-center gap-3 flex-wrap">
                    <form id="expenseFilterForm" method="GET" action="{{ route('reports.expense') }}" class="flex items-center gap-2 flex-wrap">
                        <select name="period" id="periodSelect" onchange="handleExpensePeriodChange(this.value)" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
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
                        <select name="category_id" onchange="this.form.submit()" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->nama }}</option>
                            @endforeach
                        </select>
                        <button id="expenseFilterButton" type="submit" class="linear rounded-xl bg-brand-500 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('reports.expense.export', request()->query()) }}" class="linear rounded-xl bg-green-500 px-4 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
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
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Kategori</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Toko</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Invoice</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Keterangan</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $index => $expense)
                        <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-navy-700/50">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $index + 1 }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 text-sm font-medium">
                                <span class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400">
                                    {{ $expense->kategoriExpense ? $expense->kategoriExpense->nama : '-' }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $expense->toko ? $expense->toko->nama : '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $expense->invoice ?? '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $expense->keterangan ?? '-' }}</td>
                            <td class="py-3 text-right text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($expense->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-600 dark:text-gray-400">
                                Tidak ada data expense
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
    function handleExpensePeriodChange(value) {
        const customDates = document.querySelectorAll('.custom-date');
        const filterButton = document.getElementById('expenseFilterButton');
        
        if (value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
            filterButton.style.display = 'block';
        } else {
            customDates.forEach(el => el.style.display = 'none');
            filterButton.style.display = 'none';
            document.getElementById('expenseFilterForm').submit();
        }
    }
</script>
@endpush
@endsection
