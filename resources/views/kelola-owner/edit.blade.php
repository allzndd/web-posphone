@extends('layouts.app')

@section('title', 'Edit Owner')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Edit Owner</h4>
        
        <form action="{{ route('kelola-owner.update', $owner->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Owner Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $owner->nama) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $owner->email) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Password <span class="text-gray-500">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                @if($subscription)
                <div class="md:col-span-2">
                    <div class="rounded-xl bg-blue-50 dark:bg-navy-900 p-4 mb-4">
                        <h5 class="text-sm font-bold text-navy-700 dark:text-white mb-2">Current Subscription</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Package: <span class="font-semibold">{{ $subscription->tipeLayanan->nama ?? '-' }}</span> | 
                            Started: <span class="font-semibold">{{ $subscription->started_date->format('d M Y') }}</span> | 
                            Expires: <span class="font-semibold">{{ $subscription->end_date->format('d M Y') }}</span>
                        </p>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Package</label>
                    <select name="tipe_layanan_id" id="packageSelect"
                            style="text-overflow: ellipsis; overflow: visible; white-space: normal;"
                            class="mt-2 h-auto min-h-[48px] w-full rounded-xl border border-gray-200 bg-white p-3 text-sm outline-none dark:!border-white/10 dark:text-white dark:bg-navy-800 @error('tipe_layanan_id') border-red-500 @enderror">
                        <option value="">Keep Current Package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" 
                                    data-duration="{{ $package->durasi }}"
                                    {{ old('tipe_layanan_id', $subscription->tipe_layanan_id ?? '') == $package->id ? 'selected' : '' }}>
                                {{ $package->nama }} - Rp {{ number_format($package->harga, 0, ',', '.') }} ({{ $package->durasi }} bulan)
                            </option>
                        @endforeach
                    </select>
                    @error('tipe_layanan_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Started Date</label>
                    <input type="date" name="started_date" id="startedDate" value="{{ old('started_date', $subscription->started_date->format('Y-m-d')) }}"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('started_date') border-red-500 @enderror">
                    @error('started_date')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">End Date</label>
                    <input type="date" name="end_date" id="endDate" value="{{ old('end_date', $subscription->end_date->format('Y-m-d')) }}"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center gap-4 md:col-span-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_trial" value="1" {{ old('is_trial', $subscription->is_trial) ? 'checked' : '' }}
                               class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                        <span class="ml-2 text-sm font-medium text-navy-700 dark:text-white">Trial Mode</span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}
                               class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                        <span class="ml-2 text-sm font-medium text-navy-700 dark:text-white">Active</span>
                    </label>
                </div>
                @else
                <div class="md:col-span-2">
                    <div class="rounded-xl bg-yellow-50 dark:bg-yellow-900/30 p-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">
                            No subscription found for this owner. Please create one from Services menu.
                        </p>
                    </div>
                </div>
                @endif
                
                <div class="hidden">
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('status') border-red-500 @enderror">
                        <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Expired" {{ old('status', 'Active') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Update Owner
                </button>
                <a href="{{ route('kelola-owner.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@if($subscription)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById('packageSelect');
    const startedDate = document.getElementById('startedDate');
    const endDate = document.getElementById('endDate');
    
    function calculateEndDate() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');
        const startDate = startedDate.value;
        
        if (duration && startDate && selectedOption.value) {
            const start = new Date(startDate);
            start.setMonth(start.getMonth() + parseInt(duration));
            
            const year = start.getFullYear();
            const month = String(start.getMonth() + 1).padStart(2, '0');
            const day = String(start.getDate()).padStart(2, '0');
            
            endDate.value = `${year}-${month}-${day}`;
        }
    }
    
    packageSelect.addEventListener('change', calculateEndDate);
    startedDate.addEventListener('change', calculateEndDate);
});
</script>
@endif
@endsection
