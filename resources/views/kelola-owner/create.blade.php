@extends('layouts.app')

@section('title', 'Add Owner')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Add New Owner</h4>
        
        @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-800 dark:bg-red-900/30 dark:text-red-300">
            <p class="font-bold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('kelola-owner.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Owner Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('telepon') border-red-500 @enderror">
                    @error('telepon')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white">
                </div>
                
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Package <span class="text-red-500">*</span></label>
                    <select name="tipe_layanan_id" id="packageSelect" required
                            style="text-overflow: ellipsis; overflow: visible; white-space: normal;"
                            class="mt-2 h-auto min-h-[48px] w-full rounded-xl border border-gray-200 bg-white p-3 text-sm outline-none dark:!border-white/10 dark:text-white dark:bg-navy-800 @error('tipe_layanan_id') border-red-500 @enderror">
                        <option value="">Select Package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" 
                                    data-duration="{{ $package->durasi }}" 
                                    data-price="Rp {{ number_format($package->harga, 0, ',', '.') }}">
                                {{ $package->nama }} - Rp {{ number_format($package->harga, 0, ',', '.') }} ({{ $package->durasi }} bulan)
                            </option>
                        @endforeach
                    </select>
                    @error('tipe_layanan_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Started Date <span class="text-red-500">*</span></label>
                    <input type="date" name="started_date" id="startedDate" value="{{ old('started_date', now()->format('Y-m-d')) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('started_date') border-red-500 @enderror">
                    @error('started_date')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">End Date <span class="text-gray-500">(auto-calculated)</span></label>
                    <input type="date" id="endDate" readonly
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm outline-none dark:!border-white/10 dark:text-white dark:bg-navy-700">
                </div>
                
                <div class="flex items-center gap-3 md:col-span-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_trial" value="1" {{ old('is_trial') ? 'checked' : '' }}
                               class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-800">
                        <span class="ml-2 text-sm font-medium text-navy-700 dark:text-white">Trial Mode</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Save Owner
                </button>
                <a href="{{ route('kelola-owner.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById('packageSelect');
    const startedDate = document.getElementById('startedDate');
    const endDate = document.getElementById('endDate');
    
    function calculateEndDate() {
        const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');
        const startDate = startedDate.value;
        
        if (duration && startDate) {
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
    
    // Calculate on page load if values exist
    if (packageSelect.value && startedDate.value) {
        calculateEndDate();
    }
});
</script>
@endsection
