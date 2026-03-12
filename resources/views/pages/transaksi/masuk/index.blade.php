@extends('layouts.app')

@section('title', 'Incoming Transactions')

@push('style')
<!-- Load reusable table components CSS -->
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
@include('components.access-denied-overlay', ['module' => 'Incoming Transaction', 'hasAccessRead' => $hasAccessRead])
<div class="mt-3 px-[11px] pr-[10px] @if(!$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Transactions Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Incoming Transactions</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $transaksi->total() }} total incoming transactions (sales)
                </p>
            </div>
            
            <!-- Search, Bulk Delete & Add Button -->
            <div class="flex items-center gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('transaksi.masuk.index') }}" class="relative">
                    <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white mr-2" xmlns="http://www.w3.org/2000/svg">
                            <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice, toko, pelanggan..." 
                               class="block w-full bg-transparent text-sm font-medium text-navy-700 dark:text-white outline-none placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    </div>
                </form>
                
                <!-- Bulk Delete Button (hidden by default) -->
                <button id="bulkDeleteBtn" class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:active:bg-red-800 hidden"
                        onclick="confirmBulkDelete()">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Selected
                </button>
                
                <!-- Export Button -->
                <button onclick="openExportModal()" 
                        class="flex items-center gap-2 rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2.5 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-100 dark:hover:bg-navy-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ekspor
                </button>

                <!-- Add New Button -->
                @permission('transaksi.create')
                <a href="{{ route('transaksi.masuk.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-green-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-green-600 active:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:active:bg-green-800">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    Transaksi Baru
                </a>
                @endpermission
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left" style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" 
                                   class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-navy-700 cursor-pointer"
                                   onchange="toggleSelectAll(this)">
                        </th>
                        <th class="py-3 text-left col-no">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">No</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Invoice</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Toko</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Pelanggan</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">IMEI</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Product Type</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Total</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Pembayaran</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Status</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Tanggal</p>
                        </th>
                        <th class="py-3 text-center col-actions">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $index => $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('transaksi.masuk.edit', $item) }}">
                        <td class="py-4" style="width: 40px;" onclick="event.stopPropagation()">
                            <input type="checkbox" class="transaksi-checkbox rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-navy-700 cursor-pointer" 
                                   value="{{ $item->id }}" 
                                   onchange="updateBulkDeleteButton()">
                        </td>
                        <td class="py-4 col-no">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ ($transaksi->currentPage() - 1) * $transaksi->perPage() + $loop->iteration }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->invoice }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->toko->nama ?? '-' }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->pelanggan->nama ?? '-' }}</p>
                        </td>
                        <!-- IMEI -->
                        <td class="py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                @php
                                    // First try to get IMEI from snapshot (new way)
                                    $imeis = $item->items->map(function($transItem) {
                                        // Priority: snapshot data > product relation
                                        if ($transItem->imei) {
                                            return $transItem->imei;
                                        }
                                        return $transItem->produk ? $transItem->produk->imei : null;
                                    })->filter()->unique()->values();
                                @endphp
                                @if($imeis->count() > 0)
                                    @foreach($imeis as $imei)
                                        <span class="inline-block bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mb-1 mr-1">{{ $imei }}</span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </div>
                        </td>
                        <!-- Product Type -->
                        <td class="py-4">
                            <div class="text-sm max-w-xs">
                                @php
                                    // Priority: snapshot data > product relation
                                    $productTypes = $item->items->map(function($transItem) {
                                        if ($transItem->product_type) {
                                            return $transItem->product_type;
                                        }
                                        return $transItem->produk ? $transItem->produk->product_type : null;
                                    })->filter()->unique()->values();
                                @endphp
                                @if($productTypes->count() > 0)
                                    @foreach($productTypes as $type)
                                        @if($type === 'electronic')
                                            <span class="inline-block bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mb-1 mr-1">📱 Electronic</span>
                                        @elseif($type === 'accessory' || $type === 'accessories')
                                            <span class="inline-block bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-1 rounded text-xs font-medium mb-1 mr-1">🔌 Accessory</span>
                                        @elseif($type === 'service')
                                            <span class="inline-block bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded text-xs font-medium mb-1 mr-1">🔧 Service</span>
                                        @else
                                            <span class="inline-block bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs font-medium mb-1 mr-1">{{ ucfirst($type) }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-green-500">{{ get_currency_symbol() }} {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                        </td>
                        <td class="py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-100 dark:bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-600 dark:text-purple-300">
                                {{ ucfirst($item->metode_pembayaran ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="py-4 text-center" onclick="event.stopPropagation()">
                            <div class="inline-block" x-data="statusDropdown('{{ $item->status }}', '{{ route('transaksi.masuk.update-status', $item->id) }}')">
                                <button type="button" @click="toggleDropdown($event)" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-bold cursor-pointer transition"
                                    :class="{
                                        'bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-300': status === 'pending',
                                        'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-300': status === 'completed',
                                        'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-300': status === 'cancelled'
                                    }">
                                    <span x-text="status === 'pending' ? 'Pending' : (status === 'completed' ? 'Selesai' : 'Batal')"></span>
                                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="w-36 rounded-lg bg-white dark:bg-navy-800 shadow-lg border border-gray-200 dark:border-white/10 py-1"
                                    :style="dropdownStyle">
                                    <button type="button" @click="changeStatus('pending')"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                        :class="status === 'pending' ? 'font-bold text-orange-600 dark:text-orange-300' : 'text-gray-700 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> Pending
                                    </button>
                                    <button type="button" @click="changeStatus('completed')"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                        :class="status === 'completed' ? 'font-bold text-green-600 dark:text-green-300' : 'text-gray-700 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Selesai
                                    </button>
                                    <button type="button" @click="changeStatus('cancelled')"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                        :class="status === 'cancelled' ? 'font-bold text-red-600 dark:text-red-300' : 'text-gray-700 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Batal
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="py-4 col-actions" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center">
                                <button class="btn-actions-menu relative" data-transaksi-id="{{ $item->id }}" data-transaksi-invoice="{{ $item->invoice }}" data-transaksi-edit="{{ route('transaksi.masuk.edit', $item) }}" data-transaksi-print="{{ route('transaksi.masuk.print', $item) }}" data-transaksi-pdf="{{ route('transaksi.masuk.pdf', $item) }}" data-transaksi-destroy="{{ route('transaksi.masuk.destroy', $item) }}">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 8c1.1 0 2-0.9 2-2s-0.9-2-2-2-2 0.9-2 2 0.9 2 2 2zm0 2c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2zm0 6c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">Tidak ada transaksi masuk</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Mulai dengan membuat transaksi masuk baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Items Per Page & Info -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Item per halaman:</span>
                    <form method="GET" action="{{ route('transaksi.masuk.index') }}" class="inline-block">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="per_page" onchange="this.form.submit()" 
                                class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        </select>
                    </form>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Menampilkan {{ $transaksi->firstItem() ?? 0 }} ke {{ $transaksi->lastItem() ?? 0 }} dari {{ $transaksi->total() }}
                    </span>
                </div>

                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1">
                    @php
                        $paginationParams = '';
                        if(request('search')) $paginationParams .= '&search=' . urlencode(request('search'));
                        $paginationParams .= '&per_page=' . request('per_page', 10);
                    @endphp
                    
                    @if ($transaksi->onFirstPage())
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
                    @else
                        <a href="{{ $transaksi->previousPageUrl() }}{{ $paginationParams }}" 
                           class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">◀</a>
                    @endif

                    @for ($page = max(1, $transaksi->currentPage() - 2); $page <= min($transaksi->lastPage(), $transaksi->currentPage() + 2); $page++)
                        @if ($page == $transaksi->currentPage())
                            <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $transaksi->url($page) }}{{ $paginationParams }}" 
                               class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($transaksi->hasMorePages())
                        <a href="{{ $transaksi->nextPageUrl() }}{{ $paginationParams }}" 
                           class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">▶</a>
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">▶</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Dropdown - Inline -->
<div id="actionDropdown" class="actions-dropdown">
    <button id="editMenuItem" class="actions-dropdown-item edit">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        <span>Edit</span>
    </button>
    <button id="printMenuItem" class="actions-dropdown-item print">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        <span>Cetak</span>
    </button>
    <button id="pdfMenuItem" class="actions-dropdown-item print">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Download PDF</span>
    </button>
    <button id="deleteMenuItem" class="actions-dropdown-item delete">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        <span>Hapus</span>
    </button>
</div>

<!-- Bulk Delete Form -->
<form id="bulkDeleteForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" id="bulkIds" name="ids" value="">
</form>

<div id="deleteConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-sm w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Konfirmasi Hapus</h3>
            <button type="button" id="modalCloseBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400 mb-2">Apakah Anda yakin ingin menghapus item ini?</p>
            <p class="text-sm text-gray-500 dark:text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" id="modalCancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">Batal</button>
            <button type="button" id="modalConfirmBtn" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-semibold">Hapus</button>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <div>
                <h3 class="text-lg font-bold text-navy-700 dark:text-white">Ekspor Transaksi Masuk</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pilih opsi ekspor data</p>
            </div>
            <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="exportForm" method="GET" action="{{ route('transaksi.masuk.export') }}">
            <div class="p-6 space-y-4">
                <!-- Quick Period Selector -->
                <div>
                    <label class="block text-sm font-semibold text-navy-700 dark:text-white mb-2">Periode Cepat</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="setQuickPeriod('today')" class="quick-period-btn px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-green-50 hover:border-green-300 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:border-green-500 dark:hover:text-green-400 transition">Hari Ini</button>
                        <button type="button" onclick="setQuickPeriod('week')" class="quick-period-btn px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-green-50 hover:border-green-300 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:border-green-500 dark:hover:text-green-400 transition">Minggu Ini</button>
                        <button type="button" onclick="setQuickPeriod('month')" class="quick-period-btn px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-green-50 hover:border-green-300 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:border-green-500 dark:hover:text-green-400 transition">Bulan Ini</button>
                        <button type="button" onclick="setQuickPeriod('last_month')" class="quick-period-btn px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-green-50 hover:border-green-300 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:border-green-500 dark:hover:text-green-400 transition">Bulan Lalu</button>
                        <button type="button" onclick="setQuickPeriod('year')" class="quick-period-btn px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-green-50 hover:border-green-300 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:border-green-500 dark:hover:text-green-400 transition">Tahun Ini</button>
                        <button type="button" onclick="setQuickPeriod('all')" class="quick-period-btn px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:bg-green-50 hover:border-green-300 hover:text-green-600 dark:hover:bg-green-900/20 dark:hover:border-green-500 dark:hover:text-green-400 transition">Semua</button>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="export_start_date" class="block text-sm font-semibold text-navy-700 dark:text-white mb-1">Dari Tanggal</label>
                        <input type="date" id="export_start_date" name="start_date" 
                               class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-3 py-2 text-sm text-navy-700 dark:text-white outline-none focus:border-green-500 dark:focus:border-green-400">
                    </div>
                    <div>
                        <label for="export_end_date" class="block text-sm font-semibold text-navy-700 dark:text-white mb-1">Sampai Tanggal</label>
                        <input type="date" id="export_end_date" name="end_date" 
                               class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-3 py-2 text-sm text-navy-700 dark:text-white outline-none focus:border-green-500 dark:focus:border-green-400">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="export_status" class="block text-sm font-semibold text-navy-700 dark:text-white mb-1">Status</label>
                    <select id="export_status" name="status" 
                            class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-3 py-2 text-sm text-navy-700 dark:text-white outline-none focus:border-green-500 dark:focus:border-green-400">
                        <option value="all">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Format -->
                <div>
                    <label class="block text-sm font-semibold text-navy-700 dark:text-white mb-2">Format File</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-white/10 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/20 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50 dark:has-[:checked]:border-green-400 dark:has-[:checked]:bg-green-900/20">
                            <input type="radio" name="format" value="xlsx" checked class="text-green-500 focus:ring-green-500">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 18H7v-6h1.5v6zm3.5 0h-1.5l-1-2.5L9 18H7.5l1.75-3.5L7.5 11H9l1 2.5 1-2.5h1.5l-1.75 3.5L13.5 18h-1.5z"/></svg>
                            <span class="text-sm font-medium text-navy-700 dark:text-white">Excel</span>
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-white/10 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/20 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50 dark:has-[:checked]:border-green-400 dark:has-[:checked]:bg-green-900/20">
                            <input type="radio" name="format" value="csv" class="text-green-500 focus:ring-green-500">
                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9 13h6v1H9v-1zm0 2h6v1H9v-1zm0 2h4v1H9v-1z"/></svg>
                            <span class="text-sm font-medium text-navy-700 dark:text-white">CSV</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">Batal</button>
                <button type="submit" class="flex items-center gap-2 px-5 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition font-semibold">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
<script>
function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.transaksi-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.transaksi-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
    } else {
        bulkDeleteBtn.classList.add('hidden');
        document.getElementById('selectAllCheckbox').checked = false;
    }
}

