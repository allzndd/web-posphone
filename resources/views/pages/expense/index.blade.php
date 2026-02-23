@extends('layouts.app')

@section('title', 'Expenses')

@push('style')
<!-- Load reusable table components CSS -->
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
<!-- Access Denied Overlay Component -->
@include('components.access-denied-overlay', ['module' => 'Expense', 'hasAccessRead' => $hasAccessRead])

<div class="mt-3 px-[11px] pr-[10px] @if(!$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Expenses Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Expenses</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $expenses->total() }} total expenses
                </p>
            </div>
            
            <!-- Search, Bulk Delete & Add Button -->
            <div class="flex items-center gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('expense.index') }}" class="relative">
                    <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white mr-2" xmlns="http://www.w3.org/2000/svg">
                            <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice, kategori, toko..." 
                               class="block w-full bg-transparent text-sm font-medium text-navy-700 dark:text-white outline-none placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    </div>
                </form>
                
                <!-- Bulk Delete Button (hidden by default) -->
                @permission('expense.delete')
                <button id="bulkDeleteBtn" class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:active:bg-red-800 hidden"
                        onclick="confirmBulkDelete()">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Selected
                </button>
                @endpermission
                
                <!-- Add New Button -->
                @permission('expense.create')
                <a href="{{ route('expense.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:active:bg-red-800">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    Add Expense
                </a>
                @endpermission
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left" style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" 
                                   class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-navy-700 cursor-pointer"
                                   onchange="toggleSelectAll(this)">
                        </th>
                        <th class="py-3 text-left col-no">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">No</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Invoice</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Toko</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Kategori</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Total</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Pembayaran</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Status</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Tanggal</p>
                        </th>
                        <th class="py-3 text-center col-actions">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Aksi</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $index => $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('expense.edit', $item) }}">
                        <td class="py-4" style="width: 40px;" onclick="event.stopPropagation()">
                            <input type="checkbox" class="expense-checkbox rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-navy-700 cursor-pointer" 
                                   value="{{ $item->id }}" 
                                   onchange="updateBulkDeleteButton()">
                        </td>
                        <td class="py-4 col-no">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $loop->iteration }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->invoice }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->toko->nama ?? '-' }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->kategoriExpense->nama ?? '-' }}</p>
                        </td>
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-red-500">{{ get_currency_symbol() }} {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                        </td>
                        <td class="py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-100 dark:bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-600 dark:text-purple-300">
                                {{ ucfirst($item->metode_pembayaran ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="py-4 text-center" onclick="event.stopPropagation()">
                            <div class="inline-block" x-data="statusDropdown('{{ $item->status }}', '{{ route('expense.update-status', $item->id) }}')">
                                <button type="button" @click="toggleDropdown($event)" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-bold cursor-pointer transition"
                                    :class="{
                                        'bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-300': status === 'pending',
                                        'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-300': status === 'completed',
                                        'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-300': status === 'cancelled'
                                    }">
                                    <span x-text="status === 'pending' ? 'Pending' : (status === 'completed' ? 'Selesai' : 'Batal')"></span>
                                    <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="w-36 rounded-lg bg-white dark:bg-navy-800 shadow-lg border border-gray-200 dark:border-white/10 py-1"
                                    :style="dropdownStyle">
                                    <button type="button" @click="changeStatus('pending')"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                        :class="status === 'pending' ? 'font-bold text-orange-600 dark:text-orange-300' : 'text-gray-700 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> Pending
                                    </button>
                                    <button type="button" @click="changeStatus('completed')"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                        :class="status === 'completed' ? 'font-bold text-green-600 dark:text-green-300' : 'text-gray-700 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Selesai
                                    </button>
                                    <button type="button" @click="changeStatus('cancelled')"
                                        class="w-full text-left px-3 py-2 text-xs hover:bg-gray-50 dark:hover:bg-white/5 flex items-center gap-2"
                                        :class="status === 'cancelled' ? 'font-bold text-red-600 dark:text-red-300' : 'text-gray-700 dark:text-gray-300'">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Batal
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="py-4 col-actions" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-2">
                                @permission('expense.update')
                                <a href="{{ route('expense.edit', $item) }}"
                                   class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-500 transition duration-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
                                   title="Edit">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                    </svg>
                                </a>
                                @endpermission
                                
                                @permission('expense.delete')
                                <button onclick="confirmDelete('{{ route('expense.destroy', $item) }}')"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400"
                                        title="Delete">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                                    </svg>
                                </button>
                                @endpermission
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">Tidak ada expense</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Mulai dengan membuat expense baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Items Per Page & Info -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Item per halaman:</span>
                    <form method="GET" action="{{ route('expense.index') }}" class="inline-block">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="per_page" onchange="this.form.submit()" 
                                class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        </select>
                    </form>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Menampilkan {{ $expenses->firstItem() ?? 0 }} ke {{ $expenses->lastItem() ?? 0 }} dari {{ $expenses->total() }}
                    </span>
                </div>

                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1">
                    @php
                        $paginationParams = '';
                        if(request('search')) $paginationParams .= '&search=' . urlencode(request('search'));
                        $paginationParams .= '&per_page=' . request('per_page', 10);
                    @endphp
                    
                    @if ($expenses->onFirstPage())
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
                    @else
                        <a href="{{ $expenses->previousPageUrl() }}{{ $paginationParams }}" 
                           class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">◀</a>
                    @endif

                    @for ($page = max(1, $expenses->currentPage() - 2); $page <= min($expenses->lastPage(), $expenses->currentPage() + 2); $page++)
                        @if ($page == $expenses->currentPage())
                            <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $expenses->url($page) }}{{ $paginationParams }}" 
                               class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($expenses->hasMorePages())
                        <a href="{{ $expenses->nextPageUrl() }}{{ $paginationParams }}" 
                           class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">▶</a>
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">▶</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Dropdown - Inline -->
<div id="actionDropdown" class="actions-dropdown">
    <button id="editMenuItem" class="actions-dropdown-item edit">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        <span>Edit</span>
    </button>
    <button id="deleteMenuItem" class="actions-dropdown-item delete">
        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        <span>Hapus</span>
    </button>
