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
                        <a href="{{ route('home', ['period' => 'week']) }}" 
                           class="block px-4 py-2 text-sm !text-navy-700 dark:!text-white hover:!bg-lightPrimary dark:hover:!bg-navy-800 {{ $period === 'week' ? '!bg-lightPrimary dark:!bg-navy-800 font-bold' : '' }}">
                            Mingguan
                        </a>
                        <a href="{{ route('home', ['period' => 'monthly']) }}" 
                           class="block px-4 py-2 text-sm !text-navy-700 dark:!text-white hover:!bg-lightPrimary dark:hover:!bg-navy-800 {{ $period === 'monthly' ? '!bg-lightPrimary dark:!bg-navy-800 font-bold' : '' }}">
                            Bulanan
                        </a>
                        <a href="{{ route('home', ['period' => 'yearly']) }}" 
                           class="block px-4 py-2 text-sm !text-navy-700 dark:!text-white hover:!bg-lightPrimary dark:hover:!bg-navy-800 {{ $period === 'yearly' ? '!bg-lightPrimary dark:!bg-navy-800 font-bold' : '' }}">
                            Tahunan
                        </a>
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
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-4 flex items-center">
                <i class="fas fa-trophy text-brand-500 dark:text-white mr-2"></i> HP Profit Tertinggi
            </h4>
            <div class="space-y-3">
                @forelse($topProfitProducts as $index => $product)
                <div class="p-3 bg-lightPrimary dark:bg-navy-700 rounded-xl">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold mr-2">{{ $index + 1 }}</span>
                                <h6 class="text-sm font-bold text-navy-700 dark:text-white">{{ $product->name }}</h6>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Stok: {{ $product->stock }} unit</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-500">Rp {{ number_format($product->profit, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">profit/unit</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 dark:text-gray-400 text-center py-4">Belum ada data produk</p>
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
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold mr-2">{{ $index + 1 }}</span>
                                <h6 class="text-sm font-bold text-navy-700 dark:text-white">{{ $product->name }}</h6>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Harga: Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand-500">{{ $product->total_sold }} unit</p>
                            <p class="text-xs text-gray-600">terjual</p>
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
                <a href="{{ route('transaction.index') }}" class="px-4 py-2 bg-brand-500 text-white rounded-xl text-sm font-medium hover:bg-brand-600 transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Invoice</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Customer</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Total</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Tanggal</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                        <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors">
                            <td class="py-3 px-2">
                                <a href="{{ route('transaction.show', $transaction->id) }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium text-sm">
                                    {{ $transaction->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3 px-2 text-sm text-navy-700 dark:text-white">{{ $transaction->customer->name ?? '-' }}</td>
                            <td class="py-3 px-2 text-sm font-medium text-navy-700 dark:text-white">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                            <td class="py-3 px-2">
                                @php($payStatus = $transaction->payment?->status ?? 'pending')
                                @if($payStatus === 'paid')
                                    <span class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Lunas</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full">{{ ucfirst($payStatus) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-600 dark:text-gray-400">Belum ada transaksi</td>
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
                    <a href="{{ route('product.edit', $product->id) }}" class="w-full flex items-center justify-center px-3 py-2 bg-brand-500 text-white rounded-lg text-xs font-medium hover:bg-brand-600 transition-colors">
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
