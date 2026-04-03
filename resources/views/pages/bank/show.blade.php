@extends('layouts.app')

@section('title', 'Detail Rekening')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Detail Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Detail Rekening</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Informasi rekening bank</p>
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

        <!-- Detail Information -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Nama Bank -->
            <div>
                <label class="text-sm font-bold text-gray-600 dark:text-gray-400">Nama Bank</label>
                <p class="mt-2 text-base font-medium text-navy-700 dark:text-white">{{ $bank->nama_bank }}</p>
            </div>

            <!-- Nama Rekening -->
            <div>
                <label class="text-sm font-bold text-gray-600 dark:text-gray-400">Nama Rekening</label>
                <p class="mt-2 text-base font-medium text-navy-700 dark:text-white">{{ $bank->nama_rekening }}</p>
            </div>

            <!-- Nomor Rekening -->
            <div>
                <label class="text-sm font-bold text-gray-600 dark:text-gray-400">Nomor Rekening</label>
                <p class="mt-2 text-base font-medium text-navy-700 dark:text-white">{{ $bank->nomor_rekening }}</p>
            </div>

            <!-- Created At -->
            <div>
                <label class="text-sm font-bold text-gray-600 dark:text-gray-400">Dibuat Pada</label>
                <p class="mt-2 text-base font-medium text-navy-700 dark:text-white">{{ $bank->created_at->format('d M Y H:i') }}</p>
            </div>

            @if($bank->updated_at && $bank->updated_at != $bank->created_at)
            <!-- Updated At -->
            <div>
                <label class="text-sm font-bold text-gray-600 dark:text-gray-400">Diperbarui Pada</label>
                <p class="mt-2 text-base font-medium text-navy-700 dark:text-white">{{ $bank->updated_at->format('d M Y H:i') }}</p>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('bank.edit', $bank) }}" 
               class="rounded-xl border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 px-8 py-3 text-sm font-bold text-blue-600 dark:text-blue-400 transition duration-200 hover:bg-blue-100 dark:hover:bg-blue-900/50">
                Edit Rekening
            </a>
            <form action="{{ route('bank.destroy', $bank) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="rounded-xl border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/30 px-8 py-3 text-sm font-bold text-red-600 dark:text-red-400 transition duration-200 hover:bg-red-100 dark:hover:bg-red-900/50">
                    Hapus Rekening
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
