@extends('layouts.app')

@section('title', 'Create Trade-In')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create New Trade-In</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Trade-in creates 2 transactions: Purchase (IN) & Sale (OUT)</p>
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

        <form action="{{ route('tukar-tambah.store') }}" method="POST">
            @csrf
            
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
                                <option value="{{ $toko->id }}" {{ old('pos_toko_id') == $toko->id ? 'selected' : '' }}>{{ $toko->nama }}</option>
                            @endforeach
                        </select>
                        @error('pos_toko_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
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
                                <option value="{{ $pelanggan->id }}" {{ old('pos_pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>{{ $pelanggan->nama }}</option>
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
                                <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}" {{ old('pos_produk_keluar_id') == $produk->id ? 'selected' : '' }}>
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
                            value="{{ old('harga_jual_keluar') }}"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label for="diskon_keluar" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Discount
                        </label>
                        <input type="number" id="diskon_keluar" name="diskon_keluar" min="0" step="0.01"
                            value="{{ old('diskon_keluar', 0) }}"
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
                
                <!-- Toggle Existing/New Product -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Product Type <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="produk_masuk_type" value="existing" {{ old('produk_masuk_type', 'existing') == 'existing' ? 'checked' : '' }}
                                class="mr-2" onchange="toggleProductType()">
                            <span class="text-sm text-navy-700 dark:text-white">Existing Product</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="produk_masuk_type" value="new" {{ old('produk_masuk_type') == 'new' ? 'checked' : '' }}
                                class="mr-2" onchange="toggleProductType()">
                            <span class="text-sm text-navy-700 dark:text-white">New Product</span>
                        </label>
                    </div>
                </div>

                <!-- Existing Product Selection -->
                <div id="existing_product_section" class="{{ old('produk_masuk_type', 'existing') == 'existing' ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="pos_produk_masuk_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                                Select Existing Product
                            </label>
                            <select id="pos_produk_masuk_id" name="pos_produk_masuk_id"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                                <option value="">Select Product</option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_beli }}" {{ old('pos_produk_masuk_id') == $produk->id ? 'selected' : '' }}>
                                        {{ $produk->nama }} - Buy: Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- New Product Form -->
                <div id="new_product_section" class="{{ old('produk_masuk_type') == 'new' ? '' : 'hidden' }}">
                    
                    <!-- Brand Selection -->
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Brand Type
                        </label>
                        <div class="flex gap-4 mb-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="merk_type" value="existing" {{ old('merk_type', 'existing') == 'existing' ? 'checked' : '' }}
                                    class="mr-2" onchange="toggleMerkType()">
                                <span class="text-sm text-navy-700 dark:text-white">Existing Brand</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="merk_type" value="new" {{ old('merk_type') == 'new' ? 'checked' : '' }}
                                    class="mr-2" onchange="toggleMerkType()">
                                <span class="text-sm text-navy-700 dark:text-white">New Brand</span>
                            </label>
                        </div>

                        <div id="existing_merk_section" class="{{ old('merk_type', 'existing') == 'existing' ? '' : 'hidden' }}">
                            <select id="pos_produk_merk_id" name="pos_produk_merk_id"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                                <option value="">Select Brand</option>
                                @foreach($merks as $merk)
                                    <option value="{{ $merk->id }}" {{ old('pos_produk_merk_id') == $merk->id ? 'selected' : '' }}>{{ $merk->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="new_merk_section" class="{{ old('merk_type') == 'new' ? '' : 'hidden' }}">
                            <input type="text" name="merk_nama_baru" value="{{ old('merk_nama_baru') }}" placeholder="Enter new brand name"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>
                    </div>

                    <!-- New Product Fields -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="produk_nama_baru" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                                Product Name
                            </label>
                            <input type="text" id="produk_nama_baru" name="produk_nama_baru" value="{{ old('produk_nama_baru') }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <div>
                            <label for="warna" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">Color</label>
                            <input type="text" id="warna" name="warna" value="{{ old('warna') }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <div>
                            <label for="penyimpanan" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">Storage</label>
                            <input type="text" id="penyimpanan" name="penyimpanan" value="{{ old('penyimpanan') }}" placeholder="e.g., 128GB"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <div>
                            <label for="battery_health" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">Battery Health</label>
                            <input type="text" id="battery_health" name="battery_health" value="{{ old('battery_health') }}" placeholder="e.g., 85%"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <div>
                            <label for="imei" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">IMEI</label>
                            <input type="text" id="imei" name="imei" value="{{ old('imei') }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <div>
                            <label for="aksesoris" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">Accessories</label>
                            <input type="text" id="aksesoris" name="aksesoris" value="{{ old('aksesoris') }}" placeholder="e.g., Charger, Box"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <!-- Purchase Price (Common for both) -->
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 mt-5">
                    <div>
                        <label for="harga_beli_masuk" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Purchase Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="harga_beli_masuk" name="harga_beli_masuk" required min="0" step="0.01"
                            value="{{ old('harga_beli_masuk') }}"
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
                            <option value="Cash" {{ old('metode_pembayaran') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Transfer" {{ old('metode_pembayaran') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="Credit Card" {{ old('metode_pembayaran') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Debit Card" {{ old('metode_pembayaran') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                        </select>
                    </div>

                    <div>
                        <label for="keterangan" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Notes
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="1"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">{{ old('keterangan') }}</textarea>
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
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
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
                    Create Trade-In
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Currency formatter using server-side settings
const currencySymbol = '{{ getCurrencySymbol() }}';
const currencyPosition = '{{ setting('currency_position', 'left') }}';
const thousandsSeparator = '{{ setting('thousands_separator', ',') }}';
const decimalSeparator = '{{ setting('decimal_separator', '.') }}';
const decimalPlaces = {{ setting('decimal_places', 0) }};

function formatCurrency(amount) {
    const formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: decimalPlaces,
        maximumFractionDigits: decimalPlaces
    }).format(amount);
    
    return currencyPosition === 'left' 
        ? currencySymbol + ' ' + formatted 
        : formatted + ' ' + currencySymbol;
}

function toggleProductType() {
    const type = document.querySelector('input[name="produk_masuk_type"]:checked').value;
    document.getElementById('existing_product_section').classList.toggle('hidden', type !== 'existing');
    document.getElementById('new_product_section').classList.toggle('hidden', type !== 'new');
    
    // Update required attributes
    document.getElementById('pos_produk_masuk_id').required = type === 'existing';
    document.getElementById('produk_nama_baru').required = type === 'new';
}

function toggleMerkType() {
    const type = document.querySelector('input[name="merk_type"]:checked').value;
    document.getElementById('existing_merk_section').classList.toggle('hidden', type !== 'existing');
    document.getElementById('new_merk_section').classList.toggle('hidden', type !== 'new');
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
