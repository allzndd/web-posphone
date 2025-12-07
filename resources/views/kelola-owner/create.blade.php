@extends('layouts.app')

@section('title', 'Add Owner')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Add New Owner</h4>
        
        <form action="{{ route('kelola-owner.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Company Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama_perusahaan') border-red-500 @enderror">
                    @error('nama_perusahaan')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Owner Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama_pemilik') border-red-500 @enderror">
                    @error('nama_pemilik')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('telepon') border-red-500 @enderror">
                    @error('telepon')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Package <span class="text-red-500">*</span></label>
                    <select name="paket" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('paket') border-red-500 @enderror">
                        <option value="">Select Package</option>
                        <option value="Paket Starter" {{ old('paket') == 'Paket Starter' ? 'selected' : '' }}>Starter Package</option>
                        <option value="Paket Basic" {{ old('paket') == 'Paket Basic' ? 'selected' : '' }}>Basic Package</option>
                        <option value="Paket Professional" {{ old('paket') == 'Paket Professional' ? 'selected' : '' }}>Professional Package</option>
                        <option value="Paket Premium" {{ old('paket') == 'Paket Premium' ? 'selected' : '' }}>Premium Package</option>
                        <option value="Paket Enterprise" {{ old('paket') == 'Paket Enterprise' ? 'selected' : '' }}>Enterprise Package</option>
                    </select>
                    @error('paket')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Number of Outlets <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_outlet" value="{{ old('jumlah_outlet', 1) }}" required min="1"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('jumlah_outlet') border-red-500 @enderror">
                    @error('jumlah_outlet')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Registration Date <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_daftar" value="{{ old('tanggal_daftar', now()->format('Y-m-d')) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('tanggal_daftar') border-red-500 @enderror">
                    @error('tanggal_daftar')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Expiration Date <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_expired" value="{{ old('tanggal_expired') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('tanggal_expired') border-red-500 @enderror">
                    @error('tanggal_expired')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Save Owner
                </button>
                <a href="{{ route('kelola-owner.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
