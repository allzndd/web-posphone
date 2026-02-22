@extends('layouts.app')

@section('title', 'Create Store')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create New Store</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new store/outlet location</p>
            </div>
            <a href="{{ route('toko.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Free Tier Warning -->
        @if($isFreeTier)
        <div class="mb-6 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 p-4">
            <div class="flex items-start gap-3">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-blue-800 dark:text-blue-300">Paket Free Tier</h3>
                    <p class="mt-1 text-sm text-blue-700 dark:text-blue-400">Anda saat ini menggunakan paket <strong>Free Tier</strong> yang hanya membolehkan <strong>1 toko</strong>. Untuk menambah lebih banyak toko, silakan upgrade paket Anda.</p>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('toko.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                <!-- Store Name Field -->
                <div class="md:col-span-2">
                    <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Store Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama"
                        name="nama" 
                        value="{{ old('nama') }}"
                        placeholder="Enter store name"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama') !border-red-500 @enderror"
                        autofocus
                    >
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Field -->
                <div class="md:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Address
                    </label>
                    <textarea 
                        id="alamat"
                        name="alamat" 
                        rows="3"
                        placeholder="Enter store address"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                    >{{ old('alamat') }}</textarea>
                </div>

                <!-- Modal (Capital) Field -->
                <div class="md:col-span-2">
                    <label for="modal" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Capital (Modal)
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ get_currency_symbol() }}</span>
                        </div>
                        <input 
                            type="text" 
                            id="modal"
                            name="modal" 
                            value="{{ old('modal') }}"
                            placeholder="0{{ get_decimal_places() > 0 ? '.' . str_repeat('0', get_decimal_places()) : '' }}"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('modal') !border-red-500 @enderror"

                        >
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Enter the initial capital/fund for this store</p>
                    @error('modal')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('toko.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    Create Store
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const currency = '{{ get_currency() }}';

    function formatThousands(rawDigits) {
        // rawDigits = string of digits only (no separators)
        if (!rawDigits) return '';
        if (currency === 'IDR') {
            // Use dot as thousand separator (Indonesian locale)
            return parseInt(rawDigits, 10).toLocaleString('id-ID');
        } else {
            // Use comma as thousand separator (US locale)
            return rawDigits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    }

    function formatInitialValue(rawValue) {
        // Called on page load with plain numeric string from DB / old()
        if (!rawValue || rawValue === '0.00' || rawValue === '0') return '';
        if (currency === 'IDR') {
            let num = parseInt(rawValue, 10);
            return isNaN(num) ? '' : num.toLocaleString('id-ID');
        } else {
            let num = parseFloat(rawValue);
            return isNaN(num) ? '' : num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function getRawValue(formattedValue) {
        // Strip all formatting separators to get clean numeric string for submission
        if (currency === 'IDR') {
            return formattedValue.replace(/\./g, '');
        } else {
            return formattedValue.replace(/,/g, '');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-focus on name field
        document.getElementById('nama').focus();

        const modalInput = document.getElementById('modal');
        if (!modalInput) return;

        // Format the initial value from old() if any
        if (modalInput.value) {
            modalInput.value = formatInitialValue(modalInput.value);
        }

        modalInput.addEventListener('input', function() {
            const selStart = this.selectionStart;
            const oldLen   = this.value.length;

            if (currency === 'IDR') {
                // Strip everything except digits
                let digits = this.value.replace(/\D/g, '');
                if (!digits) { this.value = ''; return; }
                let formatted = formatThousands(digits);
                this.value = formatted;
                // Reposition cursor accounting for added/removed separators
                let diff = formatted.length - oldLen;
                let newPos = Math.max(0, selStart + diff);
                this.setSelectionRange(newPos, newPos);
            } else {
                // USD / MYR: allow digits + one decimal point
                let clean = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1');
                if (!clean) { this.value = ''; return; }
                let parts = clean.split('.');
                let intPart = parts[0] ? formatThousands(parts[0]) : '';
                this.value = parts.length > 1 ? intPart + '.' + parts[1] : intPart;
                let diff = this.value.length - oldLen;
                let newPos = Math.max(0, selStart + diff);
                this.setSelectionRange(newPos, newPos);
            }
        });

        modalInput.addEventListener('blur', function() {
            if (!this.value) return;
            if (currency === 'USD' || currency === 'MYR') {
                // On blur: enforce 2 decimal places
                let raw = this.value.replace(/,/g, '');
                let num = parseFloat(raw);
                if (!isNaN(num)) {
                    this.value = num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        });

        // Strip formatting before submit so server receives plain number
        const mainForm = modalInput.closest('form');
        if (mainForm) {
            mainForm.addEventListener('submit', function() {
                modalInput.value = getRawValue(modalInput.value);
            });
        }
    });
})();
</script>
@endpush
