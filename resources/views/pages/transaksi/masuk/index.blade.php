@extends('layouts.app')

@section('title', 'Incoming Transactions')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Transactions Table Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Incoming Transactions</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $transaksi->total() }} incoming transactions (sales)
                </p>
            </div>
            
            <!-- Add New Button -->
            <div>
                <a href="{{ route('transaksi.masuk.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-green-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-green-600 active:bg-green-700 dark:bg-green-400 dark:hover:bg-green-300 dark:active:bg-green-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    New Income
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Invoice</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Toko</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Customer</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Total</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Payment</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Status</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Date</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $item)
                    <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition" 
                        data-href="{{ route('transaksi.masuk.edit', $item->id) }}"
                        onclick="window.location = this.dataset.href">
                        <!-- Invoice -->
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->invoice }}</p>
                        </td>
                        <!-- Toko -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->toko->nama ?? '-' }}</p>
                        </td>
                        <!-- Customer -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->pelanggan->nama ?? '-' }}</p>
                        </td>
                        <!-- Total -->
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-green-500">{{ get_currency_symbol() }} {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                        </td>
                        <!-- Payment Method -->
                        <td class="py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-100 dark:bg-purple-500/20 px-3 py-1 text-xs font-bold text-purple-600 dark:text-purple-300">
                                {{ ucfirst($item->metode_pembayaran ?? 'N/A') }}
                            </span>
                        </td>
                        <!-- Status -->
                        <td class="py-4 text-center">
                            @if($item->status == 'pending')
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-orange-100 dark:bg-orange-500/20 px-3 py-1 text-xs font-bold text-orange-600 dark:text-orange-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    Pending
                                </span>
                            @elseif($item->status == 'completed')
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-100 dark:bg-green-500/20 px-3 py-1 text-xs font-bold text-green-600 dark:text-green-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 dark:bg-red-500/20 px-3 py-1 text-xs font-bold text-red-600 dark:text-red-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    Cancelled
                                </span>
                            @endif
                        </td>
                        <!-- Date -->
                        <td class="py-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('d M Y') }}</p>
                        </td>
                        <!-- Actions -->
                        <td class="py-4 text-center">
                            <div class="relative" onclick="event.stopPropagation()">
                                <button onclick="toggleDropdown({{ $item->id }})" class="flex items-center justify-center rounded-lg bg-lightPrimary p-2 text-gray-600 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="dropdown-{{ $item->id }}" class="hidden absolute right-0 mt-2 w-48 rounded-lg bg-white shadow-lg dark:bg-navy-800 border border-gray-200 dark:border-white/10 z-50">
                                    <div class="py-1">
                                        <a href="{{ route('transaksi.masuk.edit', $item->id) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <a href="{{ route('transaksi.masuk.print', $item->id) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/10">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            Print
                                        </a>
                                        <form action="{{ route('transaksi.masuk.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No incoming transactions found</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Start by creating a new incoming transaction</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transaksi->hasPages())
        <div class="px-6 pb-6">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Success Message Toast (akan muncul jika ada) -->
<div id="successToast" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999;">
    <div style="background-color: #10b981; color: white; padding: 16px 24px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); min-width: 300px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p style="margin: 0; font-weight: 600; font-size: 14px;" id="successToastMessage"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Make table rows clickable
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('tr[data-href]');
        rows.forEach(row => {
            row.style.cursor = 'pointer';
        });

        // PENTING: Check untuk success message dari sessionStorage (dari halaman print)
        console.log('Checking for success message...');
        const sessionKey = 'transaksi_success_message';
        const successMessage = sessionStorage.getItem(sessionKey);
        
        console.log('Success message from sessionStorage:', successMessage);
        
        if (successMessage) {
            // Hapus dari sessionStorage
            sessionStorage.removeItem(sessionKey);
            
            console.log('Showing success toast...');
            // Tampilkan toast
            showSuccessToast(successMessage);
        }

        // Check for success message dari session Laravel (method lain)
        @if(session('success'))
            console.log('Success message from Laravel session');
            showSuccessToast('{{ session('success') }}');
        @endif
    });

    function showSuccessToast(message) {
        console.log('showSuccessToast called with message:', message);
        
        const toast = document.getElementById('successToast');
        const messageEl = document.getElementById('successToastMessage');
        
        if (!toast || !messageEl) {
            console.error('Toast elements not found!');
            // Fallback: gunakan alert
            alert(message);
            return;
        }
        
        messageEl.textContent = message;
        toast.style.display = 'block';
        
        console.log('Toast should be visible now');
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            toast.style.display = 'none';
        }, 5000);
    }

    // Toggle dropdown menu
    function toggleDropdown(id) {
        const dropdown = document.getElementById('dropdown-' + id);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        
        // Close all other dropdowns
        allDropdowns.forEach(function(dd) {
            if (dd.id !== 'dropdown-' + id) {
                dd.classList.add('hidden');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[onclick^="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            allDropdowns.forEach(function(dropdown) {
                dropdown.classList.add('hidden');
            });
        }
    });
</script>
@endpush
