@extends('layouts.app')

@section('title', 'Owner Details')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Owner Details</h4>
            <div class="flex gap-3">
                <a href="{{ route('kelola-owner.edit', $owner->id) }}"
                   class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Edit
                </a>
                <a href="{{ route('kelola-owner.index') }}"
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Back
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Company Name</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->nama_perusahaan }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Owner Name</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->nama_pemilik }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->email }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Phone</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->telepon }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Package</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->paket }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Number of Outlets</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->jumlah_outlet }} Outlet{{ $owner->jumlah_outlet > 1 ? 's' : '' }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Registration Date</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->tanggal_daftar->format('d F Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Expiration Date</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner->tanggal_expired->format('d F Y') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                <div class="mt-1">
                    @if($owner->status === 'Active')
                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-sm font-medium text-green-800 dark:text-green-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-sm font-medium text-red-800 dark:text-red-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Expired
                        </span>
                    @endif
                </div>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Created At</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $owner->created_at->format('d F Y H:i') }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Updated</p>
                <p class="mt-1 text-base text-navy-700 dark:text-white">{{ $owner->updated_at->format('d F Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
