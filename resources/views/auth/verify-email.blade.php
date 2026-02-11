@extends('layouts.auth')

@section('title', 'Verify Email - M iPhone Group')

@section('main')
    <div class="mt-16 mb-16 flex h-full w-full items-center justify-center px-2 md:mx-0 md:px-0 lg:mb-10 lg:items-center lg:justify-start">
        <!-- Email Verification Notice -->
        <div class="mt-[10vh] w-full max-w-full flex-col items-center md:pl-4 lg:pl-0 xl:max-w-[520px]">
            <!-- Icon -->
            <div class="mb-6 flex justify-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-yellow-100">
                    <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <h4 class="mb-3 text-center text-4xl font-bold text-navy-700">
                Verifikasi Email Anda
            </h4>
            <p class="mb-6 text-center text-base text-gray-600">
                Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan ke email Anda?
            </p>

            @if (session('message'))
                <div class="mb-6 rounded-xl border-l-4 border-green-500 bg-green-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('message') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Resend Button with Cooldown -->
            <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                @csrf
                <button 
                    type="submit"
                    id="resendBtn"
                    class="w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-3.5 text-base font-semibold text-white shadow-lg shadow-brand-500/30 transition-all duration-200 hover:shadow-xl hover:shadow-brand-500/40 hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:scale-100"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span id="resendBtnText">Kirim Ulang Email Verifikasi</span>
                </button>
            </form>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button 
                    type="submit" 
                    class="w-full rounded-xl border-2 border-gray-200 bg-white py-3.5 text-base font-semibold text-gray-700 transition-all duration-200 hover:bg-gray-50 hover:border-gray-300"
                >
                    Keluar
                </button>
            </form>

            <!-- Check Email -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Email dikirim ke: <strong class="text-navy-700">{{ auth()->user()->email }}</strong>
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resendForm = document.getElementById('resendForm');
            const resendBtn = document.getElementById('resendBtn');
            const resendBtnText = document.getElementById('resendBtnText');
            const cooldownKey = 'resendEmailCooldown';
            const cooldownSeconds = 60; // 60 seconds cooldown

            // Check if cooldown is active when page loads
            const lastResendTime = localStorage.getItem(cooldownKey);
            if (lastResendTime) {
                const elapsed = Math.floor((Date.now() - parseInt(lastResendTime)) / 1000);
                if (elapsed < cooldownSeconds) {
                    disableResendButton(cooldownSeconds - elapsed);
                }
            }

            function disableResendButton(remainingSeconds) {
                resendBtn.disabled = true;
                updateButtonText(remainingSeconds);
            }

            function enableResendButton() {
                resendBtn.disabled = false;
                resendBtnText.textContent = 'Kirim Ulang Email Verifikasi';
            }

            function updateButtonText(seconds) {
                resendBtnText.textContent = `Tunggu ${seconds}s`;
            }

            // Handle form submission with AJAX
            resendForm.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                // Set cooldown immediately
                localStorage.setItem(cooldownKey, Date.now().toString());
                disableResendButton(cooldownSeconds);

                // Prepare form data
                const formData = new FormData(resendForm);

                // Send AJAX request
                fetch(resendForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.text())
                .then(data => {
                    // Show success message
                    showSuccessMessage('Email verifikasi berhasil dikirim ulang!');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Terjadi kesalahan saat mengirim email.');
                });

                // Update countdown every second
                let remaining = cooldownSeconds;
                const interval = setInterval(function () {
                    remaining--;
                    if (remaining > 0) {
                        updateButtonText(remaining);
                    } else {
                        clearInterval(interval);
                        enableResendButton();
                    }
                }, 1000);
            });

            function showSuccessMessage(message) {
                // Remove existing alerts
                const existingAlert = document.querySelector('[role="alert"]');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Create success alert
                const alert = document.createElement('div');
                alert.className = 'mb-6 rounded-xl border-l-4 border-green-500 bg-green-50 p-4';
                alert.role = 'alert';
                alert.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">${message}</p>
                        </div>
                    </div>
                `;

                // Insert alert after heading
                const heading = document.querySelector('h4');
                heading.parentNode.insertBefore(alert, heading.nextSibling.nextSibling);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }

            function showErrorMessage(message) {
                // Remove existing alerts
                const existingAlert = document.querySelector('[role="alert"]');
                if (existingAlert) {
                    existingAlert.remove();
                }

                // Create error alert
                const alert = document.createElement('div');
                alert.className = 'mb-6 rounded-xl border-l-4 border-red-500 bg-red-50 p-4';
                alert.role = 'alert';
                alert.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">${message}</p>
                        </div>
                    </div>
                `;

                // Insert alert after heading
                const heading = document.querySelector('h4');
                heading.parentNode.insertBefore(alert, heading.nextSibling.nextSibling);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }
        });
    </script>
@endpush
