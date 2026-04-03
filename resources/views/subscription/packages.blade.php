@extends('layouts.app')

@section('title', 'Choose Your Plan')

@push('style')
<style>
    .package-card {
        transition: all 0.3s ease;
    }
    .package-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .package-card.selected {
        border-color: #4318FF !important;
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }
    .dark .package-card.selected {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    }
    .popular-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }
</style>
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        
        <!-- Header -->
        <div class="p-6 pb-0">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('settings.index', ['tab' => 'subscription']) }}" 
                   class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-navy-700 transition">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                </a>
                <div>
                    <h4 class="text-xl font-bold text-navy-700 dark:text-white">Choose Your Plan</h4>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select the plan that best fits your business needs</p>
                </div>
            </div>
        </div>

        <!-- Current Plan Info -->
        @if($currentSubscription && $currentSubscription->tipeLayanan)
            <div class="mx-6 mt-6 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <span class="font-bold">Current Plan:</span> {{ $currentSubscription->tipeLayanan->nama }}
                        @if($currentSubscription->end_date)
                            @php
                                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($currentSubscription->end_date), false);
                            @endphp
                            @if($daysLeft > 0)
                                <span class="ml-2">({{ (int)$daysLeft }} days remaining)</span>
                            @else
                                <span class="ml-2 text-red-600 dark:text-red-400 font-bold">(Expired)</span>
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- Pending Payment Notice -->
        @if($pendingPayment)
            <div class="mx-6 mt-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <span class="font-bold">Pending Payment:</span> Rp {{ number_format($pendingPayment->nominal, 0, ',', '.') }}
                        </p>
                    </div>
                    @if(($paymentMode ?? 'manual') === 'midtrans')
                        <button type="button" 
                                onclick="continuePayment({{ $pendingPayment->id }})"
                                class="rounded-xl bg-yellow-500 px-4 py-2 text-xs font-bold text-white hover:bg-yellow-600 transition">
                            Continue Payment
                        </button>
                    @else
                        <span class="rounded-xl bg-yellow-500 px-4 py-2 text-xs font-bold text-white">
                            Waiting Admin Verification
                        </span>
                    @endif
                </div>
            </div>
        @endif

        @if(($paymentMode ?? 'manual') === 'manual')
            <div class="mx-6 mt-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800/50 p-4">
                <p class="text-sm font-semibold text-sky-800 dark:text-sky-200">Manual Transfer Flow</p>
                <p class="mt-1 text-sm text-sky-700 dark:text-sky-300">
                    Pilih paket, pilih rekening tujuan, lalu upload bukti transfer. Status pembayaran akan menjadi <strong>Pending</strong> sampai diverifikasi admin.
                </p>
            </div>

            <div class="mx-6 mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                @forelse($banks as $bank)
                    <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $bank->nama_bank }}</p>
                        <p class="mt-1 text-sm font-bold text-navy-700 dark:text-white">{{ $bank->nama_rekening }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $bank->nomor_rekening }}</p>
                    </div>
                @empty
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 md:col-span-2 lg:col-span-3">
                        Data rekening tujuan belum tersedia. Hubungi admin terlebih dahulu.
                    </div>
                @endforelse
            </div>
        @endif

        @if($errors->any())
            <div class="mx-6 mt-4 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-300">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Package Cards -->
        <div class="p-6">
            @if($packages->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($packages as $index => $package)
                        @php
                            $isCurrentPlan = $currentSubscription && $currentSubscription->tipe_layanan_id == $package->id;
                            $isPopular = $index === 0; // First paid package as popular
                        @endphp
                        <div class="package-card relative rounded-2xl border-2 {{ $isCurrentPlan ? 'border-brand-500 bg-brand-50/30 dark:bg-brand-900/10' : 'border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900' }} p-6 {{ $isPopular ? 'ring-2 ring-brand-500 ring-offset-2 dark:ring-offset-navy-800' : '' }}">
                            
                            @if($isPopular)
                                <div class="popular-badge">
                                    <span class="rounded-full bg-brand-500 px-4 py-1 text-xs font-bold text-white shadow-lg">
                                        POPULAR
                                    </span>
                                </div>
                            @endif

                            @if($isCurrentPlan)
                                <div class="popular-badge">
                                    <span class="rounded-full bg-green-500 px-4 py-1 text-xs font-bold text-white shadow-lg">
                                        CURRENT PLAN
                                    </span>
                                </div>
                            @endif

                            <!-- Package Name -->
                            <div class="mb-4 mt-2 text-center">
                                <h5 class="text-xl font-bold text-navy-700 dark:text-white">{{ $package->nama }}</h5>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $package->duration_text }}</p>
                            </div>

                            <!-- Price -->
                            <div class="mb-6 text-center">
                                <div class="flex items-baseline justify-center gap-1">
                                    <span class="text-sm font-medium text-gray-500">Rp</span>
                                    <span class="text-4xl font-extrabold text-navy-700 dark:text-white">{{ number_format($package->harga, 0, ',', '.') }}</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">
                                    @if($package->durasi_satuan === 'bulan')
                                        per {{ $package->durasi }} bulan
                                    @elseif($package->durasi_satuan === 'hari')
                                        per {{ $package->durasi }} hari
                                    @elseif($package->durasi_satuan === 'tahun')
                                        per {{ $package->durasi }} tahun
                                    @endif
                                </p>
                            </div>

                            <!-- Features -->
                            @if($package->packagePermissions && $package->packagePermissions->count() > 0)
                                <div class="mb-6 space-y-2">
                                    @foreach($package->packagePermissions->take(5) as $pp)
                                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="h-4 w-4 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $pp->permission->modul ?? '' }} - {{ $pp->permission->aksi ?? '' }}
                                                @if($pp->max_records)
                                                    <span class="text-xs text-gray-400">(max {{ $pp->max_records }})</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($package->packagePermissions->count() > 5)
                                        <p class="text-xs text-gray-400 pl-6">+{{ $package->packagePermissions->count() - 5 }} more features</p>
                                    @endif
                                </div>
                            @endif

                            <!-- CTA Button -->
                            <button type="button" 
                                    onclick="{{ ($paymentMode ?? 'manual') === 'midtrans' ? 'selectPackage' : 'selectPackageManual' }}({{ $package->id }}, '{{ $package->nama }}', {{ (int)$package->harga }})"
                                    {{ $isCurrentPlan ? 'disabled' : '' }}
                                    class="w-full rounded-xl {{ $isCurrentPlan ? 'bg-gray-300 cursor-not-allowed' : ($isPopular ? 'bg-brand-500 hover:bg-brand-600 shadow-lg shadow-brand-500/30 hover:shadow-xl' : 'bg-navy-700 hover:bg-navy-800 dark:bg-brand-500 dark:hover:bg-brand-600') }} py-3 text-sm font-bold text-white transition-all duration-200">
                                @if($isCurrentPlan)
                                    Current Plan
                                @else
                                    Subscribe Now
                                @endif
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                    <h6 class="mt-4 text-lg font-bold text-navy-700 dark:text-white">No Plans Available</h6>
                    <p class="mt-2 text-sm text-gray-500">Please contact the administrator for available subscription plans.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div id="paymentModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm" style="display:none;">
    <div class="mx-4 w-full max-w-md rounded-2xl bg-white dark:bg-navy-800 p-6 shadow-2xl">
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/20">
                <svg class="h-8 w-8 text-brand-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
            </div>
            <h5 class="text-lg font-bold text-navy-700 dark:text-white">Confirm Payment</h5>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                You're subscribing to <strong id="modalPackageName" class="text-brand-500"></strong>
            </p>
            <p class="mt-1 text-2xl font-extrabold text-navy-700 dark:text-white">
                Rp <span id="modalPackagePrice"></span>
            </p>
        </div>
        @if(($paymentMode ?? 'manual') === 'midtrans')
            <div class="mt-6 flex gap-3">
                <button onclick="closeModal()" class="flex-1 rounded-xl border border-gray-200 dark:border-white/10 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-navy-700 transition">
                    Cancel
                </button>
                <button id="confirmPayBtn" onclick="processPayment()" class="flex-1 rounded-xl bg-brand-500 py-3 text-sm font-bold text-white hover:bg-brand-600 transition flex items-center justify-center gap-2">
                    <svg id="paySpinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span id="payBtnText">Pay Now</span>
                </button>
            </div>
        @else
            <form action="{{ route('subscription.checkout') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="package_id" id="manualPackageId">

                <div>
                    <label for="bank_id" class="mb-1 block text-sm font-semibold text-navy-700 dark:text-white">Transfer To</label>
                    <select name="bank_id" id="bank_id" required class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-3 text-sm text-navy-700 dark:text-white outline-none">
                        <option value="">Select Bank Account</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                {{ $bank->nama_bank }} - {{ $bank->nama_rekening }} ({{ $bank->nomor_rekening }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bukti_transfer" class="mb-1 block text-sm font-semibold text-navy-700 dark:text-white">Upload Proof</label>
                    <input type="file" name="bukti_transfer" id="bukti_transfer" required accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-900 p-3 text-sm text-navy-700 dark:text-white outline-none">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Allowed: JPG, PNG, PDF. Max size 10 MB.</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 rounded-xl border border-gray-200 dark:border-white/10 py-3 text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-navy-700 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-brand-500 py-3 text-sm font-bold text-white hover:bg-brand-600 transition">
                        Submit Transfer Proof
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if(($paymentMode ?? 'manual') === 'midtrans')
<script src="{{ \App\Services\MidtransService::getSnapUrl() }}" data-client-key="{{ \App\Services\MidtransService::getClientKey() }}"></script>
@endif

<script>
    let selectedPackageId = null;
    let selectedPackageName = '';
    let selectedPackagePrice = 0;

    function selectPackage(id, name, price) {
        selectedPackageId = id;
        selectedPackageName = name;
        selectedPackagePrice = price;

        document.getElementById('modalPackageName').textContent = name;
        document.getElementById('modalPackagePrice').textContent = new Intl.NumberFormat('id-ID').format(price);
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function selectPackageManual(id, name, price) {
        selectedPackageId = id;
        selectedPackageName = name;
        selectedPackagePrice = price;

        document.getElementById('modalPackageName').textContent = name;
        document.getElementById('modalPackagePrice').textContent = new Intl.NumberFormat('id-ID').format(price);
        const packageInput = document.getElementById('manualPackageId');
        if (packageInput) packageInput.value = id;
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('paymentModal').style.display = 'none';
        selectedPackageId = null;
    }

    function processPayment() {
        if ('{{ $paymentMode ?? 'manual' }}' !== 'midtrans') return;
        if (!selectedPackageId) return;

        const btn = document.getElementById('confirmPayBtn');
        const spinner = document.getElementById('paySpinner');
        const btnText = document.getElementById('payBtnText');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Processing...';

        fetch('{{ route("subscription.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                package_id: selectedPackageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                resetPayButton();
                return;
            }

            closeModal();

            // Open Midtrans Snap
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    window.location.href = '{{ route("subscription.payment.finish") }}?order_id=' + result.order_id + '&transaction_status=settlement';
                },
                onPending: function(result) {
                    window.location.href = '{{ route("subscription.payment.finish") }}?order_id=' + result.order_id + '&transaction_status=pending';
                },
                onError: function(result) {
                    alert('Payment failed. Please try again.');
                    resetPayButton();
                },
                onClose: function() {
                    resetPayButton();
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            resetPayButton();
        });
    }

    function continuePayment(paymentId) {
        if ('{{ $paymentMode ?? 'manual' }}' !== 'midtrans') return;
        fetch('{{ route("subscription.continue-payment") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                payment_id: paymentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    window.location.href = '{{ route("subscription.payment.finish") }}?order_id=' + result.order_id + '&transaction_status=settlement';
                },
                onPending: function(result) {
                    window.location.href = '{{ route("subscription.payment.finish") }}?order_id=' + result.order_id + '&transaction_status=pending';
                },
                onError: function(result) {
                    alert('Payment failed. Please try again.');
                },
                onClose: function() {
                    // User closed the popup
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function resetPayButton() {
        const btn = document.getElementById('confirmPayBtn');
        const spinner = document.getElementById('paySpinner');
        const btnText = document.getElementById('payBtnText');

        if (btn) btn.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (btnText) btnText.textContent = 'Pay Now';
    }

    // Close modal on outside click
    document.getElementById('paymentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
