@extends('layouts.app')

@section('title', 'Create Outgoing Transaction')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create Outgoing Transaction</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new purchase transaction (expense)</p>
            </div>
            <a href="{{ route('transaksi.keluar.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('transaksi.keluar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="is_transaksi_masuk" value="0">
            
            <!-- Section 1: Transaction Information -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-red-500 pl-3">Transaction Information</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Invoice Number -->
                    <div>
                        <label for="invoice" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Invoice Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="invoice"
                            name="invoice" 
                            value="{{ old('invoice', $invoiceNumber) }}"
                            readonly
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900/50 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none"
                        >
                        @error('invoice')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Toko Selection -->
                    <div>
                        <label for="pos_toko_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Store <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="pos_toko_id"
                                name="pos_toko_id" 
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_toko_id') !border-red-500 @enderror appearance-none"
                            >
                                <option value="">Select Store</option>
                                @foreach($tokos as $toko)
                                    <option value="{{ $toko->id }}" {{ old('pos_toko_id') == $toko->id ? 'selected' : '' }}>
                                        {{ $toko->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M7 10l5 5 5-5z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('pos_toko_id')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier Selection -->
                    <div>
                        <label for="pos_supplier_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Supplier
                        </label>
                        <div class="relative">
                            <select 
                                id="pos_supplier_id"
                                name="pos_supplier_id" 
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_supplier_id') !border-red-500 @enderror appearance-none"
                            >
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('pos_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M7 10l5 5 5-5z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('pos_supplier_id')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="status"
                                name="status" 
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('status') !border-red-500 @enderror appearance-none"
                            >
                                <option value="">Select Status</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M7 10l5 5 5-5z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Section 2: Transaction Items -->
            <div class="mb-8">
                <div class="mb-4 flex items-center justify-between">
                    <h5 class="text-lg font-bold text-navy-700 dark:text-white border-l-4 border-purple-500 pl-3">Transaction Items</h5>
                    <button type="button" onclick="addItem()" class="flex items-center gap-2 rounded-xl bg-purple-500 px-4 py-2 text-sm font-bold text-white transition duration-200 hover:bg-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Item
                    </button>
                </div>

                <!-- Items Container -->
                <div id="items-container" class="space-y-4">
                    <!-- Items will be added here dynamically -->
                </div>

                <!-- Total Summary -->
                <div class="mt-6 rounded-xl bg-lightPrimary dark:bg-navy-900 p-4">
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span class="text-navy-700 dark:text-white">Grand Total:</span>
                        <span id="grand-total" class="text-red-600 dark:text-red-400">{{ get_currency_symbol() }} 0</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Payment Details -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-blue-500 pl-3">Payment Details</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Total Amount -->
                    <div>
                        <label for="total_harga" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Total Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ get_currency_symbol() }}</span>
                            </div>
                            <input 
                                type="text" 
                                id="total_harga"
                                name="total_harga" 
                                value="{{ old('total_harga') }}"
                                readonly
                                placeholder="0{{ get_decimal_places() > 0 ? '.' . str_repeat('0', get_decimal_places()) : '' }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900/50 pl-12 pr-4 py-3 text-sm font-semibold text-navy-700 dark:text-white outline-none"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">Auto-calculated from items</p>
                        @error('total_harga')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="metode_pembayaran" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="metode_pembayaran"
                                name="metode_pembayaran" 
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('metode_pembayaran') !border-red-500 @enderror appearance-none"
                            >
                                <option value="">Select Payment Method</option>
                                <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="e-wallet" {{ old('metode_pembayaran') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="credit" {{ old('metode_pembayaran') == 'credit' ? 'selected' : '' }}>Credit</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M7 10l5 5 5-5z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('metode_pembayaran')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Notes
                        </label>
                        <textarea 
                            id="keterangan"
                            name="keterangan" 
                            rows="3"
                            placeholder="Enter transaction notes or remarks"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 resize-none @error('keterangan') !border-red-500 @enderror"
                        >{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Info Box -->
            <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h6 class="text-sm font-bold text-red-900 dark:text-red-300">Outgoing Transaction (Purchase)</h6>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-400">
                            This is a purchase transaction. Invoice number is automatically generated. Select the store and supplier for this transaction.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('transaksi.keluar.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-red-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-400 dark:hover:bg-red-300 dark:active:bg-red-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    Create Expense
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Quick Add Product Modal -->
<div id="quickAddProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-[20px] max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Quick Add Product</h4>
            <button onclick="closeProductModal()" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="quickProductForm" onsubmit="submitQuickProduct(event)">
            <div class="space-y-4">
                <!-- Product Name -->
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="quick_nama" required
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                           placeholder="e.g. iPhone 13 Pro Max">
                </div>

                <!-- Brand -->
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                        Brand <span class="text-red-500">*</span>
                    </label>
                    <select id="quick_merk" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        <option value="">Select Brand</option>
                        @foreach($merks ?? [] as $merk)
                            <option value="{{ $merk->id }}">{{ $merk->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Purchase Price -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Purchase Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quick_harga_beli" step="0.01" min="0" required
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                               placeholder="0">
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Selling Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quick_harga_jual" step="0.01" min="0" required
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                               placeholder="0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Color
                        </label>
                        <input type="text" id="quick_warna"
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                               placeholder="e.g. Sierra Blue">
                    </div>

                    <!-- Storage -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Storage
                        </label>
                        <input type="text" id="quick_penyimpanan"
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                               placeholder="e.g. 256GB">
                    </div>
                </div>

                <!-- Battery Health -->
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                        Battery Health
                    </label>
                    <input type="text" id="quick_battery_health"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                           placeholder="e.g. 85% or Good">
                </div>

                <!-- IMEI -->
                <div>
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                        IMEI
                    </label>
                    <input type="text" id="quick_imei"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                           placeholder="15-digit IMEI">
                </div>

                <!-- Error Messages -->
                <div id="quickProductErrors" class="hidden rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 p-3">
                    <p class="text-sm text-red-600 dark:text-red-400"></p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeProductModal()"
                        class="flex-1 rounded-xl bg-gray-100 px-4 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit" id="quickProductSubmitBtn"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const products = @json($produks);
const services = @json($services);
const currencySymbol = '{{ get_currency_symbol() }}';
const currency = '{{ get_currency() }}';
const csrfToken = '{{ csrf_token() }}';
let itemCounter = 0;
let currentItemIdForModal = null;

function formatNumber(num) {
    if (currency === 'IDR') {
        return parseInt(num).toLocaleString('id-ID');
    } else {
        return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

function addItem() {
    itemCounter++;
    const container = document.getElementById('items-container');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'item-row border border-gray-200 dark:border-white/10 rounded-xl p-4 bg-white dark:bg-navy-900';
    itemDiv.id = `item-${itemCounter}`;
    
    itemDiv.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="hidden" name="items[${itemCounter}][type]" value="product">
                <div class="md:col-span-2">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Product</label>
                            <select name="items[${itemCounter}][item_id]" id="item-select-${itemCounter}" class="item-select w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" onchange="handleItemChange(${itemCounter})" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <button type="button" onclick="openProductModal(${itemCounter})" class="rounded-lg bg-brand-500 px-3 py-2 text-xs font-bold text-white transition duration-200 hover:bg-brand-600 whitespace-nowrap">
                            + New
                        </button>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Qty</label>
                    <input type="number" name="items[${itemCounter}][quantity]" class="item-qty w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" value="1" min="1" onchange="calculateSubtotal(${itemCounter})" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Unit Price</label>
                    <input type="number" name="items[${itemCounter}][harga_satuan]" id="unit-price-${itemCounter}" class="item-price w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" value="0" step="0.01" onchange="calculateSubtotal(${itemCounter})">
                </div>
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Subtotal</label>
                    <input type="text" id="subtotal-display-${itemCounter}" class="item-subtotal w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900 px-3 py-2 text-sm font-semibold text-red-600 dark:text-red-400" readonly value="${currencySymbol} 0">
                    <input type="hidden" name="items[${itemCounter}][subtotal]" id="subtotal-value-${itemCounter}" value="0">
                </div>
            </div>
            <button type="button" onclick="removeItem(${itemCounter})" class="mt-6 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(itemDiv);
    
    // Populate products immediately
    populateProducts(itemCounter);
}

function populateProducts(itemId) {
    const itemSelect = document.getElementById(`item-select-${itemId}`);
    
    itemSelect.innerHTML = '<option value="">Select Product</option>';
    
    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = `${product.nama}${product.merk ? ' - ' + product.merk.nama : ''}`;
        option.dataset.price = product.harga_beli || product.harga_jual;
        itemSelect.appendChild(option);
    });
}

function handleItemChange(itemId) {
    const itemSelect = document.getElementById(`item-select-${itemId}`);
    const priceInput = document.getElementById(`unit-price-${itemId}`);
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.price) {
        priceInput.value = selectedOption.dataset.price;
        calculateSubtotal(itemId);
    }
}

function calculateSubtotal(itemId) {
    const qtyInput = document.querySelector(`#item-${itemId} .item-qty`);
    const priceInput = document.getElementById(`unit-price-${itemId}`);
    const subtotalDisplay = document.getElementById(`subtotal-display-${itemId}`);
    const subtotalValue = document.getElementById(`subtotal-value-${itemId}`);
    
    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const subtotal = qty * price;
    
    subtotalDisplay.value = `${currencySymbol} ${formatNumber(subtotal)}`;
    subtotalValue.value = subtotal;
    
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-value-"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    document.getElementById('grand-total').textContent = `${currencySymbol} ${formatNumber(total)}`;
    document.getElementById('total_harga').value = total;
}

function removeItem(itemId) {
    const item = document.getElementById(`item-${itemId}`);
    if (item) {
        item.remove();
        calculateGrandTotal();
    }
}

// Quick Add Product Modal Functions
function openProductModal(itemId) {
    currentItemIdForModal = itemId;
    document.getElementById('quickAddProductModal').classList.remove('hidden');
    document.getElementById('quickProductForm').reset();
    document.getElementById('quickProductErrors').classList.add('hidden');
}

function closeProductModal() {
    document.getElementById('quickAddProductModal').classList.add('hidden');
    currentItemIdForModal = null;
    document.getElementById('quickProductForm').reset();
    document.getElementById('quickProductErrors').classList.add('hidden');
}

function submitQuickProduct(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('quickProductSubmitBtn');
    const errorDiv = document.getElementById('quickProductErrors');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
    errorDiv.classList.add('hidden');
    
    const formData = {
        nama: document.getElementById('quick_nama').value,
        pos_produk_merk_id: document.getElementById('quick_merk').value,
        harga_beli: document.getElementById('quick_harga_beli').value,
        harga_jual: document.getElementById('quick_harga_jual').value,
        warna: document.getElementById('quick_warna').value,
        penyimpanan: document.getElementById('quick_penyimpanan').value,
        battery_health: document.getElementById('quick_battery_health').value,
        imei: document.getElementById('quick_imei').value,
    };
    
    fetch('{{ route("produk.quick-store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new product to products array
            products.push({
                id: data.data.id,
                nama: data.data.nama,
                merk: { nama: data.data.merk_nama },
                harga_beli: data.data.harga_beli,
                harga_jual: data.data.harga_jual
            });
            
            // Update dropdown for current item
            if (currentItemIdForModal) {
                const itemSelect = document.getElementById(`item-select-${currentItemIdForModal}`);
                const option = document.createElement('option');
                option.value = data.data.id;
                option.textContent = `${data.data.nama}${data.data.merk_nama ? ' - ' + data.data.merk_nama : ''}`;
                option.dataset.price = data.data.harga_beli || data.data.harga_jual;
                option.selected = true;
                itemSelect.appendChild(option);
                
                // Trigger change to auto-fill price
                handleItemChange(currentItemIdForModal);
            }
            
            closeProductModal();
            
            // Show success notification
            alert('Product created successfully!');
        } else {
            throw new Error(data.message || 'Failed to create product');
        }
    })
    .catch(error => {
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.message || 'An error occurred while creating the product';
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Product
        `;
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductModal();
    }
});

// Initialize - add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endpush
