@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('title', 'Product Stock Detail')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('produk-stok.index') }}" class="text-brand-500 hover:text-brand-600 font-semibold text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Stock List
            </a>
            <h4 class="text-2xl font-bold text-navy-700 dark:text-white mt-2">Stock Detail: {{ $produkStok->produk->merk->nama ?? 'Unknown' }}</h4>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Store: <span class="font-semibold text-navy-700 dark:text-white">{{ $produkStok->toko->nama ?? '-' }}</span> | 
                Total Stock: <span class="font-semibold text-navy-700 dark:text-white">{{ $produkStok->stok }}</span>
            </p>
        </div>
    </div>

    <!-- Products Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Products in This Stock Group</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $terkaitProduk->count() }} products found
                </p>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="w-16 py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">NO</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Product Name</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">IMEI</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Color</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">RAM</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Storage</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Buy Price</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Sell Price</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terkaitProduk as $produk)
                    <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5">
                        <!-- NO -->
                        <td class="w-16 py-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $loop->iteration }}</p>
                        </td>
                        
                        <!-- Product Name -->
                        <td class="py-4">
                            <div>
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $produk->nama ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $produk->id }}</p>
                            </div>
                        </td>
                        
                        <!-- IMEI -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $produk->imei ?? '-' }}</p>
                        </td>
                        
                        <!-- Color -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $produk->warna ? $produk->warna->nama : '-' }}</p>
                        </td>
                        
                        <!-- RAM -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $produk->ram ? $produk->ram->nama : '-' }}</p>
                        </td>
                        
                        <!-- Storage -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $produk->penyimpanan ? $produk->penyimpanan->nama : '-' }}</p>
                        </td>
                        
                        <!-- Buy Price -->
                        <td class="py-4">
                            <p class="text-sm font-semibold text-navy-700 dark:text-white">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</p>
                        </td>
                        
                        <!-- Sell Price -->
                        <td class="py-4">
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</p>
                        </td>
                        
                        <!-- Actions -->
                        <td class="py-4">
                            <div class="flex items-center justify-center gap-2">
                                @permission('produk.update')
                                <a href="{{ route('produk.edit', $produk->id) }}"
                                   class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-500 transition duration-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
                                   title="Edit">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                    </svg>
                                </a>
                                @endpermission
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No products found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Add any additional scripts here if needed
</script>
@endpush
@endsection
