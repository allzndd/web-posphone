@extends('layouts.app')

@section('title', 'Product Detail - ' . $produk->nama)

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('produk.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Products
        </a>
    </div>

    <!-- Product Detail Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-white/10 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-navy-700 dark:text-white">{{ $produk->nama }}</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Product Type: <span class="font-semibold">{{ ucfirst($produk->product_type) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div>
                    <h2 class="text-lg font-bold text-navy-700 dark:text-white mb-6">General Information</h2>
                    
                    <!-- Merk -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Brand</label>
                        <p class="text-base text-navy-700 dark:text-white">
                            {{ $produk->merk->nama ?? '-' }}
                        </p>
                    </div>

                    <!-- Description -->
                    @if($produk->deskripsi)
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            {{ $produk->deskripsi }}
                        </p>
                    </div>
                    @endif

                    <!-- IMEI -->
                    @if($produk->imei)
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">IMEI</label>
                        <p class="text-base font-mono text-navy-700 dark:text-white bg-gray-100 dark:bg-navy-900 px-3 py-2 rounded-lg">
                            {{ $produk->imei }}
                        </p>
                    </div>
                    @endif

                    <!-- Aksesoris -->
                    @if($produk->aksesoris)
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Accessories</label>
                        <p class="text-base text-gray-600 dark:text-gray-400">
                            {{ $produk->aksesoris }}
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div>
                    <h2 class="text-lg font-bold text-navy-700 dark:text-white mb-6">Specifications</h2>
                    
                    <!-- RAM -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">RAM</label>
                        <p class="text-base text-navy-700 dark:text-white">
                            @if($produk->pos_ram_id && $produk->ram)
                                {{ $produk->ram->kapasitas }} GB
                            @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </p>
                    </div>

                    <!-- Storage -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Storage</label>
                        <p class="text-base text-navy-700 dark:text-white">
                            @if($produk->pos_penyimpanan_id && $produk->penyimpanan)
                                {{ $produk->penyimpanan->kapasitas }} GB
                            @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </p>
                    </div>

                    <!-- Color -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Color</label>
                        <p class="text-base text-navy-700 dark:text-white">
                            @if($produk->pos_warna_id && $produk->warna)
                                {{ $produk->warna->warna }}
                            @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </p>
                    </div>

                    <!-- Battery Health -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Battery Health</label>
                        <p class="text-base text-navy-700 dark:text-white">
                            @if($produk->battery_health)
                                <div class="flex items-center gap-3">
                                    <div class="w-32 bg-gray-200 dark:bg-navy-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($produk->battery_health, 100) }}%"></div>
                                    </div>
                                    <span class="font-semibold">{{ $produk->battery_health }}%</span>
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-8 border-t border-gray-200 dark:border-white/10">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Purchase Price</label>
                    <p class="text-2xl font-bold text-navy-700 dark:text-white">
                        Rp. {{ number_format($produk->harga_beli, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Selling Price</label>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp. {{ number_format($produk->harga_jual, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Additional Costs (Biaya Tambahan) -->
            @php
                $biayaTambahanDirect = \DB::table('pos_produk_biaya_tambahan')
                    ->where('pos_produk_id', $produk->id)
                    ->get();
                
                $hasBiayaTambahan = $biayaTambahanDirect->count() > 0;
            @endphp
            
            @if($hasBiayaTambahan)
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-white/10">
                <h2 class="text-lg font-bold text-navy-700 dark:text-white mb-4">
                    <svg class="w-6 h-6 inline-block mr-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Additional Costs
                </h2>
                <div class="space-y-3">
                    @foreach($biayaTambahanDirect as $biaya)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-navy-700/50 rounded-lg border border-gray-200 dark:border-white/10 hover:border-brand-300 dark:hover:border-brand-700 transition">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/30">
                                <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $biaya->nama }}</span>
                        </div>
                        <span class="font-semibold text-navy-700 dark:text-white">
                            Rp. {{ number_format($biaya->harga, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                    
                    <!-- Total Biaya Tambahan -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-brand-50 to-orange-50 dark:from-brand-900/20 dark:to-orange-900/20 rounded-lg border-2 border-brand-300 dark:border-brand-700">
                        <span class="font-bold text-brand-700 dark:text-brand-300">Total Additional Costs</span>
                        <span class="text-xl font-bold text-brand-600 dark:text-brand-400">
                            Rp. {{ number_format($biayaTambahanDirect->sum('harga'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-white/10 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Created</label>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $produk->created_at->format('d M Y - H:i') }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Last Updated</label>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $produk->updated_at->format('d M Y - H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
