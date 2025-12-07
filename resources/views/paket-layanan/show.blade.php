@extends('layouts.app')

@section('title', 'Service Package Details')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Service Package Details</h4>
            <div class="flex gap-3">
                <a href="{{ route('paket-layanan.edit', $paket->id) }}"
                   class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Edit
                </a>
                <a href="{{ route('paket-layanan.index') }}"
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Back
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Package ID</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">#{{ str_pad($paket->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Package Name</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $paket->nama }}</p>
            </div>
            
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $paket->deskripsi }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Price</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Duration</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $paket->durasi }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                <div class="mt-1">
                    @if($paket->status === 'Active')
                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-sm font-medium text-green-800 dark:text-green-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-900/30 px-3 py-1 text-sm font-medium text-gray-800 dark:text-gray-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Inactive
                        </span>
                    @endif
                </div>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Created At</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $paket->created_at->format('d F Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Updated</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $paket->updated_at->format('d F Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
