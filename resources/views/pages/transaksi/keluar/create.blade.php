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
                        <!-- Product Name with Searchable Dropdown -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="text" 
                                           id="quick_productNameSearch"
                                           placeholder="Search or type product name..."
                                           autocomplete="off"
                                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                                    <select id="quick_pos_produk_merk_id" name="pos_produk_merk_id" required class="hidden">
                                        <option value="">Select Product Name</option>
                                        @foreach($merks ?? [] as $merk)
                                            <option value="{{ $merk->id }}">{{ $merk->nama }}</option>
                                        @endforeach
                                    </select>
                                    <!-- Dropdown List -->
                                    <div id="quick_productNameDropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-navy-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                        <div id="quick_productNameList"></div>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="openModalProductNameModal()"
                                        class="rounded-xl bg-brand-500 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 whitespace-nowrap">
                                    + New
                                </button>
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
                            <select id="quick_warna"
                                    name="warna"
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                                <option value="">Select Color</option>
                                @foreach($warnas as $warna)
                                    <option value="{{ $warna->id }}">{{ $warna->warna }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- RAM -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                RAM <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <select id="quick_ram"
                                    name="ram"
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                                <option value="">Select RAM</option>
                                @foreach($rams as $ram)
                                    <option value="{{ $ram->id }}">{{ $ram->kapasitas }} GB</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Storage -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Storage <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <select id="quick_penyimpanan"
                                    name="penyimpanan"
                                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                                <option value="">Select Storage</option>
                                @foreach($penyimpanans as $penyimpanan)
                                    <option value="{{ $penyimpanan->id }}">{{ $penyimpanan->kapasitas }} GB</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Battery Health -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Battery Health <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <input type="text" id="quick_battery_health"
                                   name="battery_health"
                                   placeholder="e.g. 85% or Good"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <!-- IMEI -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                IMEI Number <span class="text-xs text-gray-500">(Optional)</span>
                            </label>
                            <input type="text" id="quick_imei"
                                   name="imei"
                                   placeholder="Enter IMEI number"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
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
                            <input type="number" id="quick_harga_beli" step="0.01" min="0" required
                                   placeholder="0"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                                Selling Price <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="quick_harga_jual" step="0.01" min="0" required
                                   placeholder="0"
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

<!-- Quick Add Product Name Modal (for modal) -->
<div id="quickAddModalProductNameModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-white dark:bg-navy-800 border-b border-gray-200 dark:border-white/10 px-6 py-4 rounded-t-2xl">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Quick Add Product Name</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new product name quickly</p>
        </div>
        
        <form id="quickModalProductNameForm" class="p-6">
            <div id="quickModalProductNameErrors" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside"></ul>
            </div>

            <div class="mb-4">
                <label for="quick_modal_product_name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Product Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="quick_modal_product_name"
                    name="nama"
                    required
                    placeholder="e.g. iPhone, Samsung, Charger..."
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500"
                >
            </div>

            <div class="flex gap-3 justify-end pt-4 border-t border-gray-200 dark:border-white/10">
                <button type="button" onclick="closeModalProductNameModal()" class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </button>
                <button type="submit" id="submitQuickModalProductName" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600">
                    Create Product Name
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

// ============= QUICK ADD PRODUCT MODAL FUNCTIONS =============

// Switch Product Type in Modal
function switchModalProductType(type) {
    document.getElementById('modal_product_type').value = type;
    console.log('Modal Product Type changed to:', type); // Debug log
    
    const tabs = document.querySelectorAll('.modal-product-type-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
        tab.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    });
    
    const activeTab = document.getElementById('modal-tab-' + type);
    activeTab.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    activeTab.classList.add('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
    
    const specificationsSection = document.getElementById('modal-specifications-section');
    const imeiField = document.getElementById('quick_imei');
    const imeiMarker = document.getElementById('modal-imei-required-marker');
    
    if (type === 'electronic') {
        specificationsSection.classList.remove('hidden');
        imeiField.required = false;
        if (imeiMarker) imeiMarker.classList.remove('hidden');
    } else {
        specificationsSection.classList.add('hidden');
        imeiField.required = false;
        if (imeiMarker) imeiMarker.classList.add('hidden');
        imeiField.value = '';
        document.getElementById('quick_warna').value = '';
        document.getElementById('quick_ram').value = '';
        document.getElementById('quick_penyimpanan').value = '';
        document.getElementById('quick_battery_health').value = '';
    }
}

// Initialize Modal Product Name Search
let modalProductNames = @json($merks ?? []);

function initModalProductNameSearch() {
    const searchInput = document.getElementById('quick_productNameSearch');
    const dropdown = document.getElementById('quick_productNameDropdown');
    const dropdownList = document.getElementById('quick_productNameList');
    const hiddenSelect = document.getElementById('quick_pos_produk_merk_id');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('focus', function() {
        renderModalProductNameList();
        dropdown.classList.remove('hidden');
    });
    
    searchInput.addEventListener('input', function() {
        renderModalProductNameList(this.value.toLowerCase());
    });
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    function renderModalProductNameList(filter = '') {
        const filtered = modalProductNames.filter(item => 
            item.nama.toLowerCase().includes(filter)
        );
        
        if (filtered.length === 0) {
            dropdownList.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">No product names found</div>';
            return;
        }
        
        dropdownList.innerHTML = filtered.map(item => `
            <div class="px-4 py-2 hover:bg-lightPrimary dark:hover:bg-navy-700 cursor-pointer text-sm text-navy-700 dark:text-white transition-colors" 
                 onclick="selectModalProductName(${item.id}, '${item.nama.replace(/'/g, "\\'")}')"
            >
                ${item.nama}
            </div>
        `).join('');
    }
}

function selectModalProductName(id, nama) {
    document.getElementById('quick_productNameSearch').value = nama;
    document.getElementById('quick_pos_produk_merk_id').value = id;
    document.getElementById('quick_productNameDropdown').classList.add('hidden');
}

// Quick Add Product Modal Functions
function openProductModal(itemId) {
    currentItemIdForModal = itemId;
    document.getElementById('quickAddProductModal').classList.remove('hidden');
    document.getElementById('quickProductErrors').classList.add('hidden');
    
    // Reset form fields first
    document.getElementById('quickProductForm').reset();
    
    // Then set defaults and tabs (after reset to prevent reset from overriding)
    document.getElementById('modal_product_type').value = 'electronic';
    document.getElementById('quick_imei').required = false;
    document.getElementById('modal-specifications-section').classList.remove('hidden');
    
    // Reset tab styles to electronic (active state)
    const tabs = document.querySelectorAll('.modal-product-type-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
        tab.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    });
    
    const electronicTab = document.getElementById('modal-tab-electronic');
    electronicTab.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
    electronicTab.classList.add('active', 'border-brand-500', 'text-brand-500', 'dark:text-brand-400');
    
    // Initialize search
    initModalProductNameSearch();
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
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';
    errorDiv.classList.add('hidden');
    
    // Get product name from search input or generate from merk
    const searchInput = document.getElementById('quick_productNameSearch').value;
    const merkId = document.getElementById('quick_pos_produk_merk_id').value;
    const productType = document.getElementById('modal_product_type').value;
    
    console.log('Submitting product with type:', productType); // Debug log
    
    const formData = {
        nama: searchInput,
        pos_produk_merk_id: merkId,
        product_type: productType,
        deskripsi: document.getElementById('quick_deskripsi').value,
        harga_beli: document.getElementById('quick_harga_beli').value,
        harga_jual: document.getElementById('quick_harga_jual').value,
        warna: document.getElementById('quick_warna').value,
        ram: document.getElementById('quick_ram').value,
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
        } else {
            throw new Error(data.message || 'Failed to create product');
        }
    })
    .catch(error => {
        errorDiv.classList.remove('hidden');
        errorDiv.querySelector('p').textContent = error.message || 'An error occurred while creating the product';
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Product
        `;
    });
}

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
            // Add to modalProductNames array
            modalProductNames.push(data.data);
            
            // Add to hidden select
            const option = new Option(data.data.nama, data.data.id, true, true);
            document.getElementById('quick_pos_produk_merk_id').add(option);
            
            // Update search input
            document.getElementById('quick_productNameSearch').value = data.data.nama;
            
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
        submitBtn.textContent = 'Create Product Name';
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductModal();
        closeModalProductNameModal();
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
            submitBtnText.textContent = 'Create Expense';
        });
    });
});
</script>
@endpush