function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.transaksi-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) {
        alert('Pilih minimal satu transaksi untuk dihapus');
        return;
    }

    const modal = document.getElementById('deleteConfirmModal');
    const messageEl = modal.querySelector('p.text-gray-600');
    messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus <span class="font-bold text-red-600 dark:text-red-400">' + count + ' transaksi' + (count > 1 ? 's' : '') + '</span>?';
    modal.classList.remove('hidden');
    
    window.pendingDeleteIds = Array.from(checkedBoxes).map(function(cb) {
        return cb.value;
    });
}

function proceedBulkDelete() {
    // Check if this is a single delete from dropdown
    if (window.pendingDeleteUrl) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.pendingDeleteUrl;
        form.style.display = 'none';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken.content;
            form.appendChild(input);
        }
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
        delete window.pendingDeleteUrl;
        return;
    }
    
    // Otherwise it's a bulk delete
    if (!window.pendingDeleteIds || window.pendingDeleteIds.length === 0) {
        alert('Pilih minimal satu transaksi untuk dihapus');
        return;
    }

    document.getElementById('bulkIds').value = JSON.stringify(window.pendingDeleteIds);
    
    const form = document.getElementById('bulkDeleteForm');
    form.action = '{{ route('transaksi.masuk.bulk-destroy') }}';
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalCloseBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalCancelBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalConfirmBtn').addEventListener('click', proceedBulkDelete);

    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
        if (e.target.id === 'deleteConfirmModal') {
            closeDeleteModal();
        }
    });

    // Dropdown management
    let currentButton = null;
    const actionDropdown = document.getElementById('actionDropdown');
    const editMenuItem = document.getElementById('editMenuItem');
    const printMenuItem = document.getElementById('printMenuItem');
    const pdfMenuItem = document.getElementById('pdfMenuItem');
    const deleteMenuItem = document.getElementById('deleteMenuItem');

    // Handle action button click
    document.querySelectorAll('.btn-actions-menu').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentButton = btn;
            
            // Position dropdown - account for zoom: 90% (0.9) in app.blade
            const rect = btn.getBoundingClientRect();
            const zoomFactor = 0.9;
            const dropdownWidth = 140;
            actionDropdown.style.position = 'fixed';
            actionDropdown.style.top = (rect.top / zoomFactor) + 'px';
            actionDropdown.style.left = ((rect.left - dropdownWidth) / zoomFactor) + 'px';
            actionDropdown.style.zIndex = '1001';
            
            actionDropdown.classList.add('show');
        });
    });

    // Handle edit menu item click
    editMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentButton) {
            const editUrl = currentButton.getAttribute('data-transaksi-edit');
            if (editUrl) {
                window.location.href = editUrl;
            }
        }
        
        actionDropdown.classList.remove('show');
    });

    // Handle print menu item click
    printMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentButton) {
            const printUrl = currentButton.getAttribute('data-transaksi-print');
            if (printUrl) {
                window.open(printUrl, '_blank');
            }
        }
        
        actionDropdown.classList.remove('show');
    });

    // Handle PDF download menu item click
    pdfMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentButton) {
            const pdfUrl = currentButton.getAttribute('data-transaksi-pdf');
            if (pdfUrl) {
                window.open(pdfUrl, '_blank');
            }
        }
        
        actionDropdown.classList.remove('show');
    });

    // Handle delete menu item click
    deleteMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentButton) {
            const destroyUrl = currentButton.getAttribute('data-transaksi-destroy');
            const transaksiId = currentButton.getAttribute('data-transaksi-id');
            if (destroyUrl) {
                const modal = document.getElementById('deleteConfirmModal');
                const messageEl = modal.querySelector('p.text-gray-600');
                messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus transaksi ini?';
                modal.classList.remove('hidden');
                window.pendingDeleteUrl = destroyUrl;
                window.pendingDeleteIds = [transaksiId];
            }
        }
        
        actionDropdown.classList.remove('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.btn-actions-menu') && !e.target.closest('#actionDropdown')) {
            actionDropdown.classList.remove('show');
        }
    });

    // Close dropdown with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            actionDropdown.classList.remove('show');
        }
    });

    document.querySelectorAll('tr[data-href]').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-actions-menu') && !e.target.closest('.transaksi-checkbox')) {
                window.location.href = this.dataset.href;
            }
        });
    });
});

