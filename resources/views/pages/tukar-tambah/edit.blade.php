@extends('layouts.app')

@section('title', 'Edit Trade-In')

@push('style')
<style>
.hidden { display: none; }
</style>
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Trade-In #{{ $tukarTambah->id }}</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update trade-in transaction information</p>
            </div>
            <a href="{{ route('tukar-tambah.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-100 dark:bg-red-900/30 p-4">
                <p class="font-bold text-red-800 dark:text-red-300">Error:</p>
                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Transaction Info -->
        <div class="mb-6 p-4 rounded-xl bg-lightPrimary dark:bg-navy-700">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Created</p>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $tukarTambah->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Sale Invoice</p>
                    <p class="text-sm font-bold text-green-600 dark:text-green-400">{{ $tukarTambah->transaksiPenjualan->invoice ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Purchase Invoice</p>
                    <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $tukarTambah->transaksiPembelian->invoice ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Store</p>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $tukarTambah->toko->nama ?? '-' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('tukar-tambah.update', $tukarTambah->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Basic Info Section -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Basic Information</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Store Field -->
                    <div>
                        <label for="pos_toko_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Store <span class="text-red-500">*</span>
                        </label>
                        <select id="pos_toko_id" name="pos_toko_id" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                            <option value="">Select Store</option>
                            @foreach($tokos as $toko)
                                <option value="{{ $toko->id }}" {{ old('pos_toko_id', $tukarTambah->pos_toko_id) == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Field -->
                    <div>
                        <label for="pos_pelanggan_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Customer <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <select id="pos_pelanggan_id" name="pos_pelanggan_id"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                            <option value="">Select Customer</option>
                            @foreach($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}" {{ old('pos_pelanggan_id', $tukarTambah->pos_pelanggan_id) == $pelanggan->id ? 'selected' : '' }}>{{ $pelanggan->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <!-- Product OUT Section (Sale) -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                    Product OUT (Sale to Customer)
                </h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    
                    <div class="md:col-span-3">
                        <label for="pos_produk_keluar_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Select Product <span class="text-red-500">*</span>
                        </label>
                        <select id="pos_produk_keluar_id" name="pos_produk_keluar_id" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                            <option value="">Select Product</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}" 
                                    {{ old('pos_produk_keluar_id', $tukarTambah->pos_produk_keluar_id) == $produk->id ? 'selected' : '' }}>
                                    {{ $produk->nama }} - Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="harga_jual_keluar" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Sale Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="harga_jual_keluar" name="harga_jual_keluar" required min="0" step="0.01"
                            value="{{ old('harga_jual_keluar', $tukarTambah->transaksiPenjualan->items->first()->harga_satuan ?? 0) }}"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label for="diskon_keluar" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Discount
                        </label>
                        <input type="number" id="diskon_keluar" name="diskon_keluar" min="0" step="0.01"
                            value="{{ old('diskon_keluar', $tukarTambah->transaksiPenjualan->items->first()->diskon ?? 0) }}"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Net Amount
                        </label>
                        <input type="text" id="net_keluar" readonly
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-navy-700 px-4 py-3 text-sm font-bold text-navy-700 dark:text-white">
                    </div>

                </div>
            </div>

            <!-- Product IN Section (Purchase) -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
                    </svg>
                    Product IN (Purchase from Customer)
                </h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="pos_produk_masuk_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Select Product <span class="text-red-500">*</span>
                        </label>
                        <select id="pos_produk_masuk_id" name="pos_produk_masuk_id" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                            <option value="">Select Product</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_beli }}" 
                                    {{ old('pos_produk_masuk_id', $tukarTambah->pos_produk_masuk_id) == $produk->id ? 'selected' : '' }}>
                                    {{ $produk->nama }} - Buy: Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="harga_beli_masuk" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Purchase Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="harga_beli_masuk" name="harga_beli_masuk" required min="0" step="0.01"
                            value="{{ old('harga_beli_masuk', $tukarTambah->transaksiPembelian->items->first()->harga_satuan ?? 0) }}"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>

            <!-- Transaction Details Section -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Transaction Details</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <div>
                        <label for="metode_pembayaran" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select id="metode_pembayaran" name="metode_pembayaran" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                            <option value="">Select Payment Method</option>
                            @php
                                $metodePembayaran = strtolower(old('metode_pembayaran', $tukarTambah->transaksiPenjualan->metode_pembayaran ?? 'cash'));
                            @endphp
                            <option value="cash" {{ $metodePembayaran == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ $metodePembayaran == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="e-wallet" {{ $metodePembayaran == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="credit" {{ $metodePembayaran == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>

                    <div>
                        <label for="keterangan" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Notes
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="1"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">{{ old('keterangan', str_replace('Penjualan Trade-In: ', '', $tukarTambah->transaksiPenjualan->keterangan ?? '')) }}</textarea>
                    </div>

                </div>
            </div>

            <!-- Summary Box -->
            <div class="mb-6 p-4 rounded-xl bg-lightPrimary dark:bg-navy-700">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-3">Transaction Summary</h5>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-navy-900">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Sale Revenue</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400" id="summary_sale">Rp 0</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-navy-900">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Purchase Cost</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400" id="summary_purchase">Rp 0</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-navy-900">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Net Profit</p>
                        <p class="text-xl font-bold text-navy-700 dark:text-white" id="summary_profit">Rp 0</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <!-- Delete Button -->
                <button type="button" 
                        onclick="if(confirm('Apakah Anda yakin ingin menghapus trade-in ini? Ini akan menghapus 2 transaksi terkait dan mengembalikan stok. Tindakan ini tidak dapat dibatalkan.')) document.getElementById('deleteForm').submit()"
                        class="flex items-center gap-2 rounded-xl bg-red-100 px-4 py-3 text-sm font-bold text-red-600 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Trade-In
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('tukar-tambah.index') }}" 
                       class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                        </svg>
                        Update Trade-In
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="deleteForm" action="{{ route('tukar-tambah.destroy', $tukarTambah->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Currency formatter using server-side settings
const currency = '{{ get_currency() }}';
const currencySymbol = '{{ get_currency_symbol() }}';
const decimalPlaces = {{ get_decimal_places() }};

function formatCurrency(amount) {
    let formatted;
    
    if (currency === 'IDR') {
        // Indonesian: Rp 100.000 (no decimals)
        formatted = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    } else if (currency === 'MYR' || currency === 'USD') {
        // Malaysian/US: RM 100.00 or $ 100.00 (2 decimals)
        formatted = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    } else {
        formatted = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces
        }).format(amount);
    }
    
    return currencySymbol + ' ' + formatted;
}

function calculateNet() {
    const hargaJual = parseFloat(document.getElementById('harga_jual_keluar').value) || 0;
    const diskon = parseFloat(document.getElementById('diskon_keluar').value) || 0;
    const net = hargaJual - diskon;
    document.getElementById('net_keluar').value = formatCurrency(net);
    
    updateSummary();
}

function updateSummary() {
    const saleAmount = (parseFloat(document.getElementById('harga_jual_keluar').value) || 0) - 
                       (parseFloat(document.getElementById('diskon_keluar').value) || 0);
    const purchaseAmount = parseFloat(document.getElementById('harga_beli_masuk').value) || 0;
    const profit = saleAmount - purchaseAmount;
    
    document.getElementById('summary_sale').textContent = formatCurrency(saleAmount);
    document.getElementById('summary_purchase').textContent = formatCurrency(purchaseAmount);
    document.getElementById('summary_profit').textContent = formatCurrency(profit);
    document.getElementById('summary_profit').className = profit >= 0 ? 
        'text-xl font-bold text-green-600 dark:text-green-400' : 
        'text-xl font-bold text-red-600 dark:text-red-400';
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill harga jual when product keluar selected
    document.getElementById('pos_produk_keluar_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const harga = selected.getAttribute('data-harga');
        if (harga) {
            document.getElementById('harga_jual_keluar').value = harga;
            calculateNet();
        }
    });

    // Auto-fill harga beli when product masuk selected
    document.getElementById('pos_produk_masuk_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const harga = selected.getAttribute('data-harga');
        if (harga) {
            document.getElementById('harga_beli_masuk').value = harga;
            updateSummary();
        }
    });

    // Calculate net on input change
    document.getElementById('harga_jual_keluar').addEventListener('input', calculateNet);
    document.getElementById('diskon_keluar').addEventListener('input', calculateNet);
    document.getElementById('harga_beli_masuk').addEventListener('input', updateSummary);

    // Initialize
    calculateNet();
});
</script>
@endpush
