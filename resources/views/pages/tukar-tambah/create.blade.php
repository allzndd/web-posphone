@extends('layouts.app')

@section('title', 'Create Trade-In')

@push('style')
<style>
    .hidden { display: none; }
    
    /* Custom Dropdown Styles */
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
    
    /* Disable number input spinner */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
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

        <form action="{{ route('tukar-tambah.store') }}" method="POST" id="tradeInForm">
            @csrf
            <!-- Force new product type -->
            <input type="hidden" name="produk_masuk_type" value="new">
            <input type="hidden" name="merk_type" value="existing">
            
            <!-- Basic Info Section -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4 border-l-4 border-brand-500 pl-3">Basic Information</h5>
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
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4 flex items-center gap-2 border-l-4 border-green-500 pl-3">
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                    Product OUT (Sale to Customer)
                </h5>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 ml-4">Select existing product from your inventory to sell</p>
                
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
                                    {{ $produk->nama }} - {{ get_currency_symbol() }} {{ number_format($produk->harga_jual, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="harga_jual_keluar" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Sale Price <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="harga_jual_keluar" name="harga_jual_keluar" required
                            value="{{ old('harga_jual_keluar') }}"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label for="diskon_keluar" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Discount
                        </label>
                        <input type="text" id="diskon_keluar" name="diskon_keluar"
                            value="{{ old('diskon_keluar', '0') }}"
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

            <!-- Product IN Section (Purchase) - Always New Product -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4 flex items-center gap-2 border-l-4 border-red-500 pl-3">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
                    </svg>
                    Product IN (Purchase from Customer)
                </h5>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 ml-4">Each phone has unique IMEI - create new product entry</p>

                <!-- Brand & Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                        Brand & Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <!-- Brand Dropdown -->
                        <div class="custom-dropdown" id="brandDropdownWrapper">
                            <input type="hidden" id="selected_brand" name="selected_brand" value="">
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
                        
                        <!-- Type Dropdown -->
                        <div class="custom-dropdown" id="typeDropdownWrapper">
                            <input type="hidden" id="pos_produk_merk_id" name="pos_produk_merk_id" value="" required>
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
                    </div>
                    @error('pos_produk_merk_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Specifications -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-4">
                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Color</label>
                        <div class="custom-dropdown" id="colorDropdownWrapper">
                            <input type="hidden" id="pos_warna_id" name="pos_warna_id" value="">
                            <div id="colorDropdownTrigger"
                                 onclick="toggleColorDropdown()"
                                 class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                <span id="colorDropdownLabel" class="truncate text-gray-400">Select</span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <div id="colorDropdownMenu" class="custom-dropdown-menu hidden">
                                <div class="custom-dropdown-search">
                                    <input type="text" id="colorSearchInput" placeholder="Search..." oninput="filterColorDropdown()" autocomplete="off">
                                </div>
                                <div id="colorDropdownItems"></div>
                            </div>
                        </div>
                    </div>

                    <!-- RAM -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">RAM</label>
                        <div class="custom-dropdown" id="ramDropdownWrapper">
                            <input type="hidden" id="pos_ram_id" name="pos_ram_id" value="">
                            <div id="ramDropdownTrigger"
                                 onclick="toggleRamDropdown()"
                                 class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                <span id="ramDropdownLabel" class="truncate text-gray-400">Select</span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <div id="ramDropdownMenu" class="custom-dropdown-menu hidden">
                                <div class="custom-dropdown-search">
                                    <input type="text" id="ramSearchInput" placeholder="Search..." oninput="filterRamDropdown()" autocomplete="off">
                                </div>
                                <div id="ramDropdownItems"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Storage -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Storage</label>
                        <div class="custom-dropdown" id="storageDropdownWrapper">
                            <input type="hidden" id="pos_penyimpanan_id" name="pos_penyimpanan_id" value="">
                            <div id="storageDropdownTrigger"
                                 onclick="toggleStorageDropdown()"
                                 class="custom-dropdown-trigger w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                                <span id="storageDropdownLabel" class="truncate text-gray-400">Select</span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <div id="storageDropdownMenu" class="custom-dropdown-menu hidden">
                                <div class="custom-dropdown-search">
                                    <input type="text" id="storageSearchInput" placeholder="Search..." oninput="filterStorageDropdown()" autocomplete="off">
                                </div>
                                <div id="storageDropdownItems"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Battery Health -->
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">Battery %</label>
                        <input type="number" id="battery_health" name="battery_health" min="0" max="100"
                            value="{{ old('battery_health') }}" placeholder="0-100"
                            oninput="if(this.value > 100) this.value = 100; if(this.value < 0) this.value = 0;"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>
                </div>

                <!-- IMEI & Product Name -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            IMEI Number
                        </label>
                        <input type="text" id="imei" name="imei" value="{{ old('imei') }}"
                            placeholder="Enter IMEI (numbers only)"
                            pattern="[0-9]*"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Product Name <span class="text-red-500">*</span> <span class="text-xs text-gray-400">(Auto-generated)</span>
                        </label>
                        <input type="text" id="produk_nama_baru" name="produk_nama_baru" value="{{ old('produk_nama_baru') }}"
                            placeholder="Will be auto-generated from Brand + Type + Color"
                            required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-700 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none">
                    </div>
                </div>

                <!-- Biaya Tambahan / Add-on Section -->
                <div class="mb-4 p-4 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h6 class="text-sm font-bold text-orange-700 dark:text-orange-300">Biaya Tambahan / Add-on</h6>
                            <p class="text-xs text-orange-600 dark:text-orange-400">Service, repair, or other operational costs</p>
                        </div>
                        <button type="button" onclick="addBiayaTambahan()"
                            class="flex items-center gap-1 rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add
                        </button>
                    </div>
                    <div id="biayaTambahanContainer" class="space-y-2">
                        <!-- Dynamic rows will be added here -->
                    </div>
                    <div id="totalBiayaTambahanWrapper" class="hidden mt-3 pt-3 border-t border-orange-200 dark:border-orange-800/50">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-orange-700 dark:text-orange-300">Total Add-on:</span>
                            <span id="totalBiayaTambahan" class="text-sm font-bold text-orange-700 dark:text-orange-300">{{ get_currency_symbol() }} 0</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Purchase Price <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="harga_beli_masuk" name="harga_beli_masuk" required
                            value="{{ old('harga_beli_masuk') }}"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-navy-700 dark:text-white mb-2">
                            Selling Price <span class="text-xs text-gray-400">(Optional)</span>
                        </label>
                        <input type="text" id="harga_jual_masuk" name="harga_jual_masuk"
                            value="{{ old('harga_jual_masuk') }}"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                    </div>
                </div>
            </div>

            <!-- Transaction Details Section -->
            <div class="mb-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4 border-l-4 border-blue-500 pl-3">Transaction Details</h5>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    
                    <div>
                        <label for="metode_pembayaran" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select id="metode_pembayaran" name="metode_pembayaran" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500">
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="e-wallet" {{ old('metode_pembayaran') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="credit" {{ old('metode_pembayaran') == 'credit' ? 'selected' : '' }}>Credit</option>
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
                        <p class="text-xs text-gray-600 dark:text-gray-400">Sale Revenue (OUT)</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400" id="summary_sale">{{ get_currency_symbol() }} 0</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-navy-900">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Purchase Cost (IN)</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400" id="summary_purchase">{{ get_currency_symbol() }} 0</p>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-white dark:bg-navy-900">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Net Profit</p>
                        <p class="text-xl font-bold text-navy-700 dark:text-white" id="summary_profit">{{ get_currency_symbol() }} 0</p>
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
// Currency settings
const currency = '{{ get_currency() }}';
const currencySymbol = '{{ get_currency_symbol() }}';
const decimalPlaces = {{ get_decimal_places() }};

// Master data from server
const allMerks = @json($merks);
const colorItems = @json($warnas);
const ramItems = @json($rams);
const storageItems = @json($penyimpanans);

// State
let currentBrandItems = [];
let currentTypeItems = [];
let biayaTambahanCounter = 0;

// ============= CURRENCY FORMATTING =============
function formatCurrency(amount) {
    let formatted;
    if (currency === 'IDR') {
        formatted = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
    } else {
        formatted = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
    }
    return currencySymbol + ' ' + formatted;
}

function parseCurrencyValue(value) {
    if (!value) return 0;
    let str = String(value);
    if (currency === 'IDR') {
        // IDR uses dots as thousands separator - strip all non-digits
        return parseInt(str.replace(/\D/g, '')) || 0;
    } else {
        // Other currencies: commas as thousands, dot as decimal
        str = str.replace(/[^0-9.,-]/g, '').replace(/,/g, '');
        return parseFloat(str) || 0;
    }
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

// ============= BRAND & TYPE DROPDOWNS =============
function initializeBrandDropdown() {
    const uniqueBrands = [...new Set(allMerks.map(m => m.merk))].filter(b => b).sort();
    currentBrandItems = uniqueBrands;
    renderBrandItems(uniqueBrands);
}

function renderBrandItems(items) {
    const container = document.getElementById('brandDropdownItems');
    if (!items.length) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No brands found</div>';
        return;
    }
    container.innerHTML = items.map(brand => 
        `<div class="dropdown-item" onclick="selectBrand('${brand}')">${brand}</div>`
    ).join('');
}

function toggleBrandDropdown() {
    const menu = document.getElementById('brandDropdownMenu');
    closeAllDropdowns();
    menu.classList.toggle('hidden');
    if (!menu.classList.contains('hidden')) {
        document.getElementById('brandSearchInput').focus();
    }
}

function filterBrandDropdown() {
    const search = document.getElementById('brandSearchInput').value.toLowerCase();
    const filtered = currentBrandItems.filter(b => b.toLowerCase().includes(search));
    renderBrandItems(filtered);
}

function selectBrand(brand) {
    document.getElementById('selected_brand').value = brand;
    document.getElementById('brandDropdownLabel').textContent = brand;
    document.getElementById('brandDropdownLabel').classList.remove('text-gray-400');
    document.getElementById('brandDropdownMenu').classList.add('hidden');
    
    // Filter types by selected brand
    currentTypeItems = allMerks.filter(m => m.merk === brand);
    renderTypeItems(currentTypeItems);
    
    // Reset type selection
    document.getElementById('pos_produk_merk_id').value = '';
    document.getElementById('typeDropdownLabel').textContent = 'Select Type';
    document.getElementById('typeDropdownLabel').classList.add('text-gray-400');
    
    updateProductName();
}

function renderTypeItems(items) {
    const container = document.getElementById('typeDropdownItems');
    if (!items.length) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No types found</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectType(${item.id}, '${item.nama}')">${item.nama}</div>`
    ).join('');
}

function toggleTypeDropdown() {
    const menu = document.getElementById('typeDropdownMenu');
    closeAllDropdowns();
    menu.classList.toggle('hidden');
    if (!menu.classList.contains('hidden')) {
        document.getElementById('typeSearchInput').focus();
    }
}

function filterTypeDropdown() {
    const search = document.getElementById('typeSearchInput').value.toLowerCase();
    const filtered = currentTypeItems.filter(t => t.nama.toLowerCase().includes(search));
    renderTypeItems(filtered);
}

function selectType(id, nama) {
    document.getElementById('pos_produk_merk_id').value = id;
    document.getElementById('typeDropdownLabel').textContent = nama;
    document.getElementById('typeDropdownLabel').classList.remove('text-gray-400');
    document.getElementById('typeDropdownMenu').classList.add('hidden');
    updateProductName();
}

// ============= COLOR DROPDOWN =============
function initializeColorDropdown() {
    renderColorItems(colorItems);
}

function renderColorItems(items) {
    const container = document.getElementById('colorDropdownItems');
    if (!items.length) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No colors found</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectColor(${item.id}, '${item.warna}')">${item.warna}</div>`
    ).join('');
}

function toggleColorDropdown() {
    const menu = document.getElementById('colorDropdownMenu');
    closeAllDropdowns();
    menu.classList.toggle('hidden');
}

function filterColorDropdown() {
    const search = document.getElementById('colorSearchInput').value.toLowerCase();
    const filtered = colorItems.filter(c => c.warna.toLowerCase().includes(search));
    renderColorItems(filtered);
}

function selectColor(id, warna) {
    document.getElementById('pos_warna_id').value = id;
    document.getElementById('colorDropdownLabel').textContent = warna;
    document.getElementById('colorDropdownLabel').classList.remove('text-gray-400');
    document.getElementById('colorDropdownMenu').classList.add('hidden');
    updateProductName();
}

// ============= RAM DROPDOWN =============
function initializeRamDropdown() {
    renderRamItems(ramItems);
}

function renderRamItems(items) {
    const container = document.getElementById('ramDropdownItems');
    if (!items.length) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No RAM options</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectRam(${item.id}, '${item.kapasitas}')">${item.kapasitas}</div>`
    ).join('');
}

function toggleRamDropdown() {
    const menu = document.getElementById('ramDropdownMenu');
    closeAllDropdowns();
    menu.classList.toggle('hidden');
}

function filterRamDropdown() {
    const search = document.getElementById('ramSearchInput').value.toLowerCase();
    const filtered = ramItems.filter(r => r.kapasitas.toLowerCase().includes(search));
    renderRamItems(filtered);
}

function selectRam(id, kapasitas) {
    document.getElementById('pos_ram_id').value = id;
    document.getElementById('ramDropdownLabel').textContent = kapasitas;
    document.getElementById('ramDropdownLabel').classList.remove('text-gray-400');
    document.getElementById('ramDropdownMenu').classList.add('hidden');
}

// ============= STORAGE DROPDOWN =============
function initializeStorageDropdown() {
    renderStorageItems(storageItems);
}

function renderStorageItems(items) {
    const container = document.getElementById('storageDropdownItems');
    if (!items.length) {
        container.innerHTML = '<div class="dropdown-item text-gray-400">No storage options</div>';
        return;
    }
    container.innerHTML = items.map(item => 
        `<div class="dropdown-item" onclick="selectStorage(${item.id}, '${item.kapasitas}')">${item.kapasitas}</div>`
    ).join('');
}

function toggleStorageDropdown() {
    const menu = document.getElementById('storageDropdownMenu');
    closeAllDropdowns();
    menu.classList.toggle('hidden');
}

function filterStorageDropdown() {
    const search = document.getElementById('storageSearchInput').value.toLowerCase();
    const filtered = storageItems.filter(s => s.kapasitas.toLowerCase().includes(search));
    renderStorageItems(filtered);
}

function selectStorage(id, kapasitas) {
    document.getElementById('pos_penyimpanan_id').value = id;
    document.getElementById('storageDropdownLabel').textContent = kapasitas;
    document.getElementById('storageDropdownLabel').classList.remove('text-gray-400');
    document.getElementById('storageDropdownMenu').classList.add('hidden');
    updateProductName();
}

// ============= CLOSE ALL DROPDOWNS =============
function closeAllDropdowns() {
    document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
        menu.classList.add('hidden');
    });
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-dropdown')) {
        closeAllDropdowns();
    }
});

