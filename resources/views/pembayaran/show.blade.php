@extends('layouts.app')

@section('title', 'Payment Details')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Payment Details</h4>
            <div class="flex gap-3">
                <a href="{{ route('pembayaran.edit', $item->id) }}"
                   class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Edit
                </a>
                <a href="{{ route('pembayaran.index') }}"
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Back
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment ID</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paid Date</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->paid_at ? $item->paid_at->format('d F Y H:i') : 'Not Paid Yet' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Owner Name</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->owner->pengguna->name ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Package</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->langganan->tipeLayanan->nama ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subscription Period</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">
                    @if($item->langganan)
                        {{ $item->langganan->started_date->format('d M Y') }} - {{ $item->langganan->end_date->format('d M Y') }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Amount</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">Rp {{ number_format($item->nominal, 0, ',', '.') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment Method</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $item->metode_pembayaran }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                <div class="mt-1">
                    @if($item->status === 'Paid')
                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-sm font-medium text-green-800 dark:text-green-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Paid
                        </span>
                    @elseif($item->status === 'Pending')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 text-sm font-medium text-yellow-800 dark:text-yellow-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Pending
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-sm font-medium text-red-800 dark:text-red-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Failed
                        </span>
                    @endif
                </div>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Created At</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $item->created_at->format('d F Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
