@extends('layouts.app')

@section('title', 'Add Service Package')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Add New Service Package</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Fill in the form to create a new subscription package</p>
        </div>

        <form action="{{ route('paket-layanan.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Package Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nama" 
                       name="nama" 
                       value="{{ old('nama') }}"
                       class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama') border-red-500 @enderror"
                       placeholder="e.g., Starter Package"
                       required>
                @error('nama')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="harga" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Price (Rp) <span class="text-gray-400 text-xs">(optional, leave blank for free)</span>
                </label>
                <input type="number" 
                       id="harga" 
                       name="harga" 
                       value="{{ old('harga') }}"
                       min="0"
                       step="0.01"
                       class="w-full rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('harga') border-red-500 @enderror"
                       placeholder="Leave blank for free">
                @error('harga')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="durasi" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Duration <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-3">
                    <input type="number" 
                           id="durasi" 
                           name="durasi" 
                           value="{{ old('durasi') }}"
                           min="1"
                           class="w-2/3 rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('durasi') border-red-500 @enderror"
                           placeholder="e.g., 15, 1, 3, 12"
                           required>
                    <select id="durasi_satuan" name="durasi_satuan" class="w-1/3 rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('durasi_satuan') border-red-500 @enderror" required>
                        <option value="hari" {{ old('durasi_satuan') == 'hari' ? 'selected' : '' }}>Days</option>
                        <option value="bulan" {{ old('durasi_satuan', 'bulan') == 'bulan' ? 'selected' : '' }}>Months</option>
                        <option value="tahun" {{ old('durasi_satuan') == 'tahun' ? 'selected' : '' }}>Years</option>
                    </select>
                </div>
                @error('durasi')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
                @error('durasi_satuan')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Permissions Section -->
            <div class="mb-6">
                <label class="mb-3 block text-sm font-bold text-navy-700 dark:text-white">
                    Permissions & Features <span class="text-gray-400 text-xs">(pilih fitur yang tersedia untuk paket ini)</span>
                </label>
                
                <div class="rounded-xl border border-gray-200 dark:border-white/10 p-4 max-h-96 overflow-y-auto">
                    @forelse($permissions as $modul => $perms)
                    <div class="mb-4 pb-4 border-b border-gray-100 dark:border-white/10 last:border-0">
                        <div class="flex items-center justify-between mb-3">
                            <h6 class="text-sm font-bold text-navy-700 dark:text-white">{{ ucfirst($modul) }}</h6>
                            <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
                                <input type="checkbox" 
                                       class="select-all-module h-4 w-4 rounded border-gray-300 text-brand-500" 
                                       data-modul="{{ $modul }}">
                                Select All
                            </label>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($perms as $permission)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-navy-900 hover:bg-gray-100 dark:hover:bg-navy-800 transition-colors">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}" 
                                       class="permission-checkbox h-4 w-4 rounded border-gray-300 text-brand-500"
                                       data-modul="{{ $modul }}"
                                       id="perm_{{ $permission->id }}">
                                <label for="perm_{{ $permission->id }}" class="flex-1 cursor-pointer">
                                    <span class="text-sm font-medium text-navy-700 dark:text-white">{{ $permission->nama }}</span>
                                    @php
                                        $aksiColors = [
                                            'create' => 'green',
                                            'read' => 'blue',
                                            'update' => 'yellow',
                                            'delete' => 'red',
                                        ];
                                        $color = $aksiColors[$permission->aksi] ?? 'gray';
                                    @endphp
                                    <span class="ml-2 inline-flex items-center rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 px-2 py-0.5 text-xs font-medium text-{{ $color }}-800 dark:text-{{ $color }}-300">
                                        {{ ucfirst($permission->aksi) }}
                                    </span>
                                </label>
                                <input type="number" 
                                       name="max_records[{{ $permission->id }}]" 
                                       placeholder="Max records" 
                                       min="0"
                                       class="w-32 rounded-lg border border-gray-200 bg-white p-2 text-xs outline-none dark:!border-white/10 dark:bg-navy-800 dark:text-white"
                                       title="Isi angka untuk membatasi. Kosongkan atau 0 = unlimited">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">
                        Belum ada permissions. <a href="{{ route('permissions.create') }}" class="text-brand-500 hover:underline">Tambah permission</a> terlebih dahulu.
                    </p>
                    @endforelse
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="inline h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Max records: Isi angka untuk membatasi, kosongkan atau 0 untuk unlimited.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit" 
                        class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Save Package
                </button>
                <a href="{{ route('paket-layanan.index') }}" 
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all per module
    document.querySelectorAll('.select-all-module').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const modul = this.dataset.modul;
            const isChecked = this.checked;
            
            document.querySelectorAll(`.permission-checkbox[data-modul="${modul}"]`).forEach(cb => {
                cb.checked = isChecked;
            });
        });
    });
    
    // Update select-all checkbox when individual checkboxes change
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const modul = this.dataset.modul;
            const allCheckboxes = document.querySelectorAll(`.permission-checkbox[data-modul="${modul}"]`);
            const checkedCheckboxes = document.querySelectorAll(`.permission-checkbox[data-modul="${modul}"]:checked`);
            const selectAllCheckbox = document.querySelector(`.select-all-module[data-modul="${modul}"]`);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
            }
        });
    });
});
</script>
@endsection
