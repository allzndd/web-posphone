@extends('layouts.app')

@section('title', 'Laporan Tukar Tambah')

@section('main')
@include('components.access-denied-overlay', ['module' => 'Laporan Tukar Tambah', 'hasAccessRead' => $hasAccessRead])

<div class="p-3 md:pt-[100px] md:pl-3 md:pr-3 @if(!$hasAccessRead) opacity-30 pointer-events-none @endif">

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-5">
        <!-- Total Transaksi -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Tukar Tambah</p>
                        <h4 class="mt-2 text-2xl font-bold text-navy-700 dark:text-white">{{ number_format($totalTradeIns, 0, ',', '.') }}</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">transaksi</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 dark:bg-navy-700">
                        <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Harga Beli HP Masuk -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Beli HP Masuk</p>
                        <h4 class="mt-2 text-xl font-bold text-red-600 dark:text-red-400">{{ format_currency($totalTradeInValue) }}</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">kas keluar (beli HP lama)</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-navy-700">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue HP Keluar -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Jual HP Keluar</p>
                        <h4 class="mt-2 text-xl font-bold text-green-600 dark:text-green-400">{{ format_currency($totalAdditionalPayment) }}</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">kas masuk (jual HP baru)</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 dark:bg-navy-700">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Net Profit</p>
                        <h4 class="mt-2 text-xl font-bold {{ $totalProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ format_currency($totalProfit) }}
                        </h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">jual - beli</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full {{ $totalProfit >= 0 ? 'bg-emerald-100 dark:bg-navy-700' : 'bg-red-100 dark:bg-navy-700' }}">
                        <svg class="h-6 w-6 {{ $totalProfit >= 0 ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="p-6">

            <!-- Header + Filter -->
            <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h5 class="text-xl font-bold text-navy-700 dark:text-white">Detail Transaksi Tukar Tambah</h5>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">HP Masuk = HP lama dari pelanggan &nbsp;|&nbsp; HP Keluar = HP baru dari stok toko</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form id="tradeInFilterForm" method="GET" action="{{ route('reports.trade-in') }}" class="flex flex-wrap items-center gap-2">
                        <select name="period" id="periodSelect" onchange="handleTradeInPeriodChange(this.value)"
                            class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                            <option value="today"  {{ $period == 'today'  ? 'selected' : '' }}>Hari Ini</option>
                            <option value="week"   {{ $period == 'week'   ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="month"  {{ $period == 'month'  ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="year"   {{ $period == 'year'   ? 'selected' : '' }}>Tahun Ini</option>
                            <option value="all"    {{ $period == 'all'    ? 'selected' : '' }}>Semua</option>
                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="custom-date rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700"
                            style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="custom-date rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700"
                            style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                        <select name="store_id" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-200 bg-white/0 px-3 py-2 text-sm outline-none dark:!border-white/10 dark:text-white dark:!bg-navy-700">
                            <option value="">Semua Toko</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->nama }}</option>
                            @endforeach
                        </select>
                        <button id="tradeInFilterButton" type="submit"
                            class="linear rounded-xl bg-brand-500 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700"
                            style="display: {{ $period == 'custom' ? 'flex' : 'none' }};">
                            Filter
                        </button>
                    </form>
                    <a href="{{ route('reports.trade-in.export', request()->query()) }}"
                        class="linear rounded-xl bg-green-500 px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-green-600 active:bg-green-700 flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Excel
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px]">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-white/10">
                            <th class="pb-3 pr-3 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-400 w-8">#</th>
                            <th class="pb-3 pr-3 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Tanggal / Invoice</th>
                            <th class="pb-3 pr-3 text-left text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Toko / Pelanggan</th>
                            <th class="pb-3 pr-3 text-left text-xs font-bold uppercase text-red-500 dark:text-red-400">
                                <div class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"/>
                                    </svg>
                                    HP Masuk (Dari Pelanggan)
                                </div>
                            </th>
                            <th class="pb-3 pr-3 text-left text-xs font-bold uppercase text-green-500 dark:text-green-400">
                                <div class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                    </svg>
                                    HP Keluar (Ke Pelanggan)
                                </div>
                            </th>
                            <th class="pb-3 pr-3 text-right text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Harga Beli</th>
                            <th class="pb-3 pr-3 text-right text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Harga Jual</th>
                            <th class="pb-3 text-right text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tradeIns as $index => $tradeIn)
                        @php
                            $beliAmount = $tradeIn->transaksiPembelian->total_harga ?? 0;
                            $jualAmount = $tradeIn->transaksiPenjualan->total_harga ?? 0;
                            $rowProfit  = $jualAmount - $beliAmount;
                            $pm = $tradeIn->produkMasuk;
                            // Build specs string
                            $specs = collect([
                                $pm->ram->kapasitas ?? null,
                                ($pm->penyimpanan->kapasitas ?? null) ? ($pm->penyimpanan->kapasitas . 'GB') : null,
                                $pm->warna->warna ?? null,
                            ])->filter()->implode(' / ');
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-white/5 hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors">

                            <!-- # -->
                            <td class="py-4 pr-3 text-sm text-gray-500 dark:text-gray-400 align-top">{{ $index + 1 }}</td>

                            <!-- Tanggal / Invoice -->
                            <td class="py-4 pr-3 align-top">
                                <p class="text-sm font-semibold text-navy-700 dark:text-white">
                                    {{ \Carbon\Carbon::parse($tradeIn->created_at)->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($tradeIn->created_at)->format('H:i') }}
                                </p>
                                @if($tradeIn->transaksiPembelian)
                                    <span class="mt-1 inline-block rounded bg-red-100 dark:bg-red-900/40 px-1.5 py-0.5 text-[10px] font-medium text-red-600 dark:text-red-400">
                                        {{ $tradeIn->transaksiPembelian->invoice ?? '-' }}
                                    </span>
                                @endif
                                @if($tradeIn->transaksiPenjualan)
                                    <span class="mt-0.5 inline-block rounded bg-green-100 dark:bg-green-900/40 px-1.5 py-0.5 text-[10px] font-medium text-green-600 dark:text-green-400">
                                        {{ $tradeIn->transaksiPenjualan->invoice ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Toko / Pelanggan -->
                            <td class="py-4 pr-3 align-top">
                                <p class="text-sm font-semibold text-navy-700 dark:text-white">
                                    {{ $tradeIn->toko->nama ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $tradeIn->pelanggan->nama ?? 'Walk-in' }}
                                </p>
                            </td>

                            <!-- HP Masuk (dari pelanggan - old phone) -->
                            <td class="py-4 pr-3 align-top">
                                @if($pm)
                                    <p class="text-sm font-semibold text-navy-700 dark:text-white leading-tight">{{ $pm->nama ?? '-' }}</p>
                                    @if($pm->merk)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $pm->merk->merk ?? '' }}{{ $pm->merk->nama ? ' ' . $pm->merk->nama : '' }}
                                        </p>
                                    @endif
                                    @if($specs)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $specs }}</p>
                                    @endif
                                    @if($pm->battery_health)
                                        <span class="mt-1 inline-flex items-center gap-1 rounded bg-amber-100 dark:bg-amber-900/30 px-1.5 py-0.5 text-[10px] font-medium text-amber-700 dark:text-amber-400">
                                            <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="2" y="7" width="18" height="10" rx="2"/>
                                                <path d="M22 11v2"/>
                                                <rect x="4" y="9" width="{{ min(14, round(($pm->battery_health / 100) * 14)) }}" height="6" rx="1" fill="currentColor" stroke="none"/>
                                            </svg>
                                            {{ $pm->battery_health }}%
                                        </span>
                                    @endif
                                    @if($pm->imei)
                                        <div class="mt-1.5 flex items-center gap-1">
                                            <span class="rounded bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 text-[11px] font-mono font-semibold text-blue-700 dark:text-blue-300 tracking-wide">
                                                IMEI: {{ $pm->imei }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="mt-1 inline-block text-[10px] text-gray-400 dark:text-gray-600 italic">no IMEI</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>

                            <!-- HP Keluar (ke pelanggan - new phone from inventory) -->
                            <td class="py-4 pr-3 align-top">
                                @if($tradeIn->produkKeluar)
                                    <p class="text-sm font-semibold text-navy-700 dark:text-white leading-tight">{{ $tradeIn->produkKeluar->nama ?? '-' }}</p>
                                    @if($tradeIn->transaksiPenjualan && $tradeIn->transaksiPenjualan->metode_pembayaran)
                                        <span class="mt-1 inline-block rounded bg-gray-100 dark:bg-navy-600 px-1.5 py-0.5 text-[10px] text-gray-600 dark:text-gray-400">
                                            {{ ucfirst($tradeIn->transaksiPenjualan->metode_pembayaran) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>

                            <!-- Harga Beli (beli HP masuk dari pelanggan) -->
                            <td class="py-4 pr-3 text-right align-top">
                                <p class="text-sm font-semibold text-red-600 dark:text-red-400">{{ format_currency($beliAmount) }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">kas keluar</p>
                            </td>

                            <!-- Harga Jual (jual HP keluar ke pelanggan) -->
                            <td class="py-4 pr-3 text-right align-top">
                                <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ format_currency($jualAmount) }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">kas masuk</p>
                            </td>

                            <!-- Profit per row -->
                            <td class="py-4 text-right align-top">
                                <span class="inline-block rounded-lg px-2.5 py-1 text-sm font-bold
                                    {{ $rowProfit >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                                    {{ format_currency($rowProfit) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada data tukar tambah untuk periode ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($tradeIns->count() > 0)
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-700/50">
                            <td colspan="5" class="py-3 pl-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Total ({{ $totalTradeIns }} transaksi)</td>
                            <td class="py-3 pr-3 text-right text-sm font-bold text-red-600 dark:text-red-400">{{ format_currency($totalTradeInValue) }}</td>
                            <td class="py-3 pr-3 text-right text-sm font-bold text-green-600 dark:text-green-400">{{ format_currency($totalAdditionalPayment) }}</td>
                            <td class="py-3 text-right">
                                <span class="inline-block rounded-lg px-2.5 py-1 text-sm font-bold
                                    {{ $totalProfit >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                                    {{ format_currency($totalProfit) }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
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
