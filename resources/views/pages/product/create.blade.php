@extends('layouts.app')

@section('title', 'Create Product')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label for="name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="Enter product name"
                        list="productNames"
                        autocomplete="off"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('name') !border-red-500 @enderror"
                    >
                    <datalist id="productNames">
                        @if(isset($productNames))
                            @foreach($productNames as $n)
                                <option value="{{ $n }}"></option>
                            @endforeach
                        @endif
                    </datalist>
                    @error('name')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="category_id"
                        name="category_id"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('category_id') !border-red-500 @enderror"
                    >
                        <option value="">Select Category</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('category_id')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- IMEI -->
                <div>
                    <label for="imei" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        IMEI
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

                <!-- Color -->
                <div>
                    <label for="color" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Color
                    </label>
                    <select 
                        id="color"
                        name="color"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('color') !border-red-500 @enderror"
                    >
                        <option value="">Select Color</option>
                        @if(isset($colors))
                            @foreach($colors as $color)
                                <option value="{{ $color->name }}" {{ old('color') == $color->name ? 'selected' : '' }}>
                                    {{ $color->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('color')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Storage -->
                <div>
                    <label for="storage" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Storage
                    </label>
                    <select 
                        id="storage"
                        name="storage"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('storage') !border-red-500 @enderror"
                    >
                        <option value="">Select Storage</option>
                        @if(isset($storages))
                            @foreach($storages as $storage)
                                <option value="{{ $storage->name }}" {{ old('storage') == $storage->name ? 'selected' : '' }}>
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('storage')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Battery Health -->
                <div>
                    <label for="barre_health" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Battery Health
                    </label>
                    <input 
                        type="text" 
                        id="barre_health"
                        name="barre_health" 
                        value="{{ old('barre_health') }}"
                        placeholder="e.g., 85%"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('barre_health') !border-red-500 @enderror"
                    >
                    @error('barre_health')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Stock <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="stock"
                        name="stock" 
                        value="{{ old('stock') }}"
                        placeholder="Enter stock quantity"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('stock') !border-red-500 @enderror"
                    >
                    @error('stock')
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
                            <span class="text-sm text-gray-400 dark:text-gray-600">Rp</span>
                        </div>
                        <input 
                            type="text" 
                            id="harga_jual"
                            name="harga_jual_display" 
                            value="{{ old('harga_jual') ? number_format(old('harga_jual'), 0, ',', '.') : '' }}"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('harga_jual') !border-red-500 @enderror"
                        >
                        <input type="hidden" name="harga_jual" id="harga_jual_hidden" value="{{ old('harga_jual') }}">
                    </div>
                    @error('harga_jual')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Price -->
                <div>
                    <label for="harga_beli" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Purchase Price
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-sm text-gray-400 dark:text-gray-600">Rp</span>
                        </div>
                        <input 
                            type="text" 
                            id="harga_beli"
                            name="harga_beli_display" 
                            value="{{ old('harga_beli') ? number_format(old('harga_beli'), 0, ',', '.') : '' }}"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('harga_beli') !border-red-500 @enderror"
                        >
                        <input type="hidden" name="harga_beli" id="harga_beli_hidden" value="{{ old('harga_beli') }}">
                    </div>
                    @error('harga_beli')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cost Items -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Cost Items
                    </label>
                    <div id="cost-items" class="space-y-2">
                        <div class="cost-row flex gap-2">
                            <input 
                                type="text" 
                                name="costs[0][description]" 
                                placeholder="Description"
                                class="flex-1 rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                            >
                            <input 
                                type="number" 
                                step="0.01" 
                                name="costs[0][amount]" 
                                placeholder="Amount"
                                class="w-32 rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                            >
                            <button type="button" class="btn-remove-cost rounded-xl bg-red-100 px-4 py-3 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 hidden">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add-cost-row" class="mt-3 flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                        </svg>
                        Add Cost
                    </button>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Description
                    </label>
                    <textarea 
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter product description"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('description') !border-red-500 @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Accessories -->
                <div class="md:col-span-2">
                    <label for="assessoris" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Accessories
                    </label>
                    <textarea 
                        id="assessoris"
                        name="assessoris"
                        rows="3"
                        placeholder="Enter accessories included"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('assessoris') !border-red-500 @enderror"
                    >{{ old('assessoris') }}</textarea>
                    @error('assessoris')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

            </div>
            </div>

            <!-- Form Footer -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('product.index') }}" 
                   class="rounded-xl border border-gray-200 dark:border-white/10 px-5 py-2.5 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-white/10">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"></path>
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
document.addEventListener('DOMContentLoaded', function() {
    let costIndex = 1;
    
    // Add cost row
    document.getElementById('add-cost-row').onclick = function() {
        const container = document.getElementById('cost-items');
        const row = document.createElement('div');
        row.className = 'cost-row flex gap-2';
        row.innerHTML = `
            <input 
                type="text" 
                name="costs[${costIndex}][description]" 
                placeholder="Description"
                class="flex-1 rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
            >
            <input 
                type="number" 
                step="0.01" 
                name="costs[${costIndex}][amount]" 
                placeholder="Amount"
                class="w-32 rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
            >
            <button type="button" class="btn-remove-cost rounded-xl bg-red-100 px-4 py-3 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                </svg>
            </button>
        `;
        container.appendChild(row);
        costIndex++;
    };
    
    // Remove cost row
    document.getElementById('cost-items').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-cost')) {
            e.target.closest('.cost-row').remove();
        }
    });
    
    // Currency formatting functions
    function formatCurrency(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        input.value = value;
        return input.value.replace(/\./g, '');
    }
    
    // Selling Price formatting
    const hargaJualInput = document.getElementById('harga_jual');
    const hargaJualHidden = document.getElementById('harga_jual_hidden');
    
    hargaJualInput.addEventListener('input', function() {
        const rawValue = formatCurrency(this);
        hargaJualHidden.value = rawValue;
    });
    
    // Purchase Price formatting
    const hargaBeliInput = document.getElementById('harga_beli');
    const hargaBeliHidden = document.getElementById('harga_beli_hidden');
    
    hargaBeliInput.addEventListener('input', function() {
        const rawValue = formatCurrency(this);
        hargaBeliHidden.value = rawValue;
    });
});
</script>
@endpush
