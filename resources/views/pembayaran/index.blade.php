@extends('layouts.app')

@section('title', 'Subscription Payments')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Subscription Payments</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage subscription payments from owners renting the system
                </p>
            </div>
            
            <a href="{{ route('pembayaran.create') }}" 
               class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                </svg>
                Add Payment
            </a>
        </div>

        <div class="overflow-x-auto px-6 pb-6">
            @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                {{ session('success') }}
            </div>
            @endif

            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Date</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Owner</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Email</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Package</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Period</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Total</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Status</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Action</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaran as $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors">
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->tanggal->format('d/m/Y') }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->owner_name }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->email }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->paket }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->periode }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-semibold text-navy-700 dark:text-white">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                        </td>
                        <td class="py-4">
                            @if($item->status === 'Paid')
                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                                    <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    Paid
                                </span>
                            @elseif($item->status === 'Pending')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-300">
                                    <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-xs font-medium text-red-800 dark:text-red-300">
                                    <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    Failed
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('pembayaran.show', $item->id) }}" 
                                   class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-500 transition duration-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
                                   title="View">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('pembayaran.edit', $item->id) }}" 
                                   class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white"
                                   title="Edit">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                    </svg>
                                </a>
                                <button onclick="confirmDelete('{{ route('pembayaran.destroy', $item->id) }}')"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400"
                                        title="Delete">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this payment?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
