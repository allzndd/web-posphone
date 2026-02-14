@extends('layouts.app')

@section('title', 'Permissions Management')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Permissions Management</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Kelola permissions untuk mengatur akses menu dan fitur
                </p>
            </div>
            
            <a href="{{ route('permissions.create') }}" 
               class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                </svg>
                Tambah Permission
            </a>
        </div>

        <div class="overflow-x-auto px-6 pb-6">
            @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                {{ session('error') }}
            </div>
            @endif

            @php
                $groupedPermissions = $permissions->groupBy('modul');
            @endphp

            @forelse($groupedPermissions as $modul => $perms)
            <div class="mb-6 rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden">
                <!-- Module Header -->
                <div class="bg-gradient-to-r from-brand-500 to-brand-600 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <h5 class="text-base font-bold text-white">{{ ucfirst($modul) }}</h5>
                        <span class="text-xs text-white/80">{{ $perms->count() }} permissions</span>
                    </div>
                </div>

                <!-- Permissions Table -->
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-navy-900">
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="py-3 px-6 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Nama Permission</p>
                            </th>
                            <th class="py-3 px-6 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Aksi</p>
                            </th>
                            <th class="py-3 px-6 text-left">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Created At</p>
                            </th>
                            <th class="py-3 px-6 text-center">
                                <p class="text-xs font-bold text-gray-600 dark:text-white uppercase">Action</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perms as $permission)
                        <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors">
                            <td class="py-3 px-6">
                                <p class="text-sm font-medium text-navy-700 dark:text-white">{{ $permission->nama }}</p>
                            </td>
                            <td class="py-3 px-6">
                                @php
                                    $aksiColors = [
                                        'create' => 'green',
                                        'read' => 'blue',
                                        'update' => 'yellow',
                                        'delete' => 'red',
                                    ];
                                    $color = $aksiColors[$permission->aksi] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 px-3 py-1 text-xs font-medium text-{{ $color }}-800 dark:text-{{ $color }}-300">
                                    {{ ucfirst($permission->aksi) }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $permission->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('permissions.edit', $permission->id) }}" 
                                       class="flex h-8 w-8 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white"
                                       title="Edit">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="none" d="M0 0h24v24H0z"></path>
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('{{ route('permissions.destroy', $permission->id) }}')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400"
                                            title="Delete">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
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
            @empty
            <div class="py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada permissions</p>
                    <a href="{{ route('permissions.create') }}" class="mt-4 text-brand-500 hover:text-brand-600 font-medium">
                        + Tambah Permission Baru
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Apakah Anda yakin ingin menghapus permission ini?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        let csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        let methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
