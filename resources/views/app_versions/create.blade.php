@extends('layouts.app')

@section('title', 'Create App Version')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create New App Version</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new app version</p>
            </div>
            <a href="{{ route('app-version.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('app-version.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5">
                
                <!-- Platform Field -->
                <div>
                    <label for="platform" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Platform <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="platform"
                        name="platform" 
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('platform') !border-red-500 @enderror"
                        autofocus
                        required
                    >
                        <option value="" disabled selected>Select a platform...</option>
                        <option value="Android" {{ old('platform') == 'Android' ? 'selected' : '' }}>Android</option>
                        <option value="iOS" {{ old('platform') == 'iOS' ? 'selected' : '' }}>iOS</option>
                    </select>
                    @error('platform')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Select the platform (Android or iOS)</p>
                </div>

                <!-- Latest & Minimum Version Row -->
                <div class="grid grid-cols-2 gap-5">
                    <!-- Latest Version Field -->
                    <div>
                        <label for="latest_version" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Latest Version <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="latest_version"
                            name="latest_version" 
                            value="{{ old('latest_version') }}"
                            placeholder="e.g., 1.0.0, 2.1.5"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('latest_version') !border-red-500 @enderror"
                            required
                        >
                        @error('latest_version')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">The current latest version available in app stores</p>
                    </div>

                    <!-- Minimum Version Field -->
                    <div>
                        <label for="minimum_version" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Minimum Version <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="minimum_version"
                            name="minimum_version" 
                            value="{{ old('minimum_version') }}"
                            placeholder="e.g., 0.9.0, 1.5.0"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('minimum_version') !border-red-500 @enderror"
                            required
                        >
                        @error('minimum_version')
                            <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">The minimum version required to access the app</p>
                    </div>
                </div>

                <!-- Maintenance Mode Checkbox -->
                <div class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900/50 p-4">
                    <input 
                        type="checkbox" 
                        id="maintenance_mode"
                        name="maintenance_mode" 
                        value="1"
                        {{ old('maintenance_mode') ? 'checked' : '' }}
                        class="mt-1 h-5 w-5 cursor-pointer rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-900 dark:checked:bg-brand-500"
                    >
                    <div class="flex-1">
                        <label for="maintenance_mode" class="text-sm font-bold text-navy-700 dark:text-white cursor-pointer">
                            Enable Maintenance Mode
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">Disable app access for maintenance and display a maintenance message</p>
                    </div>
                </div>

                <!-- Maintenance Message Field -->
                <div>
                    <label for="maintenance_message" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Maintenance Message
                    </label>
                    <textarea 
                        id="maintenance_message"
                        name="maintenance_message" 
                        placeholder="Message to display when maintenance mode is on"
                        rows="4"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('maintenance_message') !border-red-500 @enderror"
                    >{{ old('maintenance_message') }}</textarea>
                    @error('maintenance_message')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">E.g., "We're performing maintenance. Please try again later."</p>
                </div>

                <!-- Store URL Field -->
                <div>
                    <label for="store_url" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Store URL
                    </label>
                    <input 
                        type="url" 
                        id="store_url"
                        name="store_url" 
                        value="{{ old('store_url') }}"
                        placeholder="https://example.com/app or https://apps.apple.com/..."
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('store_url') !border-red-500 @enderror"
                    >
                    @error('store_url')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Link to download the app from store (Apple App Store, Google Play, etc.)</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('app-version.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Save App Version
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on platform field
    document.getElementById('platform').focus();
});
</script>
@endpush
