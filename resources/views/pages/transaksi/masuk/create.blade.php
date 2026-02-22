@extends('layouts.app')

@section('title', 'Create Incoming Transaction')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create Incoming Transaction</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new sales transaction (income)</p>
            </div>
            <a href="{{ route('transaksi.masuk.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form id="transaksiForm" action="{{ route('transaksi.masuk.store') }}" method="POST">
            @csrf
            <input type="hidden" name="is_transaksi_masuk" value="1">
            
            <!-- Section 1: Transaction Information -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-green-500 pl-3">Transaction Information</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Invoice Number -->
                    <div>
                        <label for="invoice" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Invoice Number <span class="text-gray-400 font-normal text-xs">(Auto-generated)</span>
                        </label>
                        <input 
                            type="text" 
                            id="invoice"
                            name="invoice" 
                            value="{{ old('invoice', $invoiceNumber) }}"
                            placeholder="Will be auto-generated (INV-IN-YYYYMMDD-XXXX)"
                            readonly
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900/50 px-4 py-3 text-sm text-gray-500 dark:text-gray-400 outline-none cursor-not-allowed"
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

                    <!-- Customer Selection -->
                    <div>
                        <label for="pos_pelanggan_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Customer
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <select 
                                    id="pos_pelanggan_id"
                                    name="pos_pelanggan_id" 
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_pelanggan_id') !border-red-500 @enderror appearance-none"
                                >
                                    <option value="">Select Customer</option>
                                    @foreach($pelanggans as $pelanggan)
                                        <option value="{{ $pelanggan->id }}" {{ old('pos_pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                                            {{ $pelanggan->nama }}
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
                            <button type="button" onclick="openAddCustomerModal()" class="flex items-center gap-2 rounded-xl bg-blue-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-blue-600 active:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800" title="Add New Customer">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                                </svg>
                                New
                            </button>
                        </div>
                        @error('pos_pelanggan_id')
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
                        <span id="grand-total" class="text-green-600 dark:text-green-400">{{ get_currency_symbol() }} 0</span>
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
            <div class="mb-6 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h6 class="text-sm font-bold text-green-900 dark:text-green-300">Incoming Transaction (Sales)</h6>
                        <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                            This is a sales transaction. Invoice number is automatically generated. Select the store and customer for this transaction.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('transaksi.masuk.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="flex items-center gap-2 rounded-xl bg-green-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-green-600 active:bg-green-700 dark:bg-green-400 dark:hover:bg-green-300 dark:active:bg-green-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    <span id="submitBtnText">Create Income</span>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Add New Customer</h3>
            <button type="button" onclick="closeAddCustomerModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addCustomerForm" class="p-6 space-y-4">
            @csrf

            <!-- Customer Name -->
            <div>
                <label for="modal_nama" class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                    Customer Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="modal_nama"
                    name="nama" 
                    placeholder="Enter customer name"
                    required
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                >
                <span class="text-xs text-red-500 hidden" id="error_nama"></span>
            </div>

            <!-- Phone Number -->
            <div>
                <label for="modal_nomor_hp" class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                    Phone Number
                </label>
                <input 
                    type="text" 
                    id="modal_nomor_hp"
                    name="nomor_hp" 
                    placeholder="e.g., 08123456789"
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                >
            </div>

            <!-- Email -->
            <div>
                <label for="modal_email" class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                    Email Address
                </label>
                <input 
                    type="email" 
                    id="modal_email"
                    name="email" 
                    placeholder="e.g., customer@email.com"
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                >
            </div>

            <!-- Address -->
            <div>
                <label for="modal_alamat" class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                    Address
                </label>
                <textarea 
                    id="modal_alamat"
                    name="alamat" 
                    rows="2"
                    placeholder="Enter customer address"
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 resize-none"
                ></textarea>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" onclick="closeAddCustomerModal()" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition font-medium">
                Cancel
            </button>
            <button type="button" onclick="submitAddCustomer()" id="addCustomerSubmitBtn" class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition font-medium flex items-center gap-2">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                </svg>
                Add Customer
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const products = @json($produks);
const services = @json($services);
const currencySymbol = '{{ get_currency_symbol() }}';
const currency = '{{ get_currency() }}';
let itemCounter = 0;
let selectedTokoId = null;

// Check if there are products available
document.addEventListener('DOMContentLoaded', function() {
    // Listen to store selection change
    const tokoSelect = document.getElementById('pos_toko_id');
    tokoSelect.addEventListener('change', function() {
        selectedTokoId = this.value;
        updateAllProductDropdowns();
        
        // Show/hide warning based on available products
        checkProductAvailability();
    });
    
    // Initial check
    if (tokoSelect.value) {
        selectedTokoId = tokoSelect.value;
        checkProductAvailability();
    }
});

function checkProductAvailability() {
    const existingAlert = document.querySelector('.stock-warning-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    if (!selectedTokoId) {
        return; // No store selected yet
    }
    
    const availableProducts = products.filter(p => {
        const stok = p.stok_per_toko?.[selectedTokoId] || 0;
        return stok > 0;
    });
    
    if (availableProducts.length === 0) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'stock-warning-alert mb-6 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50 p-4';
        alertDiv.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h6 class="text-sm font-bold text-orange-900 dark:text-orange-300">No Products Available in This Store</h6>
                    <p class="mt-1 text-sm text-orange-700 dark:text-orange-400">
                        All products are out of stock in the selected store. Please restock or select a different store.
                    </p>
                </div>
            </div>
        `;
        const form = document.getElementById('transaksiForm');
        form.parentNode.insertBefore(alertDiv, form);
    }
}

function updateAllProductDropdowns() {
    // Update all existing item dropdowns
    document.querySelectorAll('.item-type').forEach(select => {
        const itemId = select.closest('[id^="item-"]').id.replace('item-', '');
        if (select.value) {
            handleProductTypeChange(itemId);
        }
    });
}

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
            <div class="flex-1 grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="hidden" name="items[${itemCounter}][type]" id="item-type-hidden-${itemCounter}" value="">
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Product Type</label>
                    <select name="items[${itemCounter}][product_type]" class="item-type w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" onchange="handleProductTypeChange(${itemCounter})">
                        <option value="">Select</option>
                        <option value="electronic">Electronic</option>
                        <option value="accessories">Accessories</option>
                        <option value="service">Service</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Item</label>
                    <div class="relative">
                        <input type="text" id="item-search-${itemCounter}" placeholder="Search or select item..." class="item-search w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" onkeyup="filterItemDropdown(${itemCounter})" style="display: none;">
                        <select name="items[${itemCounter}][item_id]" id="item-select-${itemCounter}" class="item-select w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" onchange="handleItemChange(${itemCounter})" disabled>
                            <option value="">Select Item</option>
                        </select>
                        <div id="item-dropdown-${itemCounter}" class="hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-navy-800 border border-gray-200 dark:border-white/10 rounded-lg shadow-lg z-10 max-h-48 overflow-y-auto"></div>
                    </div>
                    <span id="stock-info-${itemCounter}" class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden"></span>
                </div>
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Qty</label>
                    <input type="number" name="items[${itemCounter}][quantity]" id="qty-input-${itemCounter}" class="item-qty w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" value="1" min="1" onchange="calculateSubtotal(${itemCounter})">
                </div>
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Unit Price</label>
                    <input type="number" name="items[${itemCounter}][harga_satuan]" id="unit-price-${itemCounter}" class="item-price w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-3 py-2 text-sm" value="0" step="0.01" onchange="calculateSubtotal(${itemCounter})">
                </div>
                <div>
                    <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Subtotal</label>
                    <input type="text" id="subtotal-display-${itemCounter}" class="item-subtotal w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900 px-3 py-2 text-sm font-semibold text-green-600 dark:text-green-400" readonly value="${currencySymbol} 0">
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
}

function handleProductTypeChange(itemId) {
    const typeSelect = document.querySelector(`#item-${itemId} .item-type`);
    const itemSelect = document.getElementById(`item-select-${itemId}`);
    const itemSearch = document.getElementById(`item-search-${itemId}`);
    const itemDropdown = document.getElementById(`item-dropdown-${itemId}`);
    const typeHiddenInput = document.getElementById(`item-type-hidden-${itemId}`);
    const stockInfo = document.getElementById(`stock-info-${itemId}`);
    const productType = typeSelect.value;
    
    itemSelect.innerHTML = '<option value="">Select Item</option>';
    itemSelect.disabled = !productType;
    if (stockInfo) stockInfo.classList.add('hidden');
    if (itemSearch) itemSearch.value = '';
    if (itemDropdown) itemDropdown.innerHTML = '';
    
    // Show/hide search input and select based on product type
    const showSearch = (productType === 'electronic' || productType === 'accessories');
    if (itemSearch) {
        itemSearch.style.display = showSearch ? 'block' : 'none';
    }
    if (itemSelect) {
        itemSelect.style.display = showSearch ? 'none' : 'block';
    }
    
    if (productType === 'service') {
        // Set hidden type field to service
        if (typeHiddenInput) typeHiddenInput.value = 'service';
        
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.nama;
            option.dataset.price = service.harga;
            itemSelect.appendChild(option);
        });
    } else if (productType === 'electronic' || productType === 'accessories') {
        // Set hidden type field to product
        if (typeHiddenInput) typeHiddenInput.value = 'product';
        
        // Check if store is selected first
        if (!selectedTokoId) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Please select a store first';
            option.disabled = true;
            itemSelect.appendChild(option);
            return;
        }
        
        // Filter products by selected product type and available stock
        const availableProducts = products.filter(product => {
            const stok = product.stok_per_toko?.[selectedTokoId] || 0;
            return stok > 0 && product.product_type === productType;
        });
        
        if (availableProducts.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = `No ${productType} products with available stock in this store`;
            option.disabled = true;
            itemSelect.appendChild(option);
            return;
        }
        
        // Store products in a data attribute for filtering
        itemSearch.dataset.products = JSON.stringify(availableProducts);
        
        availableProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            
            // Get stock for selected store
            const stok = product.stok_per_toko?.[selectedTokoId] || 0;
            
            // Display product name with IMEI
            const displayText = `${product.nama}${product.merk ? ' - ' + product.merk.nama : ''}${product.imei ? ' - IMEI: ' + product.imei : ''}`;
            option.textContent = displayText;
            option.dataset.price = product.harga_jual;
            option.dataset.stock = stok;
            option.dataset.productId = product.id;
            option.dataset.displayText = displayText;
            
            itemSelect.appendChild(option);
        });
    }
}

