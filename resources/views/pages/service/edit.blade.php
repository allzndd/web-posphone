@extends('layouts.app')

@section('title', 'Edit Service')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('service.index') }}" 
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-navy-700 dark:text-gray-400 dark:hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Services
        </a>
    </div>

    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="border-b border-gray-200 dark:border-white/10 p-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Service</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update service information</p>
        </div>

        <!-- Form -->
        <form action="{{ route('service.update', $service->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Service Name -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Service Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $service->nama) }}" required maxlength="45"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('nama') border-red-500 @enderror"
                           placeholder="e.g., Screen Repair, Battery Replacement">
                    @error('nama')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Store <span class="text-red-500">*</span>
                    </label>
                    <select name="pos_toko_id" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('pos_toko_id') border-red-500 @enderror">
                        <option value="">Select Store</option>
                        @foreach($tokos as $toko)
                            <option value="{{ $toko->id }}" {{ old('pos_toko_id', $service->pos_toko_id) == $toko->id ? 'selected' : '' }}>
                                {{ $toko->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('pos_toko_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Customer (Optional) -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Customer <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <select name="pos_pelanggan_id"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('pos_pelanggan_id') border-red-500 @enderror">
                        <option value="">Select Customer</option>
                        @foreach($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->id }}" {{ old('pos_pelanggan_id', $service->pos_pelanggan_id) == $pelanggan->id ? 'selected' : '' }}>
                                {{ $pelanggan->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('pos_pelanggan_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ get_currency_symbol() }}</span>
                        </div>
                        <input type="text" id="harga" name="harga" value="{{ old('harga', $service->harga) }}" required inputmode="numeric"
                               class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('harga') border-red-500 @enderror"
                               placeholder="0{{ get_decimal_places() > 0 ? '.' . str_repeat('0', get_decimal_places()) : '' }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">Currency: {{ get_currency() }}</p>
                    @error('harga')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration (in minutes) -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Duration (minutes) <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="number" name="durasi" value="{{ old('durasi', $service->durasi) }}" min="0"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('durasi') border-red-500 @enderror"
                           placeholder="e.g., 30, 60, 120">
                    @error('durasi')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Description <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $service->keterangan) }}" maxlength="45"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 @error('keterangan') border-red-500 @enderror"
                           placeholder="Brief description">
                    @error('keterangan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('service.index') }}" 
                   class="rounded-xl border border-gray-200 dark:border-white/10 px-6 py-3 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-white/5">
                    Cancel
                </a>
                <button type="submit" 
                        class="rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Update Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currency = '{{ get_currency() }}';
    
    function formatCurrencyDisplay(value) {
        // Remove all non-numeric characters
        let cleanValue = value.replace(/[^0-9]/g, '');
        
        if (!cleanValue || cleanValue === '') return '';
        
        // Format with thousands separator only, no decimals
        if (currency === 'IDR') {
            return parseInt(cleanValue).toLocaleString('id-ID');
        } else {
            return parseInt(cleanValue).toLocaleString('en-US');
        }
    }
    
    function unformatCurrency(displayValue) {
        // Remove all formatting characters, keep only numbers
        let cleaned = displayValue.replace(/[^0-9]/g, '');
        return cleaned || '0';
    }
    
    const priceInput = document.querySelector('#harga');
    if (priceInput) {
        // Format the initial value on load for readability
        if (priceInput.value) {
            priceInput.value = formatCurrencyDisplay(priceInput.value);
        }
        
        priceInput.addEventListener('input', function(e) {
            // Store cursor position
            let cursorPos = this.selectionStart;
            let oldValue = this.value;
            let oldLength = oldValue.length;
            
            // Remove invalid characters, keep only numbers
            let cleanValue = this.value.replace(/[^0-9]/g, '');
            
            // Don't format if empty
            if (!cleanValue) {
                this.value = '';
                return;
            }
            
            // Apply formatting
            const formatted = formatCurrencyDisplay(cleanValue);
            
            if (formatted && formatted !== '0') {
                this.value = formatted;
                
                // Adjust cursor position based on added characters (commas)
                const newLength = formatted.length;
                const diff = newLength - oldLength;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            } else {
                this.value = cleanValue;
            }
        });
        
        priceInput.addEventListener('blur', function() {
            // Final formatting on blur
            if (this.value) {
                this.value = formatCurrencyDisplay(this.value);
            }
        });
        
        priceInput.closest('form').addEventListener('submit', function() {
            // Convert back to cents/integer for database
            if (priceInput.value) {
                priceInput.value = unformatCurrency(priceInput.value);
            }
        });
    }
});
</script>
@endpush
