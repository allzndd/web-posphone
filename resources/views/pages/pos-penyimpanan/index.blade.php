@extends('layouts.app')

@section('title', 'Penyimpanan Master Data')

@push('style')
<!-- Load reusable table components CSS -->
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Header -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-bold text-navy-700 dark:text-white">
                Penyimpanan
            </h1>
            <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                Kelola data penyimpanan dengan kapasitas maksimal
            </p>
        </div>
        <a href="{{ route('pos-penyimpanan.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300 px-6 py-3 text-sm font-bold text-white transition-all duration-200 w-full md:w-auto">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Penyimpanan
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 dark:border-green-900/20 dark:bg-green-900/10 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-green-600 dark:text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-green-800 dark:text-green-200">Berhasil</h3>
                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Table Card -->
    <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-navy-800 shadow-sm">
        <div class="overflow-x-auto">
            @if($posPenyimpanans->count() > 0)
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-navy-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-navy-700 dark:text-white">
                                No
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-navy-700 dark:text-white">
                                Owner
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-navy-700 dark:text-white">
                                Kapasitas
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-navy-700 dark:text-white">
                                Global
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-navy-700 dark:text-white">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posPenyimpanans as $index => $penyimpanan)
                            <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-navy-700/20 transition-colors">
                                <td class="px-6 py-4 text-sm text-navy-700 dark:text-white">
                                    {{ ($posPenyimpanans->currentPage() - 1) * $posPenyimpanans->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($penyimpanan->id_owner)
                                        <div class="text-navy-700 dark:text-white">
                                            <p class="font-medium">{{ $penyimpanan->owner?->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $penyimpanan->owner?->email ?? '' }}</p>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 dark:bg-blue-900/20 px-3 py-1 text-xs font-semibold text-blue-600 dark:text-blue-300">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                                            </svg>
                                            Global
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="text-navy-700 dark:text-white font-medium">
                                        {{ $penyimpanan->kapasitas }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($penyimpanan->id_global)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 dark:bg-green-900/20 px-3 py-1 text-xs font-semibold text-green-600 dark:text-green-300">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Ya
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 dark:bg-gray-900/20 px-3 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            Tidak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a 
                                            href="{{ route('pos-penyimpanan.edit', $penyimpanan->id) }}" 
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 px-3 py-2 text-sm font-semibold text-blue-600 dark:text-blue-300 transition-all duration-200"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($posPenyimpanans->hasPages())
                    <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
                        {{ $posPenyimpanans->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-navy-700 dark:text-white mb-1">Tidak ada data penyimpanan</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Mulai dengan membuat penyimpanan baru</p>
                    <a href="{{ route('pos-penyimpanan.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300 px-6 py-3 text-sm font-bold text-white transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Penyimpanan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
