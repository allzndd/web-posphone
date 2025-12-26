@extends('layouts.app')

@section('title', 'Laporan Tukar Tambah')

@section('main')
<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Tukar Tambah</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalTradeIns, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nilai Tukar Tambah</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalTradeInValue, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tambahan Pembayaran</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalAdditionalPayment, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
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
                        <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trade-Ins Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h5 class="text-xl font-bold text-navy-700 dark:text-white">Detail Tukar Tambah</h5>
                <div class="flex items-center gap-3">
                    <form id="tradeInFilterForm" method="GET" action="{{ route('reports.trade-in') }}" class="flex items-center gap-2">
                        <select name="period" id="periodSelect" onchange="handleTradeInPeriodChange(this.value)" class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
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
                        <button id="tradeInFilterButton" type="submit" class="linear rounded-xl bg-brand-500 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('reports.trade-in.export', request()->query()) }}" class="linear rounded-xl bg-green-500 px-4 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
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
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Pelanggan</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Produk Lama</th>
                            <th class="pb-3 text-left text-sm font-bold text-gray-600 dark:text-gray-400">Produk Baru</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Nilai TT</th>
                            <th class="pb-3 text-right text-sm font-bold text-gray-600 dark:text-gray-400">Tambahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tradeIns as $index => $tradeIn)
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $index + 1 }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ \Carbon\Carbon::parse($tradeIn->created_at)->format('d/m/Y') }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $tradeIn->pelanggan->nama ?? '-' }}</td>
                            <td class="py-3 text-sm text-navy-700 dark:text-white">{{ $tradeIn->produkMasuk->nama ?? '-' }}</td>
                            <td class="py-3 text-sm font-medium text-navy-700 dark:text-white">{{ $tradeIn->produkKeluar->nama ?? '-' }}</td>
                            <td class="py-3 text-right text-sm font-medium text-navy-700 dark:text-white">Rp {{ number_format($tradeIn->transaksiPembelian->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm text-navy-700 dark:text-white">Rp {{ number_format($tradeIn->transaksiPenjualan->total_harga ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-gray-600 dark:text-gray-400">
                                Tidak ada data tukar tambah
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
    function handleTradeInPeriodChange(value) {
        const customDates = document.querySelectorAll('.custom-date');
        const filterButton = document.getElementById('tradeInFilterButton');
        
        if (value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
            filterButton.style.display = 'flex';
        } else {
            customDates.forEach(el => el.style.display = 'none');
            filterButton.style.display = 'none';
            document.getElementById('tradeInFilterForm').submit();
        }
    }
</script>
@endpush
@endsection
