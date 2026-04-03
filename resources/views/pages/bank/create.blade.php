@extends('layouts.app')

@section('title', 'Tambah Rekening')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Tambah Rekening Baru</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tambahkan informasi rekening bank baru</p>
            </div>
            <a href="{{ route('bank.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <form action="{{ route('bank.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                <!-- Nama Bank Field -->
                <div class="md:col-span-2">
                    <label for="nama_bank" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama_bank"
                        name="nama_bank" 
                        value="{{ old('nama_bank') }}"
                        placeholder="Contoh: Bank Mandiri, Bank BCA, dsb"
                        maxlength="100"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama_bank') !border-red-500 @enderror"
                        autofocus
                    >
                    @error('nama_bank')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Rekening Field -->
                <div class="md:col-span-2">
                    <label for="nama_rekening" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Nama Rekening <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama_rekening"
                        name="nama_rekening" 
                        value="{{ old('nama_rekening') }}"
                        placeholder="Nama pemilik rekening"
                        maxlength="150"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama_rekening') !border-red-500 @enderror"
                    >
                    @error('nama_rekening')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rekening Field -->
                <div class="md:col-span-2">
                    <label for="nomor_rekening" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nomor_rekening"
                        name="nomor_rekening" 
                        value="{{ old('nomor_rekening') }}"
                        placeholder="Nomor rekening bank"
                        maxlength="50"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nomor_rekening') !border-red-500 @enderror"
                    >
                    @error('nomor_rekening')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('bank.index') }}" 
                   class="rounded-xl border border-gray-200 dark:border-white/10 px-8 py-3 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5">
                    Batal
                </a>
                <button type="submit" class="linear rounded-xl bg-brand-500 px-8 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300">
                    Simpan Rekening
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
