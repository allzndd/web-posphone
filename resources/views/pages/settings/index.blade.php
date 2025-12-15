@extends('layouts.app')

@section('title', 'Settings')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Settings Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <!-- Header -->
        <div class="mb-6 border-b border-gray-200 dark:border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-500 dark:bg-brand-400">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 00.12-.61l-1.92-3.32a.488.488 0 00-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 00-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58a.49.49 0 00-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-navy-700 dark:text-white">Settings</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your application preferences</p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Currency Section -->
            <div class="mb-8">
                <h5 class="mb-4 text-lg font-bold text-navy-700 dark:text-white border-l-4 border-brand-500 pl-3">Currency Settings</h5>
                
                <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
                    <label for="currency" class="mb-3 block text-sm font-bold text-navy-700 dark:text-white">
                        Default Currency <span class="text-red-500">*</span>
                    </label>
                    <p class="mb-4 text-xs text-gray-600 dark:text-gray-400">
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
                            <div class="rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 p-4 transition-all peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-navy-700/100">
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
                            <div class="rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 p-4 transition-all peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-navy-700/100">
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
                            <div class="rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 p-4 transition-all peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-navy-700/100">
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

            <!-- Info Box -->
            <div class="mb-6 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Currency Format</p>
                        <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">
                            All prices throughout the application will be displayed in your selected currency format with appropriate thousand separators.
                        </p>
                    </div>
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <button type="submit" 
                        {{ $hasData ? 'disabled' : '' }}
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
