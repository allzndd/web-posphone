@extends('layouts.auth')

@section('title', 'Login M iPhone Group')

@section('main')
    <div class="mt-16 mb-16 flex h-full w-full items-center justify-center px-2 md:mx-0 md:px-0 lg:mb-10 lg:items-center lg:justify-start">
        <!-- Sign in section -->
        <div class="mt-[10vh] w-full max-w-full flex-col items-center md:pl-4 lg:pl-0 xl:max-w-[460px]">
            <h4 class="mb-3 text-4xl font-bold text-navy-700">
                Welcome Back
            </h4>
            <p class="mb-8 text-base text-gray-500">
                Sign in to access your dashboard and manage your business
            </p>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="ml-1.5 text-sm font-semibold text-navy-700">
                        Email Address
                    </label>
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input 
                            id="email" 
                            type="email"
                            name="email" 
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            class="h-12 w-full pl-10 pr-4 rounded-xl border-2 bg-white text-sm outline-none transition-all @error('email') border-red-500 text-red-500 placeholder:text-red-400 focus:border-red-600 @else border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @enderror"
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

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="ml-1.5 text-sm font-semibold text-navy-700">
                        Password
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
                            placeholder="Enter your password"
                            class="h-12 w-full pl-10 pr-12 rounded-xl border-2 bg-white text-sm outline-none transition-all @error('password') border-red-500 text-red-500 placeholder:text-red-400 focus:border-red-600 @else border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @enderror"
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

                <!-- Remember & Forgot Password -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember"
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 focus:ring-2"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">
                            Remember me
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                        Forgot Password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-3.5 text-base font-semibold text-white shadow-lg shadow-brand-500/30 transition-all duration-200 hover:shadow-xl hover:shadow-brand-500/40 hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2"
                >
                    <span>Sign In</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>

                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="font-semibold text-brand-500 hover:text-brand-600 transition-colors">
                            Sign up for free
                        </a>
                    </p>
                </div>
                
                <!-- Divider -->
                <div class="mt-8 flex items-center gap-3">
                    <div class="h-px w-full bg-gray-200"></div>
                    <p class="text-xs text-gray-400">Secure Login</p>
                    <div class="h-px w-full bg-gray-200"></div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-password').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var input = document.querySelector(btn.getAttribute('data-target'));
                    if (!input) return;
                    var show = input.getAttribute('type') === 'password';
                    input.setAttribute('type', show ? 'text' : 'password');
                    var icon = btn.querySelector('i');
                    if (icon){
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            });
        });
    </script>
@endpush
