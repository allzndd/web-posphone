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
                                value="{{ old('total_harga') }}"
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatCurrency(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            const currency = '{{ get_currency() }}';
            if (currency === 'IDR') {
                input.value = parseInt(value).toLocaleString('id-ID');
            } else {
                input.value = (parseInt(value) / 100).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
    }
    
    function unformatCurrency(value) {
        return value.replace(/[^0-9]/g, '');
    }
    
    const priceInput = document.querySelector('#total_harga');
    if (priceInput) {
        priceInput.addEventListener('input', function(e) {
            let cursorPos = this.selectionStart;
            let oldValue = this.value;
            const currency = '{{ get_currency() }}';
            
            let cleanValue;
            if (currency === 'USD' || currency === 'MYR') {
                cleanValue = this.value.replace(/[^0-9.]/g, '');
                
                let parts = cleanValue.split('.');
                if (parts.length > 2) {
                    cleanValue = parts[0] + '.' + parts.slice(1).join('');
                }
                if (parts.length === 2 && parts[1].length > 2) {
                    cleanValue = parts[0] + '.' + parts[1].substring(0, 2);
                }
                
                this.value = cleanValue;
            } else {
                cleanValue = this.value.replace(/[^0-9]/g, '');
                
                if (!cleanValue) {
                    this.value = '';
                    return;
                }
                
                this.value = parseInt(cleanValue).toLocaleString('id-ID');
            }
            
            if (this.value.length !== oldValue.length) {
                let diff = this.value.length - oldValue.length;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            } else {
                this.setSelectionRange(cursorPos, cursorPos);
            }
        });
        
        priceInput.addEventListener('blur', function() {
            if (this.value) {
                const currency = '{{ get_currency() }}';
                
                if (currency === 'USD' || currency === 'MYR') {
                    let num = parseFloat(this.value);
                    if (!isNaN(num)) {
                        this.value = num.toFixed(2);
                    }
                } else {
                    let cleanValue = this.value.replace(/[^0-9]/g, '');
                    if (cleanValue) {
                        this.value = parseInt(cleanValue).toLocaleString('id-ID');
                    }
                }
            }
        });
        
        priceInput.closest('form').addEventListener('submit', function() {
            if (priceInput.value) {
                const currency = '{{ get_currency() }}';
                
                if (currency === 'USD' || currency === 'MYR') {
                    let cleanValue = priceInput.value.replace(/[^0-9.]/g, '');
                    priceInput.value = parseFloat(cleanValue).toFixed(2);
                } else {
                    let cleanValue = priceInput.value.replace(/[^0-9]/g, '');
                    priceInput.value = cleanValue;
                }
            }
        });
    }
});
</script>
@endpush
