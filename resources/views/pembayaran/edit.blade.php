@extends('layouts.app')

@section('title', 'Edit Payment')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Edit Payment</h4>
        
        <form action="{{ route('pembayaran.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Date</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $item->tanggal->format('Y-m-d')) }}" required
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('tanggal') border-red-500 @enderror">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Owner</label>
                    <select name="owner_id" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('owner_id') border-red-500 @enderror">
                        <option value="">Select Owner</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ (old('owner_id', $item->owner_id) == $owner->id) ? 'selected' : '' }}>
                                {{ $owner->name }} ({{ $owner->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Package</label>
                    <select name="paket" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('paket') border-red-500 @enderror">
                        <option value="">Select Package</option>
                        <option value="Starter Package" {{ old('paket', $item->paket) == 'Starter Package' ? 'selected' : '' }}>Starter Package</option>
                        <option value="Basic Package" {{ old('paket', $item->paket) == 'Basic Package' ? 'selected' : '' }}>Basic Package</option>
                        <option value="Professional Package" {{ old('paket', $item->paket) == 'Professional Package' ? 'selected' : '' }}>Professional Package</option>
                        <option value="Premium Package" {{ old('paket', $item->paket) == 'Premium Package' ? 'selected' : '' }}>Premium Package</option>
                        <option value="Enterprise Package" {{ old('paket', $item->paket) == 'Enterprise Package' ? 'selected' : '' }}>Enterprise Package</option>
                    </select>
                    @error('paket')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Period</label>
                    <select name="periode" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('periode') border-red-500 @enderror">
                        <option value="">Select Period</option>
                        <option value="1 Month" {{ old('periode', $item->periode) == '1 Month' ? 'selected' : '' }}>1 Month</option>
                        <option value="3 Months" {{ old('periode', $item->periode) == '3 Months' ? 'selected' : '' }}>3 Months</option>
                        <option value="6 Months" {{ old('periode', $item->periode) == '6 Months' ? 'selected' : '' }}>6 Months</option>
                        <option value="1 Year" {{ old('periode', $item->periode) == '1 Year' ? 'selected' : '' }}>1 Year</option>
                    </select>
                    @error('periode')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Total Amount (Rp)</label>
                    <input type="number" name="total" value="{{ old('total', $item->total) }}" required min="0" step="0.01"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('total') border-red-500 @enderror">
                    @error('total')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Status</label>
                    <select name="status" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('status') border-red-500 @enderror">
                        <option value="Pending" {{ old('status', $item->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ old('status', $item->status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Failed" {{ old('status', $item->status) == 'Failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Notes (Optional)</label>
                    <textarea name="notes" rows="3"
                              class="mt-2 flex w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes', $item->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Update
                </button>
                <a href="{{ route('pembayaran.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