// ============= PRODUCT NAME AUTO-GENERATION =============
function updateProductName() {
    const brand = document.getElementById('selected_brand').value;
    const type = document.getElementById('typeDropdownLabel').textContent;
    const colorLabel = document.getElementById('colorDropdownLabel').textContent;
    const storageLabel = document.getElementById('storageDropdownLabel').textContent;
    
    let name = '';
    if (brand && type !== 'Select Type') {
        name = brand + ' ' + type;
        if (colorLabel && colorLabel !== 'Select') name += ' ' + colorLabel;
        if (storageLabel && storageLabel !== 'Select') name += ' ' + storageLabel;
    }
    
    document.getElementById('produk_nama_baru').value = name;
}

// ============= BIAYA TAMBAHAN / ADD-ON =============
function addBiayaTambahan() {
    biayaTambahanCounter++;
    const container = document.getElementById('biayaTambahanContainer');
    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center';
    row.id = `biaya_row_${biayaTambahanCounter}`;
    row.innerHTML = `
        <input type="text" name="biaya_tambahan_nama[]" placeholder="Description" 
               class="flex-1 rounded-lg border border-orange-200 dark:border-orange-800/50 bg-white dark:bg-navy-900 px-3 py-2 text-sm outline-none">
        <input type="text" name="biaya_tambahan_nilai[]" placeholder="Amount" 
               oninput="formatCurrencyInput(this); calculateTotalBiayaTambahan();"
               class="w-32 rounded-lg border border-orange-200 dark:border-orange-800/50 bg-white dark:bg-navy-900 px-3 py-2 text-sm outline-none">
        <button type="button" onclick="removeBiayaTambahan(${biayaTambahanCounter})" 
                class="rounded-lg bg-red-100 p-2 text-red-500 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(row);
    document.getElementById('totalBiayaTambahanWrapper').classList.remove('hidden');
    calculateTotalBiayaTambahan();
}

function removeBiayaTambahan(id) {
    const row = document.getElementById(`biaya_row_${id}`);
    if (row) row.remove();
    calculateTotalBiayaTambahan();
    
    // Hide total wrapper if no items
    const container = document.getElementById('biayaTambahanContainer');
    if (container.children.length === 0) {
        document.getElementById('totalBiayaTambahanWrapper').classList.add('hidden');
    }
}

function calculateTotalBiayaTambahan() {
    const inputs = document.querySelectorAll('input[name="biaya_tambahan_nilai[]"]');
    let total = 0;
    inputs.forEach(input => {
        total += parseCurrencyValue(input.value);
    });
    document.getElementById('totalBiayaTambahan').textContent = formatCurrency(total);
    return total;
}

// ============= SUMMARY CALCULATIONS =============
function calculateNet() {
    const hargaJual = parseCurrencyValue(document.getElementById('harga_jual_keluar').value);
    const diskon = parseCurrencyValue(document.getElementById('diskon_keluar').value);
    const net = hargaJual - diskon;
    document.getElementById('net_keluar').value = formatCurrency(net);
    updateSummary();
}

function updateSummary() {
    const saleAmount = parseCurrencyValue(document.getElementById('harga_jual_keluar').value) - 
                       parseCurrencyValue(document.getElementById('diskon_keluar').value);
    const purchaseAmount = parseCurrencyValue(document.getElementById('harga_beli_masuk').value);
    const profit = saleAmount - purchaseAmount;
    
    document.getElementById('summary_sale').textContent = formatCurrency(saleAmount);
    document.getElementById('summary_purchase').textContent = formatCurrency(purchaseAmount);
    document.getElementById('summary_profit').textContent = formatCurrency(profit);
    document.getElementById('summary_profit').className = profit >= 0 ? 
        'text-xl font-bold text-green-600 dark:text-green-400' : 
        'text-xl font-bold text-red-600 dark:text-red-400';
}

// ============= INITIALIZATION =============
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    initializeBrandDropdown();
    initializeColorDropdown();
    initializeRamDropdown();
    initializeStorageDropdown();
    
    // Product OUT handlers
    document.getElementById('pos_produk_keluar_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const harga = selected.getAttribute('data-harga');
        if (harga) {
            document.getElementById('harga_jual_keluar').value = parseInt(harga).toLocaleString('id-ID');
            calculateNet();
        }
    });
    
    // Calculate on input change
    document.getElementById('harga_jual_keluar').addEventListener('input', function() {
        formatCurrencyInput(this);
        calculateNet();
    });
    document.getElementById('diskon_keluar').addEventListener('input', function() {
        formatCurrencyInput(this);
        calculateNet();
    });
    document.getElementById('harga_beli_masuk').addEventListener('input', function() {
        formatCurrencyInput(this);
        updateSummary();
    });
    document.getElementById('harga_jual_masuk').addEventListener('input', function() {
        formatCurrencyInput(this);
    });
    
    // Initialize calculations
    calculateNet();
});

// Form submission - convert formatted values back to numbers
document.getElementById('tradeInForm').addEventListener('submit', function() {
    const fields = ['harga_jual_keluar', 'diskon_keluar', 'harga_beli_masuk', 'harga_jual_masuk'];
    fields.forEach(id => {
        const input = document.getElementById(id);
        if (input && input.value) {
            input.value = parseCurrencyValue(input.value);
        }
    });
    
    // Convert biaya tambahan values
    document.querySelectorAll('input[name="biaya_tambahan_nilai[]"]').forEach(input => {
        input.value = parseCurrencyValue(input.value);
    });
});
</script>
@endpush
