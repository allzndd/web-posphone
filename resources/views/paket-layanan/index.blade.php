@extends('layouts.app')

@section('title', 'Service Packages')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Subscription Packages</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage POS system subscription packages for owners
                </p>
            </div>
            
            <a href="{{ route('paket-layanan.create') }}" 
               class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                </svg>
                Add Package
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
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Package Name</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Price</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Duration</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Permissions</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Action</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paket as $item)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors">
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->nama }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-semibold text-navy-700 dark:text-white">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->duration_text }}</p>
                        </td>
                        <td class="py-4">
                            @php
                                $permCount = $item->packagePermissions->count();
                                $permissionsData = $item->packagePermissions->map(function($pp) {
                                    return [
                                        'modul' => $pp->permission->modul,
                                        'aksi' => $pp->permission->aksi,
                                        'max_records' => $pp->max_records,
                                    ];
                                })->toArray();
                            @endphp
                            @if($permCount > 0)
                                <button onclick="showPermissionsModal({{ json_encode($permissionsData) }})" 
                                   class="inline-flex items-center gap-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 px-3 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $permCount }} permissions
                                </button>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">No permissions</span>
                            @endif
                        </td>
                        <td class="py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('paket-layanan.show', $item->id) }}"
                                   class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-blue-500 transition duration-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
                                   title="View">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('paket-layanan.edit', $item->id) }}" 
                                   class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white"
                                   title="Edit">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                    </svg>
                                </a>
                                <button onclick="confirmDelete('{{ route('paket-layanan.destroy', $item->id) }}')"
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
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No service packages found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this package?')) {
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

function showPermissionsModal(permissions) {
    let permissionsHtml = '';
    
    if (permissions && permissions.length > 0) {
        const aksiColors = {
            'create': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'read': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'update': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'delete': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
        };
        
        permissionsHtml = permissions.map(perm => {
            const aksiColor = aksiColors[perm.aksi] || 'bg-gray-100 text-gray-800';
            return `
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-navy-700/50">
                    <div class="flex items-center gap-3">
                        <div>
                            <p class="text-sm font-semibold text-navy-700 dark:text-white">${perm.modul}</p>
                            <span class="inline-block mt-2 rounded-md ${aksiColor} px-2.5 py-1 text-xs font-bold uppercase">${perm.aksi}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-600 dark:text-gray-400">Max Records</p>
                        <p class="text-lg font-bold text-navy-700 dark:text-white">${perm.max_records === 0 ? '∞' : perm.max_records}</p>
                    </div>
                </div>
            `;
        }).join('');
    } else {
        permissionsHtml = '<p class="py-8 text-center text-gray-500 dark:text-gray-400">No permissions assigned</p>';
    }
    
    const modalHtml = `
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70" id="permissionsModal">
            <div class="relative w-full max-w-2xl rounded-xl bg-white shadow-2xl dark:bg-navy-800">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-white/10">
                    <h3 class="text-lg font-bold text-navy-700 dark:text-white">Assigned Permissions</h3>
                    <button onclick="document.getElementById('permissionsModal').remove()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="max-h-96 space-y-3 overflow-y-auto px-6 py-4">
                    ${permissionsHtml}
                </div>

                <!-- Footer -->
                <div class="flex justify-end border-t border-gray-200 px-6 py-4 dark:border-white/10">
                    <button onclick="document.getElementById('permissionsModal').remove()" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-gray-300 dark:bg-navy-700 dark:text-white dark:hover:bg-navy-600 transition duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}
</script>
@endsection