function handleItemChange(itemId) {
    const itemSelect = document.getElementById(`item-select-${itemId}`);
    const priceInput = document.getElementById(`unit-price-${itemId}`);
    const qtyInput = document.getElementById(`qty-input-${itemId}`);
    const stockInfo = document.getElementById(`stock-info-${itemId}`);
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.price) {
        priceInput.value = selectedOption.dataset.price;
        
        // Show stock info for products
        if (selectedOption.dataset.stock !== undefined) {
            const stock = parseInt(selectedOption.dataset.stock);
            if (stockInfo) {
                stockInfo.textContent = `Available stock: ${stock} units`;
                stockInfo.classList.remove('hidden');
                stockInfo.classList.remove('text-red-500', 'text-green-500', 'text-orange-500');
                
                if (stock <= 0) {
                    stockInfo.textContent = 'OUT OF STOCK';
                    stockInfo.classList.add('text-red-500');
                } else if (stock <= 10) {
                    stockInfo.classList.add('text-orange-500');
                } else {
                    stockInfo.classList.add('text-green-500');
                }
            }
            
            // Set max quantity to available stock
            qtyInput.max = stock;
            if (parseInt(qtyInput.value) > stock) {
                qtyInput.value = stock;
            }
        } else if (stockInfo) {
            stockInfo.classList.add('hidden');
            qtyInput.removeAttribute('max');
        }
        
        calculateSubtotal(itemId);
    }
}