</div>

<!-- Bulk Delete Form -->
<form id="bulkDeleteForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" id="bulkIds" name="ids" value="">
</form>

<div id="deleteConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-sm w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Konfirmasi Hapus</h3>
            <button type="button" id="modalCloseBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400 mb-2">Apakah Anda yakin ingin menghapus item ini?</p>
            <p class="text-sm text-gray-500 dark:text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" id="modalCancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">Batal</button>
            <button type="button" id="modalConfirmBtn" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-semibold">Hapus</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(deleteUrl, itemName) {
    const modal = document.getElementById('deleteConfirmModal');
    const messageEl = modal.querySelector('p.text-gray-600');
    messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus <span class="font-bold">' + (itemName || 'expense') + '</span>?';
    modal.classList.remove('hidden');
    
    window.pendingDeleteUrl = deleteUrl;
    window.pendingDeleteIds = [];
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
    window.pendingDeleteUrl = null;
    window.pendingDeleteIds = [];
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.expense-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.expense-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
    } else {
        bulkDeleteBtn.classList.add('hidden');
        document.getElementById('selectAllCheckbox').checked = false;
    }
}

function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.expense-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) {
        alert('Pilih minimal satu expense untuk dihapus');
        return;
    }

    const modal = document.getElementById('deleteConfirmModal');
    const messageEl = modal.querySelector('p.text-gray-600');
    messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus <span class="font-bold text-red-600 dark:text-red-400">' + count + ' expense</span>?';
    modal.classList.remove('hidden');
    
    window.pendingDeleteIds = Array.from(checkedBoxes).map(function(cb) {
        return cb.value;
    });
    window.pendingDeleteUrl = null;
}

function proceedDelete() {
    // Check if this is a single delete
    if (window.pendingDeleteUrl) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.pendingDeleteUrl;
        form.style.display = 'none';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken.content;
            form.appendChild(input);
        }
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
        return;
    }
    
    // Otherwise it's a bulk delete
    if (!window.pendingDeleteIds || window.pendingDeleteIds.length === 0) {
        alert('Pilih minimal satu expense untuk dihapus');
        return;
    }

    document.getElementById('bulkIds').value = JSON.stringify(window.pendingDeleteIds);
    
    const form = document.getElementById('bulkDeleteForm');
    form.action = '{{ route('expense.bulk-destroy') }}';
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalCloseBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalCancelBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalConfirmBtn').addEventListener('click', proceedDelete);

    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
        if (e.target.id === 'deleteConfirmModal') {
            closeDeleteModal();
        }
    });
});

function statusDropdown(initialStatus, url) {
    return {
        open: false,
        status: initialStatus,
        loading: false,
        async changeStatus(newStatus) {
            if (this.status === newStatus || this.loading) { this.open = false; return; }
            this.loading = true;
            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await response.json();
                if (data.success) {
                    this.status = newStatus;
                    showStatusToast(data.message, 'success');
                } else {
                    showStatusToast(data.message || 'Gagal mengubah status', 'error');
                }
            } catch (err) {
                showStatusToast('Gagal mengubah status', 'error');
            }
            this.loading = false;
            this.open = false;
        }
    };
}

function showStatusToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-5 right-5 z-[9999] px-6 py-3 rounded-lg shadow-lg text-white text-sm font-medium transition-opacity duration-500 ' +
        (type === 'success' ? 'bg-green-500' : 'bg-red-500');
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 3000);
}
</script>
@endpush
