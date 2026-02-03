@extends('layouts.app')

@section('title', 'Tambah Warna')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Tambah Warna Baru</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tambahkan warna produk ke sistem</p>
            </div>
            <a href="{{ route('pos-warna.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-100 p-4 dark:bg-red-900/30">
            <div class="flex items-start gap-3">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-red-800 dark:text-red-300">Terjadi kesalahan</h3>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('pos-warna.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5">
                
                <!-- Owner Field - Only show for non-superadmin -->
                @if(!auth()->user()->isSuperadmin())
                <div>
                    <label for="id_owner" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Owner <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="id_owner"
                        name="id_owner" 
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('id_owner') !border-red-500 @enderror"
                    >
                        <option value="">-- Pilih Owner --</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ old('id_owner') == $owner->id ? 'selected' : '' }}>
                                {{ $owner->name }} ({{ $owner->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_owner')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Pilih owner yang akan mengelola warna ini</p>
                </div>
                @else
                <!-- Hidden input for superadmin -->
                <input type="hidden" name="id_owner" value="">
                @endif

                <!-- Warna Field -->
                <div>
                    <label for="warna" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Warna <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="warna"
                        name="warna" 
                        value="{{ old('warna') }}"
                        placeholder="Contoh: Black, Emerald, Red, Blue"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('warna') !border-red-500 @enderror"
                        required
                    >
                    @error('warna')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Masukkan nama warna (contoh: Black, Emerald, Red, Blue, dll)</p>
                </div>

                <!-- Is Global (Hidden) -->
                <input type="hidden" name="is_global" value="1">

            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('pos-warna.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Batal
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    Simpan Warna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on warna field
    document.getElementById('warna').focus();
});
</script>
@endpush
