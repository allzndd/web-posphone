@extends('layouts.app')

@section('title', 'Tambah Owner')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Tambah Owner Baru</h4>
        
        <form action="{{ route('kelola-owner.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Nama Pemilik</label>
                    <input type="text" name="nama_pemilik" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Email</label>
                    <input type="email" name="email" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Telepon</label>
                    <input type="text" name="telepon" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Paket</label>
                    <select name="paket" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                        <option value="">Pilih Paket</option>
                        <option value="Paket Starter">Paket Starter</option>
                        <option value="Paket Basic">Paket Basic</option>
                        <option value="Paket Professional">Paket Professional</option>
                        <option value="Paket Premium">Paket Premium</option>
                        <option value="Paket Enterprise">Paket Enterprise</option>
                    </select>
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Jumlah Outlet</label>
                    <input type="number" name="jumlah_outlet" required min="1"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Tanggal Daftar</label>
                    <input type="date" name="tanggal_daftar" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Tanggal Expired</label>
                    <input type="date" name="tanggal_expired" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Simpan
                </button>
                <a href="{{ route('kelola-owner.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
