@extends('layouts.app')

@section('title', 'Create Outgoing Transaction')

@push('style')
<style>
    .custom-dropdown {
        position: relative;
    }
    .custom-dropdown-trigger {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .custom-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 50;
        max-height: 200px;
        overflow-y: auto;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        background: white;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        margin-top: 4px;
    }
    .dark .custom-dropdown-menu {
        background: #1b2559;
        border-color: rgba(255,255,255,0.1);
    }
    .custom-dropdown-menu .dropdown-item {
        padding: 8px 16px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    .custom-dropdown-menu .dropdown-item:hover {
        background: #f3f4f6;
    }
    .dark .custom-dropdown-menu .dropdown-item:hover {
        background: rgba(255,255,255,0.1);
    }
    .custom-dropdown-menu .dropdown-item.selected {
        background: #4318FF;
        color: white;
    }
    .custom-dropdown-search {
        position: sticky;
        top: 0;
        padding: 8px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
    }
    .dark .custom-dropdown-search {
        background: #1b2559;
        border-bottom-color: rgba(255,255,255,0.1);
    }
    
    /* Disable number input spinner buttons to prevent accidental clicks */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
    .custom-dropdown-search input {
        width: 100%;
        padding: 6px 10px;
        font-size: 0.8rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        outline: none;
        background: white;
    }
    .dark .custom-dropdown-search input {
        background: #0b1437;
        border-color: rgba(255,255,255,0.1);
        color: white;
    }
    
    /* Clickable product display hover effect */
    .product-display-editable {
        transition: all 0.2s ease;
    }
    .product-display-editable:hover {
        background-color: rgba(255, 119, 0, 0.05) !important;
        border-color: #ff7700 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(255, 119, 0, 0.15);
    }
    .dark .product-display-editable:hover {
        background-color: rgba(255, 119, 0, 0.1) !important;
    }
</style>
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

        <form id="transaksiForm" action="{{ route('transaksi.keluar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="is_transaksi_masuk" value="0">
            
            <!-- Section 1: Transaction Information -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-red-500 pl-3">Transaction Information</h5>
                
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
                            placeholder="Will be auto-generated (INV-OUT-YYYYMMDD-XXXX)"
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

                    <!-- Supplier Selection -->
                    <div>
                        <label for="pos_supplier_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Supplier
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
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
                            <button type="button" onclick="openQuickAddSupplierModal()" 
                                    class="flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                                </svg>
                                New
                            </button>
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
                <button type="submit" id="submitBtn"
                        class="flex items-center gap-2 rounded-xl bg-red-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-400 dark:hover:bg-red-300 dark:active:bg-red-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    <span id="submitBtnText">Create Expense</span>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Quick Add Product Modal -->
<div id="quickAddProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[999] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-navy-800 rounded-[20px] max-w-4xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200 dark:border-white/10">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Quick Add Product</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new product to your inventory</p>
            </div>
            <button onclick="closeProductModal()" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Product Type Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-white/10">
            <nav class="flex gap-4">
                <button type="button" onclick="switchModalProductType('electronic')" id="modal-tab-electronic" class="modal-product-type-tab active border-b-2 border-brand-500 px-4 py-3 text-sm font-bold text-brand-500 dark:text-brand-400 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Electronic / HP
                    </div>
                </button>
                <button type="button" onclick="switchModalProductType('accessories')" id="modal-tab-accessories" class="modal-product-type-tab border-b-2 border-transparent px-4 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-brand-500 dark:hover:text-brand-400 hover:border-brand-500 transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Accessories
                    </div>
                </button>
            </nav>
        </div>
        
        <form id="quickProductForm" onsubmit="submitQuickProduct(event)">
            <!-- Hidden Product Type Input -->
            <input type="hidden" name="product_type" id="modal_product_type" value="electronic">

            <div class="space-y-5">
                <!-- Basic Information Section -->
                <div>
                    <h5 class="mb-4 text-sm font-bold text-navy-700 dark:text-white border-l-4 border-brand-500 pl-3">Basic Information</h5>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Brand and Product Type Selection (Horizontal) -->
                        <div id="modal-brand-type-section">
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Brand & Type <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <!-- Brand Dropdown (Custom) -->
                                <div class="flex-1 custom-dropdown" id="brandDropdownWrapper">
                                    <input type="hidden" id="quick_merk_select" name="merk" value="">
                                    <div id="brandDropdownTrigger"
                                         onclick="toggleBrandDropdown()"
                                         class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                        <span id="brandDropdownLabel" class="truncate text-gray-400">Select Brand</span>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                    <div id="brandDropdownMenu" class="custom-dropdown-menu hidden">
                                        <div class="custom-dropdown-search">
                                            <input type="text" id="brandSearchInput" placeholder="Search brand..." oninput="filterBrandDropdown()" autocomplete="off">
                                        </div>
                                        <div id="brandDropdownItems">
                                            <!-- Items populated by JS -->
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Product Type Dropdown (Custom) -->
                                <div class="flex-1 custom-dropdown" id="typeDropdownWrapper">
                                    <input type="hidden" id="quick_pos_produk_merk_id" name="pos_produk_merk_id" required value="">
                                    <div id="typeDropdownTrigger"
                                         onclick="toggleTypeDropdown()"
                                         class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                        <span id="typeDropdownLabel" class="truncate text-gray-400">Select Type</span>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                    <div id="typeDropdownMenu" class="custom-dropdown-menu hidden">
                                        <div class="custom-dropdown-search">
                                            <input type="text" id="typeSearchInput" placeholder="Search type..." oninput="filterTypeDropdown()" autocomplete="off">
                                        </div>
                                        <div id="typeDropdownItems">
                                            <div class="dropdown-item text-gray-400">Select brand first</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- <button type="button" 
                                        onclick="openModalProductNameModal()"
                                        class="rounded-xl bg-brand-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 whitespace-nowrap">
                                    + New
                                </button> -->
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Description <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <textarea id="quick_deskripsi"
                                      name="deskripsi"
                                      rows="3"
                                      placeholder="Enter product description..."
                                      class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 resize-none"></textarea>
                        </div>

                    </div>
                </div>

                <!-- Specifications Section (Electronic Only) -->
                <div id="modal-specifications-section">
                    <h5 class="mb-4 text-sm font-bold text-navy-700 dark:text-white border-l-4 border-blue-500 pl-3">Specifications</h5>
                    <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">This section only for electronic/phone products</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Color -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Color <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <div class="custom-dropdown" id="colorDropdownWrapper">
                                <input type="hidden" id="quick_warna" name="warna" value="">
                                <div id="colorDropdownTrigger"
                                     onclick="toggleColorDropdown()"
                                     class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                    <span id="colorDropdownLabel" class="truncate text-gray-400">Select Color</span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                                <div id="colorDropdownMenu" class="custom-dropdown-menu hidden">
                                    <div class="custom-dropdown-search">
                                        <input type="text" id="colorSearchInput" placeholder="Search color..." oninput="filterColorDropdown()" autocomplete="off">
                                    </div>
                                    <div id="colorDropdownItems">
                                        <!-- Items populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RAM -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                RAM <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <div class="custom-dropdown" id="ramDropdownWrapper">
                                <input type="hidden" id="quick_ram" name="ram" value="">
                                <div id="ramDropdownTrigger"
                                     onclick="toggleRamDropdown()"
                                     class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                    <span id="ramDropdownLabel" class="truncate text-gray-400">Select RAM</span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                                <div id="ramDropdownMenu" class="custom-dropdown-menu hidden">
                                    <div class="custom-dropdown-search">
                                        <input type="text" id="ramSearchInput" placeholder="Search RAM..." oninput="filterRamDropdown()" autocomplete="off">
                                    </div>
                                    <div id="ramDropdownItems">
                                        <!-- Items populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Storage -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Storage <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <div class="custom-dropdown" id="storageDropdownWrapper">
                                <input type="hidden" id="quick_penyimpanan" name="penyimpanan" value="">
                                <div id="storageDropdownTrigger"
                                     onclick="toggleStorageDropdown()"
                                     class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                    <span id="storageDropdownLabel" class="truncate text-gray-400">Select Storage</span>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                                <div id="storageDropdownMenu" class="custom-dropdown-menu hidden">
                                    <div class="custom-dropdown-search">
                                        <input type="text" id="storageSearchInput" placeholder="Search storage..." oninput="filterStorageDropdown()" autocomplete="off">
                                    </div>
                                    <div id="storageDropdownItems">
                                        <!-- Items populated by JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Battery Health -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Battery Health % <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <input type="number" id="quick_battery_health"
                                   name="battery_health"
                                   min="0" max="100"
                                   placeholder="0-100"
                                   oninput="if(this.value > 100) this.value = 100; if(this.value < 0) this.value = 0;"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <!-- IMEI -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                IMEI Number <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <input type="text" id="quick_imei"
                                   name="imei"
                                   placeholder="Enter IMEI (numbers only)"
                                   pattern="[0-9]*"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <!-- Add On / Biaya Tambahan Section (Electronic Only) -->
                <div id="modal-addon-section">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h5 class="text-sm font-bold text-navy-700 dark:text-white border-l-4 border-orange-500 pl-3">Add On / Biaya Tambahan</h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pl-4">Optional operational costs (service, repair, etc)</p>
                        </div>
                        <button type="button" onclick="addBiayaTambahan()"
                                class="flex items-center gap-1 rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-bold text-white transition duration-200 hover:bg-orange-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Item
                        </button>
                    </div>
                    
                    <div id="biayaTambahanContainer" class="space-y-3">
                        <!-- Dynamic rows will be added here -->
                    </div>
                    
                    <!-- Total Biaya Tambahan -->
                    <div id="totalBiayaTambahanWrapper" class="hidden mt-4 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl border border-orange-200 dark:border-orange-800/50">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-orange-700 dark:text-orange-300">Total Biaya Tambahan:</span>
                            <span id="totalBiayaTambahan" class="text-sm font-bold text-orange-700 dark:text-orange-300">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div>
                    <h5 class="mb-4 text-sm font-bold text-navy-700 dark:text-white border-l-4 border-green-500 pl-3">Pricing Information</h5>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Purchase Price -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Purchase Price <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="quick_harga_beli" required
                                   placeholder="0"
                                   oninput="formatCurrencyInput(this)"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Selling Price <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="quick_harga_jual" required
                                   placeholder="0"
                                   oninput="formatCurrencyInput(this)"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                <div id="quickProductErrors" class="hidden rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 p-3">
                    <p class="text-sm text-red-600 dark:text-red-400"></p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-white/10">
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

<!-- Quick Add Supplier Modal -->
<div id="quickAddSupplierModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-navy-800 border-b border-gray-200 dark:border-white/10 px-6 py-4 rounded-t-2xl">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Quick Add Supplier</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new supplier quickly</p>
        </div>
        
        <form id="quickSupplierForm" class="p-6">
            <div id="quickSupplierErrors" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside"></ul>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <!-- Supplier Name -->
                <div>
                    <label for="quick_supplier_nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Supplier Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="quick_supplier_nama"
                        name="nama" 
                        required
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        placeholder="Enter supplier name"
                    >
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="quick_supplier_nomor_hp" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Phone Number
                    </label>
                    <input 
                        type="text" 
                        id="quick_supplier_nomor_hp"
                        name="nomor_hp" 
                        maxlength="45"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        placeholder="e.g., 08123456789"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="quick_supplier_email" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="quick_supplier_email"
                        name="email" 
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        placeholder="supplier@example.com"
                    >
                </div>

                <!-- Address -->
                <div>
                    <label for="quick_supplier_alamat" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Address
                    </label>
                    <input 
                        type="text" 
                        id="quick_supplier_alamat"
                        name="alamat" 
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        placeholder="Enter address"
                    >
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="quick_supplier_keterangan" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Description
                    </label>
                    <input 
                        type="text" 
                        id="quick_supplier_keterangan"
                        name="keterangan" 
                        maxlength="255"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        placeholder="Additional notes about this supplier"
                    >
                </div>
            </div>

            <div class="flex gap-3 justify-end pt-4 mt-6 border-t border-gray-200 dark:border-white/10">
                <button type="button" onclick="closeQuickAddSupplierModal()" class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit" id="submitQuickSupplier" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Add Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Add Product Name Modal (for modal) -->
<div id="quickAddModalProductNameModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-white dark:bg-navy-800 border-b border-gray-200 dark:border-white/10 px-6 py-4 rounded-t-2xl">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Quick Add Brand & Type</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new brand and product type</p>
        </div>
        
        <form id="quickModalProductNameForm" class="p-6">
            <div id="quickModalProductNameErrors" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside"></ul>
            </div>

            <div class="mb-4">
                <label for="quick_modal_brand" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Brand <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="quick_modal_brand"
                    name="merk"
                    required
                    placeholder="e.g. Apple, Samsung, Xiaomi..."
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                >
            </div>

            <div class="mb-4">
                <label for="quick_modal_product_name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Product Type <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="quick_modal_product_name"
                    name="nama"
                    required
                    placeholder="e.g. iPhone 15, Galaxy S24, Redmi Note..."
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                >
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-white/10">
                <button type="button" onclick="closeModalProductNameModal()" class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit" id="submitQuickModalProductName" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600">
                    Create Brand & Type
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
let itemDataStore = {}; // Store complete form data for each item to allow editing

// Real-time currency formatting for input fields (dot as thousand separator)
function formatCurrencyInput(input) {
    // Get cursor position
    let cursorPos = input.selectionStart;
    let oldLength = input.value.length;
    
    // Remove all non-digit characters
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Remove leading zeros
    value = value.replace(/^0+/, '') || '';
    
    // Format with dot as thousand separator
    if (value.length > 0) {
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    } else {
        input.value = '';
    }
    
    // Adjust cursor position
    let newLength = input.value.length;
    cursorPos = cursorPos + (newLength - oldLength);
    if (cursorPos < 0) cursorPos = 0;
    input.setSelectionRange(cursorPos, cursorPos);
}

// Parse formatted currency string back to number
function parseCurrencyValue(value) {
    if (!value) return 0;
    // Remove all dots (thousand separators) and convert to number
    const cleanValue = String(value).replace(/\./g, '');
    return parseInt(cleanValue, 10) || 0;
}

function formatNumber(num) {
    if (currency === 'IDR') {
        return parseInt(num).toLocaleString('id-ID');
    } else {
        return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// Format a number with currency symbol for display
function formatCurrency(num) {
    return currencySymbol + ' ' + formatNumber(num);
}

function addItem() {
    itemCounter++;
    const container = document.getElementById('items-container');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'item-row border border-gray-200 dark:border-white/10 rounded-xl p-4 bg-white dark:bg-navy-900';
    itemDiv.id = `item-${itemCounter}`;
    
    itemDiv.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-1">
                <!-- Product Type (Hidden - Defaults to electronic) -->
                <input type="hidden" name="items[${itemCounter}][product_type]" value="electronic">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="hidden" name="items[${itemCounter}][type]" value="product">
                    <input type="hidden" name="items[${itemCounter}][item_id]" id="item-select-${itemCounter}" value="">
                    
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-navy-700 dark:text-white mb-1 block">Product</label>
                        <div class="flex gap-2">
                            <div class="flex-1 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900 px-3 py-2 text-sm">
                                <span id="productDropdownLabel-${itemCounter}" class="text-gray-500 dark:text-gray-400">-</span>
                            </div>
                            <button type="button" onclick="openProductModal(${itemCounter})" class="rounded-lg bg-brand-500 px-3 py-2 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 whitespace-nowrap">
                                + New
                            </button>
                        </div>
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
                        <input type="text" id="subtotal-display-${itemCounter}" class="item-subtotal w-full rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900 px-3 py-2 text-sm font-semibold text-red-600 dark:text-red-400" readonly value="${currencySymbol} 0">
                        <input type="hidden" name="items[${itemCounter}][subtotal]" id="subtotal-value-${itemCounter}" value="0">
                    </div>
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

// Update product type value (just for form submission) - DISABLED as product_type is now hardcoded
// function updateItemProductType(itemId, type) {
//     // No need to do anything else - radio button is already capturing the value
//     // This function is here for potential future enhancements
// }

function populateProducts(itemId) {
    const container = document.getElementById(`productDropdownItems-${itemId}`);
    
    if (!container) return; // Exit if container not found
    
    if (products.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No products found</div>';
        return;
    }
    
    container.innerHTML = products.map(product => {
        let description = product.nama;
        const specs = [];
        
        // Only add specs if they exist
        if (product.ram) specs.push(product.ram + ' GB RAM');
        if (product.penyimpanan) specs.push(product.penyimpanan + ' GB');
        if (product.battery_health) specs.push(product.battery_health + '% Battery');
        
        if (specs.length > 0) {
            description += ' - ' + specs.join(' / ');
        }
        
        return `<div class="dropdown-item" onclick="selectProduct(${itemId}, ${product.id}, '${description.replace(/'/g, "\\'")}')">${description}</div>`;
    }).join('');
}

function filterProductDropdown(itemId) {
    const searchInput = document.getElementById(`productSearchInput-${itemId}`);
    const container = document.getElementById(`productDropdownItems-${itemId}`);
    
    if (!searchInput || !container) return; // Exit if elements not found
    
    const search = searchInput.value.toLowerCase();
    
    if (products.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No products found</div>';
        return;
    }
    
    const filtered = products.filter(product => {
        return product.nama.toLowerCase().includes(search);
    });
    
    if (filtered.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No products found</div>';
        return;
    }
    
    container.innerHTML = filtered.map(product => {
        let description = product.nama;
        const specs = [];
        
        // Only add specs if they exist
        if (product.ram) specs.push(product.ram + ' GB RAM');
        if (product.penyimpanan) specs.push(product.penyimpanan + ' GB');
        if (product.battery_health) specs.push(product.battery_health + '% Battery');
        
        if (specs.length > 0) {
            description += ' - ' + specs.join(' / ');
        }
        
        return `<div class="dropdown-item" onclick="selectProduct(${itemId}, ${product.id}, '${description.replace(/'/g, "\\'")}')">${description}</div>`;
    }).join('');
}

// DISABLED - Product dropdown removed, only modal for adding products
// function toggleProductDropdown(itemId) {
//     const menu = document.getElementById(`productDropdownMenu-${itemId}`);
//     if (!menu) return;
//     document.querySelectorAll('[id^="productDropdownMenu-"]').forEach(m => {
//         if (m.id !== `productDropdownMenu-${itemId}`) {
//             m.classList.add('hidden');
//         }
//     });
//     menu.classList.toggle('hidden');
//     if (!menu.classList.contains('hidden')) {
//         const searchInput = document.getElementById(`productSearchInput-${itemId}`);
//         if (searchInput) {
//             searchInput.value = '';
//             populateProducts(itemId);
//             setTimeout(() => searchInput.focus(), 50);
//         }
//     }
// }

function closeAllDropdowns() {
    document.querySelectorAll('[id^="productDropdownMenu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
}

function selectProduct(itemId, productId, description) {
    // Set hidden product ID
    const itemSelectElem = document.getElementById(`item-select-${itemId}`);
    const labelElem = document.getElementById(`productDropdownLabel-${itemId}`);
    const menuElem = document.getElementById(`productDropdownMenu-${itemId}`);
    
    if (itemSelectElem) {
        itemSelectElem.value = productId;
    }
    if (labelElem) {
        labelElem.textContent = description;
        labelElem.classList.remove('text-gray-400');
        labelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    // IMPORTANT: Hide the dropdown menu
    if (menuElem) {
        menuElem.classList.add('hidden');
    }
    
    const product = products.find(p => p.id == productId);
    
    if (product) {
        // Set price from product
        const priceInput = document.getElementById(`unit-price-${itemId}`);
        if (priceInput) {
            const priceToSet = product.harga_beli || product.harga_jual || 0;
            priceInput.value = priceToSet;
            
            // Make sure dropdown stays hidden after setting values
            setTimeout(() => {
                const menu = document.getElementById(`productDropdownMenu-${itemId}`);
                if (menu) menu.classList.add('hidden');
            }, 0);
        }
        
        // Calculate subtotal with current quantity (don't modify quantity)
        calculateSubtotal(itemId);
    }

}

function calculateSubtotal(itemId) {
    // Find qty input
    let qtyInput = document.getElementById(`qty-input-${itemId}`);
    
    if (!qtyInput) {
        return;
    }
    
    const priceInput = document.getElementById(`unit-price-${itemId}`);
    const subtotalDisplay = document.getElementById(`subtotal-display-${itemId}`);
    const subtotalValue = document.getElementById(`subtotal-value-${itemId}`);
    
    if (!priceInput || !subtotalDisplay || !subtotalValue) {
        return;
    }
    
    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const subtotal = qty * price;
    
    // Update display
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
        // Clear stored form data for this item
        delete itemDataStore[itemId];
        
        item.remove();
        calculateGrandTotal();
    }
}

// ============= QUICK ADD PRODUCT MODAL FUNCTIONS =============

// Switch Product Type in Modal
function switchModalProductType(type) {
    currentModalProductType = type; // Store current product type
    document.getElementById('modal_product_type').value = type;
    
    const tabs = document.querySelectorAll('.modal-product-type-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
        tab.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    });
    
    const activeTab = document.getElementById('modal-tab-' + type);
    activeTab.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    activeTab.classList.add('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
    
    const specificationsSection = document.getElementById('modal-specifications-section');
    const addonSection = document.getElementById('modal-addon-section');
    const imeiField = document.getElementById('quick_imei');
    const imeiMarker = document.getElementById('modal-imei-required-marker');
    const brandTypeSection = document.getElementById('modal-brand-type-section');
    
    if (type === 'electronic') {
        // Electronic: Show brand dropdown, type dropdown, specs, and addon
        if (brandTypeSection) brandTypeSection.classList.remove('hidden');
        if (specificationsSection) specificationsSection.classList.remove('hidden');
        if (addonSection) addonSection.classList.remove('hidden');
        imeiField.required = false;
        if (imeiMarker) imeiMarker.classList.remove('hidden');
        // Initialize with filtered data for electronic
        initializeMerkDropdownForProductType('electronic');
    } else if (type === 'accessories') {
        // Accessories: Show brand dropdown and type dropdown only (no specs, no addon)
        if (brandTypeSection) brandTypeSection.classList.remove('hidden');
        if (specificationsSection) specificationsSection.classList.add('hidden');
        if (addonSection) addonSection.classList.add('hidden');
        if (imeiField) imeiField.required = false;
        if (imeiMarker) imeiMarker.classList.add('hidden');
        if (imeiField) imeiField.value = '';
        const warnaElem = document.getElementById('quick_warna');
        const ramElem = document.getElementById('quick_ram');
        const penyimpananElem = document.getElementById('quick_penyimpanan');
        const batteryElem = document.getElementById('quick_battery_health');
        if (warnaElem) warnaElem.value = '';
        if (ramElem) ramElem.value = '';
        if (penyimpananElem) penyimpananElem.value = '';
        if (batteryElem) batteryElem.value = '';
        // Clear biaya tambahan when switching to accessories
        clearBiayaTambahan();
        // Initialize with filtered data for accessories
        initializeMerkDropdownForProductType('accessories');
    }
}

// Product Merks Data
let allMerks = @json($merks ?? []);
console.log('All Merks data:', allMerks);

// ============= CUSTOM DROPDOWN FUNCTIONS =============
let currentBrandItems = [];
let currentTypeItems = [];
let currentModalProductType = 'electronic'; // Track current product type in modal

// Initialize brand dropdown with unique merks
function initializeMerkDropdown() {
    // Get current product type from modal
    const currentType = document.getElementById('modal_product_type').value || 'electronic';
    initializeMerkDropdownForProductType(currentType);
}

// Initialize brand dropdown with unique merks filtered by product type
function initializeMerkDropdownForProductType(productType) {
    // Filter allMerks by product_type
    const filteredMerks = allMerks.filter(item => item.product_type === productType);
    
    const uniqueMerks = [...new Set(filteredMerks.map(item => item.merk).filter(Boolean))].sort();
    currentBrandItems = uniqueMerks;
    
    // Reset hidden input & label
    const merkSelectElem = document.getElementById('quick_merk_select');
    const brandLabelElem = document.getElementById('brandDropdownLabel');
    const typeIdElem = document.getElementById('quick_pos_produk_merk_id');
    const typeLabelElem = document.getElementById('typeDropdownLabel');
    
    if (merkSelectElem) merkSelectElem.value = '';
    if (brandLabelElem) {
        brandLabelElem.textContent = 'Select Brand';
        brandLabelElem.classList.add('text-gray-400');
        brandLabelElem.classList.remove('text-navy-700', 'dark:text-white');
    }
    
    // Reset type dropdown
    if (typeIdElem) typeIdElem.value = '';
    if (typeLabelElem) {
        typeLabelElem.textContent = 'Select Type';
        typeLabelElem.classList.add('text-gray-400');
        typeLabelElem.classList.remove('text-navy-700', 'dark:text-white');
    }
    
    renderBrandItems(uniqueMerks);
    renderTypeItems([]);
}

function renderBrandItems(items) {
    const container = document.getElementById('brandDropdownItems');
    if (!container) return; // Exit if container not found
    
    if (items.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No brands found</div>';
        return;
    }
    container.innerHTML = items.map(brand => 
        `<div class="dropdown-item" onclick="selectBrand('${brand.replace(/'/g, "\\'")}')">` + brand + `</div>`
    ).join('');
}

function renderTypeItems(items) {
    const container = document.getElementById('typeDropdownItems');
    if (!container) return; // Exit if container not found
    
    if (items.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">Select brand first</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectType(${item.id}, '${item.nama.replace(/'/g, "\\'")}')">` + item.nama + `</div>`
    ).join('');
}

function toggleBrandDropdown() {
    const menu = document.getElementById('brandDropdownMenu');
    const typeMenu = document.getElementById('typeDropdownMenu');
    
    if (typeMenu) typeMenu.classList.add('hidden');
    if (menu) {
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            const searchInput = document.getElementById('brandSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterBrandDropdown();
                setTimeout(() => searchInput.focus(), 50);
            }
        }
    }
}

function toggleTypeDropdown() {
    const menu = document.getElementById('typeDropdownMenu');
    const brandMenu = document.getElementById('brandDropdownMenu');
    
    if (brandMenu) brandMenu.classList.add('hidden');
    if (menu) {
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            const searchInput = document.getElementById('typeSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterTypeDropdown();
                setTimeout(() => searchInput.focus(), 50);
            }
        }
    }
}

function filterBrandDropdown() {
    const search = document.getElementById('brandSearchInput').value.toLowerCase();
    const filtered = currentBrandItems.filter(b => b.toLowerCase().includes(search));
    renderBrandItems(filtered);
}

function filterTypeDropdown() {
    const search = document.getElementById('typeSearchInput').value.toLowerCase();
    const filtered = currentTypeItems.filter(t => t.nama.toLowerCase().includes(search));
    renderTypeItems(filtered);
}

function selectBrand(brand) {
    const merkSelectElem = document.getElementById('quick_merk_select');
    const brandLabelElem = document.getElementById('brandDropdownLabel');
    const brandMenuElem = document.getElementById('brandDropdownMenu');
    
    if (merkSelectElem) merkSelectElem.value = brand;
    if (brandLabelElem) {
        brandLabelElem.textContent = brand;
        brandLabelElem.classList.remove('text-gray-400');
        brandLabelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    if (brandMenuElem) brandMenuElem.classList.add('hidden');
    
    // Trigger type dropdown update
    handleMerkChange();
}

function selectType(id, nama) {
    const typeIdElem = document.getElementById('quick_pos_produk_merk_id');
    const typeLabelElem = document.getElementById('typeDropdownLabel');
    const typeMenuElem = document.getElementById('typeDropdownMenu');
    
    if (typeIdElem) typeIdElem.value = id;
    if (typeLabelElem) {
        typeLabelElem.textContent = nama;
        typeLabelElem.classList.remove('text-gray-400');
        typeLabelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    if (typeMenuElem) typeMenuElem.classList.add('hidden');
}

// Handle Brand/Merk Selection Change
function handleMerkChange() {
    const merkSelectElem = document.getElementById('quick_merk_select');
    const selectedMerk = merkSelectElem ? merkSelectElem.value : '';
    
    // Reset type
    const typeIdElem = document.getElementById('quick_pos_produk_merk_id');
    const typeLabelElem = document.getElementById('typeDropdownLabel');
    
    if (typeIdElem) typeIdElem.value = '';
    if (typeLabelElem) {
        typeLabelElem.textContent = 'Select Type';
        typeLabelElem.classList.add('text-gray-400');
        typeLabelElem.classList.remove('text-navy-700', 'dark:text-white');
    }
    
    if (selectedMerk === '') {
        currentTypeItems = [];
        renderTypeItems([]);
        return;
    }
    
    // Filter merks by selected brand AND current product type
    const filteredMerks = allMerks.filter(item => 
        item.merk === selectedMerk && 
        item.product_type === currentModalProductType
    );
    currentTypeItems = filteredMerks;
    renderTypeItems(filteredMerks);
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    const brandWrapper = document.getElementById('brandDropdownWrapper');
    const typeWrapper = document.getElementById('typeDropdownWrapper');
    const colorWrapper = document.getElementById('colorDropdownWrapper');
    const ramWrapper = document.getElementById('ramDropdownWrapper');
    const storageWrapper = document.getElementById('storageDropdownWrapper');
    
    if (brandWrapper && !brandWrapper.contains(e.target)) {
        document.getElementById('brandDropdownMenu').classList.add('hidden');
    }
    if (typeWrapper && !typeWrapper.contains(e.target)) {
        document.getElementById('typeDropdownMenu').classList.add('hidden');
    }
    if (colorWrapper && !colorWrapper.contains(e.target)) {
        document.getElementById('colorDropdownMenu').classList.add('hidden');
    }
    if (ramWrapper && !ramWrapper.contains(e.target)) {
        document.getElementById('ramDropdownMenu').classList.add('hidden');
    }
    if (storageWrapper && !storageWrapper.contains(e.target)) {
        document.getElementById('storageDropdownMenu').classList.add('hidden');
    }
    
    // Close product dropdowns in transaction items
    document.querySelectorAll('[id^="productDropdownWrapper-"]').forEach(wrapper => {
        if (!wrapper.contains(e.target)) {
            const itemId = wrapper.id.split('-')[1];
            const menu = document.getElementById(`productDropdownMenu-${itemId}`);
            if (menu) {
                menu.classList.add('hidden');
            }
        }
    });
});

// Color Dropdown Functions
let colorItems = @json($warnas ?? []);
let filteredColorItems = [];

function initializeColorDropdown() {
    filteredColorItems = colorItems;
    renderColorItems(filteredColorItems);
}

function renderColorItems(items) {
    const container = document.getElementById('colorDropdownItems');
    if (!container) return; // Exit if container not found
    
    if (items.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No colors found</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectColor(${item.id}, '${item.warna.replace(/'/g, "\\'")}')">` + item.warna + `</div>`
    ).join('');
}

function toggleColorDropdown() {
    const menu = document.getElementById('colorDropdownMenu');
    const brandMenu = document.getElementById('brandDropdownMenu');
    const typeMenu = document.getElementById('typeDropdownMenu');
    const ramMenu = document.getElementById('ramDropdownMenu');
    const storageMenu = document.getElementById('storageDropdownMenu');
    
    if (brandMenu) brandMenu.classList.add('hidden');
    if (typeMenu) typeMenu.classList.add('hidden');
    if (ramMenu) ramMenu.classList.add('hidden');
    if (storageMenu) storageMenu.classList.add('hidden');
    
    if (menu) {
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            const searchInput = document.getElementById('colorSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterColorDropdown();
                setTimeout(() => searchInput.focus(), 50);
            }
        }
    }
}

function filterColorDropdown() {
    const search = document.getElementById('colorSearchInput').value.toLowerCase();
    const filtered = colorItems.filter(item => item.warna.toLowerCase().includes(search));
    renderColorItems(filtered);
}

function selectColor(id, warna) {
    console.log('selectColor called with id:', id, 'warna:', warna);
    const warnaElem = document.getElementById('quick_warna');
    const labelElem = document.getElementById('colorDropdownLabel');
    const menuElem = document.getElementById('colorDropdownMenu');
    
    if (warnaElem) {
        warnaElem.value = id;
        console.log('quick_warna value after setting:', warnaElem.value);
    }
    if (labelElem) {
        labelElem.textContent = warna;
        labelElem.classList.remove('text-gray-400');
        labelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    if (menuElem) menuElem.classList.add('hidden');
}

// RAM Dropdown Functions
let ramItems = @json($rams ?? []);
let filteredRamItems = [];

function initializeRamDropdown() {
    filteredRamItems = ramItems;
    renderRamItems(filteredRamItems);
}

function renderRamItems(items) {
    const container = document.getElementById('ramDropdownItems');
    if (!container) return; // Exit if container not found
    
    if (items.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No RAM options found</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectRam(${item.id}, '${item.kapasitas} GB')">` + item.kapasitas + ` GB</div>`
    ).join('');
}

function toggleRamDropdown() {
    const menu = document.getElementById('ramDropdownMenu');
    const brandMenu = document.getElementById('brandDropdownMenu');
    const typeMenu = document.getElementById('typeDropdownMenu');
    const colorMenu = document.getElementById('colorDropdownMenu');
    const storageMenu = document.getElementById('storageDropdownMenu');
    
    if (brandMenu) brandMenu.classList.add('hidden');
    if (typeMenu) typeMenu.classList.add('hidden');
    if (colorMenu) colorMenu.classList.add('hidden');
    if (storageMenu) storageMenu.classList.add('hidden');
    
    if (menu) {
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            const searchInput = document.getElementById('ramSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterRamDropdown();
                setTimeout(() => searchInput.focus(), 50);
            }
        }
    }
}

function filterRamDropdown() {
    const search = document.getElementById('ramSearchInput').value.toLowerCase();
    const filtered = ramItems.filter(item => item.kapasitas.toString().includes(search));
    renderRamItems(filtered);
}

function selectRam(id, kapasitas) {
    console.log('selectRam called with id:', id, 'kapasitas:', kapasitas);
    const ramElem = document.getElementById('quick_ram');
    const labelElem = document.getElementById('ramDropdownLabel');
    const menuElem = document.getElementById('ramDropdownMenu');
    
    if (ramElem) {
        ramElem.value = id;
        console.log('quick_ram value after setting:', ramElem.value);
    }
    if (labelElem) {
        labelElem.textContent = kapasitas;
        labelElem.classList.remove('text-gray-400');
        labelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    if (menuElem) menuElem.classList.add('hidden');
}

// Storage Dropdown Functions
let storageItems = @json($penyimpanans ?? []);
let filteredStorageItems = [];

function initializeStorageDropdown() {
    filteredStorageItems = storageItems;
    renderStorageItems(filteredStorageItems);
}

function renderStorageItems(items) {
    const container = document.getElementById('storageDropdownItems');
    if (!container) return; // Exit if container not found
    
    if (items.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No storage options found</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectStorage(${item.id}, '${item.kapasitas} GB')">` + item.kapasitas + ` GB</div>`
    ).join('');
}

function toggleStorageDropdown() {
    const menu = document.getElementById('storageDropdownMenu');
    const brandMenu = document.getElementById('brandDropdownMenu');
    const typeMenu = document.getElementById('typeDropdownMenu');
    const colorMenu = document.getElementById('colorDropdownMenu');
    const ramMenu = document.getElementById('ramDropdownMenu');
    
    if (brandMenu) brandMenu.classList.add('hidden');
    if (typeMenu) typeMenu.classList.add('hidden');
    if (colorMenu) colorMenu.classList.add('hidden');
    if (ramMenu) ramMenu.classList.add('hidden');
    
    if (menu) {
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            const searchInput = document.getElementById('storageSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterStorageDropdown();
                setTimeout(() => searchInput.focus(), 50);
            }
        }
    }
}

function filterStorageDropdown() {
    const search = document.getElementById('storageSearchInput').value.toLowerCase();
    const filtered = storageItems.filter(item => item.kapasitas.toString().includes(search));
    renderStorageItems(filtered);
}

function selectStorage(id, kapasitas) {
    console.log('selectStorage called with id:', id, 'kapasitas:', kapasitas);
    const penyimpananElem = document.getElementById('quick_penyimpanan');
    const labelElem = document.getElementById('storageDropdownLabel');
    const menuElem = document.getElementById('storageDropdownMenu');
    
    if (penyimpananElem) {
        penyimpananElem.value = id;
        console.log('quick_penyimpanan value after setting:', penyimpananElem.value);
    }
    if (labelElem) {
        labelElem.textContent = kapasitas;
        labelElem.classList.remove('text-gray-400');
        labelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    if (menuElem) menuElem.classList.add('hidden');
}

// Quick Add Product Modal Functions
function openProductModal(itemId) {
    currentItemIdForModal = itemId;
    const modal = document.getElementById('quickAddProductModal');
    const form = document.getElementById('quickProductForm');
    const errorDiv = document.getElementById('quickProductErrors');
    const productTypeInput = document.getElementById('modal_product_type');
    const imeiInput = document.getElementById('quick_imei');
    const specsSection = document.getElementById('modal-specifications-section');
    const brandSection = document.getElementById('modal-brand-type-section');
    const serviceSection = document.getElementById('modal-service-fields');
    
    if (modal) modal.classList.remove('hidden');
    if (errorDiv) errorDiv.classList.add('hidden');
    
    // Check if this item has stored data (for editing)
    const storedData = itemDataStore[itemId];
    
    if (storedData) {
        // Editing existing item - populate with stored data
        console.log('Editing item with stored data:', storedData);
        
        // Set product type first
        const productType = storedData.product_type || 'electronic';
        switchModalProductType(productType);
        
        // Populate all form fields
        setTimeout(() => {
            // Set merk/brand and type
            if (storedData.merk_select) {
                document.getElementById('quick_merk_select').value = storedData.merk_select;
                const brandLabel = document.getElementById('brandDropdownLabel');
                if (brandLabel) {
                    brandLabel.textContent = storedData.merk_select;
                    brandLabel.classList.remove('text-gray-400');
                    brandLabel.classList.add('text-navy-700', 'dark:text-white');
                }
                handleMerkChange(); // Populate type dropdown
            }
            
            if (storedData.pos_produk_merk_id) {
                document.getElementById('quick_pos_produk_merk_id').value = storedData.pos_produk_merk_id;
                // Find and set type label
                const selectedMerk = allMerks.find(m => m.id == storedData.pos_produk_merk_id);
                if (selectedMerk) {
                    const typeLabel = document.getElementById('typeDropdownLabel');
                    if (typeLabel) {
                        typeLabel.textContent = selectedMerk.nama;
                        typeLabel.classList.remove('text-gray-400');
                        typeLabel.classList.add('text-navy-700', 'dark:text-white');
                    }
                }
            }
            
            // Set color
            if (storedData.warna) {
                document.getElementById('quick_warna').value = storedData.warna;
                const colorLabel = document.getElementById('colorDropdownLabel');
                if (colorLabel) {
                    colorLabel.textContent = storedData.warna_label || storedData.warna;
                    colorLabel.classList.remove('text-gray-400');
                    colorLabel.classList.add('text-navy-700', 'dark:text-white');
                }
            }
            
            // Set RAM
            if (storedData.ram) {
                document.getElementById('quick_ram').value = storedData.ram;
                const ramLabel = document.getElementById('ramDropdownLabel');
                if (ramLabel) {
                    ramLabel.textContent = storedData.ram_label || storedData.ram;
                    ramLabel.classList.remove('text-gray-400');
                    ramLabel.classList.add('text-navy-700', 'dark:text-white');
                }
            }
            
            // Set Storage
            if (storedData.penyimpanan) {
                document.getElementById('quick_penyimpanan').value = storedData.penyimpanan;
                const storageLabel = document.getElementById('storageDropdownLabel');
                if (storageLabel) {
                    storageLabel.textContent = storedData.penyimpanan_label || storedData.penyimpanan;
                    storageLabel.classList.remove('text-gray-400');
                    storageLabel.classList.add('text-navy-700', 'dark:text-white');
                }
            }
            
            // Set other fields
            if (storedData.battery_health) document.getElementById('quick_battery_health').value = storedData.battery_health;
            if (storedData.imei) document.getElementById('quick_imei').value = storedData.imei;
            if (storedData.harga_beli) document.getElementById('quick_harga_beli').value = formatNumber(storedData.harga_beli);
            if (storedData.harga_jual) document.getElementById('quick_harga_jual').value = formatNumber(storedData.harga_jual);
            
            // Restore biaya tambahan
            clearBiayaTambahan();
            if (storedData.biaya_tambahan && storedData.biaya_tambahan.length > 0) {
                storedData.biaya_tambahan.forEach(biaya => {
                    addBiayaTambahan();
                    const rows = document.querySelectorAll('[id^="biayaTambahanRow_"]');
                    const lastRow = rows[rows.length - 1];
                    if (lastRow) {
                        const namaInput = lastRow.querySelector('.biaya-tambahan-nama');
                        const hargaInput = lastRow.querySelector('.biaya-tambahan-harga');
                        if (namaInput) namaInput.value = biaya.nama;
                        if (hargaInput) hargaInput.value = formatNumber(biaya.harga);
                    }
                });
                calculateTotalBiayaTambahan();
            }
        }, 100); // Small delay to ensure dropdowns are initialized
        
    } else {
        // New item - reset form fields
        if (form) form.reset();
        
        // Set defaults and tabs (after reset to prevent reset from overriding)
        if (productTypeInput) productTypeInput.value = 'electronic';
        if (imeiInput) imeiInput.required = false;
        if (specsSection) specsSection.classList.remove('hidden');
        if (brandSection) brandSection.classList.remove('hidden');
        if (serviceSection) serviceSection.classList.add('hidden');
        
        // Reset tab styles to electronic (active state)
        const tabs = document.querySelectorAll('.modal-product-type-tab');
        tabs.forEach(tab => {
            tab.classList.remove('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
            tab.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
        });
        
        const electronicTab = document.getElementById('modal-tab-electronic');
        electronicTab.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
        electronicTab.classList.add('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
        
        // Initialize dropdowns
        initializeMerkDropdown();
        initializeColorDropdown();
        initializeRamDropdown();
        initializeStorageDropdown();
        
        // Clear biaya tambahan
        clearBiayaTambahan();
    }
}

function closeProductModal() {
    document.getElementById('quickAddProductModal').classList.add('hidden');
    currentItemIdForModal = null;
    document.getElementById('quickProductForm').reset();
    document.getElementById('quickProductErrors').classList.add('hidden');
    
    // Reset biaya tambahan
    clearBiayaTambahan();
}

// Make item editable by adding click handler (no icon, just clickable area)
function makeItemEditable(itemId) {
    const labelElem = document.getElementById(`productDropdownLabel-${itemId}`);
    
    if (!labelElem) return;
    
    // Find the parent flex container (the display div)
    const displayDiv = labelElem.closest('.flex-1.rounded-lg');
    
    if (displayDiv) {
        // Add CSS class for hover effect
        displayDiv.classList.add('product-display-editable');
        
        // Add styling for clickable state
        displayDiv.style.cursor = 'pointer';
        displayDiv.title = 'Click to edit product details';
        
        // Remove existing click handler if any
        displayDiv.onclick = null;
        
        // Add click handler
        displayDiv.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            openProductModal(itemId);
        };
    }
}

// ============= BIAYA TAMBAHAN / ADD ON FUNCTIONS =============

let biayaTambahanCounter = 0;

function addBiayaTambahan() {
    biayaTambahanCounter++;
    const container = document.getElementById('biayaTambahanContainer');
    const totalWrapper = document.getElementById('totalBiayaTambahanWrapper');
    
    const row = document.createElement('div');
    row.id = `biayaTambahanRow_${biayaTambahanCounter}`;
    row.className = 'flex gap-3 items-start animate-fade-in';
    row.innerHTML = `
        <div class="flex-1">
            <input type="text" 
                   name="biaya_tambahan[${biayaTambahanCounter}][nama]"
                   placeholder="Description (e.g. Service LCD, Repair...)"
                   class="biaya-tambahan-nama w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-orange-500">
        </div>
        <div class="w-36">
            <input type="text" 
                   name="biaya_tambahan[${biayaTambahanCounter}][harga]"
                   placeholder="Amount"
                   oninput="formatCurrencyInput(this); calculateTotalBiayaTambahan();"
                   class="biaya-tambahan-harga w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-orange-500">
        </div>
        <button type="button" onclick="removeBiayaTambahan(${biayaTambahanCounter})"
                class="flex-shrink-0 p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-500 hover:bg-red-200 dark:hover:bg-red-900/50 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    
    container.appendChild(row);
    totalWrapper.classList.remove('hidden');
    calculateTotalBiayaTambahan();
}

function removeBiayaTambahan(index) {
    const row = document.getElementById(`biayaTambahanRow_${index}`);
    if (row) {
        row.remove();
        calculateTotalBiayaTambahan();
        
        // Hide total wrapper if no more items
        const container = document.getElementById('biayaTambahanContainer');
        const totalWrapper = document.getElementById('totalBiayaTambahanWrapper');
        if (container.children.length === 0) {
            totalWrapper.classList.add('hidden');
        }
    }
}

function clearBiayaTambahan() {
    const container = document.getElementById('biayaTambahanContainer');
    const totalWrapper = document.getElementById('totalBiayaTambahanWrapper');
    
    if (container) container.innerHTML = '';
    if (totalWrapper) totalWrapper.classList.add('hidden');
    biayaTambahanCounter = 0;
}

function calculateTotalBiayaTambahan() {
    const hargaInputs = document.querySelectorAll('.biaya-tambahan-harga');
    let total = 0;
    
    hargaInputs.forEach(input => {
        total += parseCurrencyValue(input.value);
    });
    
    const totalElement = document.getElementById('totalBiayaTambahan');
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }
    
    return total;
}

function getBiayaTambahanData() {
    const container = document.getElementById('biayaTambahanContainer');
    const rows = container.querySelectorAll('[id^="biayaTambahanRow_"]');
    const data = [];
    
    rows.forEach(row => {
        const nama = row.querySelector('.biaya-tambahan-nama').value.trim();
        const harga = parseCurrencyValue(row.querySelector('.biaya-tambahan-harga').value);
        
        if (nama && harga > 0) {
            data.push({ nama: nama, harga: harga });
        }
    });
    
    return data;
}

function submitQuickProduct(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('quickProductSubmitBtn');
    const errorDiv = document.getElementById('quickProductErrors');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
    errorDiv.classList.add('hidden');
    
    // Get product info from dropdowns
    const merkIdElem = document.getElementById('quick_pos_produk_merk_id');
    const merkId = merkIdElem ? merkIdElem.value : '';
    const productTypeElem = document.getElementById('modal_product_type');
    const productType = productTypeElem ? productTypeElem.value : 'electronic';
    
    console.log('submitQuickProduct - merkId:', merkId, 'productType:', productType);
    
    if (!merkId) {
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = 'Please select a Brand and Type first';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Create Product';
        return;
    }
    
    // Get biaya tambahan data (only for electronic)
    const biayaTambahanData = productType === 'electronic' ? getBiayaTambahanData() : [];
    const totalBiayaTambahan = biayaTambahanData.reduce((sum, item) => sum + item.harga, 0);
    
    // Build basic form data
    const basePurchasePrice = parseCurrencyValue(document.getElementById('quick_harga_beli').value);
    const typeLabel = document.getElementById('typeDropdownLabel').textContent;
    const nama = (typeLabel && typeLabel !== 'Select Type') ? typeLabel : 'Produk Baru';
    
    // Check if we're editing an existing item (has stored data with product_id)
    const existingItemData = itemDataStore[currentItemIdForModal];
    const isEditing = existingItemData && existingItemData.product_id;
    
    console.log('isEditing:', isEditing, 'existingItemData:', existingItemData);
    
    // STORE DATA LOCALLY - Don't create product in database yet!
    // Product will be created when transaction is submitted
    console.log('Storing product data locally (not creating in DB yet)');
    
    // Get labels for display
    const colorLabel = document.getElementById('colorDropdownLabel').textContent;
    const ramLabel = document.getElementById('ramDropdownLabel').textContent;
    const storageLabel = document.getElementById('storageDropdownLabel').textContent;
    const brandLabel = document.getElementById('brandDropdownLabel').textContent;
    const hargaJual = parseCurrencyValue(document.getElementById('quick_harga_jual').value);
    
    // Store complete form data
    itemDataStore[currentItemIdForModal] = {
        product_type: productType,
        pos_produk_merk_id: merkId,
        merk_select: document.getElementById('quick_merk_select').value,
        nama: nama,
        warna: document.getElementById('quick_warna').value,
        warna_label: (colorLabel && colorLabel !== 'Select Color') ? colorLabel : '',
        ram: document.getElementById('quick_ram').value,
        ram_label: (ramLabel && ramLabel !== 'Select RAM') ? ramLabel : '',
        penyimpanan: document.getElementById('quick_penyimpanan').value,
        penyimpanan_label: (storageLabel && storageLabel !== 'Select Storage') ? storageLabel : '',
        battery_health: document.getElementById('quick_battery_health').value,
        imei: document.getElementById('quick_imei').value,
        harga_beli: basePurchasePrice,
        harga_jual: hargaJual,
        biaya_tambahan: biayaTambahanData,
        harga_beli_total: basePurchasePrice + totalBiayaTambahan
    };
    
    console.log('Stored item data for item', currentItemIdForModal, ':', itemDataStore[currentItemIdForModal]);
    
    // Update the product label display
    const labelElem = document.getElementById(`productDropdownLabel-${currentItemIdForModal}`);
    if (labelElem) {
        let description = nama;
        const specs = [];
        
        if (itemDataStore[currentItemIdForModal].ram) specs.push(itemDataStore[currentItemIdForModal].ram + ' GB RAM');
        if (itemDataStore[currentItemIdForModal].penyimpanan) specs.push(itemDataStore[currentItemIdForModal].penyimpanan + ' GB');
        if (itemDataStore[currentItemIdForModal].battery_health) specs.push(itemDataStore[currentItemIdForModal].battery_health + '% Battery');
        
        if (specs.length > 0) {
            description += ' - ' + specs.join(' / ');
        }
        
        labelElem.textContent = description;
        labelElem.classList.remove('text-gray-500', 'dark:text-gray-400');
        labelElem.classList.add('text-navy-700', 'dark:text-white');
    }
    
    // Set the product ID (use negative number as temporary ID to indicate it's not created yet)
    const itemSelectElem = document.getElementById(`item-select-${currentItemIdForModal}`);
    if (itemSelectElem) {
        itemSelectElem.value = `temp_${currentItemIdForModal}`; // Temporary ID
    }
    
    // Update prices in the item row
    const unitPriceInput = document.getElementById(`unit-price-${currentItemIdForModal}`);
    if (unitPriceInput) {
        unitPriceInput.value = hargaJual;
        calculateSubtotal(currentItemIdForModal);
    }
    
    // Make the item editable (clickable)
    makeItemEditable(currentItemIdForModal);
    
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Create Product';
    
    closeProductModal();
}

// ============= QUICK ADD SUPPLIER MODAL FUNCTIONS =============

function openQuickAddSupplierModal() {
    document.getElementById('quickAddSupplierModal').classList.remove('hidden');
    document.getElementById('quick_supplier_nama').focus();
    document.getElementById('quickSupplierForm').reset();
    document.getElementById('quickSupplierErrors').classList.add('hidden');
}

function closeQuickAddSupplierModal() {
    document.getElementById('quickAddSupplierModal').classList.add('hidden');
    document.getElementById('quickSupplierForm').reset();
}

// Submit Quick Add Supplier
document.getElementById('quickSupplierForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitQuickSupplier');
    const errorDiv = document.getElementById('quickSupplierErrors');
    const errorList = errorDiv.querySelector('ul');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    errorDiv.classList.add('hidden');
    
    const formData = {
        nama: document.getElementById('quick_supplier_nama').value,
        nomor_hp: document.getElementById('quick_supplier_nomor_hp').value,
        email: document.getElementById('quick_supplier_email').value,
        alamat: document.getElementById('quick_supplier_alamat').value,
        keterangan: document.getElementById('quick_supplier_keterangan').value
    };
    
    try {
        const response = await fetch('{{ route("supplier.store") }}', {
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
            // Add to supplier dropdown
            const supplierSelect = document.getElementById('pos_supplier_id');
            const option = new Option(data.data.nama, data.data.id, true, true);
            supplierSelect.add(option);
            supplierSelect.value = data.data.id;
            
            closeQuickAddSupplierModal();
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
        submitBtn.textContent = 'Add Supplier';
    }
});

// ============= PRODUCT NAME MODAL FUNCTIONS =============

function openModalProductNameModal() {
    document.getElementById('quickAddModalProductNameModal').classList.remove('hidden');
    document.getElementById('quick_modal_product_name').focus();
    document.getElementById('quickModalProductNameForm').reset();
    document.getElementById('quickModalProductNameErrors').classList.add('hidden');
}

function closeModalProductNameModal() {
    document.getElementById('quickAddModalProductNameModal').classList.add('hidden');
    document.getElementById('quickModalProductNameForm').reset();
}

document.getElementById('quickModalProductNameForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitQuickModalProductName');
    const errorDiv = document.getElementById('quickModalProductNameErrors');
    const errorList = errorDiv.querySelector('ul');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    errorDiv.classList.add('hidden');
    
    const formData = {
        merk: document.getElementById('quick_modal_brand').value,
        nama: document.getElementById('quick_modal_product_name').value
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
            // Add to allMerks array
            allMerks.push(data.data);
            
            // Refresh brand items
            const uniqueMerks = [...new Set(allMerks.map(item => item.merk).filter(Boolean))].sort();
            currentBrandItems = uniqueMerks;
            
            // Select the newly added brand
            selectBrand(data.data.merk);
            
            // Select the newly added type
            selectType(data.data.id, data.data.nama);
            
            closeModalProductNameModal();
        } else {
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
        submitBtn.textContent = 'Create Brand & Type';
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductModal();
        closeModalProductNameModal();
        closeQuickAddSupplierModal();
    }
});

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
        
        // Validate all quantities are valid before submission
        let hasInvalidQty = false;
        document.querySelectorAll('[id^="qty-input-"]').forEach(qtyInput => {
            const qty = parseInt(qtyInput.value, 10);
            if (isNaN(qty) || qty < 1) {
                hasInvalidQty = true;
                alert('Please ensure all items have a valid quantity (minimum 1)');
                return false;
            }
        });
        
        if (hasInvalidQty) {
            return;
        }
        
        // DEBUG: Log all quantity values before FormData
        console.log('=== QUANTITY DEBUG BEFORE SUBMIT ===');
        document.querySelectorAll('[id^="qty-input-"]').forEach(qtyInput => {
            const itemId = qtyInput.id.replace('qty-input-', '');
            console.log(`Item ${itemId}: qty field value = "${qtyInput.value}"`);
        });
        
        // Disable submit button
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Processing...';
        
        // FIRST: Create products for items with temporary IDs (from modal)
        const itemsToCreate = [];
        const itemIdsMap = {}; // Map from temporary ID to real product ID
        
        // Check all items for temporary IDs
        document.querySelectorAll('[id^="item-select-"]').forEach(itemSelect => {
            const itemId = itemSelect.id.replace('item-select-', '');
            const itemValue = itemSelect.value;
            
            // Check if this is a temporary ID (starts with "temp_")
            if (itemValue && itemValue.startsWith('temp_')) {
                const storedData = itemDataStore[itemId];
                if (storedData) {
                    itemsToCreate.push({
                        tempId: itemValue,
                        itemId: itemId,
                        data: storedData
                    });
                }
            }
        });
        
        console.log('Items to create before transaction:', itemsToCreate);
        
        // Function to create products sequentially
        async function createProducts() {
            for (const item of itemsToCreate) {
                try {
                    const formData = {
                        product_type: item.data.product_type,
                        harga_beli: item.data.harga_beli,
                        harga_beli_total: item.data.harga_beli_total,
                        harga_jual: item.data.harga_jual,
                        biaya_tambahan: item.data.biaya_tambahan || [],
                        nama: item.data.nama,
                        pos_produk_merk_id: item.data.pos_produk_merk_id,
                        warna: item.data.warna,
                        ram: item.data.ram,
                        penyimpanan: item.data.penyimpanan,
                        battery_health: item.data.battery_health,
                        imei: item.data.imei,
                    };
                    
                    const response = await fetch('{{ route("produk.quick-store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Map temporary ID to real product ID
                        itemIdsMap[item.tempId] = data.data.id;
                        
                        // Update the hidden input with real product ID
                        const itemSelect = document.getElementById(`item-select-${item.itemId}`);
                        if (itemSelect) {
                            itemSelect.value = data.data.id;
                        }
                        
                        console.log(`Product created: ${item.tempId} -> ${data.data.id}`);
                    } else {
                        throw new Error(data.message || 'Failed to create product');
                    }
                } catch (error) {
                    console.error('Error creating product:', error);
                    alert('Error creating product: ' + error.message);
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Create Expense';
                    throw error; // Stop the process
                }
            }
        }
        
        // Create products first, then submit transaction
        createProducts()
            .then(() => {
                // Prepare form data
                const formData = new FormData(form);
                
                // DEBUG: Log all FormData entries for quantities
                console.log('=== QUANTITY DEBUG FROM FORMDATA ===');
                for (let [key, value] of formData.entries()) {
                    if (key.includes('[quantity]')) {
                        console.log(`${key} = "${value}"`);
                    }
                }
                
                // Submit form via AJAX
                return fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
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
                submitBtnText.textContent = 'Create Expense';
            });
    });
});
</script>
@endpush
