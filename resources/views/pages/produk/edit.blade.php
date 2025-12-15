@extends('layouts.app')

@section('title', 'Edit Product')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Product</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update product information</p>
            </div>
            <a href="{{ route('produk.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('produk.update', $produk->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Product Info Badge -->
            <div class="mb-6 rounded-xl bg-lightPrimary dark:bg-navy-700 p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-lg font-bold text-navy-700 dark:text-white">{{ $produk->nama }}</p>
                        @if($produk->merk)
                            <span class="mt-1 inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-xs font-medium text-blue-800 dark:text-blue-300">
                                {{ $produk->merk->nama }}
                            </span>
                        @endif
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            Created {{ $produk->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-500">Purchase Price</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">Selling Price</p>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 1: Basic Information -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-brand-500 pl-3">Basic Information</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Product Name -->
                    <div class="md:col-span-2">
                        <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama"
                            name="nama" 
                            value="{{ old('nama', $produk->nama) }}"
                            placeholder="Enter product name"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama') !border-red-500 @enderror"
                        >
                        @error('nama')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand Selection -->
                    <div>
                        <label for="pos_produk_merk_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Brand <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select 
                                id="pos_produk_merk_id"
                                name="pos_produk_merk_id" 
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_produk_merk_id') !border-red-500 @enderror appearance-none"
                            >
                                <option value="">Select Brand</option>
                                @foreach($merks as $merk)
                                    <option value="{{ $merk->id }}" {{ old('pos_produk_merk_id', $produk->pos_produk_merk_id) == $merk->id ? 'selected' : '' }}>
                                        {{ $merk->nama }}
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
                        @error('pos_produk_merk_id')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="deskripsi" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Description
                        </label>
                        <textarea 
                            id="deskripsi"
                            name="deskripsi" 
                            rows="3"
                            placeholder="Enter product description"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 resize-none @error('deskripsi') !border-red-500 @enderror"
                        >{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Section 2: Specifications -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-blue-500 pl-3">Specifications</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Color -->
                    <div>
                        <label for="warna" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Color
                        </label>
                        <input 
                            type="text" 
                            id="warna"
                            name="warna" 
                            value="{{ old('warna', $produk->warna) }}"
                            placeholder="e.g., Midnight Black"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('warna') !border-red-500 @enderror"
                        >
                        @error('warna')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Storage -->
                    <div>
                        <label for="penyimpanan" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Storage
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="penyimpanan"
                                name="penyimpanan" 
                                value="{{ old('penyimpanan', $produk->penyimpanan) }}"
                                placeholder="e.g., 128"
                                inputmode="numeric"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 pr-12 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('penyimpanan') !border-red-500 @enderror"
                            >
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">GB</span>
                            </div>
                        </div>
                        @error('penyimpanan')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Battery Health -->
                    <div>
                        <label for="battery_health" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Battery Health
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="battery_health"
                                name="battery_health" 
                                value="{{ old('battery_health', $produk->battery_health) }}"
                                placeholder="e.g., 95"
                                inputmode="numeric"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 pr-12 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('battery_health') !border-red-500 @enderror"
                            >
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">%</span>
                            </div>
                        </div>
                        @error('battery_health')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- IMEI -->
                    <div>
                        <label for="imei" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            IMEI Number
                        </label>
                        <input 
                            type="text" 
                            id="imei"
                            name="imei" 
                            value="{{ old('imei', $produk->imei) }}"
                            placeholder="Enter IMEI number"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('imei') !border-red-500 @enderror"
                        >
                        @error('imei')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Accessories -->
                    <div class="md:col-span-2">
                        <label for="aksesoris" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Accessories
                        </label>
                        <input 
                            type="text" 
                            id="aksesoris"
                            name="aksesoris" 
                            value="{{ old('aksesoris', $produk->aksesoris) }}"
                            placeholder="e.g., Charger, Earphones, Case"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('aksesoris') !border-red-500 @enderror"
                        >
                        @error('aksesoris')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Section 3: Pricing -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-green-500 pl-3">Pricing Information</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <!-- Purchase Price -->
                    <div>
                        <label for="harga_beli" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Purchase Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ get_currency_symbol() }}</span>
                            </div>
                            <input 
                                type="text" 
                                id="harga_beli"
                                name="harga_beli" 
                                value="{{ old('harga_beli', $produk->harga_beli) }}"
                                placeholder="0{{ get_decimal_places() > 0 ? '.' . str_repeat('0', get_decimal_places()) : '' }}"
                                inputmode="numeric"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('harga_beli') !border-red-500 @enderror"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">Currency: {{ get_currency() }}</p>
                        @error('harga_beli')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label for="harga_jual" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Selling Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ get_currency_symbol() }}</span>
                            </div>
                            <input 
                                type="text" 
                                id="harga_jual"
                                name="harga_jual" 
                                value="{{ old('harga_jual', $produk->harga_jual) }}"
                                placeholder="0{{ get_decimal_places() > 0 ? '.' . str_repeat('0', get_decimal_places()) : '' }}"
                                inputmode="numeric"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('harga_jual') !border-red-500 @enderror"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">Currency: {{ get_currency() }}</p>
                        @error('harga_jual')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Costs -->
                    <div class="md:col-span-2">
                        <div class="mb-3 flex items-center justify-between">
                            <label class="text-sm font-bold text-navy-700 dark:text-white">
                                Additional Costs
                            </label>
                            <button type="button" id="addCostBtn" 
                                    class="flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-bold text-white transition duration-200 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Cost
                            </button>
                        </div>
                        
                        <div id="costsContainer" class="space-y-3">
                            <!-- Existing costs will be loaded here -->
                        </div>
                        
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Optional: Add any additional costs (e.g., repair, upgrade, shipping)</p>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-between border-t border-gray-200 dark:border-white/10 pt-6">
                <!-- Delete Button -->
                <button type="button" onclick="document.getElementById('deleteForm').submit()" 
                        class="flex items-center gap-2 rounded-xl bg-red-100 px-6 py-3 text-sm font-bold text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Product
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('produk.index') }}" 
                       class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                        </svg>
                        Update Product
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="deleteForm" action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('nama').focus();
    
    const currency = '{{ get_currency() }}';
    
    // Currency formatting function
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
    
    // Apply to price inputs
    const priceInputs = document.querySelectorAll('#harga_beli, #harga_jual');
    
    priceInputs.forEach(input => {
        // Format the initial value on load for readability
        if (input.value) {
            input.value = formatCurrencyDisplay(input.value);
        }
        
        input.addEventListener('input', function(e) {
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
        
        input.addEventListener('blur', function() {
            // Final formatting on blur
            if (this.value) {
                this.value = formatCurrencyDisplay(this.value);
            }
        });
    });
    
    // Convert back to cents before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        priceInputs.forEach(inp => {
            if (inp.value) {
                inp.value = unformatCurrency(inp.value);
            }
        });
    });
    
    // Additional Costs Dynamic Fields
    let costIndex = 0;
    const costsContainer = document.getElementById('costsContainer');
    const addCostBtn = document.getElementById('addCostBtn');
    
    function addCostField(name = '', amount = '') {
        const costItem = document.createElement('div');
        costItem.className = 'flex gap-3 items-start';
        costItem.innerHTML = `
            <div class="flex-1">
                <input type="text" 
                       name="cost_names[]" 
                       value="${name}"
                       placeholder="Cost name (e.g., Repair, Upgrade)"
                       class="w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-3 py-2 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
            </div>
            <div class="flex-1">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ get_currency_symbol() }}</span>
                    </div>
                    <input type="text" 
                           name="cost_amounts[]" 
                           value="${amount}"
                           placeholder="0"
                           inputmode="numeric"
                           class="cost-amount w-full rounded-lg border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-10 pr-3 py-2 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                </div>
            </div>
            <button type="button" 
                    class="remove-cost rounded-lg bg-red-100 p-2 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        
        costsContainer.appendChild(costItem);
        
        // Add formatting to the new amount input
        const amountInput = costItem.querySelector('.cost-amount');
        amountInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9.,]/g, '');
            formatCurrency(this);
        });
        
        amountInput.addEventListener('blur', function() {
            formatCurrency(this);
        });
        
        // Remove button handler
        costItem.querySelector('.remove-cost').addEventListener('click', function() {
            costItem.remove();
        });
        
        costIndex++;
    }
    
    addCostBtn.addEventListener('click', function() {
        addCostField();
    });
    
    // Load existing costs
    @if($produk->biaya_tambahan)
        const existingCosts = @json($produk->biaya_tambahan);
        for (const [name, amount] of Object.entries(existingCosts)) {
            addCostField(name, amount);
        }
    @endif
    
    // Unformat cost amounts before submit
    document.querySelector('form').addEventListener('submit', function() {
        document.querySelectorAll('.cost-amount').forEach(input => {
            input.value = unformatCurrency(input.value);
        });
    });
    
    // Confirm before delete
    document.querySelector('button[onclick*="deleteForm"]').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('deleteForm').submit();
        }
    });
});
</script>
@endpush
