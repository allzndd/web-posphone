@extends('layouts.app')

@section('title', 'Kelola Rekening')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Bank Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Daftar Rekening</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $banks->total() }} total rekening
                </p>
            </div>
            
            <!-- Search & Add Button -->
            <div class="flex items-center gap-3">
                <!-- Add New Button -->
                <a href="{{ route('bank.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    Tambah Rekening
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            @if($banks->count())
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">No</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Nama Bank</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Nama Rekening</p>
                            </th>
                            <th class="py-3 text-left">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Nomor Rekening</p>
                            </th>
                            <th class="py-3 text-center">
                                <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banks as $bank)
                        <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('bank.edit', $bank) }}">
                            <td class="py-4">
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ ($banks->currentPage() - 1) * $banks->perPage() + $loop->iteration }}</p>
                            </td>
                            <td class="py-4">
                                <span class="text-sm font-medium text-navy-700 dark:text-white">{{ $bank->nama_bank }}</span>
                            </td>
                            <td class="py-4">
                                <span class="text-sm font-medium text-navy-700 dark:text-white">{{ $bank->nama_rekening }}</span>
                            </td>
                            <td class="py-4">
                                <span class="text-sm font-medium text-navy-700 dark:text-white">{{ $bank->nomor_rekening }}</span>
                            </td>
                            <td class="py-4" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('bank.edit', $bank) }}" 
                                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 px-3 py-2 text-xs font-bold text-blue-600 dark:text-blue-400 transition duration-200 hover:bg-blue-200 dark:hover:bg-blue-900/50">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="none" d="M0 0h24v24H0z"></path>
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"></path>
                                            <path d="M20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('bank.destroy', $bank) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-100 dark:bg-red-900/30 px-3 py-2 text-xs font-bold text-red-600 dark:text-red-400 transition duration-200 hover:bg-red-200 dark:hover:bg-red-900/50">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                                <path fill="none" d="M0 0h24v24H0z"></path>
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Items Per Page & Info -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Items per page:</span>
                            <form method="GET" action="{{ route('bank.index') }}" class="inline-block">
                                <select name="per_page" onchange="this.form.submit()" 
                                        class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                                </select>
                            </form>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                Showing {{ $banks->firstItem() ?? 0 }} to {{ $banks->lastItem() ?? 0 }} of {{ $banks->total() }}
                            </span>
                        </div>

                        <!-- Pagination Buttons -->
                        <div class="flex items-center gap-1">
                            @if ($banks->onFirstPage())
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
                            @else
                                <a href="{{ $banks->previousPageUrl() }}&per_page={{ request('per_page', 15) }}" 
                                   class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">◀</a>
                            @endif

                            @for ($page = max(1, $banks->currentPage() - 2); $page <= min($banks->lastPage(), $banks->currentPage() + 2); $page++)
                                @if ($page == $banks->currentPage())
                                    <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $banks->url($page) }}&per_page={{ request('per_page', 15) }}" 
                                       class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor

                            @if ($banks->hasMorePages())
                                <a href="{{ $banks->nextPageUrl() }}&per_page={{ request('per_page', 15) }}" 
                                   class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">▶</a>
                            @else
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">▶</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c1.1 0 2-0.9 2-2s-0.9-2-2-2-2 0.9-2 2 0.9 2 2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-navy-700 dark:text-white mb-1">Tidak ada data rekening</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Mulai dengan membuat rekening baru</p>
                    <a href="{{ route('bank.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300 px-6 py-3 text-sm font-bold text-white transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Rekening
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Row click navigation
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', function() {
            window.location.href = this.getAttribute('data-href');
        });
    });
</script>
@endsection
