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
</div>
