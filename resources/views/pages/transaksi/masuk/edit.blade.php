@extends('layouts.app')

@section('title', 'Edit Incoming Transaction')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Incoming Transaction</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update sales transaction details</p>
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

        <!-- Transaction Info Badge -->
        <div class="mb-6 flex flex-wrap items-center gap-3 rounded-xl bg-lightPrimary dark:bg-navy-900 p-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Invoice</p>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $transaksi->invoice }}</p>
                </div>
            </div>
            <div class="h-8 w-px bg-gray-300 dark:bg-white/10"></div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Total Amount</p>
                    <p class="text-sm font-bold text-green-500">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="h-8 w-px bg-gray-300 dark:bg-white/10"></div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Created</p>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $transaksi->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('transaksi.masuk.update', $transaksi->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_transaksi_masuk" value="1">
            
            <!-- Section 1: Transaction Information -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-green-500 pl-3">Transaction Information</h5>
                
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
                            value="{{ old('invoice', $transaksi->invoice) }}"
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
                                    <option value="{{ $toko->id }}" {{ old('pos_toko_id', $transaksi->pos_toko_id) == $toko->id ? 'selected' : '' }}>
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
                        <div class="relative">
                            <select 
                                id="pos_pelanggan_id"
                                name="pos_pelanggan_id" 
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_pelanggan_id') !border-red-500 @enderror appearance-none"
                            >
                                <option value="">Select Customer</option>
                                @foreach($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" {{ old('pos_pelanggan_id', $transaksi->pos_pelanggan_id) == $pelanggan->id ? 'selected' : '' }}>
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
                                <option value="pending" {{ old('status', $transaksi->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $transaksi->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $transaksi->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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

            <!-- Section 2: Payment Details -->
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
                                value="{{ old('total_harga', $transaksi->total_harga) }}"
                                inputmode="numeric"
                                placeholder="0{{ get_decimal_places() > 0 ? '.' . str_repeat('0', get_decimal_places()) : '' }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('total_harga') !border-red-500 @enderror"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">Currency: {{ get_currency() }}</p>
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
                                <option value="cash" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="e-wallet" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="credit" {{ old('metode_pembayaran', $transaksi->metode_pembayaran) == 'credit' ? 'selected' : '' }}>Credit</option>
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
                        >{{ old('keterangan', $transaksi->keterangan) }}</textarea>
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
                        <p class="text-sm text-green-700 dark:text-green-400">
                            This is an incoming sales transaction. The invoice number cannot be changed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between border-t border-gray-200 dark:border-white/10 pt-6">
                <!-- Delete Button -->
                <button type="button" 
                        onclick="if(confirm('Are you sure you want to delete this transaction? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }"
                        class="flex items-center gap-2 rounded-xl bg-red-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Transaction
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('transaksi.masuk.index') }}" 
                       class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-green-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-green-600 active:bg-green-700 dark:bg-green-400 dark:hover:bg-green-300 dark:active:bg-green-200">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                        </svg>
                        Update Income
                    </button>
                </div>
            </div>

        </form>

        <!-- Delete Form (Hidden) -->
        <form id="delete-form" action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currency = '{{ get_currency() }}';
    
    function formatCurrencyDisplay(value) {
        // Remove all non-numeric characters
        let cleanValue = value.replace(/[^0-9]/g, '');
        
        if (!cleanValue || cleanValue === '') return '';
        
        // Format with thousands separator only, no decimals
        if (currency === 'IDR') {
            return parseInt(cleanValue).toLocaleString('id-ID');
        } else {
            return parseInt(cleanValue).toLocaleString('en-US');
        }
    }
    
    function unformatCurrency(displayValue) {
        // Remove all formatting characters, keep only numbers
        let cleaned = displayValue.replace(/[^0-9]/g, '');
        return cleaned || '0';
    }
    
    const priceInput = document.querySelector('#total_harga');
    if (priceInput) {
        // Format the initial value on load for readability
        if (priceInput.value) {
            priceInput.value = formatCurrencyDisplay(priceInput.value);
        }
        
        priceInput.addEventListener('input', function(e) {
            // Store cursor position
            let cursorPos = this.selectionStart;
            let oldValue = this.value;
            let oldLength = oldValue.length;
            
            // Remove invalid characters, keep only numbers
            let cleanValue = this.value.replace(/[^0-9]/g, '');
            
            // Don't format if empty
            if (!cleanValue) {
                this.value = '';
                return;
            }
            
            // Apply formatting
            const formatted = formatCurrencyDisplay(cleanValue);
            
            if (formatted && formatted !== '0') {
                this.value = formatted;
                
                // Adjust cursor position based on added characters (commas)
                const newLength = formatted.length;
                const diff = newLength - oldLength;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            } else {
                this.value = cleanValue;
            }
        });
        
        priceInput.addEventListener('blur', function() {
            // Final formatting on blur
            if (this.value) {
                this.value = formatCurrencyDisplay(this.value);
            }
        });
        
        priceInput.closest('form').addEventListener('submit', function() {
            // Convert back to cents/integer for database
            if (priceInput.value) {
                priceInput.value = unformatCurrency(priceInput.value);
            }
        });
    }
});
</script>
@endpush
