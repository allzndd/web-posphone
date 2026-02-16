@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('main')
    <div class="mt-16 mb-16 flex h-full w-full items-center justify-center px-2 md:mx-0 md:px-0 lg:mb-10 lg:items-center lg:justify-start">
        <!-- Forgot Password section -->
        <div class="mt-[10vh] w-full max-w-full flex-col items-center md:pl-4 lg:pl-0 xl:max-w-[460px]">
            <h4 class="mb-3 text-4xl font-bold text-navy-700">
                Lupa Password?
            </h4>
            <p class="mb-8 text-base text-gray-500">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
            </p>

            @if (session('status'))
                <div class="mb-6 w-full rounded-xl border-2 border-green-200 bg-green-50 p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 flex-shrink-0 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
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
                            value="{{ old('email') }}"
                            class="h-12 w-full pl-10 pr-4 rounded-xl border-2 bg-white text-sm outline-none transition-all @error('email') border-red-500 text-red-500 placeholder:text-red-400 focus:border-red-600 @else border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @enderror"
                            required
                            autofocus
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

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="linear mt-2 w-full rounded-xl bg-brand-500 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 disabled:opacity-50"
                >
                    Kirim Link Reset Password
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    Ingat password Anda?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                        Kembali ke Login
                    </a>
                </p>
            </div>
        </div>
    </div>
@endsection
