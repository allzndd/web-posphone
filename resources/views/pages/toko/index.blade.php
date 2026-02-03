@extends('layouts.app')

@section('title', 'Stores')

@push('style')
<!-- Load reusable table components CSS -->
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Stores Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Stores</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $tokos->total() }} total stores
                </p>
            </div>
            
            <!-- Search & Add & Bulk Delete Button -->
            <div class="flex items-center gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('toko.index') }}" class="relative">
                    <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white mr-2" xmlns="http://www.w3.org/2000/svg">
                            <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                        </svg>
                        <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Search stores..." 
                               class="block w-full bg-transparent text-sm font-medium text-navy-700 dark:text-white outline-none placeholder:text-gray-400 dark:placeholder:text-gray-500" />
                    </div>
                </form>
                
                <!-- Bulk Delete Button (hidden by default) -->
                <button id="bulkDeleteBtn" class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-red-600 active:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:active:bg-red-800 hidden"
                        onclick="confirmBulkDelete()">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Selected
                </button>
                
                <!-- Add New Button -->
                <a href="{{ route('toko.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    Add New Store
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-center w-12">
                            <input type="checkbox" id="selectAllCheckbox" 
                                   class="w-4 h-4 text-brand-500 border-gray-300 rounded focus:ring-brand-500 dark:focus:ring-brand-400 dark:ring-offset-gray-800 focus:ring-2 dark:bg-navy-700 dark:border-gray-600"
                                   onchange="toggleSelectAll(this)" />
                        </th>
                        <th class="py-3 text-center w-16">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">NO</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Store Name</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Address</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Employees</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokos as $toko)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('toko.edit', $toko) }}">
                        <td class="py-4 text-center" onclick="event.stopPropagation()">
                            <input type="checkbox" value="{{ $toko->id }}" 
                                   class="toko-checkbox w-4 h-4 text-brand-500 border-gray-300 rounded focus:ring-brand-500 dark:focus:ring-brand-400 dark:ring-offset-gray-800 focus:ring-2 dark:bg-navy-700 dark:border-gray-600"
                                   onchange="updateBulkDeleteButton()" />
                        </td>
                        <td class="py-4 text-center">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $loop->iteration }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $toko->nama }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $toko->alamat ?? '-' }}</p>
                        </td>
                        <td class="py-4">
                            <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-xs font-medium text-blue-800 dark:text-blue-300">
                                {{ $toko->pengguna()->count() }} employees
                            </span>
                        </td>
                        <td class="py-4" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center">
                                <button class="btn-actions-menu relative" data-toko-id="{{ $toko->id }}" data-toko-name="{{ $toko->nama }}" data-toko-edit="{{ route('toko.edit', $toko) }}" data-toko-destroy="{{ route('toko.destroy', $toko) }}">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 8c1.1 0 2-0.9 2-2s-0.9-2-2-2-2 0.9-2 2 0.9 2 2 2zm0 2c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2zm0 6c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No stores found</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Try adjusting your search criteria</p>
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
                    <span class="text-sm text-gray-600 dark:text-gray-400">Items per page:</span>
                    <form method="GET" action="{{ route('toko.index') }}" class="inline-block">
                        @if(request('nama'))
                            <input type="hidden" name="nama" value="{{ request('nama') }}">
                        @endif
                        <select name="per_page" onchange="this.form.submit()" 
                                class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        </select>
                    </form>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $tokos->firstItem() ?? 0 }} to {{ $tokos->lastItem() ?? 0 }} of {{ $tokos->total() }}
                    </span>
                </div>

                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1">
                    @if ($tokos->onFirstPage())
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
                    @else
                        <a href="{{ $tokos->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" 
                           class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">◀</a>
                    @endif

                    @for ($page = max(1, $tokos->currentPage() - 2); $page <= min($tokos->lastPage(), $tokos->currentPage() + 2); $page++)
                        @if ($page == $tokos->currentPage())
                            <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $tokos->url($page) }}&per_page={{ request('per_page', 10) }}" 
                               class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($tokos->hasMorePages())
                        <a href="{{ $tokos->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" 
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
function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.toko-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.toko-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
    } else {
        bulkDeleteBtn.classList.add('hidden');
        document.getElementById('selectAllCheckbox').checked = false;
    }
}

function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.toko-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) {
        alert('Pilih minimal satu toko untuk dihapus');
        return;
    }

    const modal = document.getElementById('deleteConfirmModal');
    const messageEl = modal.querySelector('p.text-gray-600');
    messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus <span class="font-bold text-red-600 dark:text-red-400">' + count + ' toko' + (count > 1 ? '' : '') + '</span>?';
    modal.classList.remove('hidden');
    
    window.pendingDeleteIds = Array.from(checkedBoxes).map(function(cb) {
        return cb.value;
    });
}

function proceedBulkDelete() {
    // Check if this is a single delete from dropdown
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
        delete window.pendingDeleteUrl;
        return;
    }
    
    // Otherwise it's a bulk delete
    if (!window.pendingDeleteIds || window.pendingDeleteIds.length === 0) {
        alert('Pilih minimal satu toko untuk dihapus');
        return;
    }

    document.getElementById('bulkIds').value = JSON.stringify(window.pendingDeleteIds);
    
    const form = document.getElementById('bulkDeleteForm');
    form.action = '{{ route('toko.bulk-destroy') }}';
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

    // Dropdown management
    let currentButton = null;
    const actionDropdown = document.getElementById('actionDropdown');
    const editMenuItem = document.getElementById('editMenuItem');
    const deleteMenuItem = document.getElementById('deleteMenuItem');

    // Handle action button click
    document.querySelectorAll('.btn-actions-menu').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentButton = btn;
            
            // Position dropdown - account for zoom: 90% (0.9) in app.blade
            const rect = btn.getBoundingClientRect();
            const zoomFactor = 0.9;
            const dropdownWidth = 140;
            actionDropdown.style.position = 'fixed';
            actionDropdown.style.top = (rect.top / zoomFactor) + 'px';
            actionDropdown.style.left = ((rect.left - dropdownWidth) / zoomFactor) + 'px';
            actionDropdown.style.zIndex = '1001';
            
            actionDropdown.classList.add('show');
        });
    });

    // Handle edit menu item click
    editMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentButton) {
            const editUrl = currentButton.getAttribute('data-toko-edit');
            if (editUrl) {
                window.location.href = editUrl;
            }
        }
        
        actionDropdown.classList.remove('show');
    });

    // Handle delete menu item click
    deleteMenuItem.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (currentButton) {
            const destroyUrl = currentButton.getAttribute('data-toko-destroy');
            if (destroyUrl) {
                const modal = document.getElementById('deleteConfirmModal');
                const messageEl = modal.querySelector('p.text-gray-600');
                messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus toko ini?';
                modal.classList.remove('hidden');
                window.pendingDeleteUrl = destroyUrl;
            }
        }
        
        actionDropdown.classList.remove('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.btn-actions-menu') && !e.target.closest('#actionDropdown')) {
            actionDropdown.classList.remove('show');
        }
    });

    // Close dropdown with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            actionDropdown.classList.remove('show');
        }
    });

    // Row click to edit
    document.querySelectorAll('tr[data-href]').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-actions-menu') && !e.target.closest('.toko-checkbox')) {
                window.location.href = this.dataset.href;
            }
        });
    });
});
</script>
@endpush
