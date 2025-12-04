@extends('layouts.auth')

@section('title', 'Login M iPhone Group')

@section('main')
    <div class="mt-16 mb-16 flex h-full w-full items-center justify-center px-2 md:mx-0 md:px-0 lg:mb-10 lg:items-center lg:justify-start">
        <!-- Sign in section -->
        <div class="mt-[10vh] w-full max-w-full flex-col items-center md:pl-4 lg:pl-0 xl:max-w-[420px]">
            <h4 class="mb-2.5 text-4xl font-bold text-navy-700">
                Sign In
            </h4>
            <p class="mb-9 ml-1 text-base text-gray-600">
                Enter your email and password to sign in!
            </p>

            <!-- Divider -->
            <div class="mb-6 flex items-center gap-3">
                <div class="h-px w-full bg-gray-200"></div>
                <p class="text-base text-gray-600">Welcome</p>
                <div class="h-px w-full bg-gray-200"></div>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="ml-1.5 text-sm font-medium text-navy-700">
                        Email*
                    </label>
                    <input 
                        id="email" 
                        type="email"
                        name="email" 
                        placeholder="mail@example.com"
                        value="{{ old('email') }}"
                        class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border bg-white/0 p-3 text-sm outline-none @error('email') border-red-500 text-red-500 placeholder:text-red-500 @else border-gray-200 @enderror"
                        autofocus
                    >
                    @error('email')
                        <p class="ml-1.5 mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="ml-1.5 text-sm font-medium text-navy-700">
                        Password*
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            type="password"
                            name="password" 
                            placeholder="Min. 8 characters"
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border bg-white/0 p-3 pr-12 text-sm outline-none @error('password') border-red-500 text-red-500 placeholder:text-red-500 @else border-gray-200 @enderror"
                        >
                        <button 
                            type="button" 
                            class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 mt-1 text-gray-400 hover:text-gray-600"
                            data-target="#password"
                        >
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="ml-1.5 mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot Password -->
                <div class="mb-4 flex items-center justify-between px-2">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember"
                            class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500"
                        >
                        <label for="remember" class="ml-2 text-sm font-medium text-navy-700">
                            Keep me logged in
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                        Forgot Password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="mt-2 w-full rounded-xl bg-brand-500 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700"
                >
                    Sign In
                </button>

                <!-- Register Link -->
                <div class="mt-4">
                    <span class="text-sm font-medium text-navy-700">
                        Not registered yet?
                    </span>
                    <a href="{{ route('register') }}" class="ml-1 text-sm font-medium text-brand-500 hover:text-brand-600">
                        Create an account
                    </a>
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
