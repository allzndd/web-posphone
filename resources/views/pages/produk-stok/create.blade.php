@extends('layouts.app')

@section('title', 'Add Product Stock')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('produk-stok.index') }}" 
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-navy-700 dark:text-gray-400 dark:hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Stock List
        </a>
    </div>

    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="border-b border-gray-200 dark:border-white/10 p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Add New Stock</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add product stock for a specific store</p>
        </div>

        <!-- Form -->
        <form action="{{ route('produk-stok.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Product -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Product <span class="text-red-500">*</span>
                    </label>
                    <select name="pos_produk_id" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('pos_produk_id') border-red-500 @enderror">
                        <option value="">Select Product</option>
                        @foreach($produk as $item)
                            <option value="{{ $item->id }}" {{ old('pos_produk_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->display_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pos_produk_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Store <span class="text-red-500">*</span>
                    </label>
                    <select name="pos_toko_id" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('pos_toko_id') border-red-500 @enderror">
                        <option value="">Select Store</option>
                        @foreach($toko as $item)
                            <option value="{{ $item->id }}" {{ old('pos_toko_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('pos_toko_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Stock Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stok" value="{{ old('stok', 0) }}" required min="0"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('stok') border-red-500 @enderror"
                           placeholder="Enter stock quantity">
                    @error('stok')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('produk-stok.index') }}" 
                   class="rounded-xl border border-gray-200 dark:border-white/10 px-6 py-3 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" 
                        class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Add Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
