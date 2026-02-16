@extends('layouts.auth')

@section('title', 'Reset Password')

@section('main')
    <div class="mt-16 mb-16 flex h-full w-full items-center justify-center px-2 md:mx-0 md:px-0 lg:mb-10 lg:items-center lg:justify-start">
        <!-- Reset Password section -->
        <div class="mt-[10vh] w-full max-w-full flex-col items-center md:pl-4 lg:pl-0 xl:max-w-[460px]">
            <h4 class="mb-3 text-4xl font-bold text-navy-700">
                Reset Password
            </h4>
            <p class="mb-8 text-base text-gray-500">
                Masukkan password baru Anda untuk reset akun.
            </p>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="ml-1.5 text-sm font-semibold text-navy-700">
                        Email Address
                    </label>
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Masukkan email Anda"
                            value="{{ old('email', $request->email) }}"
                            class="h-12 w-full pl-10 pr-4 rounded-xl border-2 bg-gray-100 text-sm outline-none transition-all border-gray-300 text-gray-600 cursor-not-allowed"
                            readonly
                        >
                    </div>
                    @error('email')
                        <p class="ml-1.5 mt-2 text-sm text-red-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Baru -->
                <div class="mb-4">
                    <label for="password" class="ml-1.5 text-sm font-semibold text-navy-700">
                        Password Baru
                    </label>
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Masukkan password baru"
                            class="h-12 w-full pl-10 pr-12 rounded-xl border-2 bg-white text-sm outline-none transition-all @error('password') border-red-500 text-red-500 placeholder:text-red-400 focus:border-red-600 @else border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @enderror"
                            required
                        >
                        <button
                            type="button"
                            class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-500 transition-colors"
                            data-target="#password"
                        >
                            <i class="far fa-eye text-lg"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="ml-1.5 mt-2 text-sm text-red-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="ml-1.5 text-sm font-semibold text-navy-700">
                        Konfirmasi Password
                    </label>
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Konfirmasi password baru"
                            class="h-12 w-full pl-10 pr-12 rounded-xl border-2 bg-white text-sm outline-none transition-all @error('password_confirmation') border-red-500 text-red-500 placeholder:text-red-400 focus:border-red-600 @else border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @enderror"
                            required
                        >
                        <button
                            type="button"
                            class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-500 transition-colors"
                            data-target="#password_confirmation"
                        >
                            <i class="far fa-eye text-lg"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="ml-1.5 mt-2 text-sm text-red-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="linear mt-2 w-full rounded-xl bg-brand-500 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 disabled:opacity-50"
                >
                    Reset Password
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                        Kembali ke Login
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                const icon = this.querySelector('i');

                if (target.type === 'password') {
                    target.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    target.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
@endsection
