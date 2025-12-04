@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('main')
<div class="p-4 md:p-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-5">
        <!-- Total Transaksi -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-500 bg-opacity-10">
                    <i class="fas fa-receipt text-2xl text-brand-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <h4 class="text-2xl font-bold text-navy-700">{{ $totalTransactions }}</h4>
                </div>
            </div>
        </div>

        <!-- Total Customer -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-500 bg-opacity-10">
                    <i class="fas fa-users text-2xl text-green-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Customer</p>
                    <h4 class="text-2xl font-bold text-navy-700">{{ $totalCustomers }}</h4>
                </div>
            </div>
        </div>

        <!-- Total Stok Produk -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-yellow-500 bg-opacity-10">
                    <i class="fas fa-boxes text-2xl text-yellow-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Stok Produk</p>
                    <h4 class="text-2xl font-bold text-navy-700">{{ $totalProducts }}</h4>
                </div>
            </div>
        </div>

        @if(auth()->user()->isOwner())
        <!-- Total Profit -->
        <div class="stat-card bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-500 bg-opacity-10">
                    <i class="fas fa-chart-line text-2xl text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Profit</p>
                    <h4 class="text-xl font-bold text-navy-700">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        @endif
    </div>    <!-- Revenue Chart (Owner Only) -->
    @if(auth()->user()->isOwner())
    <div class="mb-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h4 class="text-xl font-bold text-navy-700">Grafik Profit</h4>
                    <p class="text-sm text-gray-600">
                        @if($period === 'monthly')
                            12 Bulan Terakhir
                        @elseif($period === 'yearly')
                            5 Tahun Terakhir
                        @else
                            7 Hari Terakhir
                        @endif
                    </p>
                </div>
                <form method="GET" action="{{ route('home') }}" class="flex items-center">
                    <select name="period" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-navy-700 outline-none focus:border-brand-500" onchange="this.form.submit()">
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </form>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="profitChart"></canvas>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hari Ini</p>
                    <h6 class="text-lg font-bold text-navy-700">Rp {{ number_format($todayProfit, 0, ',', '.') }}</h6>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Minggu Ini</p>
                    <h6 class="text-lg font-bold text-navy-700">Rp {{ number_format($thisWeekProfit, 0, ',', '.') }}</h6>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Bulan Ini</p>
                    <h6 class="text-lg font-bold text-navy-700">Rp {{ number_format($thisMonthProfit, 0, ',', '.') }}</h6>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tahun Ini</p>
                    <h6 class="text-lg font-bold text-navy-700">Rp {{ number_format($thisYearProfit, 0, ',', '.') }}</h6>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Trade-in Statistics & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <!-- Trade-in Statistics -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h4 class="text-xl font-bold text-navy-700 mb-4">Statistik Trade-In</h4>
            <div class="space-y-4">
                <div class="p-4 bg-lightPrimary rounded-xl">
                    <p class="text-sm text-gray-600 mb-1">Total Trade-In</p>
                    <h5 class="text-2xl font-bold text-navy-700">{{ $totalTradeIns }} <span class="text-base font-normal text-gray-600">Unit</span></h5>
                </div>
                <div class="p-4 bg-lightPrimary rounded-xl">
                    <p class="text-sm text-gray-600 mb-1">Total Nilai</p>
                    <h5 class="text-xl font-bold text-navy-700">Rp {{ number_format($tradeInValue, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
            <h4 class="text-xl font-bold text-navy-700 mb-4">Top 5 Produk Terlaris</h4>
            <div class="space-y-4">
                @forelse($topProductsData as $product)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-navy-700">{{ $product['name'] }}</span>
                        <span class="text-sm text-gray-600">{{ $product['sold'] }} terjual - Rp {{ number_format($product['revenue'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-lightPrimary rounded-full h-2">
                        <div class="bg-brand-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ ($product['sold'] / max(array_column($topProductsData, 'sold'))) * 100 }}%">
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 text-center py-4">Belum ada data penjualan produk</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recommendations Section (Owner Only) -->
    @if(auth()->user()->isOwner())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
        <!-- Top Profit Products -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h4 class="text-xl font-bold text-navy-700 mb-4 flex items-center">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i> HP Profit Tertinggi
            </h4>
            <div class="space-y-3">
                @forelse($topProfitProducts as $index => $product)
                <div class="p-3 bg-lightPrimary rounded-xl">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-bold mr-2">{{ $index + 1 }}</span>
                                <h6 class="text-sm font-bold text-navy-700">{{ $product->name }}</h6>
                            </div>
                            <p class="text-xs text-gray-600">Stok: {{ $product->stock }} unit</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-500">Rp {{ number_format($product->profit, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-600">profit/unit</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 text-center py-4">Belum ada data produk</p>
                @endforelse
            </div>
        </div>

        <!-- Best Selling Products -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h4 class="text-xl font-bold text-navy-700 mb-4 flex items-center">
                <i class="fas fa-fire text-red-500 mr-2"></i> HP Penjualan Terbanyak
            </h4>
            <div class="space-y-3">
                @forelse($bestSellingProducts as $index => $product)
                <div class="p-3 bg-lightPrimary rounded-xl">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white text-xs font-bold mr-2">{{ $index + 1 }}</span>
                                <h6 class="text-sm font-bold text-navy-700">{{ $product->name }}</h6>
                            </div>
                            <p class="text-xs text-gray-600">Harga: Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand-500">{{ $product->total_sold }} unit</p>
                            <p class="text-xs text-gray-600">terjual</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 text-center py-4">Belum ada data penjualan</p>
                @endforelse
            </div>
        </div>

        <!-- Popular Customers -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h4 class="text-xl font-bold text-navy-700 mb-4 flex items-center">
                <i class="fas fa-star text-blue-500 mr-2"></i> Customer Populer
            </h4>
            <div class="space-y-3">
                @forelse($popularCustomers as $index => $customer)
                <div class="p-3 bg-lightPrimary rounded-xl">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-500 text-white text-xs font-bold mr-2">{{ $index + 1 }}</span>
                                <div>
                                    <h6 class="text-sm font-bold text-navy-700">{{ $customer->name }}</h6>
                                    <p class="text-xs text-gray-600">{{ $customer->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-blue-500">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-600">{{ $customer->total_transactions }} transaksi</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-600 text-center py-4">Belum ada data customer</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Transactions & Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold text-navy-700">Transaksi Terbaru</h4>
                <a href="{{ route('transaction.index') }}" class="px-4 py-2 bg-brand-500 text-white rounded-xl text-sm font-medium hover:bg-brand-600 transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600">Invoice</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600">Customer</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600">Total</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600">Tanggal</th>
                            <th class="text-left py-3 px-2 text-sm font-bold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                        <tr class="border-b border-gray-100 hover:bg-lightPrimary transition-colors">
                            <td class="py-3 px-2">
                                <a href="{{ route('transaction.show', $transaction->id) }}" class="text-brand-500 hover:text-brand-600 font-medium text-sm">
                                    {{ $transaction->invoice_number }}
                                </a>
                            </td>
                            <td class="py-3 px-2 text-sm text-navy-700">{{ $transaction->customer->name ?? '-' }}</td>
                            <td class="py-3 px-2 text-sm font-medium text-navy-700">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-sm text-gray-600">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
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
                            <td colspan="5" class="text-center py-8 text-gray-600">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h4 class="text-xl font-bold text-navy-700 mb-4">Stok Menipis</h4>
            <div class="space-y-3">
                @if($lowStockProducts->count() > 0)
                @foreach($lowStockProducts as $product)
                <div class="p-3 bg-lightPrimary rounded-xl">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h6 class="text-sm font-bold text-navy-700 mb-1">{{ $product->name }}</h6>
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
                <p class="text-gray-600 text-center py-8">Semua produk stok aman</p>
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
