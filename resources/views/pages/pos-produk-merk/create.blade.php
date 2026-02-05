@extends('layouts.app')

@section('title', 'Create Brand')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create New Product Name</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new product name</p>
            </div>
            <a href="{{ route('pos-produk-merk.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('pos-produk-merk.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5">
                
                <!-- Brand/Merk Field -->
                <div>
                    <label for="merk" class="mb-2 flex items-center gap-2 text-sm font-bold text-navy-700 dark:text-white">
                        Brand <span class="text-red-500">*</span>
                        <button type="button" id="addNewMerkBtn" 
                                class="flex items-center gap-1 rounded-md bg-brand-500 px-3 py-1 text-xs font-semibold text-white transition duration-200 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-3 w-3" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                            </svg>
                            Add New
                        </button>
                    </label>
                    <select id="merk" name="merk" 
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('merk') !border-red-500 @enderror">
                        <option value="" selected>-- Select Brand --</option>
                        @foreach($merks as $m)
                            <option value="{{ $m }}" {{ old('merk') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('merk')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Select an existing brand or add a new one</p>
                </div>
                
                <!-- Product Name Field -->
                <div>
                    <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama"
                        name="nama" 
                        value="{{ old('nama') }}"
                        placeholder="e.g., Apple, Samsung, Xiaomi"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama') !border-red-500 @enderror"
                    >
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Enter the product brand name (e.g., Apple, Samsung, Oppo, Vivo)</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('pos-produk-merk.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Save Product Name
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Add New Brand/Merk -->
<div id="addMerkModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Add New Brand</h3>
            <button type="button" id="modalCloseBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label for="newMerkName" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Brand Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="newMerkName"
                    placeholder="e.g., Samsung, Apple, Xiaomi"
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                >
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Enter a unique brand name</p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" id="modalCancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">Cancel</button>
            <button type="button" id="modalConfirmBtn" class="px-4 py-2 rounded-lg bg-brand-500 text-white hover:bg-brand-600 transition font-semibold dark:bg-brand-400 dark:hover:bg-brand-300">Add Brand</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addNewMerkBtn = document.getElementById('addNewMerkBtn');
    const addMerkModal = document.getElementById('addMerkModal');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalCancelBtn = document.getElementById('modalCancelBtn');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    const newMerkName = document.getElementById('newMerkName');
    const merkSelect = document.getElementById('merk');
    
    // Open modal
    addNewMerkBtn.addEventListener('click', function(e) {
        e.preventDefault();
        addMerkModal.classList.remove('hidden');
        newMerkName.focus();
    });
    
    // Close modal
    function closeModal() {
        addMerkModal.classList.add('hidden');
        newMerkName.value = '';
    }
    
    modalCloseBtn.addEventListener('click', closeModal);
    modalCancelBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    addMerkModal.addEventListener('click', function(e) {
        if (e.target.id === 'addMerkModal') {
            closeModal();
        }
    });
    
    // Add brand and update dropdown
    modalConfirmBtn.addEventListener('click', function() {
        const merkValue = newMerkName.value.trim();
        
        if (!merkValue) {
            alert('Please enter a brand name');
            return;
        }
        
        // Check if merk already exists
        const exists = Array.from(merkSelect.options).some(opt => opt.value === merkValue);
        if (exists) {
            alert('This brand already exists');
            return;
        }
        
        // Add new option to select
        const newOption = document.createElement('option');
        newOption.value = merkValue;
        newOption.textContent = merkValue;
        newOption.selected = true;
        
        // Insert in alphabetical order
        let inserted = false;
        for (let i = 1; i < merkSelect.options.length; i++) {
            if (merkSelect.options[i].value > merkValue) {
                merkSelect.insertBefore(newOption, merkSelect.options[i]);
                inserted = true;
                break;
            }
        }
        
        if (!inserted) {
            merkSelect.appendChild(newOption);
        }
        
        closeModal();
    });
    
    // Allow Enter key to confirm
    newMerkName.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            modalConfirmBtn.click();
        }
    });
});
</script>
@endpush