function filterItemDropdown(itemId) {
    const searchInput = document.getElementById(`item-search-${itemId}`);
    const itemSelect = document.getElementById(`item-select-${itemId}`);
    const itemDropdown = document.getElementById(`item-dropdown-${itemId}`);
    const searchText = searchInput.value.toLowerCase().trim();
    
    // Get all options except the first "Select Item" option
    const options = Array.from(itemSelect.querySelectorAll('option')).slice(1);
    
    // Filter matching options
    const matchingOptions = searchText === '' 
        ? options 
        : options.filter(option => {
            const optionText = option.textContent.toLowerCase();
            return optionText.includes(searchText);
        });
    
    // Clear and rebuild dropdown
    itemDropdown.innerHTML = '';
    
    if (matchingOptions.length === 0 && searchText !== '') {
        const emptyItem = document.createElement('div');
        emptyItem.className = 'px-3 py-2 text-sm text-gray-500 dark:text-gray-400';
        emptyItem.textContent = 'No results found';
        itemDropdown.appendChild(emptyItem);
        itemDropdown.classList.remove('hidden');
    } else if (matchingOptions.length > 0) {
        matchingOptions.forEach(option => {
            const dropdownItem = document.createElement('div');
            dropdownItem.className = 'px-3 py-2 text-sm text-navy-700 dark:text-white hover:bg-lightPrimary dark:hover:bg-navy-700 cursor-pointer border-b border-gray-100 dark:border-white/5 last:border-b-0';
            dropdownItem.textContent = option.textContent;
            
            dropdownItem.addEventListener('click', function() {
                // Set the hidden select value
                itemSelect.value = option.value;
                
                // Update search input with selection
                searchInput.value = option.textContent;
                
                // Close dropdown
                itemDropdown.classList.add('hidden');
                
                // Trigger change event
                const event = new Event('change', { bubbles: true });
                itemSelect.dispatchEvent(event);
            });
            
            itemDropdown.appendChild(dropdownItem);
        });
        itemDropdown.classList.remove('hidden');
    }
}

