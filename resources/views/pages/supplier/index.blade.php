@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('title', 'Suppliers')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Suppliers Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Suppliers</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $suppliers->total() }} total suppliers
                </p>
            </div>
            
            <!-- Bulk Delete Button -->
            <button id="bulkDeleteBtn" class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:active:bg-red-800 hidden"
                    onclick="confirmBulkDelete()">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                </svg>
                Delete Selected
            </button>
            
            <!-- Add New Button -->
            <a href="{{ route('supplier.create') }}" 
               class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                </svg>
                Add New Supplier
            </a>
        </div>

        <!-- Filters -->
        <div class="px-6 pb-4">
            <form method="GET" action="{{ route('supplier.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <!-- Search Name -->
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Search supplier name..." 
                       class="rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 placeholder:text-gray-400 dark:placeholder:text-gray-500" />

                <!-- Search Phone -->
                <input type="text" name="nomor_hp" value="{{ request('nomor_hp') }}" placeholder="Search phone number..." 
                       class="rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 placeholder:text-gray-400 dark:placeholder:text-gray-500" />

                <div class="flex gap-2">
                    <button type="submit" 
                            class="flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-2 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                    @if(request()->hasAny(['nama', 'nomor_hp']))
                        <a href="{{ route('supplier.index') }}" 
                           class="flex items-center justify-center rounded-xl bg-gray-100 px-5 py-2 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <form id="bulkDeleteForm" method="POST" action="{{ route('supplier.bulk-destroy') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" id="bulkIds" name="ids" value="[]">
            </form>
            
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="w-12 py-3">
                            <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 dark:border-gray-600 cursor-pointer" onchange="toggleSelectAll(this)">
                        </th>
                        <th class="w-16 py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">NO</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Supplier Name</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Phone</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Email</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Address</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5" onclick="if(!event.target.closest('.supplier-checkbox, .btn-actions-menu')) window.location.href='{{ route('supplier.edit', $supplier) }}'">
                        <!-- Checkbox -->
                        <td class="w-12 py-4" onclick="event.stopPropagation()">
                            <input type="checkbox" class="supplier-checkbox rounded border-gray-300 dark:border-gray-600 cursor-pointer" data-id="{{ $supplier->id }}" onchange="updateBulkDeleteButton()">
                        </td>
                        
                        <!-- NO -->
                        <td class="w-16 py-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $loop->iteration }}</p>
                        </td>
                        
                        <!-- Name -->
                        <td class="py-4">
                            <div>
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $supplier->nama }}</p>
                                @if($supplier->keterangan)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supplier->keterangan }}</p>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Phone -->
                        <td class="py-4">
                            @if($supplier->nomor_hp)
                                <a href="tel:{{ $supplier->nomor_hp }}" onclick="event.stopPropagation()" 
                                   class="inline-flex items-center gap-1.5 text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $supplier->nomor_hp }}
                                </a>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        
                        <!-- Email -->
                        <td class="py-4">
                            @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" onclick="event.stopPropagation()" 
                                   class="inline-flex items-center gap-1.5 text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $supplier->email }}
                                </a>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        
                        <!-- Address -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($supplier->alamat, 40) ?? '-' }}</p>
                        </td>
                        
                        <!-- Actions -->
                        <td class="py-4 col-actions" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center">
                                <button class="btn-actions-menu relative" data-supplier-id="{{ $supplier->id }}" data-supplier-name="{{ $supplier->nama }}" data-supplier-edit="{{ route('supplier.edit', $supplier) }}" data-supplier-destroy="{{ route('supplier.destroy', $supplier) }}">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 8c1.1 0 2-0.9 2-2s-0.9-2-2-2-2 0.9-2 2 0.9 2 2 2zm0 2c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2zm0 6c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No suppliers found</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Start by adding a new supplier</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 dark:border-white/10 px-6 py-4 gap-4">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm text-gray-600 dark:text-gray-400">Items per page:</span>
                <form method="GET" action="{{ route('supplier.index') }}" class="inline-block">
                    <input type="hidden" name="nama" value="{{ request('nama') }}">
                    <input type="hidden" name="nomor_hp" value="{{ request('nomor_hp') }}">
                    <select name="per_page" onchange="this.form.submit()" 
                            class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 [&>option]:!bg-white [&>option]:dark:!bg-navy-800 [&>option]:!text-navy-700 [&>option]:dark:!text-white">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                    </select>
                </form>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} results
                </span>
            </div>
            <div class="flex items-center gap-1">
                {{-- Previous Button --}}
                @if ($suppliers->onFirstPage())
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
                @else
                    <a href="{{ $suppliers->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">◀</a>
                @endif

                {{-- Page Numbers --}}
                @for ($page = max(1, $suppliers->currentPage() - 2); $page <= min($suppliers->lastPage(), $suppliers->currentPage() + 2); $page++)
                    @if ($page == $suppliers->currentPage())
                        <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $suppliers->appends(request()->except('page'))->url($page) }}" 
                           class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Next Button --}}
                @if ($suppliers->hasMorePages())
                    <a href="{{ $suppliers->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">▶</a>
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">▶</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Action Dropdown - Inline -->
<div id="actionDropdown" class="actions-dropdown">
    <button id="editMenuItem" class="actions-dropdown-item edit">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path fill="none" d="M0 0h24v24H0z"></path>
            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
        </svg>
        <span>Edit</span>
    </button>
    <button id="deleteMenuItem" class="actions-dropdown-item delete">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
            <path fill="none" d="M0 0h24v24H0z"></path>
            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
        </svg>
        <span>Delete</span>
    </button>
