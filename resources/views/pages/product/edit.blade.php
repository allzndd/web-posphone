@extends('layouts.app')

@section('title', 'Edit Product')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Advanced Forms</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('product.index') }}">Products</a></div>
                    <div class="breadcrumb-item">Edit Product</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Product</h2>



                <div class="card">
                    <form action="{{ route('product.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h4>Input Text</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $product->name) }}" list="productNames" autocomplete="off" placeholder="Pilih atau ketik nama produk">
                                <datalist id="productNames">
                                    @if(isset($productNames))
                                        @foreach($productNames as $n)
                                            <option value="{{ $n }}"></option>
                                        @endforeach
                                    @endif
                                </datalist>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Kategori</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Harga Jual (Selling Price)</label>
                                <input type="number" class="form-control @error('harga_jual') is-invalid @enderror"
                                    name="harga_jual" value="{{ old('harga_jual', $product->sell_price) }}">
                                @error('harga_jual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Harga Beli (Purchase Price)</label>
                                <input type="number" class="form-control @error('harga_beli') is-invalid @enderror"
                                    name="harga_beli" value="{{ old('harga_beli', $product->buy_price) }}">
                                @error('harga_beli')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Cost Items</label>
                                <div id="cost-items">
                                    @php
                                        $costs = old('costs', is_string($product->costs) ? json_decode($product->costs, true) : $product->costs);
                                        if (!$costs || !is_array($costs)) $costs = [[]];
                                    @endphp
                                    @foreach ($costs as $i => $cost)
                                    <div class="cost-row mb-2 d-flex">
                                        <input type="text" name="costs[{{ $i }}][description]" class="form-control mr-2" placeholder="Description" value="{{ $cost['description'] ?? '' }}">
                                        <input type="number" step="0.01" name="costs[{{ $i }}][amount]" class="form-control mr-2" placeholder="Amount" value="{{ $cost['amount'] ?? '' }}">
                                        <button type="button" class="btn btn-danger btn-remove-cost" style="display:{{ $i==0 ? 'none' : 'inline-block' }}">Remove</button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-primary mt-2" id="add-cost-row">Add Cost</button>
                            </div>
                            <div class="form-group">
                                <label>Battery Health (BH)</label>
                                <input type="text" class="form-control @error('barre_health') is-invalid @enderror"
                                    name="barre_health" value="{{ old('barre_health', $product->barre_health) }}" placeholder="e.g. 85%">
                                @error('barre_health')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Color</label>
                                <select class="form-control @error('color') is-invalid @enderror" name="color">
                                    <option value="">Pilih Color</option>
                                    @if(isset($colors))
                                        @foreach($colors as $color)
                                            <option value="{{ $color->name }}" {{ old('color', $product->color) == $color->name ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Storage</label>
                                <select class="form-control @error('storage') is-invalid @enderror" name="storage">
                                    <option value="">Pilih Storage</option>
                                    @if(isset($storages))
                                        @foreach($storages as $storage)
                                            <option value="{{ $storage->name }}" {{ old('storage', $product->storage) == $storage->name ? 'selected' : '' }}>
                                                {{ $storage->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('storage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                    name="stock" value="{{ old('stock', $product->stock) }}">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Category removed: product name will act as category for UX; DB uses default category automatically -->
                            <div class="form-group">
                                <label>IMEI</label>
                                <input type="text" class="form-control @error('imei') is-invalid @enderror"
                                    name="imei" value="{{ old('imei', $product->imei) }}">
                                @error('imei')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Accessories</label>
                                <textarea class="form-control @error('assessoris') is-invalid @enderror" name="assessoris" rows="3">{{ old('assessoris', $product->assessoris) }}</textarea>
                                @error('assessoris')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let costIndex = {{ count($costs ?? [[]]) }};
        document.getElementById('add-cost-row').onclick = function() {
            const container = document.getElementById('cost-items');
            const row = document.createElement('div');
            row.className = 'cost-row mb-2 d-flex';
            row.innerHTML = `<input type="text" name="costs[${costIndex}][description]" class="form-control mr-2" placeholder="Description">
                <input type="number" step="0.01" name="costs[${costIndex}][amount]" class="form-control mr-2" placeholder="Amount">
                <button type="button" class="btn btn-danger btn-remove-cost">Remove</button>`;
            container.appendChild(row);
            costIndex++;
        };
        document.getElementById('cost-items').addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-cost')) {
                e.target.parentElement.remove();
            }
        });
    });
</script>
@endpush