function statusDropdown(initialStatus, url) {
    return {
        open: false,
        status: initialStatus,
        loading: false,
        dropdownStyle: '',
        toggleDropdown(event) {
            if (this.open) { this.open = false; return; }
            const btn = event.currentTarget;
            const rect = btn.getBoundingClientRect();
            const zoomFactor = 0.9;
            const dropdownHeight = 110;
            const dropdownWidth = 144;
            const spaceBelow = window.innerHeight - rect.bottom;
            let top, left;
            if (spaceBelow < dropdownHeight) {
                top = (rect.top / zoomFactor) - dropdownHeight - 4;
            } else {
                top = (rect.bottom / zoomFactor) + 4;
            }
            left = ((rect.left + rect.width / 2) / zoomFactor) - (dropdownWidth / 2);
            if (left < 8) left = 8;
            this.dropdownStyle = 'position:fixed;top:' + top + 'px;left:' + left + 'px;z-index:9999;';
            this.open = true;
            const self = this;
            const closeOnScroll = () => { self.open = false; window.removeEventListener('scroll', closeOnScroll, true); };
            window.addEventListener('scroll', closeOnScroll, true);
        },
        async changeStatus(newStatus) {
            if (this.status === newStatus || this.loading) { this.open = false; return; }
            this.loading = true;
            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await response.json();
                if (data.success) {
                    this.status = newStatus;
                    showStatusToast(data.message, 'success');
                } else {
                    showStatusToast(data.message || 'Gagal mengubah status', 'error');
                }
            } catch (err) {
                showStatusToast('Gagal mengubah status', 'error');
            }
            this.loading = false;
            this.open = false;
        }
    };
}

function showStatusToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-5 right-5 z-[9999] px-6 py-3 rounded-lg shadow-lg text-white text-sm font-medium transition-opacity duration-500 ' +
        (type === 'success' ? 'bg-green-500' : 'bg-red-500');
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 3000);
}

// ============= EXPORT FUNCTIONS =============
function openExportModal() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    document.getElementById('export_start_date').value = formatDateInput(firstDay);
    document.getElementById('export_end_date').value = formatDateInput(now);
    highlightQuickPeriod('month');
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

function formatDateInput(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
}

function setQuickPeriod(period) {
    const now = new Date();
    let start, end;

    switch (period) {
        case 'today':
            start = end = new Date(now);
            break;
        case 'week':
            start = new Date(now);
            start.setDate(now.getDate() - now.getDay());
            end = new Date(now);
            break;
        case 'month':
            start = new Date(now.getFullYear(), now.getMonth(), 1);
            end = new Date(now);
            break;
        case 'last_month':
            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            end = new Date(now.getFullYear(), now.getMonth(), 0);
            break;
        case 'year':
            start = new Date(now.getFullYear(), 0, 1);
            end = new Date(now);
            break;
        case 'all':
            start = new Date(2020, 0, 1);
            end = new Date(now);
            break;
    }

    document.getElementById('export_start_date').value = formatDateInput(start);
    document.getElementById('export_end_date').value = formatDateInput(end);
    highlightQuickPeriod(period);
}

function highlightQuickPeriod(activePeriod) {
    const buttons = document.querySelectorAll('.quick-period-btn');
    const periods = ['today', 'week', 'month', 'last_month', 'year', 'all'];
    buttons.forEach((btn, index) => {
        if (periods[index] === activePeriod) {
            btn.classList.add('!bg-green-50', '!border-green-500', '!text-green-600', 'dark:!bg-green-900/20', 'dark:!border-green-400', 'dark:!text-green-400');
        } else {
            btn.classList.remove('!bg-green-50', '!border-green-500', '!text-green-600', 'dark:!bg-green-900/20', 'dark:!border-green-400', 'dark:!text-green-400');
        }
    });
}

document.getElementById('exportModal').addEventListener('click', function(e) {
    if (e.target.id === 'exportModal') closeExportModal();
});
</script>
@endpush
