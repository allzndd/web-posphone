@extends('layouts.app')

@section('title', 'Edit Service Package')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Service Package</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update the package information</p>
        </div>

        <form action="{{ route('paket-layanan.update', $paket->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Package Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nama" 
                       name="nama" 
                       value="{{ old('nama', $paket->nama) }}"
                       class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama') border-red-500 @enderror"
                       placeholder="e.g., Starter Package"
                       required>
                @error('nama')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="deskripsi" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="deskripsi" 
                          name="deskripsi" 
                          rows="4"
                          class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('deskripsi') border-red-500 @enderror"
                          placeholder="Describe the package features..."
                          required>{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="harga" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Price (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="harga" 
                       name="harga" 
                       value="{{ old('harga', $paket->harga) }}"
                       min="0"
                       step="0.01"
                       class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('harga') border-red-500 @enderror"
                       placeholder="0"
                       required>
                @error('harga')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="durasi" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Duration <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="durasi" 
                       name="durasi" 
                       value="{{ old('durasi', $paket->durasi) }}"
                       class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('durasi') border-red-500 @enderror"
                       placeholder="e.g., 1 Month, 3 Months, 1 Year"
                       required>
                @error('durasi')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="status" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" 
                        name="status"
                        class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('status') border-red-500 @enderror"
                        required>
                    <option value="Active" {{ old('status', $paket->status) === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $paket->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" 
                        class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Update Package
                </button>
                <a href="{{ route('paket-layanan.index') }}" 
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
