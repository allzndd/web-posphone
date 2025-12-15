@extends('layouts.app')

@section('title', 'Transactions')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Transactions Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Transactions</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $transaksi->total() }} total transactions
                </p>
            </div>
            
            <!-- Search & Add Button -->
            <div class="flex items-center gap-3">
                <!-- Download Button -->
                <button onclick="document.getElementById('downloadTransaksiModal').classList.remove('hidden')"
                        class="flex items-center gap-2 rounded-xl bg-green-500 px-4 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download
                </button>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('transaksi.index') }}" class="relative">
                    <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white mr-2" xmlns="http://www.w3.org/2000/svg">
                            <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                        </svg>
                        <input type="text" name="invoice" value="{{ request('invoice') }}" placeholder="Search by invoice..." 
                               class="block w-full bg-transparent text-sm font-medium text-navy-700 dark:text-white outline-none placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    </div>
                </form>
                
                <!-- Status Filter -->
                <form method="GET" action="{{ route('transaksi.index') }}" class="relative">
                    <select name="status" onchange="this.form.submit()" 
                            class="rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
                
                <!-- Add New Button -->
                <a href="{{ route('transaksi.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    New Transaction
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Invoice</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Type</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Toko</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Customer/Supplier</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Total</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Payment</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Status</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksi as $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('transaksi.edit', $item->id) }}">
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->invoice }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="py-4">
                            @if($item->is_transaksi_masuk == 1)
                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                    Income
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-xs font-medium text-red-800 dark:text-red-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                    Expense
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->toko->nama ?? '-' }}</p>
                        </td>
                        <td class="py-4">
                            @if($item->pelanggan)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->pelanggan->nama }}</p>
                            @elseif($item->supplier)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->supplier->nama }}</p>
                            @else
                                <p class="text-sm text-gray-400 dark:text-gray-500">-</p>
                            @endif
                        </td>
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">
                                {{ get_currency_symbol() }} {{ number_format($item->total_harga, 0, ',', '.') }}
                            </p>
                        </td>
                        <td class="py-4">
                            <span class="inline-flex items-center rounded-full bg-purple-100 dark:bg-purple-900/30 px-3 py-1 text-xs font-medium text-purple-800 dark:text-purple-300">
                                {{ ucfirst($item->metode_pembayaran) }}
                            </span>
                        </td>
                        <td class="py-4">
                            @if($item->status == 'completed')
                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                                    Completed
                                </span>
                            @elseif($item->status == 'pending')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300">
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-xs font-medium text-red-800 dark:text-red-300">
                                    Cancelled
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('transaksi.edit', $item->id) }}" 
                                   class="flex items-center justify-center rounded-lg bg-lightPrimary p-2 text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 000-1.41l-2.34-2.34a.996.996 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No transactions found</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Start by creating your first transaction</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transaksi->hasPages())
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Download Report Modal -->
<div id="downloadTransaksiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-[20px] max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Download Transactions Report</h4>
            <button onclick="document.getElementById('downloadTransaksiModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('transaksi.download-report') }}" method="GET">
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
                
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Status Filter (Optional)</label>
                    <select name="status"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="document.getElementById('downloadTransaksiModal').classList.add('hidden')"
                        class="flex-1 rounded-xl bg-gray-100 px-4 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-green-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download CSV
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Make table rows clickable
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                window.location.href = this.dataset.href;
            }
        });
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('downloadTransaksiModal').classList.add('hidden');
        }
    });
</script>
@endsection
