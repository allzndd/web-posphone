@extends('layouts.app')

@section('title', 'Create POS User')

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
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Create New POS User</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new employee to your POS system</p>
            </div>
            <a href="{{ route('pos-pengguna.index') }}" 
               class="flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path>
                </svg>
                Back to List
            </a>
        </div>

        <form action="{{ route('pos-pengguna.store') }}" method="POST">
            @csrf
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                
                <!-- Name Field -->
                <div class="md:col-span-2">
                    <label for="nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama"
                        name="nama" 
                        value="{{ old('nama') }}"
                        placeholder="Enter full name"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('nama') !border-red-500 @enderror"
                    >
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="Enter email address"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('email') !border-red-500 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="password"
                            name="password"
                            placeholder="Enter password"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 pl-12 pr-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('password') !border-red-500 @enderror"
                        >
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Minimum 6 characters</p>
                </div>

                <!-- Role Selection -->
                <div>
                    <label for="pos_role_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select 
                            id="pos_role_id"
                            name="pos_role_id" 
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 @error('pos_role_id') !border-red-500 @enderror appearance-none"
                        >
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('pos_role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M7 10l5 5 5-5z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('pos_role_id')
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-2">
                        <button type="button" onclick="openRoleModal()" class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                            + Add New Role
                        </button>
                    </div>
                </div>

                <!-- Store Selection -->
                <div>
                    <label for="pos_toko_id" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                        Store (Optional)
                    </label>
                    <div class="relative">
                        <select 
                            id="pos_toko_id"
                            name="pos_toko_id" 
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0 appearance-none"
                        >
                            <option value="">No Store Assignment</option>
                            @foreach($tokos as $toko)
                                <option value="{{ $toko->id }}" {{ old('pos_toko_id') == $toko->id ? 'selected' : '' }}>
                                    {{ $toko->nama }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5 text-gray-400 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M7 10l5 5 5-5z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- <p class="mt-2 text-xs text-gray-500 dark:text-gray-600">Assign user to a specific store</p> -->
                    <div class="mt-2">
                        <button type="button" onclick="openStoreModal()" class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                            + Add New Store
                        </button>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 dark:border-white/10 pt-6">
                <a href="{{ route('pos-pengguna.index') }}" 
                   class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    Create User
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Add New Store -->
    <div id="storeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md rounded-[20px] bg-white dark:bg-navy-800 shadow-3xl p-6">
            <div class="mb-4 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white">Add New Store</h5>
                <button type="button" onclick="closeStoreModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                    </svg>
                </button>
            </div>

            <form id="storeForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="store_nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Store Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="store_nama"
                            name="nama"
                            placeholder="Enter store name"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                            required
                        >
                        <p id="store_nama_error" class="mt-2 text-sm text-red-500 dark:text-red-400 hidden"></p>
                    </div>

                    <div>
                        <label for="store_alamat" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Address
                        </label>
                        <textarea 
                            id="store_alamat"
                            name="alamat"
                            rows="3"
                            placeholder="Enter store address"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeStoreModal()" 
                            class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                        </svg>
                        <span id="submitStoreBtnText">Save Store</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add New Role -->
    <div id="roleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md rounded-[20px] bg-white dark:bg-navy-800 shadow-3xl p-6">
            <div class="mb-4 flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-4">
                <h5 class="text-lg font-bold text-navy-700 dark:text-white">Add New Role</h5>
                <button type="button" onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                    </svg>
                </button>
            </div>

            <form id="roleForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="role_nama" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Role Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="role_nama"
                            name="nama"
                            placeholder="Enter role name"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                            required
                        >
                        <p id="role_nama_error" class="mt-2 text-sm text-red-500 dark:text-red-400 hidden"></p>
                    </div>

                    <div>
                        <label for="role_deskripsi" class="mb-2 block text-sm font-bold text-navy-700 dark:text-white">
                            Description
                        </label>
                        <textarea 
                            id="role_deskripsi"
                            name="deskripsi"
                            rows="3"
                            placeholder="Enter role description"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-3 text-sm text-navy-700 dark:text-white outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-brand-500 dark:focus:border-brand-400 focus:ring-0"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRoleModal()" 
                            class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 dark:bg-brand-400 dark:hover:bg-brand-300">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                        </svg>
                        <span id="submitBtnText">Save Role</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on name field
    document.getElementById('nama').focus();
});

// Modal functions for Store
function openStoreModal() {
    document.getElementById('storeModal').classList.remove('hidden');
    document.getElementById('storeModal').classList.add('flex');
    document.getElementById('store_nama').focus();
}

function closeStoreModal() {
    document.getElementById('storeModal').classList.add('hidden');
    document.getElementById('storeModal').classList.remove('flex');
    document.getElementById('storeForm').reset();
    document.getElementById('store_nama_error').classList.add('hidden');
}

// Modal functions for Role
function openRoleModal() {
    document.getElementById('roleModal').classList.remove('hidden');
    document.getElementById('roleModal').classList.add('flex');
    document.getElementById('role_nama').focus();
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
    document.getElementById('roleModal').classList.remove('flex');
    document.getElementById('roleForm').reset();
    document.getElementById('role_nama_error').classList.add('hidden');
}

// Handle store form submission
document.getElementById('storeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const submitBtnText = document.getElementById('submitStoreBtnText');
    const originalText = submitBtnText.textContent;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtnText.textContent = 'Saving...';
    
    // Hide previous errors
    document.getElementById('store_nama_error').classList.add('hidden');
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("toko.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server tidak mengembalikan JSON response');
        }
        
        const data = await response.json();
        
        if (response.ok) {
            // Add new option to select
            const select = document.getElementById('pos_toko_id');
            const option = new Option(data.toko.nama, data.toko.id, true, true);
            select.add(option);
            
            // Close modal and reset form
            closeStoreModal();
        } else {
            // Show validation errors
            if (data.errors && data.errors.nama) {
                const errorElement = document.getElementById('store_nama_error');
                errorElement.textContent = data.errors.nama[0];
                errorElement.classList.remove('hidden');
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan store');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan store');
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtnText.textContent = originalText;
    }
});

// Handle role form submission
document.getElementById('roleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const submitBtnText = document.getElementById('submitBtnText');
    const originalText = submitBtnText.textContent;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtnText.textContent = 'Saving...';
    
    // Hide previous errors
    document.getElementById('role_nama_error').classList.add('hidden');
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("pos-role.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server tidak mengembalikan JSON response');
        }
        
        const data = await response.json();
        
        if (response.ok) {
            // Add new option to select
            const select = document.getElementById('pos_role_id');
            const option = new Option(data.role.nama, data.role.id, true, true);
            select.add(option);
            
            // Close modal and reset form
            closeRoleModal();
        } else {
            // Show validation errors
            if (data.errors && data.errors.nama) {
                const errorElement = document.getElementById('role_nama_error');
                errorElement.textContent = data.errors.nama[0];
                errorElement.classList.remove('hidden');
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan role');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan role');
    } finally {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtnText.textContent = originalText;
    }
});

// Close store modal when clicking outside
document.getElementById('storeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStoreModal();
    }
});

// Close role modal when clicking outside
document.getElementById('roleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRoleModal();
    }
});
</script>
@endpush
