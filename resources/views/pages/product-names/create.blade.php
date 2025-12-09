@extends('layouts.app')

@section('title', 'Create Product Name')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <form action="{{ route('product-name.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5">
                
                <!-- Product Name -->
                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        value="{{ old('name') }}"
                        placeholder="Example: iPhone 13 Pro Max"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('name') !border-red-500 @enderror"
                        required
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Description
                    </label>
                    <textarea 
                        id="description"
                        name="description" 
                        rows="3"
                        placeholder="Optional product name description..."
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('description') !border-red-500 @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('product-name.index') }}" 
                   class="flex items-center gap-2 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 px-5 py-2.5 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-navy-700">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"></path>
                    </svg>
                    Save Product Name
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Page-specific scripts -->
@endpush
