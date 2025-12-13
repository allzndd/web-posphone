@extends('layouts.app')

@section('title', 'Add Service')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Add New Service</h4>
        
        <form action="{{ route('langganan.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Owner</label>
                    <select name="owner_id" id="owner_id" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('owner_id') border-red-500 @enderror">
                        <option value="">Select Owner</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                                {{ $owner->pengguna->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Service Package</label>
                    <select name="tipe_layanan_id" id="tipe_layanan_id" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('tipe_layanan_id') border-red-500 @enderror">
                        <option value="">Select Package</option>
                        @foreach($paketLayanan as $paket)
                            <option value="{{ $paket->id }}" 
                                    data-durasi="{{ $paket->durasi }}"
                                    {{ old('tipe_layanan_id') == $paket->id ? 'selected' : '' }}>
                                {{ $paket->nama }} - Rp {{ number_format($paket->harga, 0, ',', '.') }} ({{ $paket->duration_text }})
                            </option>
                        @endforeach
                    </select>
                    @error('tipe_layanan_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Start Date</label>
                    <input type="date" name="started_date" id="started_date" value="{{ old('started_date', date('Y-m-d')) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('started_date') border-red-500 @enderror">
                    @error('started_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" readonly
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-gray-100 p-3 text-sm outline-none dark:!border-white/10 dark:bg-navy-900 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-calculated based on package duration</p>
                </div>
                
                <div class="md:col-span-2">
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_trial" value="1" {{ old('is_trial') ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-900">
                            <span class="text-sm font-medium text-navy-700 dark:text-white">Trial Period</span>
                        </label>
                        
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-white/10 dark:bg-navy-900">
                            <span class="text-sm font-medium text-navy-700 dark:text-white">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Save
                </button>
                <a href="{{ route('langganan.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-calculate end date based on package duration
document.getElementById('tipe_layanan_id').addEventListener('change', calculateEndDate);
document.getElementById('started_date').addEventListener('change', calculateEndDate);

function calculateEndDate() {
    const packageSelect = document.getElementById('tipe_layanan_id');
    const startDateInput = document.getElementById('started_date');
    const endDateInput = document.getElementById('end_date');
    
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    const durasi = selectedOption.dataset.durasi; // durasi dalam bulan
    const startDate = startDateInput.value;
    
    if (durasi && startDate) {
        const start = new Date(startDate);
        start.setMonth(start.getMonth() + parseInt(durasi));
        
        const year = start.getFullYear();
        const month = String(start.getMonth() + 1).padStart(2, '0');
        const day = String(start.getDate()).padStart(2, '0');
        
        endDateInput.value = `${year}-${month}-${day}`;
    }
}

// Calculate on page load if values exist
if (document.getElementById('tipe_layanan_id').value && document.getElementById('started_date').value) {
    calculateEndDate();
}
</script>
@endsection
