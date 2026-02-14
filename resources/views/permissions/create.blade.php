@extends('layouts.app')

@section('title', 'Tambah Permission')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-2">Tambah Permission Baru</h4>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Pilih halaman dari daftar yang tersedia atau input custom modul, lalu centang aksi yang akan dibuat
        </p>
        
        @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-800 dark:bg-red-900/30 dark:text-red-300">
            <p class="font-bold mb-2">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Pilih atau Ketik Nama Halaman <span class="text-red-500">*</span></label>
                    <input type="text" name="modul" value="{{ old('modul') }}" required list="pages-list"
                           placeholder="Ketik atau pilih dari daftar..."
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white dark:bg-navy-800 @error('modul') border-red-500 @enderror">
                    
                    <datalist id="pages-list">
                        @foreach($pages as $page)
                            <option value="{{ strtolower(str_replace(' ', '_', $page)) }}">{{ $page }}</option>
                        @endforeach
                    </datalist>
                    
                    @error('modul')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Klik untuk melihat daftar halaman yang tersedia, atau ketik custom modul
                    </p>
                </div>

                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white mb-3 block">Pilih Aksi yang Akan Dibuat <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 dark:border-white/10 hover:border-brand-500 dark:hover:border-brand-500 cursor-pointer transition-all">
                            <input type="checkbox" name="aksi[]" value="create" checked
                                   class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                            <div>
                                <p class="font-semibold text-sm text-navy-700 dark:text-white">Create</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tambah data</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 dark:border-white/10 hover:border-brand-500 dark:hover:border-brand-500 cursor-pointer transition-all">
                            <input type="checkbox" name="aksi[]" value="read" checked
                                   class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                            <div>
                                <p class="font-semibold text-sm text-navy-700 dark:text-white">Read</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Lihat data</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 dark:border-white/10 hover:border-brand-500 dark:hover:border-brand-500 cursor-pointer transition-all">
                            <input type="checkbox" name="aksi[]" value="update" checked
                                   class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                            <div>
                                <p class="font-semibold text-sm text-navy-700 dark:text-white">Update</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Edit data</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 dark:border-white/10 hover:border-brand-500 dark:hover:border-brand-500 cursor-pointer transition-all">
                            <input type="checkbox" name="aksi[]" value="delete" checked
                                   class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                            <div>
                                <p class="font-semibold text-sm text-navy-700 dark:text-white">Delete</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hapus data</p>
                            </div>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Centang aksi yang ingin dibuat. Default semua tercentang.
                    </p>
                </div>

                <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-4">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">
                        <svg class="inline h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Contoh Hasil
                    </p>
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        Jika pilih halaman <span class="font-mono font-semibold">Service</span> dan centang Create, Read:<br>
                        • <span class="font-mono">service.create</span><br>
                        • <span class="font-mono">service.read</span><br><br>
                        <strong>Daftar halaman otomatis di-scan dari folder:</strong> <span class="font-mono">resources/views/pages/</span>
                    </p>
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    <svg class="inline h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Simpan Permission
                </button>
                <a href="{{ route('permissions.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
