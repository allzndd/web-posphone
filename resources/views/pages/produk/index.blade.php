@extends('layouts.app')

@section('title', 'Products')

@push('style')
<!-- Load reusable table components CSS -->
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Products Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Products</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $produk->total() }} total products
                </p>
            </div>
            
            <!-- Search & Add & Bulk Delete Button -->
            <div class="flex items-center gap-3">
                <!-- Search Form -->
                <form method="GET" action="{{ route('produk.index') }}" class="relative">
                    <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 px-4 py-2">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white mr-2" xmlns="http://www.w3.org/2000/svg">
                            <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                        </svg>
                        <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Search products..." 
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
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Product Name</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">SKU/Model</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Product Type</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Storage</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">IMEI</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Purchase Price</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Selling Price</p>
                        </th>
                        <th class="py-3 text-center col-actions">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produk as $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('produk.edit', $item) }}">
                        <td class="py-4" style="width: 40px;" onclick="event.stopPropagation()">
                            <input type="checkbox" class="produk-checkbox rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-navy-700 cursor-pointer" 
                                   value="{{ $item->id }}" 
                                   onchange="updateBulkDeleteButton()">
                        </td>
                        <td class="py-4 col-no">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ ($produk->currentPage() - 1) * $produk->perPage() + $loop->iteration }}</p>
                        </td>
                        <td class="py-4">
                            @if($item->merk)
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->merk->nama }}</p>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800/30 px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                                    No Product Name
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $item->merk->nama ?? '-' }} {{ $item->warna ?? '' }}
                            </p>
                        </td>
                        <td class="py-4">
                            @if($item->product_type === 'electronic')
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-3 py-1 text-xs font-medium text-blue-800 dark:text-blue-300">
                                    <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                    Electronic/HP
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-purple-100 dark:bg-purple-900/30 px-3 py-1 text-xs font-medium text-purple-800 dark:text-purple-300">
                                    <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                                    </svg>
                                    Accessories
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->penyimpanan ?? '-' }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->imei ?? '-' }}</p>
                        </td>
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-brand-500 dark:text-brand-400">
                                {{ get_currency_symbol() }} {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </p>
                        </td>
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-green-500 dark:text-green-400">
                                {{ get_currency_symbol() }} {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </p>
                        </td>
                        <td class="py-4 col-actions" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center">
                                <button class="btn-actions-menu relative" data-produk-id="{{ $item->id }}" data-produk-name="{{ $item->nama }}" data-produk-edit="{{ route('produk.edit', $item) }}" data-produk-destroy="{{ route('produk.destroy', $item) }}" data-destroy="{{ route('produk.destroy', $item) }}">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 8c1.1 0 2-0.9 2-2s-0.9-2-2-2-2 0.9-2 2 0.9 2 2 2zm0 2c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2zm0 6c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No products found</p>
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
                    <form method="GET" action="{{ route('produk.index') }}" class="inline-block">
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
                        Showing {{ $produk->firstItem() ?? 0 }} to {{ $produk->lastItem() ?? 0 }} of {{ $produk->total() }}
                    </span>
                </div>

                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1">
                    @if ($produk->onFirstPage())
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">◀</span>
                    @else
                        <a href="{{ $produk->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" 
                           class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">◀</a>
                    @endif

                    @for ($page = max(1, $produk->currentPage() - 2); $page <= min($produk->lastPage(), $produk->currentPage() + 2); $page++)
                        @if ($page == $produk->currentPage())
                            <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $produk->url($page) }}&per_page={{ request('per_page', 10) }}" 
                               class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($produk->hasMorePages())
                        <a href="{{ $produk->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" 
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
    const checkboxes = document.querySelectorAll('.produk-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.produk-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
    } else {
        bulkDeleteBtn.classList.add('hidden');
        document.getElementById('selectAllCheckbox').checked = false;
    }
}

function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.produk-checkbox:checked');
    const count = checkedBoxes.length;
    
    if (count === 0) {
        alert('Pilih minimal satu produk untuk dihapus');
        return;
    }

    const modal = document.getElementById('deleteConfirmModal');
    const messageEl = modal.querySelector('p.text-gray-600');
    messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus <span class="font-bold text-red-600 dark:text-red-400">' + count + ' produk' + (count > 1 ? '' : '') + '</span>?';
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
        alert('Pilih minimal satu produk untuk dihapus');
        return;
    }

    document.getElementById('bulkIds').value = JSON.stringify(window.pendingDeleteIds);
    
    const form = document.getElementById('bulkDeleteForm');
    form.action = '{{ route('produk.bulk-destroy') }}';
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
        confirmDeleteMessage: 'Apakah Anda yakin ingin menghapus produk ini?'
    });

    // Custom handling for delete menu item click
    document.getElementById('deleteMenuItem').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Get the active button from the dropdown
        const activeButton = document.querySelector('.btn-actions-menu[data-active="true"]') || 
                            document.querySelector('.btn-actions-menu:focus') ||
                            window.currentActionButton;
        
        if (activeButton) {
            const destroyUrl = activeButton.getAttribute('data-destroy') || 
                              activeButton.getAttribute('data-produk-destroy');
            
            if (destroyUrl) {
                // Show modal and set the URL
                const modal = document.getElementById('deleteConfirmModal');
                const messageEl = modal.querySelector('p.text-gray-600');
                messageEl.innerHTML = 'Apakah Anda yakin ingin menghapus produk ini?';
                modal.classList.remove('hidden');
                window.pendingDeleteUrl = destroyUrl;
            } else {
                alert('Delete URL not found');
            }
        }
    });

    // Store button reference when action menu is clicked
    document.querySelectorAll('.btn-actions-menu').forEach(button => {
        button.addEventListener('click', function() {
            window.currentActionButton = this;
        });
    });

    document.querySelectorAll('tr[data-href]').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-actions-menu') && !e.target.closest('.produk-checkbox')) {
                window.location.href = this.dataset.href;
            }
        });
    });
});

</script>
@endpush
