<!-- Finance Settings Section -->
<div class="space-y-6">
    <!-- Currency Settings -->
    <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
        <h5 class="mb-4 text-base font-bold text-navy-700 dark:text-white">Currency Settings</h5>
        <p class="mb-6 text-xs text-gray-600 dark:text-gray-400">
            Select the currency for all price displays and transactions
        </p>

        @if($hasData)
            <div class="mb-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">Currency is locked</p>
                        <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-300">
                            You cannot change currency because you already have products, services, or transactions. Changing currency would cause price inconsistencies in your existing data.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <!-- IDR Option -->
            <label class="relative {{ $hasData ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                <input type="radio" name="currency" value="IDR" 
                       {{ old('currency', $settings->currency) == 'IDR' ? 'checked' : '' }}
                       {{ $hasData ? 'disabled' : '' }}
                       class="peer sr-only">
                <div class="rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-4 transition-all peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-navy-700">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 dark:bg-brand-400 text-white font-bold text-sm">
                            Rp
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">Indonesian Rupiah</p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">IDR (Rp)</p>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Example: Rp 100.000</p>
                        </div>
                        <div class="hidden peer-checked:block">
                            <svg class="h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </label>

            <!-- MYR Option -->
            <label class="relative {{ $hasData ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                <input type="radio" name="currency" value="MYR" 
                       {{ old('currency', $settings->currency) == 'MYR' ? 'checked' : '' }}
                       {{ $hasData ? 'disabled' : '' }}
                       class="peer sr-only">
                <div class="rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-4 transition-all peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-navy-700">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 dark:bg-brand-400 text-white font-bold text-sm">
                            RM
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">Malaysian Ringgit</p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">MYR (RM)</p>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Example: RM 100.00</p>
                        </div>
                        <div class="hidden peer-checked:block">
                            <svg class="h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </label>

            <!-- USD Option -->
            <label class="relative {{ $hasData ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                <input type="radio" name="currency" value="USD" 
                       {{ old('currency', $settings->currency) == 'USD' ? 'checked' : '' }}
                       {{ $hasData ? 'disabled' : '' }}
                       class="peer sr-only">
                <div class="rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-4 transition-all peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-navy-700">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 dark:bg-brand-400 text-white font-bold text-sm">
                            $
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">US Dollar</p>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">USD ($)</p>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Example: $ 100.00</p>
                        </div>
                        <div class="hidden peer-checked:block">
                            <svg class="h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        @error('currency')
            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
