@extends('layouts.app')

@section('title', 'Edit Permission')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-2">Edit Permission</h4>
        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Update data permission
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
        
        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Nama Permission <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $permission->nama) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Format: modul.aksi (contoh: services.create)
                    </p>
                </div>

                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Modul <span class="text-red-500">*</span></label>
                    <input type="text" name="modul" value="{{ old('modul', $permission->modul) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('modul') border-red-500 @enderror">
                    @error('modul')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Aksi <span class="text-red-500">*</span></label>
                    <select name="aksi" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('aksi') border-red-500 @enderror">
                        <option value="create" {{ old('aksi', $permission->aksi) == 'create' ? 'selected' : '' }}>Create</option>
                        <option value="read" {{ old('aksi', $permission->aksi) == 'read' ? 'selected' : '' }}>Read</option>
                        <option value="update" {{ old('aksi', $permission->aksi) == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="delete" {{ old('aksi', $permission->aksi) == 'delete' ? 'selected' : '' }}>Delete</option>
                    </select>
                    @error('aksi')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    <svg class="inline h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Update Permission
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
