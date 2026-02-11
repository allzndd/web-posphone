
@extends('layouts.app')

@section('title', 'Subscription Status')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Subscription Status</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    View the current subscription status of all owners
                </p>
            </div>
            
            <!-- No add button for subscription status -->
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
                        <!-- No Paid Date column -->
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Owner</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Package</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Subscription Status</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Period</p>
                        </th>
                        <!-- Remove Amount, Payment Method, Payment Status, Action columns -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayaran as $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors">
                        <!-- No Paid Date cell -->
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">
                                {{ $item->owner->pengguna->nama ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $item->owner->pengguna->email ?? '' }}
                            </p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $item->tipeLayanan->nama ?? 'Trial' }}
                            </p>
                        </td>
                        <td class="py-4">
                            @if($item->is_active == 1 && $item->is_trial == 0)
                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/30 px-3 py-1 text-xs font-medium text-green-800 dark:text-green-300">
                                    <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    Aktif
                                </span>
                            @elseif($item->is_trial == 1 && $item->is_active == 0)
                                <span class="inline-flex items-center rounded-full bg-purple-100 dark:bg-purple-900/30 px-3 py-1 text-xs font-medium text-purple-800 dark:text-purple-300">
                                    <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    Trial
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-900/30 px-3 py-1 text-xs font-medium text-gray-800 dark:text-gray-300">
                                    <svg class="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                @if($item->started_date && $item->end_date)
                                    {{ \Carbon\Carbon::parse($item->started_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </td>
                        <!-- Remove Amount, Payment Method, Payment Status, Action cells -->
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
