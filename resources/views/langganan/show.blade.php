@extends('layouts.app')

@section('title', 'Service Details')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Service Details</h4>
            <div class="flex gap-3">
                <a href="{{ route('langganan.edit', $item->id) }}"
                   class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Edit
                </a>
                <a href="{{ route('langganan.index') }}"
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Back
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subscription ID</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                <div class="mt-1">
                    @php
                        $today = \Carbon\Carbon::today();
                        $isExpired = $item->end_date < $today;
                        $isActive = $item->is_active == 1;
                        $isTrial = $item->is_trial == 1;
                        
                        if ($isExpired) {
                            $status = 'Expired';
                            $statusClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                        } elseif (!$isActive) {
                            $status = 'Inactive';
                            $statusClass = 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300';
                        } elseif ($isTrial) {
                            $status = 'Trial';
                            $statusClass = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
                        } else {
                            $status = 'Active';
                            $statusClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300';
                        }
                    @endphp
                    <span class="inline-flex items-center rounded-full {{ $statusClass }} px-3 py-1 text-sm font-medium">
                        <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        {{ $status }}
                    </span>
                </div>
            </div>
            
            <div class="md:col-span-2 border-b border-gray-200 dark:border-white/10 pb-4">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Owner Information</h5>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Owner Name</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->owner->pengguna->name ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->owner->pengguna->email ?? 'N/A' }}</p>
            </div>
            
            <div class="md:col-span-2 border-b border-gray-200 dark:border-white/10 pb-4 mt-2">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Package Information</h5>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Service Package</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->tipeLayanan->nama ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Package Price</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">
                    Rp {{ number_format($item->tipeLayanan->harga ?? 0, 0, ',', '.') }}
                </p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Package Duration</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->tipeLayanan->duration_text ?? 'N/A' }}</p>
            </div>
            
            <div class="md:col-span-2 border-b border-gray-200 dark:border-white/10 pb-4 mt-2">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Subscription Period</h5>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Start Date</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->started_date->format('d F Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">End Date</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->end_date->format('d F Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Days Remaining / Expired</p>
                @php
                    $today = \Carbon\Carbon::today();
                    $daysRemaining = $today->diffInDays($item->end_date, false);
                @endphp
                <p class="mt-1 text-base font-bold {{ $daysRemaining < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                    @if($daysRemaining < 0)
                        Expired {{ abs($daysRemaining) }} days ago
                    @elseif($daysRemaining == 0)
                        Expires today
                    @else
                        {{ $daysRemaining }} days remaining
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Duration</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">
                    {{ $item->started_date->diffInDays($item->end_date) }} days
                </p>
            </div>
            
            <div class="md:col-span-2 border-b border-gray-200 dark:border-white/10 pb-4 mt-2">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Settings</h5>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Trial Period</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">
                    @if($item->is_trial)
                        <span class="text-blue-600 dark:text-blue-400">Yes</span>
                    @else
                        <span class="text-gray-600 dark:text-gray-400">No</span>
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Status</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">
                    @if($item->is_active)
                        <span class="text-green-600 dark:text-green-400">Active</span>
                    @else
                        <span class="text-gray-600 dark:text-gray-400">Inactive</span>
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Created At</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $item->created_at->format('d F Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Updated</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $item->updated_at->format('d F Y H:i') }}</p>
            </div>
            
            @if($item->pembayaran && $item->pembayaran->count() > 0)
            <div class="md:col-span-2 mt-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Payment History</h5>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-navy-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-white uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach($item->pembayaran as $payment)
                            <tr>
                                <td class="px-4 py-3 text-sm text-navy-700 dark:text-white">
                                    {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-navy-700 dark:text-white">
                                    Rp {{ number_format($payment->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $payment->metode_pembayaran }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($payment->status === 'Paid')
                                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                                            Paid
                                        </span>
                                    @elseif($payment->status === 'Pending')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-1 text-xs font-medium text-red-800 dark:text-red-300">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="md:col-span-2 mt-6">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white mb-4">Payment History</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">No payment records found for this subscription.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
