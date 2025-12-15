@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('supplier.index') }}" 
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-navy-700 dark:text-gray-400 dark:hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Suppliers
        </a>
    </div>

    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="border-b border-gray-200 dark:border-white/10 p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Supplier</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update supplier information</p>
        </div>

        <!-- Form -->
        <form action="{{ route('supplier.update', $supplier) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Supplier Name -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Supplier Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $supplier->nama) }}" required maxlength="255"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('nama') border-red-500 @enderror"
                           placeholder="Enter supplier name">
                    @error('nama')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Phone Number
                    </label>
                    <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $supplier->nomor_hp) }}" maxlength="45"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('nomor_hp') border-red-500 @enderror"
                           placeholder="e.g., 08123456789">
                    @error('nomor_hp')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}" maxlength="255"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('email') border-red-500 @enderror"
                           placeholder="supplier@example.com">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Address
                    </label>
                    <input type="text" name="alamat" value="{{ old('alamat', $supplier->alamat) }}" maxlength="255"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('alamat') border-red-500 @enderror"
                           placeholder="Enter address">
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Description
                    </label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $supplier->keterangan) }}" maxlength="255"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('keterangan') border-red-500 @enderror"
                           placeholder="Additional notes about this supplier">
                    @error('keterangan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('supplier.index') }}" 
                   class="rounded-xl border border-gray-200 dark:border-white/10 px-6 py-3 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" 
                        class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Update Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
