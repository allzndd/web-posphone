<!-- Subscription Settings Section -->
<div class="space-y-6">
    <!-- Current Subscription Status -->
    <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
        <h5 class="mb-4 text-base font-bold text-navy-700 dark:text-white">Active Subscription</h5>
        <p class="mb-6 text-xs text-gray-600 dark:text-gray-400">
            Your current subscription plan and usage information
        </p>

        @if($subscription)
            @php
                $daysLeft = $subscription->end_date 
                    ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription->end_date), false) 
                    : null;
                $isExpired = $daysLeft !== null && $daysLeft <= 0;
                $isFreeTier = $subscription->tipeLayanan && $subscription->tipeLayanan->harga <= 0 && !$subscription->is_trial;
                $isTrial = $subscription->is_trial == 1;
            @endphp

            <div class="rounded-xl border-2 {{ $isExpired ? 'border-red-300 bg-red-50 dark:border-red-800 dark:bg-red-900/20' : ($isTrial ? 'border-yellow-300 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20' : ($isFreeTier ? 'border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50' : 'border-green-300 bg-green-50 dark:border-green-800 dark:bg-green-900/20')) }} p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <!-- Package Icon -->
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl {{ $isExpired ? 'bg-red-500' : ($isTrial ? 'bg-yellow-500' : ($isFreeTier ? 'bg-gray-500' : 'bg-brand-500')) }}">
                            @if($isTrial)
                                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            @elseif($isFreeTier)
                                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.74 0-2.2-1.88-2.95-3.65-3.45z"/></svg>
                            @else
                                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h6 class="text-lg font-bold text-navy-700 dark:text-white">
                                    {{ $subscription->tipeLayanan->nama ?? 'Unknown Package' }}
                                </h6>
                                @if($isTrial)
                                    <span class="rounded-full bg-yellow-500 px-2.5 py-0.5 text-xs font-bold text-white">TRIAL</span>
                                @elseif($isFreeTier)
                                    <span class="rounded-full bg-gray-500 px-2.5 py-0.5 text-xs font-bold text-white">FREE</span>
                                @elseif($isExpired)
                                    <span class="rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-bold text-white">EXPIRED</span>
                                @else
                                    <span class="rounded-full bg-green-500 px-2.5 py-0.5 text-xs font-bold text-white">ACTIVE</span>
                                @endif
                            </div>

                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <div>
                                    <span class="font-medium">Start:</span> 
                                    {{ $subscription->started_date ? \Carbon\Carbon::parse($subscription->started_date)->format('d M Y') : '-' }}
                                </div>
                                <div>
                                    <span class="font-medium">End:</span> 
                                    {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') : 'Unlimited' }}
                                </div>
                                <div>
                                    @if($daysLeft !== null)
                                        @if($isExpired)
                                            <span class="font-bold text-red-600 dark:text-red-400">Expired {{ abs((int)$daysLeft) }} days ago</span>
                                        @elseif($daysLeft <= 3)
                                            <span class="font-bold text-red-600 dark:text-red-400">{{ (int)$daysLeft }} days remaining</span>
                                        @elseif($daysLeft <= 7)
                                            <span class="font-bold text-yellow-600 dark:text-yellow-400">{{ (int)$daysLeft }} days remaining</span>
                                        @else
                                            <span class="font-medium text-green-600 dark:text-green-400">{{ (int)$daysLeft }} days remaining</span>
                                        @endif
                                    @else
                                        <span class="font-medium text-gray-500">Unlimited</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress bar for days remaining -->
                @if($subscription->started_date && $subscription->end_date && !$isFreeTier)
                    @php
                        $totalDays = \Carbon\Carbon::parse($subscription->started_date)->diffInDays(\Carbon\Carbon::parse($subscription->end_date));
                        $elapsed = \Carbon\Carbon::parse($subscription->started_date)->diffInDays(\Carbon\Carbon::now());
                        $percentage = $totalDays > 0 ? min(100, ($elapsed / $totalDays) * 100) : 100;
                    @endphp
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                            <span>Usage</span>
                            <span>{{ round($percentage) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-navy-700">
                            <div class="h-2 rounded-full transition-all duration-500 {{ $percentage > 80 ? 'bg-red-500' : ($percentage > 60 ? 'bg-yellow-500' : 'bg-brand-500') }}"
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Warning for expiring soon -->
            @if($daysLeft !== null && $daysLeft <= 7 && $daysLeft > 0)
                <div class="mt-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-yellow-800 dark:text-yellow-200">Subscription Expiring Soon!</p>
                            <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-300">
                                Your subscription will expire in <strong>{{ (int)$daysLeft }} days</strong>. 
                                Upgrade or renew now to avoid service interruption.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- No subscription -->
            <div class="rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <h6 class="mt-3 text-base font-bold text-navy-700 dark:text-white">No Active Subscription</h6>
                <p class="mt-1 text-sm text-gray-500">Choose a plan to get started</p>
            </div>
        @endif
    </div>

    <!-- Upgrade / Renew Button -->
    <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
        <h5 class="mb-4 text-base font-bold text-navy-700 dark:text-white">Upgrade or Renew Plan</h5>
        <p class="mb-6 text-xs text-gray-600 dark:text-gray-400">
            Choose a plan that suits your business needs
        </p>
        
        <a href="{{ route('subscription.packages') }}" 
           class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            View Available Plans
        </a>
    </div>

    <!-- Payment History -->
    <div class="rounded-xl bg-lightPrimary dark:bg-navy-900/50 p-6">
        <h5 class="mb-4 text-base font-bold text-navy-700 dark:text-white">Payment History</h5>
        <p class="mb-6 text-xs text-gray-600 dark:text-gray-400">
            Your recent payment transactions
        </p>

        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="pb-3 text-left font-bold text-gray-600 dark:text-gray-400">Date</th>
                            <th class="pb-3 text-left font-bold text-gray-600 dark:text-gray-400">Order ID</th>
                            <th class="pb-3 text-left font-bold text-gray-600 dark:text-gray-400">Amount</th>
                            <th class="pb-3 text-left font-bold text-gray-600 dark:text-gray-400">Method</th>
                            <th class="pb-3 text-left font-bold text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr class="border-b border-gray-100 dark:border-white/5">
                                <td class="py-3 text-navy-700 dark:text-white">
                                    {{ $payment->created_at ? $payment->created_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">
                                    {{ $payment->midtrans_order_id ?? '-' }}
                                </td>
                                <td class="py-3 font-bold text-navy-700 dark:text-white">
                                    Rp {{ number_format($payment->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($payment->metode_pembayaran ?? '-') }}
                                </td>
                                <td class="py-3">
                                    @if($payment->status === 'Paid')
                                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700 dark:bg-green-900/30 dark:text-green-400">Paid</span>
                                    @elseif($payment->status === 'Pending')
                                        <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-bold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>
                                    @else
                                        <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $payment->status ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">No payment history yet</p>
            </div>
        @endif
    </div>
</div>
