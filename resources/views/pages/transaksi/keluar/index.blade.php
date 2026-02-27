@extends('layouts.app')

@section('title', 'Outgoing Transactions')

@push('style')
<!-- Page-specific styles -->
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
<style>
    .col-no { width: 50px; }
    .col-actions { width: 60px; }
</style>
@endpush

@section('main')
@include('components.access-denied-overlay', ['module' => 'Outgoing Transaction', 'hasAccessRead' => $hasAccessRead])
<div class="mt-3 px-[11px] pr-[10px] @if(!$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Transactions Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Outgoing Transactions</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $transaksi->total() }} outgoing transactions (purchases)
                </p>
            </div>
            
            <!-- Search, Bulk Delete & Add Button -->
            <div class="flex items-center gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('transaksi.keluar.index') }}" class="relative">
                    <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white mr-2" xmlns="http://www.w3.org/2000/svg">
                            <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice, toko, supplier..." 
                               class="block w-full bg-transparent text-sm font-medium text-navy-700 dark:text-white outline-none placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    </div>
                </form>
                
                <!-- Bulk Delete Button (Hidden by default) -->
                <button id="bulkDeleteBtn" onclick="confirmBulkDelete()" 
                        style="display: none;"
                        class="flex items-center gap-2 rounded-xl bg-red-500 px-4 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-400 dark:hover:bg-red-300 dark:active:bg-red-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Selected
                </button>

                @permission('transaksi.create')
                <a href="{{ route('transaksi.keluar.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-400 dark:hover:bg-red-300 dark:active:bg-red-200">
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
            <form id="bulkDeleteForm" method="POST" action="{{ route('transaksi.keluar.bulk-destroy') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="ids" id="bulkDeleteIds">

                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <!-- Checkbox -->
                            <th class="py-3 text-center" style="width: 40px;">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)"
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 dark:border-white/20">
                            </th>
                            <!-- NO -->
                            <th class="col-no py-3 text-center">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">No</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Invoice</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Toko</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Supplier</p>
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
                            <th class="col-actions py-3 text-center">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Aksi</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition"
                            data-href="{{ route('transaksi.keluar.edit', $item->id) }}"
                            onclick="if(!event.target.closest('input, button, .actions-dropdown')) window.location = this.dataset.href">
                            <!-- Checkbox -->
                            <td class="py-4 text-center" style="width: 40px;" onclick="event.stopPropagation();">
                                <input type="checkbox" class="transaksi-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 dark:border-white/20"
                                       value="{{ $item->id }}" onchange="updateBulkDeleteButton()">
                            </td>
                            <!-- NO -->
                            <td class="col-no py-4 text-center">
                                <p class="text-sm font-bold text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</p>
                            </td>
                            <!-- Invoice -->
                            <td class="py-4">
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->invoice }}</p>
                            </td>
                            <!-- Toko -->
                            <td class="py-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->toko->nama ?? '-' }}</p>
                            </td>
                            <!-- Supplier -->
                            <td class="py-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->supplier->nama ?? '-' }}</p>
                            </td>
                            <!-- IMEI -->
                            <td class="py-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                    @php
                                        $imeis = $item->items->pluck('produk.imei')->filter()->unique()->values();
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
                                        $productTypes = $item->items->pluck('produk.product_type')->filter()->unique()->values();
                                    @endphp
                                    @if($productTypes->count() > 0)
                                        @foreach($productTypes as $type)
                                            @if($type === 'electronic')
                                                <span class="inline-block bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-medium mb-1 mr-1">📱 Electronic</span>
                                            @elseif($type === 'accessory')
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
                            <!-- Total -->
                            <td class="py-4 text-right">
                                <p class="text-sm font-bold text-red-500">{{ get_currency_symbol() }} {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                            </td>
                            <!-- Payment Method -->
                            <td class="py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-100 dark:bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-600 dark:text-purple-300">
                                    {{ ucfirst($item->metode_pembayaran ?? 'N/A') }}
                                </span>
                            </td>
                            <!-- Status -->
                            <td class="py-4 text-center" onclick="event.stopPropagation()">
                                <div class="inline-block" x-data="statusDropdown('{{ $item->status }}', '{{ route('transaksi.keluar.update-status', $item->id) }}')">
                                    <button type="button" @click="toggleDropdown($event)" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-bold cursor-pointer transition"
                                        :class="{
                                            'bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-300': status === 'pending',
                                            'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-300': status === 'completed',
                                            'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-300': status === 'cancelled'
                                        }">
                                        <span x-text="status === 'pending' ? 'Pending' : (status === 'completed' ? 'Completed' : 'Cancelled')"></span>
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
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Completed
                                        </button>
                                        <button type="button" @click="changeStatus('cancelled')"
                                            class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                            :class="status === 'cancelled' ? 'font-bold text-red-600 dark:text-red-300' : 'text-gray-700 dark:text-gray-300'">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Cancelled
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <!-- Date -->
                            <td class="py-4 text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                            </td>
                            <!-- Actions -->
                            <td class="py-4 text-center" onclick="event.stopPropagation();">
                                <button class="btn-actions-menu relative" 
                                        data-transaksi-id="{{ $item->id }}"
                                        data-transaksi-invoice="{{ $item->invoice }}"
                                        data-transaksi-edit="{{ route('transaksi.keluar.edit', $item->id) }}"
                                        data-transaksi-print="{{ route('transaksi.keluar.print', $item->id) }}"
                                        data-transaksi-destroy="{{ route('transaksi.keluar.destroy', $item->id) }}">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No outgoing transactions found</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Start by creating a new outgoing transaction</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Items Per Page & Info -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Item per halaman:</span>
                    <form method="GET" action="{{ route('transaksi.keluar.index') }}" class="inline-block">
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
    <button id="deleteMenuItem" class="actions-dropdown-item delete">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        <span>Hapus</span>
    </button>
