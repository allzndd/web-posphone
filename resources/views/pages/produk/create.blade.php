@extends('layouts.app')

@section('title', 'Create Product')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create New Product</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new product to your inventory</p>
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

        <!-- Product Type Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-white/10">
            <nav class="flex gap-4">
                <button type="button" onclick="switchProductType('electronic')" id="tab-electronic" class="product-type-tab active border-b-2 border-brand-500 px-4 py-3 text-sm font-bold text-brand-500 dark:text-brand-400 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Electronic / HP
                    </div>
                </button>
                <button type="button" onclick="switchProductType('accessories')" id="tab-accessories" class="product-type-tab border-b-2 border-transparent px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-brand-500 dark:hover:text-brand-400 hover:border-brand-500 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Accessories
                    </div>
                </button>
            </nav>
        </div>

        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            
            <!-- Hidden Product Type Input -->
            <input type="hidden" name="product_type" id="product_type" value="electronic">
            
            <!-- Section 1: Basic Information -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-brand-500 pl-3">Basic Information</h5>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <!-- Product Name Field -->
                    <div>
                        <label for="pos_produk_merk_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input 
                                    type="text" 
                                    id="productNameSearch"
                                    placeholder="Search Product Name..."
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                                >
                                <select 
                                    id="pos_produk_merk_id"
                                    name="pos_produk_merk_id" 
                                    class="hidden w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_produk_merk_id') !border-red-500 @enderror"
                                >
                                    <option value="">Select Product Name</option>
                                    @foreach($merks as $merk)
                                        <option value="{{ $merk->id }}" {{ old('pos_produk_merk_id') == $merk->id ? 'selected' : '' }}>
                                            {{ $merk->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="productNameDropdown" class="absolute z-50 mt-1 w-full hidden bg-white dark:bg-navy-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    <div id="productNameList" class="py-1"></div>
                                </div>
                            </div>
                            <button type="button" onclick="openProductNameModal()" class="flex items-center gap-1 rounded-xl bg-green-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-green-600 active:bg-green-700 dark:bg-green-400 dark:hover:bg-green-300 whitespace-nowrap">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                                </svg>
                                New
                            </button>
                        </div>
                        @error('pos_produk_merk_id')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Select or add a new product name</p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <button type="button" onclick="toggleSection('description')" class="mb-2 flex items-center gap-2 text-sm font-bold text-navy-700 dark:text-white hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                            <svg id="description-icon" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Description (Optional)
                        </button>
                        <div id="description-section" class="hidden">
                            <textarea 
                                id="deskripsi"
                                name="deskripsi" 
                                rows="3"
                                placeholder="Enter product description"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 resize-none @error('deskripsi') !border-red-500 @enderror"
                            >{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <!-- Section 2: Specifications (Electronic Only) -->
            <div class="mb-8" id="specifications-section">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-blue-500 pl-3">Specifications</h5>
                <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">This section only for electronic/phone products</p>
                
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
                            value="{{ old('warna') }}"
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
                                value="{{ old('penyimpanan') }}"
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
                                value="{{ old('battery_health') }}"
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
                            value="{{ old('imei') }}"
                            placeholder="Enter IMEI number"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('imei') !border-red-500 @enderror"
                        >
                        @error('imei')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Accessories -->
                    <div class="md:col-span-2">
                        <button type="button" onclick="toggleSection('accessories')" class="mb-2 flex items-center gap-2 text-sm font-bold text-navy-700 dark:text-white hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                            <svg id="accessories-icon" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Accessories (Optional)
                        </button>
                        <div id="accessories-section" class="hidden">
                            <input 
                                type="text" 
                                id="aksesoris"
                                name="aksesoris" 
                                value="{{ old('aksesoris') }}"
                                placeholder="e.g., Charger, Earphones, Case"
                                class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('aksesoris') !border-red-500 @enderror"
                            >
                            @error('aksesoris')
                                <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
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
                                value="{{ old('harga_beli') }}"
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
                                value="{{ old('harga_jual') }}"
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
                            <!-- Cost items will be added here dynamically -->
                        </div>
                        
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Optional: Add any additional costs (e.g., repair, upgrade, shipping)</p>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
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
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Add Product Name Modal -->
<div id="quickAddProductNameModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-navy-800 border-b border-gray-200 dark:border-white/10 px-6 py-4 rounded-t-2xl">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Quick Add Product Name</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new product name quickly</p>
        </div>
        
        <form id="quickProductNameForm" class="p-6">
            <div id="quickProductNameErrors" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside"></ul>
            </div>

            <!-- Product Name -->
            <div class="mb-4">
                <label for="quick_product_name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Product Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="quick_product_name"
                    name="nama" 
                    placeholder="e.g., iPhone, Samsung Galaxy, Xiaomi"
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                >
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-white/10">
                <button type="button" onclick="closeProductNameModal()" class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit" id="submitQuickProductName" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300">
                    Create Product Name
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let productNames = @json($merks);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize searchable dropdown
    initProductNameSearch();
    
    // Auto-focus on search field
    document.getElementById('productNameSearch').focus();
    
    // Currency formatting function
    function formatCurrency(input) {
        const cursorPosition = input.selectionStart;
        const oldLength = input.value.length;
        
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            const currency = '{{ get_currency() }}';
            if (currency === 'IDR') {
                input.value = parseInt(value).toLocaleString('id-ID');
            } else {
                input.value = (parseInt(value) / 100).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
            
            // Restore cursor position
            const newLength = input.value.length;
            const newPosition = cursorPosition + (newLength - oldLength);
            input.setSelectionRange(newPosition, newPosition);
        }
    }
    
    function unformatCurrency(value) {
        return value.replace(/[^0-9]/g, '');
    }
    
    // Apply to price inputs
    const priceInputs = document.querySelectorAll('#harga_beli, #harga_jual');
    priceInputs.forEach(input => {
        // Real-time formatting on every keystroke
        input.addEventListener('input', function(e) {
            let cursorPos = this.selectionStart;
            let oldValue = this.value;
            const currency = '{{ get_currency() }}';
            
            // Clean based on currency
            let cleanValue;
            if (currency === 'USD' || currency === 'MYR') {
                cleanValue = this.value.replace(/[^0-9.]/g, '');
                
                // Prevent multiple decimal points
                let parts = cleanValue.split('.');
                if (parts.length > 2) {
                    cleanValue = parts[0] + '.' + parts.slice(1).join('');
                }
                
                // Limit to 2 decimal places
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
            
            // Adjust cursor
            if (this.value.length !== oldValue.length) {
                let diff = this.value.length - oldValue.length;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            } else {
                this.setSelectionRange(cursorPos, cursorPos);
            }
        });
        
        // Also format on blur
        input.addEventListener('blur', function() {
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
        
        // Convert before submit
        input.closest('form').addEventListener('submit', function() {
            priceInputs.forEach(inp => {
                if (inp.value) {
                    const currency = '{{ get_currency() }}';
                    
                    if (currency === 'USD' || currency === 'MYR') {
                        let cleanValue = inp.value.replace(/[^0-9.]/g, '');
                        inp.value = parseFloat(cleanValue).toFixed(2);
                    } else {
                        let cleanValue = inp.value.replace(/[^0-9]/g, '');
                        inp.value = cleanValue;
                    }
                }
            });
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
    
    // Unformat cost amounts before submit
    document.querySelector('form').addEventListener('submit', function() {
        const currency = '{{ get_currency() }}';
        document.querySelectorAll('.cost-amount').forEach(input => {
            if (input.value) {
                let cents = unformatCurrency(input.value);
                // For USD and MYR, convert cents back to dollars
                if (currency === 'USD' || currency === 'MYR') {
                    input.value = (parseInt(cents) / 100).toFixed(2);
                } else {
                    input.value = cents;
                }
            }
        });
    });
});

// Toggle collapsible sections
function toggleSection(sectionName) {
    const section = document.getElementById(sectionName + '-section');
    const icon = document.getElementById(sectionName + '-icon');
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.style.transform = 'rotate(90deg)';
    } else {
        section.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Switch Product Type Tabs
function switchProductType(type) {
    // Update hidden input
    document.getElementById('product_type').value = type;
    
    // Update tab styles
    const tabs = document.querySelectorAll('.product-type-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
        tab.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    });
    
    const activeTab = document.getElementById('tab-' + type);
    activeTab.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    activeTab.classList.add('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
    
    // Show/hide specifications section
    const specificationsSection = document.getElementById('specifications-section');
    if (type === 'electronic') {
        specificationsSection.classList.remove('hidden');
    } else {
        specificationsSection.classList.add('hidden');
    }
}

// Initialize Product Name Searchable Dropdown
function initProductNameSearch() {
    const searchInput = document.getElementById('productNameSearch');
    const dropdown = document.getElementById('productNameDropdown');
    const dropdownList = document.getElementById('productNameList');
    const hiddenSelect = document.getElementById('pos_produk_merk_id');
    
    // Show dropdown on focus
    searchInput.addEventListener('focus', function() {
        renderProductNameList();
        dropdown.classList.remove('hidden');
    });
    
    // Filter on input
    searchInput.addEventListener('input', function() {
        renderProductNameList(this.value.toLowerCase());
    });
    
    // Hide dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Render list
    function renderProductNameList(filter = '') {
        const filtered = productNames.filter(item => 
            item.nama.toLowerCase().includes(filter)
        );
        
        if (filtered.length === 0) {
            dropdownList.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No product names found</div>';
            return;
        }
        
        dropdownList.innerHTML = filtered.map(item => `
            <div class="px-4 py-2 hover:bg-lightPrimary dark:hover:bg-navy-700 cursor-pointer text-sm text-navy-700 dark:text-white transition-colors" 
                 onclick="selectProductName(${item.id}, '${item.nama.replace(/'/g, "\\'")}')"
            >
                ${item.nama}
            </div>
        `).join('');
    }
}

function selectProductName(id, nama) {
    document.getElementById('productNameSearch').value = nama;
    document.getElementById('pos_produk_merk_id').value = id;
    document.getElementById('productNameDropdown').classList.add('hidden');
}

// Quick Add Product Name Modal Functions
function openProductNameModal() {
    document.getElementById('quickAddProductNameModal').classList.remove('hidden');
    document.getElementById('quick_product_name').focus();
    document.getElementById('quickProductNameForm').reset();
    document.getElementById('quickProductNameErrors').classList.add('hidden');
}

function closeProductNameModal() {
    document.getElementById('quickAddProductNameModal').classList.add('hidden');
    document.getElementById('quickProductNameForm').reset();
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductNameModal();
    }
});

// Submit Quick Product Name
document.getElementById('quickProductNameForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitQuickProductName');
    const errorDiv = document.getElementById('quickProductNameErrors');
    const errorList = errorDiv.querySelector('ul');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    errorDiv.classList.add('hidden');
    
    const formData = {
        nama: document.getElementById('quick_product_name').value
    };
    
    try {
        const response = await fetch('{{ route("pos-produk-merk.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Add to productNames array
            productNames.push(data.data);
            
            // Add to hidden select and set as selected
            const option = new Option(data.data.nama, data.data.id, true, true);
            document.getElementById('pos_produk_merk_id').add(option);
            
            // Update search input with newly created product name
            document.getElementById('productNameSearch').value = data.data.nama;
            
            closeProductNameModal();
        } else {
            // Show validation errors
            errorList.innerHTML = '';
            if (data.errors) {
                Object.values(data.errors).forEach(errorArray => {
                    errorArray.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                });
            } else {
                const li = document.createElement('li');
                li.textContent = data.message || 'An error occurred';
                errorList.appendChild(li);
            }
            errorDiv.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        errorList.innerHTML = '<li>Network error. Please try again.</li>';
        errorDiv.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Product Name';
    }
});
</script>
@endpush
