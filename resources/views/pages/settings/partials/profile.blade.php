<!-- Profile Settings Section -->
<div class="space-y-6">
    <!-- Profile Information -->
    <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
        <h5 class="mb-4 text-base font-bold text-navy-700 dark:text-white">Profile Information</h5>
        <p class="mb-6 text-xs text-gray-600 dark:text-gray-400">
            Update your account's profile information and email address.
        </p>

        <div class="space-y-4">
            <!-- Name -->
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', auth()->user()->nama) }}"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-navy-700 outline-none transition-all placeholder:text-gray-400 focus:border-brand-500 dark:border-white/10 dark:bg-navy-900 dark:text-white dark:focus:border-brand-400"
                    placeholder="Enter your full name">
                @error('name')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', auth()->user()->email) }}"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-navy-700 outline-none transition-all placeholder:text-gray-400 focus:border-brand-500 dark:border-white/10 dark:bg-navy-900 dark:text-white dark:focus:border-brand-400"
                    placeholder="Enter your email address">
                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                    Changing your email will require verification. A confirmation link will be sent to your new email.
                </p>
                @error('email')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role (Read-only) -->
            <div>
                <label class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Role
                </label>
                <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:border-white/10 dark:bg-navy-900/50 dark:text-gray-400">
                    {{ auth()->user()->role ? auth()->user()->role->nama : 'No Role Assigned' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
        <h5 class="mb-4 text-base font-bold text-navy-700 dark:text-white">Change Password</h5>
        <p class="mb-6 text-xs text-gray-600 dark:text-gray-400">
            Ensure your account is using a long, random password to stay secure.
        </p>

        <div class="space-y-4">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Current Password
                </label>
                <input 
                    type="password" 
                    id="current_password" 
                    name="current_password"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-navy-700 outline-none transition-all placeholder:text-gray-400 focus:border-brand-500 dark:border-white/10 dark:bg-navy-900 dark:text-white dark:focus:border-brand-400"
                    placeholder="Enter current password">
                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                    Required only if you're changing your password.
                </p>
                @error('current_password')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    New Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-navy-700 outline-none transition-all placeholder:text-gray-400 focus:border-brand-500 dark:border-white/10 dark:bg-navy-900 dark:text-white dark:focus:border-brand-400"
                    placeholder="Enter new password">
                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                    Minimum 8 characters. Leave empty if you don't want to change it.
                </p>
                @error('password')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                    Confirm Password
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-navy-700 outline-none transition-all placeholder:text-gray-400 focus:border-brand-500 dark:border-white/10 dark:bg-navy-900 dark:text-white dark:focus:border-brand-400"
                    placeholder="Confirm new password">
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Account Security</p>
                <ul class="mt-1 text-xs text-blue-700 dark:text-blue-300 space-y-1 list-disc list-inside">
                    <li>Use a strong password that you don't use elsewhere.</li>
                    <li>Changing your email requires verification for security.</li>
                    <li>Leave password fields empty if you don't want to change it.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