// Show dropdown on focus/click
document.addEventListener('focus', function(e) {
    if (e.target.classList.contains('item-search')) {
        const itemId = e.target.id.replace('item-search-', '');
        const dropdown = document.getElementById(`item-dropdown-${itemId}`);
        
        // Trigger filter to show all items
        filterItemDropdown(itemId);
    }
}, true);

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="item-dropdown-"]').forEach(dropdown => {
        if (!dropdown.parentElement.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});

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

// Initialize - add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
    
    // Handle form submission with AJAX
    const form = document.getElementById('transaksiForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Check if at least one item exists
        const itemsContainer = document.getElementById('items-container');
        if (itemsContainer.children.length === 0) {
            alert('Please add at least one item to the transaction');
            return;
        }
        
        // Validate product stock for each item
        let stockError = false;
        document.querySelectorAll('.item-select').forEach(select => {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.dataset.stock !== undefined) {
                const stock = parseInt(selectedOption.dataset.stock);
                const itemId = select.id.replace('item-select-', '');
                const qtyInput = document.getElementById(`qty-input-${itemId}`);
                const qty = parseInt(qtyInput.value) || 0;
                
                if (stock <= 0) {
                    alert(`Cannot add ${selectedOption.textContent.split(' (Stock:')[0]} - Product is out of stock!`);
                    stockError = true;
                    return;
                }
                
                if (qty > stock) {
                    alert(`Insufficient stock for ${selectedOption.textContent.split(' (Stock:')[0]}! Available: ${stock}, Requested: ${qty}`);
                    stockError = true;
                    return;
                }
            }
        });
        
        if (stockError) {
            return;
        }
        
        // Disable submit button
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Processing...';
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to print page
                window.location.href = data.print_url || data.redirect_url;
            } else {
                throw new Error(data.message || 'Failed to create transaction');
            }
        })
        .catch(error => {
            alert('Error: ' + (error.message || 'An error occurred while creating the transaction'));
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Create Income';
        });
    });
});

// Modal Functions
function openAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.remove('hidden');
    document.getElementById('modal_nama').focus();
}

function closeAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.add('hidden');
    document.getElementById('addCustomerForm').reset();
    // Clear error messages
    document.querySelectorAll('[id^="error_"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
}

function submitAddCustomer() {
    const form = document.getElementById('addCustomerForm');
    const nama = document.getElementById('modal_nama').value.trim();
    const nomor_hp = document.getElementById('modal_nomor_hp').value.trim();
    const email = document.getElementById('modal_email').value.trim();
    const alamat = document.getElementById('modal_alamat').value.trim();
    
    // Clear previous errors
    document.querySelectorAll('[id^="error_"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    // Validate
    let hasError = false;
    if (!nama) {
        document.getElementById('error_nama').textContent = 'Customer name is required';
        document.getElementById('error_nama').classList.remove('hidden');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Disable submit button
    const submitBtn = document.getElementById('addCustomerSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch('{{ route("pelanggan.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response. Status: ' + response.status);
        }
        
        if (!response.ok) {
            return response.json().then(data => {
                throw {status: response.status, data: data};
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success || data.id) {
            // Get the customer ID from response
            const customerId = data.id;
            const customerName = data.nama || nama;
            
            // Add new option to select
            const select = document.getElementById('pos_pelanggan_id');
            const option = document.createElement('option');
            option.value = customerId;
            option.textContent = customerName;
            option.selected = true;
            select.appendChild(option);
            
            // Close modal and reset
            closeAddCustomerModal();
        } else {
            throw new Error(data.message || 'Failed to add customer');
        }
    })
    .catch(error => {
        let errorMsg = 'An error occurred while adding customer';
        
        if (error.status === 422 && error.data?.errors) {
            // Validation errors
            const errors = error.data.errors;
            if (errors.nama) {
                document.getElementById('error_nama').textContent = errors.nama[0];
                document.getElementById('error_nama').classList.remove('hidden');
            }
            errorMsg = 'Please check the form for errors';
        } else if (error.status === 403) {
            errorMsg = error.data?.message || 'You do not have permission to add customers';
        } else if (error.message) {
            errorMsg = error.message;
        }
        
        console.error('Add Customer Error:', error);
        if (errorMsg) {
            alert('Error: ' + errorMsg);
        }
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Close modal when clicking outside
document.getElementById('addCustomerModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'addCustomerModal') {
        closeAddCustomerModal();
    }
});
</script>
@endpush
