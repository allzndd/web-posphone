@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('main')
<div class="p-4 md:p-6">
    <!-- Statistics Cards - Horizon Style -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-5">
        <!-- Total Transaksi -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex h-full w-full flex-row items-center justify-between rounded-t-2xl p-4">
                <div class="flex flex-col">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Transaksi</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white">{{ $totalTransactions }}</h4>
                </div>
                <div class="flex h-[45px] w-[45px] items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                    <i class="fas fa-receipt text-brand-500 dark:text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Customer -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex h-full w-full flex-row items-center justify-between rounded-t-2xl p-4">
                <div class="flex flex-col">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Customer</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white">{{ $totalCustomers }}</h4>
                </div>
                <div class="flex h-[45px] w-[45px] items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                    <i class="fas fa-users text-brand-500 dark:text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Stok Produk -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex h-full w-full flex-row items-center justify-between rounded-t-2xl p-4">
                <div class="flex flex-col">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Stok Produk</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white">{{ $totalProducts }}</h4>
                </div>
                <div class="flex h-[45px] w-[45px] items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                    <i class="fas fa-boxes text-brand-500 dark:text-white text-xl"></i>
                </div>
            </div>
        </div>

        @if(auth()->user()->isOwner())
        <!-- Total Profit -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex h-full w-full flex-row items-center justify-between rounded-t-2xl p-4">
                <div class="flex flex-col">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Profit</p>
                    <h4 class="text-xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h4>
                </div>
                <div class="flex h-[45px] w-[45px] items-center justify-center rounded-full bg-lightPrimary dark:bg-navy-700">
                    <i class="fas fa-chart-line text-brand-500 dark:text-white text-xl"></i>
                </div>
            </div>
        </div>
        @endif
    </div>    <!-- Revenue Chart (Owner Only) -->
    @if(auth()->user()->isOwner())
    <div class="mb-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-xl font-bold text-navy-700 dark:text-white">Grafik Profit</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($period === 'monthly')
                            12 Bulan Terakhir
                        @elseif($period === 'yearly')
                            5 Tahun Terakhir
                        @else
                            7 Hari Terakhir
                        @endif
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Download Report Button -->
                    <button onclick="document.getElementById('downloadModal').classList.remove('hidden')"
                            class="flex items-center gap-2 rounded-xl bg-green-500 px-4 py-2 text-sm font-bold text-white transition duration-200 hover:bg-green-600 active:bg-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Report
                    </button>
                    
                    <!-- Custom Dropdown with Alpine.js -->
                    <div x-data="{ open: false, selected: '{{ $period }}' }" class="relative">
                        <button @click="open = !open" type="button" class="flex items-center justify-between rounded-xl border border-gray-200 dark:!border-white/10 !bg-white dark:!bg-navy-700 px-4 py-2 text-sm font-medium !text-navy-700 dark:!text-white outline-none focus:border-brand-500 min-w-[120px]">
                            <span x-text="selected === 'week' ? 'Mingguan' : (selected === 'monthly' ? 'Bulanan' : 'Tahunan')"></span>
                            <svg class="ml-2 h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-[120px] rounded-xl !bg-white dark:!bg-navy-700 shadow-xl shadow-shadow-500 dark:shadow-none border border-gray-200 dark:border-white/10 py-1 z-10">
                            <a href="{{ route('dashboard', ['period' => 'week']) }}" 
                               class="block px-4 py-2 text-sm !text-navy-700 dark:!text-white hover:!bg-lightPrimary dark:hover:!bg-navy-800 {{ $period === 'week' ? '!bg-lightPrimary dark:!bg-navy-800 font-bold' : '' }}">
                                Mingguan
                            </a>
                            <a href="{{ route('dashboard', ['period' => 'monthly']) }}" 
                               class="block px-4 py-2 text-sm !text-navy-700 dark:!text-white hover:!bg-lightPrimary dark:hover:!bg-navy-800 {{ $period === 'monthly' ? '!bg-lightPrimary dark:!bg-navy-800 font-bold' : '' }}">
                                Bulanan
                            </a>
                            <a href="{{ route('dashboard', ['period' => 'yearly']) }}" 
                               class="block px-4 py-2 text-sm !text-navy-700 dark:!text-white hover:!bg-lightPrimary dark:hover:!bg-navy-800 {{ $period === 'yearly' ? '!bg-lightPrimary dark:!bg-navy-800 font-bold' : '' }}">
                                Tahunan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="profitChart"></canvas>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-white/10">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Hari Ini</p>
                    <h6 class="text-lg font-bold text-navy-700 dark:text-white">Rp {{ number_format($todayProfit, 0, ',', '.') }}</h6>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Minggu Ini</p>
                    <h6 class="text-lg font-bold text-navy-700 dark:text-white">Rp {{ number_format($thisWeekProfit, 0, ',', '.') }}</h6>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Bulan Ini</p>
                    <h6 class="text-lg font-bold text-navy-700 dark:text-white">Rp {{ number_format($thisMonthProfit, 0, ',', '.') }}</h6>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tahun Ini</p>
                    <h6 class="text-lg font-bold text-navy-700 dark:text-white">Rp {{ number_format($thisYearProfit, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Current Balance Section (Owner Only) -->
    @if(auth()->user()->isOwner())
    <div class="mb-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-white/10">
                <div>
                    <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-1 flex items-center">
                        <i class="fas fa-wallet text-brand-500 dark:text-white mr-2"></i> Current Balance (Saldo Kas Riil)
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Modal Awal + Total Cash In - Total Cash Out (Sejak Awal Operasional)</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Semua Outlet</p>
                    <h3 class="text-2xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($totalCurrentBalance, 0, ',', '.') }}</h3>
                    @if($totalCurrentBalance < 0)
                        <span class="inline-block mt-2 px-3 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
                            ⚠️ Balance Negatif
                        </span>
                    @elseif($totalCurrentBalance > 100000000)
                        <span class="inline-block mt-2 px-3 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full">
                            🚀 Excellent Performance
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stores Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($currentBalancePerOutlet as $outlet)
                <div class="bg-lightPrimary dark:bg-navy-700 rounded-xl p-4 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h6 class="text-base font-bold text-navy-700 dark:text-white mb-1 flex items-center">
                                <i class="fas fa-store text-brand-500 dark:text-white mr-2 text-sm"></i>
                                {{ $outlet->store_name }}
                            </h6>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold {{ $outlet->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($outlet->current_balance / 1000000, 1) }}jt
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">💰 Modal Awal:</span>
                            <span class="text-navy-700 dark:text-white font-medium">Rp {{ number_format($outlet->modal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">📈 Total Cash In:</span>
                            <span class="text-green-600 dark:text-green-400 font-medium">Rp {{ number_format($outlet->total_cash_in, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">📉 Total Cash Out:</span>
                            <span class="text-red-600 dark:text-red-400 font-medium">Rp {{ number_format($outlet->total_cash_out, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-gray-200 dark:border-white/10">
                            <div class="flex justify-between items-center">
                                <span class="text-navy-700 dark:text-white font-bold">💵 Current Balance:</span>
                                <span class="text-sm font-bold {{ $outlet->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    Rp {{ number_format($outlet->current_balance, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Indicator -->
                    @if($outlet->current_balance < 0)
                        <div class="mt-3 px-3 py-2 bg-red-100 border border-red-200 rounded-lg text-center">
                            <span class="text-xs font-bold text-red-700">⚠️ Perlu Perhatian</span>
                        </div>
                    @elseif($outlet->current_balance > $outlet->modal * 2)
                        <div class="mt-3 px-3 py-2 bg-green-100 border border-green-200 rounded-lg text-center">
                            <span class="text-xs font-bold text-green-700">✅ Performa Bagus</span>
                        </div>
                    @endif
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-600 dark:text-gray-400">
                    <i class="fas fa-store-slash text-4xl mb-3"></i>
                    <p>Belum ada data toko</p>
                </div>
                @endforelse
            </div>

            <!-- Summary Footer -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-white/10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">💰 Total Modal Awal</p>
                        <p class="text-lg font-bold text-navy-700 dark:text-white">Rp {{ number_format(collect($currentBalancePerOutlet)->sum('modal'), 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">📈 Total Cash In (All Time)</p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">Rp {{ number_format(collect($currentBalancePerOutlet)->sum('total_cash_in'), 0, ',', '.') }}</p>
                    </div>
                    <div class="text-center p-4 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">📉 Total Cash Out (All Time)</p>
                        <p class="text-lg font-bold text-red-600 dark:text-red-400">Rp {{ number_format(collect($currentBalancePerOutlet)->sum('total_cash_out'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Trade-in Statistics & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <!-- Trade-in Statistics -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Statistik Trade-In</h4>
            <div class="space-y-4">
                <div class="p-4 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Trade-In</p>
                    <h5 class="text-2xl font-bold text-navy-700 dark:text-white">{{ $totalTradeIns }} <span class="text-base font-normal text-gray-600 dark:text-gray-400">Unit</span></h5>
                </div>
                <div class="p-4 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Nilai</p>
                    <h5 class="text-xl font-bold text-navy-700 dark:text-white">Rp {{ number_format($tradeInValue, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="lg:col-span-2 !z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Top 5 Produk Terlaris</h4>
            <div class="space-y-4">
                @forelse($topProductsData as $product)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-navy-700 dark:text-white">{{ $product['name'] }}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $product['sold'] }} terjual - Rp {{ number_format($product['revenue'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-lightPrimary dark:bg-navy-700 rounded-full h-2">
                        <div class="bg-brand-500 dark:bg-brand-400 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ ($product['sold'] / max(array_column($topProductsData, 'sold'))) * 100 }}%">
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">Belum ada data penjualan produk</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recommendations Section (Owner Only) -->
    @if(auth()->user()->isOwner())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
        <!-- Top Profit Products -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shladow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4 flex items-center">
                <i class="fas fa-trophy text-brand-500 dark:text-white mr-2"></i> HP Profit Tertinggi
            </h4>
            <div class="space-y-3">
                @forelse($topProfitProducts as $index => $product)
                <div class="p-3 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-shrink-0">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="text-sm font-bold text-navy-700 dark:text-white truncate">{{ $product->name }}</h6>
                            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-0.5 mt-1">
                                @if($product->merk_name)
                                    <p>Brand: <span class="font-medium">{{ $product->merk_name }}</span></p>
                                @endif
                                @if($product->penyimpanan)
                                    <p>Storage: <span class="font-medium">{{ $product->penyimpanan }} GB</span></p>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <p class="text-lg font-bold text-green-500">Rp {{ number_format($product->profit, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Profit</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>

        <!-- Best Selling Products -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4 flex items-center">
                <i class="fas fa-fire text-brand-500 dark:text-white mr-2"></i> HP Penjualan Terbanyak
            </h4>
            <div class="space-y-3">
                @forelse($bestSellingProducts as $index => $product)
                <div class="p-3 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-shrink-0">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="text-sm font-bold text-navy-700 dark:text-white truncate">{{ $product->name }}</h6>
                            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-0.5 mt-1">
                                @if($product->merk_name)
                                    <p>Brand: <span class="font-medium">{{ $product->merk_name }}</span></p>
                                @endif
                                @if($product->penyimpanan)
                                    <p>Storage: <span class="font-medium">{{ $product->penyimpanan }} GB</span></p>
                                @endif
                                <p class="pt-1 font-medium text-navy-700 dark:text-white">Harga: Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <p class="text-xl font-bold text-brand-500">{{ $product->total_sold }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">unit terjual</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>

        <!-- Popular Customers -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4 flex items-center">
                <i class="fas fa-star text-brand-500 dark:text-white mr-2"></i> Customer Populer
            </h4>
            <div class="space-y-3">
                @forelse($popularCustomers as $index => $customer)
                <div class="p-3 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold mr-2">{{ $index + 1 }}</span>
                                <div>
                                    <h6 class="text-sm font-bold text-navy-700 dark:text-white">{{ $customer->name }}</h6>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand-500">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $customer->total_transactions }} transaksi</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">Belum ada data customer</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Transactions & Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 !z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Transaksi Terbaru</h4>
                <a href="{{ route('transaksi.index') }}" class="px-4 py-2 bg-brand-500 text-white rounded-xl text-sm font-medium hover:bg-brand-600 transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Invoice</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Type</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Customer/Supplier</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Total</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Tanggal</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                        <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors">
                            <td class="py-3 px-2">
                                <a href="{{ route('transaksi.show', $transaction->id) }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium text-sm">
                                    {{ $transaction->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3 px-2">
                                @if($transaction->transaction_type === 'income')
                                    <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Income</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Expense</span>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-sm text-navy-700 dark:text-white">{{ $transaction->customer->name ?? '-' }}</td>
                            <td class="py-3 px-2 text-sm font-medium text-navy-700 dark:text-white">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                            <td class="py-3 px-2">
                                @php($payStatus = $transaction->payment?->status ?? 'pending')
                                @if($payStatus === 'paid' || $payStatus === 'completed')
                                    <span class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">{{ ucfirst($payStatus) }}</span>
                                @elseif($payStatus === 'pending')
                                    <span class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full">{{ ucfirst($payStatus) }}</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">{{ ucfirst($payStatus) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-600 dark:text-gray-400">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4">Stok Menipis</h4>
            <div class="space-y-3">
                @if($lowStockProducts->count() > 0)
                @foreach($lowStockProducts as $product)
                <div class="p-3 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h6 class="text-sm font-bold text-navy-700 dark:text-white mb-1">{{ $product->name }}</h6>
                            @if($product->stock == 0)
                                <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Habis</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full">Sisa {{ $product->stock }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('produk-stok.index') }}" class="w-full flex items-center justify-center px-3 py-2 bg-brand-500 text-white rounded-lg text-xs font-medium hover:bg-brand-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Tambah Stok
                    </a>
                </div>
                @endforeach
                @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">Semua produk stok aman</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Download Report Modal -->
<div id="downloadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-[20px] max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Download Financial Report</h4>
            <button onclick="document.getElementById('downloadModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('dashboard.download-report') }}" method="GET">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="document.getElementById('downloadModal').classList.add('hidden')"
                        class="flex-1 rounded-xl bg-gray-100 px-4 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-green-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profit Chart with Horizon Style
    var ctx = document.getElementById('profitChart');
    if (ctx) {
        ctx = ctx.getContext('2d');
        var profitChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Profit',
                    data: {!! json_encode($profitData) !!},
                    borderColor: '#422AFB',
                    backgroundColor: 'rgba(66, 42, 251, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#422AFB',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            fontColor: '#A3AED0',
                            fontSize: 12,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            padding: 10
                        },
                        gridLines: {
                            color: 'rgba(163, 174, 208, 0.2)',
                            drawBorder: false,
                            zeroLineColor: 'rgba(163, 174, 208, 0.2)'
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontColor: '#A3AED0',
                            fontSize: 12,
                            padding: 10
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }]
                },
                tooltips: {
                    backgroundColor: '#1B254B',
                    titleFontColor: '#fff',
                    bodyFontColor: '#fff',
                    borderColor: '#422AFB',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(tooltipItem) {
                            return 'Profit: Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
