@extends('layouts.app')

@section('title', 'Edit Store')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Store</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update store information</p>
            </div>
            <a href="{{ route('toko.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('toko.update', $toko) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Store Info Badge -->
            <div class="mb-6 flex items-center gap-4 rounded-xl bg-lightPrimary dark:bg-navy-700 p-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 dark:bg-brand-400">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $toko->nama }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $toko->slug }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                        {{ $toko->pengguna()->count() }} employees assigned
                    </p>
                </div>
            </div>

            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                <!-- Store Name Field -->
                <div class="md:col-span-2">
                    <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Store Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama"
                        name="nama" 
                        value="{{ old('nama', $toko->nama) }}"
                        placeholder="Enter store name"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama') !border-red-500 @enderror"
                        autofocus
                    >
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Field -->
                <div class="md:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Address
                    </label>
                    <textarea 
                        id="alamat"
                        name="alamat" 
                        rows="3"
                        placeholder="Enter store address"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                    >{{ old('alamat', $toko->alamat) }}</textarea>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-between gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <!-- Delete Button -->
                <button type="button" 
                        onclick="document.getElementById('deleteForm').submit();"
                        class="flex items-center gap-2 rounded-xl bg-red-100 px-6 py-3 text-sm font-bold text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Store
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('toko.index') }}" 
                       class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                        </svg>
                        Update Store
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="deleteForm" action="{{ route('toko.destroy', $toko) }}" method="POST" class="hidden">
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
    
    // Confirm before delete
    document.querySelector('button[onclick*="deleteForm"]').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus toko ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('deleteForm').submit();
        }
    });
});
</script>
@endpush
