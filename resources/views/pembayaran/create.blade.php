@extends('layouts.app')

@section('title', 'Add Payment')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none p-6">
        <h4 class="text-xl font-bold text-navy-700 dark:text-white mb-6">Add New Payment</h4>
        
        <form action="{{ route('pembayaran.store') }}" method="POST">
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
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Subscription</label>
                    <select name="langganan_id" id="langganan_id" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('langganan_id') border-red-500 @enderror">
                        <option value="">Select Owner First</option>
                    </select>
                    @error('langganan_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Amount (Rp)</label>
                    <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}" required min="0" step="0.01"
                           class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('nominal') border-red-500 @enderror">
                    @error('nominal')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Payment Method</label>
                    <select name="metode_pembayaran" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('metode_pembayaran') border-red-500 @enderror">
                        <option value="">Select Method</option>
                        <option value="Transfer Bank" {{ old('metode_pembayaran') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="E-Wallet" {{ old('metode_pembayaran') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="Cash" {{ old('metode_pembayaran') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Credit Card" {{ old('metode_pembayaran') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                    </select>
                    @error('metode_pembayaran')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-navy-700 dark:text-white">Status</label>
                    <select name="status" required
                            class="mt-2 flex h-12 w-full items-center justify-center rounded-xl border border-gray-200 bg-white/0 p-3 text-sm outline-none dark:!border-white/10 dark:text-white @error('status') border-red-500 @enderror">
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ old('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Failed" {{ old('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="linear rounded-xl bg-brand-500 px-6 py-3 text-base font-medium text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700">
                    Save
                </button>
                <a href="{{ route('pembayaran.index') }}"
                   class="linear rounded-xl bg-gray-100 px-6 py-3 text-base font-medium text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('owner_id').addEventListener('change', function() {
    const ownerId = this.value;
    const langgananSelect = document.getElementById('langganan_id');
    const nominalInput = document.getElementById('nominal');
    
    langgananSelect.innerHTML = '<option value="">Loading...</option>';
    
    if (!ownerId) {
        langgananSelect.innerHTML = '<option value="">Select Owner First</option>';
        nominalInput.value = '';
        return;
    }
    
    fetch(`/api/langganan/owner/${ownerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                langgananSelect.innerHTML = '<option value="">No Subscription Found</option>';
                nominalInput.value = '';
            } else {
                langgananSelect.innerHTML = '<option value="">Select Subscription</option>';
                data.forEach(langganan => {
                    const option = document.createElement('option');
                    option.value = langganan.id;
                    option.textContent = `${langganan.tipe_layanan.nama} (${langganan.started_date} - ${langganan.end_date})`;
                    option.dataset.harga = langganan.tipe_layanan.harga;
                    langgananSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            langgananSelect.innerHTML = '<option value="">Error Loading Data</option>';
        });
});

document.getElementById('langganan_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const harga = selectedOption.dataset.harga;
    const nominalInput = document.getElementById('nominal');
    
    if (harga) {
        nominalInput.value = harga;
    } else {
        nominalInput.value = '';
    }
});
</script>
@endsection