</div>

<!-- Bulk Delete Form -->
<form id="bulkDeleteForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" id="bulkIds" name="ids" value="">
</form>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-sm w-full mx-4">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">Confirm Delete</h3>
            <button type="button" id="modalCloseBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-700 dark:text-gray-300">Are you sure you want to delete <span id="deleteItemCount">item</span>?</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">This action cannot be undone.</p>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" id="modalCancelBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">Cancel</button>
            <button type="button" id="modalConfirmBtn" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-semibold">Delete</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.supplier-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkedCount = document.querySelectorAll('.supplier-checkbox:checked').length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkedCount > 0) {
        bulkDeleteBtn.classList.remove('hidden');
    } else {
        bulkDeleteBtn.classList.add('hidden');
    }
}

function confirmBulkDelete(ids = null) {
    let selectedIds = [];
    
    if (ids) {
        selectedIds = ids;
    } else {
        const checkboxes = document.querySelectorAll('.supplier-checkbox:checked');
        selectedIds = Array.from(checkboxes).map(cb => cb.dataset.id);
    }
    
    if (selectedIds.length === 0) {
        alert('Please select at least one supplier to delete');
        return;
    }
    
    const modal = document.getElementById('deleteConfirmModal');
    const itemCount = document.getElementById('deleteItemCount');
    itemCount.textContent = selectedIds.length > 1 ? selectedIds.length + ' suppliers' : selectedIds.length + ' supplier';
    
    modal.classList.remove('hidden');
    window.pendingDeleteIds = selectedIds;
}

function proceedBulkDelete() {
    const ids = window.pendingDeleteIds;
    if (!ids || ids.length === 0) return;
    
    document.getElementById('bulkIds').value = JSON.stringify(ids);
    
    const form = document.getElementById('bulkDeleteForm');
    form.action = '{{ route('supplier.bulk-destroy') }}';
    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalCloseBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalCancelBtn').addEventListener('click', closeDeleteModal);
    document.getElementById('modalConfirmBtn').addEventListener('click', proceedBulkDelete);

    document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
        if (e.target.id === 'deleteConfirmModal') {
            closeDeleteModal();
        }
    });

    const dropdown = new TableActionDropdown({
        dropdownSelector: '#actionDropdown',
        buttonSelector: '.btn-actions-menu',
        editMenuSelector: '#editMenuItem',
        deleteMenuSelector: '#deleteMenuItem',
        zoomFactor: 0.9,
        confirmDeleteMessage: 'Are you sure you want to delete this supplier?'
    });

    document.querySelectorAll('tr[data-href]').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-actions-menu') && !e.target.closest('.supplier-checkbox')) {
                window.location.href = this.dataset.href;
            }
        });
    });
});
</script>
@endpush
@endsection
