@extends('layouts.app')

@section('title', 'Edit User')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="p-4 md:p-6">
    <!-- Back Button & Header -->
    <div class="mb-5">
        <a href="{{ route('user.index') }}" 
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-brand-500 dark:hover:text-brand-400 transition-colors mb-4">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                <path fill="none" d="M0 0h24v24H0z"></path>
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
            </svg>
            Back to Users
        </a>
        <h3 class="text-2xl font-bold text-navy-700 dark:text-white mb-2">Edit User</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Update the information for {{ $user->name }}'s account.
        </p>
    </div>

    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <form action="{{ route('user.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- User Info Badge -->
            <div class="mb-6 flex items-center gap-4 rounded-xl bg-lightPrimary dark:bg-navy-700 p-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 dark:bg-brand-400">
                    <span class="text-xl font-bold text-white">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $user->name }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                        Member since {{ $user->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>

            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                <!-- Name Field -->
                <div class="md:col-span-2">
                    <label for="name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        placeholder="Enter full name"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 @error('name') !border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            value="{{ old('email', $user->email) }}"
                            placeholder="user@example.com"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 @error('email') !border-red-500 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Phone Number
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            id="phone"
                            name="phone" 
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="0812-3456-7890"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        >
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        New Password
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password"
                            name="password"
                            placeholder="Leave blank to keep current"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 @error('password') !border-red-500 @enderror"
                        >
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Leave empty to keep current password</p>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Re-enter new password"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400"
                        >
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="md:col-span-2">
                    <label class="mb-3 block text-sm font-bold text-navy-700 dark:text-white">
                        User Role <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Owner Option -->
                        <label class="relative flex cursor-pointer items-center justify-between rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-4 transition-all hover:border-brand-500 dark:hover:border-brand-400 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 dark:has-[:checked]:border-brand-400 dark:has-[:checked]:bg-navy-700">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500 dark:bg-brand-400">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-navy-700 dark:text-white">Owner</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Full system access</p>
                                </div>
                            </div>
                            <input type="radio" name="roles" value="OWNER" {{ old('roles', $user->roles) == 'OWNER' ? 'checked' : '' }} 
                                   class="h-5 w-5 text-brand-500 focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400">
                        </label>

                        <!-- Admin Option -->
                        <label class="relative flex cursor-pointer items-center justify-between rounded-xl border-2 border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-4 transition-all hover:border-brand-500 dark:hover:border-brand-400 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 dark:has-[:checked]:border-brand-400 dark:has-[:checked]:bg-navy-700">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 dark:bg-blue-400">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-navy-700 dark:text-white">Admin</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Limited access</p>
                                </div>
                            </div>
                            <input type="radio" name="roles" value="ADMIN" {{ old('roles', $user->roles) == 'ADMIN' ? 'checked' : '' }} 
                                   class="h-5 w-5 text-brand-500 focus:ring-2 focus:ring-brand-500 dark:focus:ring-brand-400">
                        </label>
                    </div>
                    @error('roles')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('user.index') }}" 
                   class="rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-navy-900 px-6 py-3 text-sm font-bold text-navy-700 dark:text-white transition duration-200 hover:bg-gray-50 dark:hover:bg-navy-800">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                    </svg>
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Page-specific scripts -->
@endpush
