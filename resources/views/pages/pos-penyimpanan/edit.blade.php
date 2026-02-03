@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-navy-700 dark:text-white">
                Edit Penyimpanan
            </h1>
            <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                Perbarui data penyimpanan
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 shadow-sm p-6">
        <form action="{{ route('pos-penyimpanan.update', $posPenyimpanan->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- ID Field (Read-only) -->
                <div>
                    <label for="id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        ID
                    </label>
                    <input 
                        type="text" 
                        id="id"
                        value="{{ $posPenyimpanan->id }}"
                        readonly
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900/50 px-4 py-3 text-sm text-gray-600 dark:text-gray-400 cursor-not-allowed"
                    >
                </div>

                <!-- Owner Field -->
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
                            <option value="{{ $owner->id }}" {{ old('id_owner', $posPenyimpanan->id_owner) == $owner->id ? 'selected' : '' }}>
                                {{ $owner->name }} ({{ $owner->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_owner')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Pilih owner yang akan mengelola penyimpanan ini</p>
                </div>

                <!-- Kapasitas Field -->
                <div>
                    <label for="kapasitas" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Kapasitas <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="kapasitas"
                        name="kapasitas" 
                        value="{{ old('kapasitas', $posPenyimpanan->kapasitas) }}"
                        placeholder="Masukkan kapasitas penyimpanan"
                        min="1"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('kapasitas') !border-red-500 @enderror"
                        required
                    >
                    @error('kapasitas')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Masukkan kapasitas penyimpanan dalam satuan unit</p>
                </div>

                <!-- Is Global Field - Auto checked for Superadmin -->
                <div>
                    <label for="id_global" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Global <span class="text-gray-500">(Otomatis aktif)</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input 
                            type="hidden" 
                            name="id_global" 
                            value="0"
                        >
                        <input 
                            type="checkbox" 
                            id="id_global"
                            name="id_global" 
                            value="1"
                            checked
                            disabled
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-900 cursor-not-allowed"
                        >
                        <label for="id_global" class="text-sm text-gray-600 dark:text-gray-400">
                            Penyimpanan ini tersedia secara global untuk semua owner
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Untuk superadmin, penyimpanan selalu bersifat global</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 items-center justify-between pt-6 border-t border-gray-200 dark:border-white/10">
                <form action="{{ route('pos-penyimpanan.destroy', $posPenyimpanan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penyimpanan ini? Data yang dihapus tidak dapat dikembalikan.');">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 px-6 py-3 text-sm font-bold text-red-600 dark:text-red-300 transition-all duration-200"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </form>
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300 px-6 py-3 text-sm font-bold text-white transition-all duration-200"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update
                    </button>
                    <a 
                        href="{{ route('pos-penyimpanan.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 px-6 py-3 text-sm font-bold text-navy-700 dark:text-white transition-all duration-200"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
