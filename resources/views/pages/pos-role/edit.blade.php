@extends('layouts.app')

@section('title', 'Edit Role')

@push('style')
<!-- Page-specific styles -->
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Form Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Edit Role</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update role information</p>
            </div>
            <a href="{{ route('pos-role.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('pos-role.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5">
                
                <!-- Role Name Field -->
                <div>
                    <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Role Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama"
                        name="nama" 
                        value="{{ old('nama', $role->nama) }}"
                        placeholder="e.g., Kasir, Manager, Admin Gudang"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama') !border-red-500 @enderror"
                        autofocus
                    >
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Enter the name of the role (e.g., Kasir, Manager, Supervisor)</p>
                </div>

                <!-- Role Info -->
                <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-lightPrimary dark:bg-navy-900 p-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-600">Slug</p>
                            <p class="mt-1 text-sm font-medium text-navy-700 dark:text-white">{{ $role->slug }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-600">Users with this role</p>
                            <p class="mt-1 text-sm font-medium text-navy-700 dark:text-white">{{ $role->pengguna()->count() }} users</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-600">Created</p>
                            <p class="mt-1 text-sm font-medium text-navy-700 dark:text-white">{{ $role->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-600">Last Updated</p>
                            <p class="mt-1 text-sm font-medium text-navy-700 dark:text-white">{{ $role->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Warning Box if users exist -->
                @if($role->pengguna()->count() > 0)
                <div class="rounded-xl border border-yellow-200 dark:border-yellow-800/30 bg-yellow-50 dark:bg-yellow-900/20 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-yellow-500 dark:bg-yellow-600">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-yellow-900 dark:text-yellow-300">Active Users</p>
                            <p class="mt-1 text-xs text-yellow-800 dark:text-yellow-400">
                                This role is currently assigned to {{ $role->pengguna()->count() }} user(s). Changing the role name will update it for all assigned users.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-between gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <!-- Delete Button -->
                <button type="button" 
                        onclick="document.getElementById('deleteForm').submit();"
                        class="flex items-center gap-2 rounded-xl bg-red-100 px-6 py-3 text-sm font-bold text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                    </svg>
                    Delete Role
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('pos-role.index') }}" 
                       class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                        </svg>
                        Update Role
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (Hidden) -->
        <form id="deleteForm" action="{{ route('pos-role.destroy', $role->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('nama').focus();
    
    // Confirm before delete
    document.querySelector('button[onclick*="deleteForm"]').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus role ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('deleteForm').submit();
        }
    });
});
</script>
@endpush
