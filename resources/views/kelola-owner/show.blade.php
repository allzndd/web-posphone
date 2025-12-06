@extends('layouts.app')

@section('title', 'Detail Owner')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-bold text-navy-700 dark:text-white">Detail Owner</h4>
            <div class="flex gap-3">
                <a href="{{ route('kelola-owner.edit', $owner['id']) }}"
                   class="linear rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Edit
                </a>
                <a href="{{ route('kelola-owner.index') }}"
                   class="linear rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Kembali
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Perusahaan</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner['nama_perusahaan'] }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nama Pemilik</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner['nama_pemilik'] }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner['email'] }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Telepon</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner['telepon'] }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paket</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner['paket'] }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jumlah Outlet</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ $owner['jumlah_outlet'] }} Outlet</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Daftar</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ date('d F Y', strtotime($owner['tanggal_daftar'])) }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Expired</p>
                <p class="mt-1 text-base font-bold text-navy-700 dark:text-white">{{ date('d F Y', strtotime($owner['tanggal_expired'])) }}</p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                <div class="mt-1">
                    @if($owner['status'] === 'Aktif')
                        <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-sm font-medium text-green-800 dark:text-green-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-sm font-medium text-red-800 dark:text-red-300">
                            <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Expired
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