</div>

<!-- Delete Confirmation Modal -->
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
            <p class="text-gray-600 dark:text-gray-400 mb-2">Apakah Anda yakin ingin menghapus transaksi ini?</p>
            <p class="text-sm text-gray-500 dark:text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" id="modalCancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">Batal</button>
            <button type="button" id="modalConfirmBtn" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-semibold">Hapus</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/table-action-dropdown@latest/dist/table-action-dropdown.min.js"></script>
<script>
    // Initialize TableActionDropdown with custom handlers
    document.addEventListener('DOMContentLoaded', function() {
        new TableActionDropdown({
            dropDownSelector: '.actions-dropdown',
            buttonSelector: '.actions-dropdown-button',
            editMenuSelector: 'a[href*="/edit"]',
            deleteMenuSelector: 'a[href*="/destroy"]',
            customHandlers: {
                print: function(button) {
                    const printUrl = button.getAttribute('data-transaksi-print');
                    if (printUrl) {
                        window.open(printUrl, '_blank');
                    }
                }
            }
        });

        // Dropdown menu structure
        const tableBody = document.querySelector('tbody');
        if (tableBody) {
            tableBody.querySelectorAll('.actions-dropdown-button').forEach(button => {
                const dropdown = document.createElement('div');
                dropdown.className = 'actions-dropdown hidden absolute right-0 mt-2 w-48 rounded-lg bg-white shadow-lg dark:bg-navy-800 border border-gray-200 dark:border-white/10 z-50';
                
                const editUrl = button.getAttribute('data-transaksi-edit');
                const printUrl = button.getAttribute('data-transaksi-print');
                const deleteUrl = button.getAttribute('data-transaksi-delete');
                
                dropdown.innerHTML = `
                    <div class="py-1">
                        <a href="${editUrl}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <button onclick="window.open('${printUrl}', '_blank')" class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print
                        </button>
                        <button onclick="event.stopPropagation(); deleteSingleTransaction('${deleteUrl}')" class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </div>
                `;
                
                button.parentElement.appendChild(dropdown);
                button.onclick = function(e) {
                    e.stopPropagation();
                    const allDropdowns = document.querySelectorAll('.actions-dropdown');
                    allDropdowns.forEach(dd => dd.classList.add('hidden'));
                    dropdown.classList.toggle('hidden');
                };
            });
        }

        @if(session('success'))
            showSuccessToast('{{ session('success') }}');
        @endif
    });

    // Modal event handlers
    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
        window.pendingDeleteUrl = null;
        window.pendingDeleteIds = null;
    }

    document.getElementById('modalCloseBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalCancelBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalConfirmBtn').addEventListener('click', proceedBulkDelete);
    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
        if (e.target.id === 'deleteConfirmModal') closeDeleteModal();
    });

    // Dropdown management
    let currentButton = null;
    const actionDropdown = document.getElementById('actionDropdown');
    const editMenuItem = document.getElementById('editMenuItem');
    const printMenuItem = document.getElementById('printMenuItem');
    const deleteMenuItem = document.getElementById('deleteMenuItem');

    // Action button click handler with zoom compensation
    document.querySelectorAll('.btn-actions-menu').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentButton = btn;
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

    // Edit menu item handler
    editMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (currentButton) {
            const editUrl = currentButton.getAttribute('data-transaksi-edit');
            if (editUrl) window.location.href = editUrl;
        }
        actionDropdown.classList.remove('show');
    });

    // Print menu item handler
    printMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (currentButton) {
            const printUrl = currentButton.getAttribute('data-transaksi-print');
            if (printUrl) window.open(printUrl, '_blank');
        }
        actionDropdown.classList.remove('show');
    });

    // Delete menu item handler
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

    // Close dropdown when clicking outside or pressing Escape
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.btn-actions-menu') && !e.target.closest('#actionDropdown')) {
            actionDropdown.classList.remove('show');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') actionDropdown.classList.remove('show');
    });

    // Row click navigation to edit page
    document.querySelectorAll('tr[data-href]').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-actions-menu') && !e.target.closest('.transaksi-checkbox')) {
                window.location.href = this.dataset.href;
            }
        });
    });

    // Checkbox functions
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.transaksi-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBulkDeleteButton();
    }

    function updateBulkDeleteButton() {
        const checkboxes = document.querySelectorAll('.transaksi-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        
        if (checkboxes.length > 0) {
            bulkDeleteBtn.style.display = 'flex';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }

        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.transaksi-checkbox');
        if (checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    function confirmBulkDelete() {
        const checkboxes = document.querySelectorAll('.transaksi-checkbox:checked');
        const count = checkboxes.length;
        
        if (count === 0) {
            alert('Please select at least one transaction');
            return;
        }

        const modal = document.getElementById('deleteConfirmModal');
        const messageEl = modal.querySelector('p.text-gray-600');
        messageEl.innerHTML = `Apakah Anda yakin ingin menghapus ${count} transaksi?`;
        modal.classList.remove('hidden');
        
        // Store bulk delete IDs
        window.pendingDeleteIds = Array.from(checkboxes).map(cb => cb.value);
        window.pendingDeleteUrl = null;
    }

    function proceedBulkDelete() {
        // Single delete from dropdown
        if (window.pendingDeleteUrl && !window.pendingDeleteIds.length) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.pendingDeleteUrl;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        } 
        // Bulk delete from checkboxes
        else if (window.pendingDeleteIds && window.pendingDeleteIds.length) {
            document.getElementById('bulkDeleteIds').value = JSON.stringify(window.pendingDeleteIds);
            document.getElementById('bulkDeleteForm').submit();
        }
        
        closeDeleteModal();
    }

    function showSuccessToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

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
</script>
@endpush
