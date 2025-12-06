@extends('layouts.app')

@section('title', 'Dashboard Superadmin')

@section('main')
<div class="mt-3">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 mb-5">
        <!-- Total Owners -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Owner</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white mt-1">{{ $totalOwners }}</h4>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-lightPrimary">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-7 w-7 text-brand-500" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Owners -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Owner Aktif</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white mt-1">{{ $activeOwners }}</h4>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-7 w-7 text-green-500" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-7 w-7 text-blue-500" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pembayaran Pending</p>
                    <h4 class="text-2xl font-bold text-navy-700 dark:text-white mt-1">{{ $pendingPayments }}</h4>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-yellow-100">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-7 w-7 text-yellow-500" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-5">
        <!-- Recent Owners -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex items-center justify-between p-6 pb-4">
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Owner Terbaru</h4>
                <a href="{{ route('kelola-owner.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                    Lihat Semua →
                </a>
            </div>

            <div class="overflow-x-auto px-6 pb-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Nama</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Email</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Telepon</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Terdaftar</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOwners as $owner)
                        <tr class="border-b border-gray-100 dark:border-white/10">
                            <td class="py-3">
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $owner->name }}</p>
                            </td>
                            <td class="py-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $owner->email }}</p>
                            </td>
                            <td class="py-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $owner->phone ?? '-' }}</p>
                            </td>
                            <td class="py-3">
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $owner->created_at->format('d/m/Y') }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                                Belum ada owner terdaftar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex items-center justify-between p-6 pb-4">
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Pembayaran Terbaru</h4>
                <a href="{{ route('pembayaran.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                    Lihat Semua →
                </a>
            </div>

            <div class="overflow-x-auto px-6 pb-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Owner</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Paket</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Total</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Status</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                        <tr class="border-b border-gray-100 dark:border-white/10">
                            <td class="py-3">
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $payment['owner'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ date('d/m/Y', strtotime($payment['tanggal'])) }}</p>
                            </td>
                            <td class="py-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $payment['paket'] }}</p>
                            </td>
                            <td class="py-3">
                                <p class="text-sm font-semibold text-navy-700 dark:text-white">Rp {{ number_format($payment['total'], 0, ',', '.') }}</p>
                            </td>
                            <td class="py-3">
                                @if($payment['status'] === 'Lunas')
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                                        Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300">
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Popular Packages -->
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="flex items-center justify-between p-6 pb-4">
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Paket Populer</h4>
                <a href="{{ route('paket-layanan.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                    Lihat Semua →
                </a>
            </div>

            <div class="px-6 pb-6">
                @foreach($popularPackages as $package)
                <div class="mb-4 rounded-xl border border-gray-200 dark:border-white/10 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-base font-bold text-navy-700 dark:text-white">{{ $package['nama'] }}</h5>
                        <span class="rounded-full bg-brand-100 dark:bg-brand-900/30 px-3 py-1 text-xs font-bold text-brand-500">
                            {{ $package['total_owner'] }} Owner
                        </span>
                    </div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                        Revenue: <span class="text-brand-500">Rp {{ number_format($package['revenue'], 0, ',', '.') }}</span>
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Owner Growth Chart -->
    <div class="mt-5">
        <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
            <div class="p-6 pb-4">
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Pertumbuhan Owner {{ date('Y') }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Jumlah owner baru yang terdaftar setiap bulan</p>
            </div>

            <div class="px-6 pb-6">
                <div class="h-64 flex items-end justify-between gap-2">
                    @foreach($monthlyOwners as $data)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-brand-500 rounded-t-lg hover:bg-brand-600 transition-colors" 
                             style="height: {{ $data['total'] > 0 ? ($data['total'] / max(array_column($monthlyOwners, 'total'))) * 200 : 10 }}px;"
                             title="{{ $data['total'] }} owner">
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">{{ $data['month'] }}</p>
                        <p class="text-xs font-bold text-navy-700 dark:text-white">{{ $data['total'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        let csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        let methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
