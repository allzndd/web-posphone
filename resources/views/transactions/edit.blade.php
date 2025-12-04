@extends('layouts.app')

@section('title', 'Edit Transaction')

@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/bootstrap-daterangepicker/daterangepicker.css') }}">
<style>
    .product-row:not(:last-child) {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Transaction</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('transaction.index') }}">Transactions</a></div>
                <div class="breadcrumb-item">Edit</div>
                <div class="breadcrumb-item active">#{{ $transaction->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <form action="{{ route('transaction.update', $transaction->id) }}" method="POST" id="transactionForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-12 col-md-7">
                        <!-- Customer & Basic Info -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Transaction Details</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $transaction->customer->name }}"
                                           readonly>
                                    <input type="hidden" name="customer_id" value="{{ $transaction->customer_id }}">
                                </div>

                                <div class="form-group">
                                    <label for="type">Transaction Type</label>
                                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                                        <option value="purchase" {{ $transaction->type == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                        <option value="trade-in" {{ $transaction->type == 'trade-in' ? 'selected' : '' }}>Trade-in</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="date">Transaction Date</label>
                                    <input type="text"
                                           class="form-control datepicker @error('date') is-invalid @enderror"
                                           name="date"
                                           value="{{ old('date', $transaction->date->format('Y-m-d')) }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Products</h4>
                                <div class="card-header-action">
                                    <button type="button" class="btn btn-primary" id="addProductRow">
                                        <i class="fas fa-plus"></i> Add Product
                                    </button>
                                </div>
                            </div>
                            <div class="card-body" id="productRows">
                                @foreach($transaction->items as $index => $item)
                                    <div class="product-row">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>{{ $item->type === 'service' ? 'Service' : 'Product' }}</label>
                                                    <select name="items[{{ $index }}][{{ $item->type === 'service' ? 'service_id' : 'product_id' }}]" class="form-control product-select">
                                                        @if($item->type === 'product' && $item->product)
                                                            <option value="{{ $item->product_id }}" selected>
                                                                {{ $item->product->name }} (Stock: {{ $item->product->stock + $item->quantity }})
                                                            </option>
                                                        @else
                                                            <option value="">Unknown Item</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Quantity</label>
                                                    <input type="number"
                                                           name="items[{{ $index }}][quantity]"
                                                           class="form-control item-quantity"
                                                           value="{{ $item->quantity }}"
                                                           min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Price/Item</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">Rp</div>
                                                        </div>
                                                        <input type="number"
                                                               name="items[{{ $index }}][price_per_item]"
                                                               class="form-control item-price"
                                                               value="{{ $item->price_per_item }}"
                                                               min="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Subtotal</label>
                                                    <input type="text" class="form-control item-subtotal" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                @if($index > 0)
                                                    <button type="button" class="btn btn-danger btn-sm remove-product">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Additional Notes</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <textarea name="notes"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              style="height: 100px"
                                              placeholder="Add any additional notes here...">{{ old('notes', $transaction->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Warranty -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Garansi</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <label>Periode Garansi</label>
                                    <select name="warranty_period" class="form-control @error('warranty_period') is-invalid @enderror">
                                        <option value="" {{ old('warranty_period', $transaction->warranty_period) == '' ? 'selected' : '' }}>Tanpa Garansi</option>
                                        <option value="7" {{ old('warranty_period', $transaction->warranty_period) == 7 ? 'selected' : '' }}>1 Minggu</option>
                                        <option value="30" {{ old('warranty_period', $transaction->warranty_period) == 30 ? 'selected' : '' }}>1 Bulan</option>
                                        <option value="60" {{ old('warranty_period', $transaction->warranty_period) == 60 ? 'selected' : '' }}>2 Bulan</option>
                                        <option value="90" {{ old('warranty_period', $transaction->warranty_period) == 90 ? 'selected' : '' }}>3 Bulan</option>
                                        <option value="180" {{ old('warranty_period', $transaction->warranty_period) == 180 ? 'selected' : '' }}>6 Bulan</option>
                                        <option value="365" {{ old('warranty_period', $transaction->warranty_period) == 365 ? 'selected' : '' }}>1 Tahun</option>
                                    </select>
                                    @error('warranty_period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($transaction->warranty_expires_at)
                                        <small class="text-muted d-block mt-2">
                                            Current: Expires on {{ $transaction->warranty_expires_at->format('d M Y') }}
                                            @if(now()->isAfter($transaction->warranty_expires_at))
                                                <span class="text-danger">(Expired)</span>
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="col-12 col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h4>Payment Details</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Subtotal</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Rp</div>
                                        </div>
                                        <input type="text" class="form-control currency" id="subtotal" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="delivery_cost">Delivery Cost</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Rp</div>
                                        </div>
                                        <input type="number"
                                               name="delivery_cost"
                                               id="delivery_cost"
                                               class="form-control @error('delivery_cost') is-invalid @enderror"
                                               value="{{ old('delivery_cost', $transaction->delivery_cost) }}"
                                               min="0">
                                        @error('delivery_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tax_cost">Tax</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Rp</div>
                                        </div>
                                        <input type="number"
                                               name="tax_cost"
                                               id="tax_cost"
                                               class="form-control @error('tax_cost') is-invalid @enderror"
                                               value="{{ old('tax_cost', $transaction->tax_cost) }}"
                                               min="0">
                                        @error('tax_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Total</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">Rp</div>
                                        </div>
                                        <input type="number"
                                               name="total_price"
                                               id="total_price"
                                               class="form-control @error('total_price') is-invalid @enderror"
                                               readonly>
                                        @error('total_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method"
                                            id="payment_method"
                                            class="form-control @error('payment_method') is-invalid @enderror">
                                        <option value="cash" {{ $transaction->payment->method == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="credit_card" {{ $transaction->payment->method == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="debit" {{ $transaction->payment->method == 'debit' ? 'selected' : '' }}>Debit</option>
                                        <option value="transfer" {{ $transaction->payment->method == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="payment_status">Payment Status</label>
                                    <select name="payment_status"
                                            id="payment_status"
                                            class="form-control @error('payment_status') is-invalid @enderror">
                                        <option value="paid" {{ $transaction->payment->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="pending" {{ $transaction->payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                    @error('payment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer bg-whitesmoke text-right">
                                <a href="{{ route('transaction.show', $transaction->id) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Transaction</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label>Product</label>
                    <select name="items[%index%][product_id]" class="form-control product-select">
                        <option value="">Select Product</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number"
                           name="items[%index%][quantity]"
                           class="form-control item-quantity"
                           value="1"
                           min="1">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Price/Item</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Rp</div>
                        </div>
                        <input type="number"
                               name="items[%index%][price_per_item]"
                               class="form-control item-price"
                               value="0"
                               min="0">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Subtotal</label>
                    <input type="text" class="form-control item-subtotal" readonly>
                </div>
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-danger btn-sm remove-product">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<!-- JS Libraries -->
<script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('library/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for existing product selects
    $('.product-select').each(function() {
        $(this).select2({
            ajax: {
                url: '/api/products/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(product => ({
                            id: product.product_id,
                            text: `${product.name} (Stock: ${product.stock})`,
                            price: product.selling_price
                        }))
                    };
                }
            },
            minimumInputLength: 1
        }).on('select2:select', function(e) {
            const price = e.params.data.price;
            $(this).closest('.product-row').find('.item-price').val(price).trigger('input');
        });
    });

    // Initialize datepicker
    $('.datepicker').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoApply: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // Product row management
    let productRowCount = {{ count($transaction->items) }};

    $('#addProductRow').click(function() {
        const template = $('#productRowTemplate').html()
            .replace(/%index%/g, productRowCount++);

        const $row = $(template);

        // Initialize product select
        const $productSelect = $row.find('.product-select');
        $productSelect.select2({
            ajax: {
                url: '/api/products/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(product => ({
                            id: product.product_id,
                            text: `${product.name} (Stock: ${product.stock})`,
                            price: product.selling_price
                        }))
                    };
                }
            },
            minimumInputLength: 1
        }).on('select2:select', function(e) {
            const price = e.params.data.price;
            $row.find('.item-price').val(price).trigger('input');
        });

        // Calculate subtotal when quantity or price changes
        function calculateSubtotal() {
            const quantity = parseInt($row.find('.item-quantity').val()) || 0;
            const price = parseFloat($row.find('.item-price').val()) || 0;
            const subtotal = quantity * price;
            $row.find('.item-subtotal').val(formatCurrency(subtotal));
            calculateTotal();
        }

        $row.find('.item-quantity, .item-price').on('input', calculateSubtotal);

        // Remove row button
        $row.find('.remove-product').click(function() {
            $row.remove();
            calculateTotal();
        });

        // Add the row
        $('#productRows').append($row);
    });

    // Calculate subtotals for existing rows
    $('.product-row').each(function() {
        const $row = $(this);
        function calculateSubtotal() {
            const quantity = parseInt($row.find('.item-quantity').val()) || 0;
            const price = parseFloat($row.find('.item-price').val()) || 0;
            const subtotal = quantity * price;
            $row.find('.item-subtotal').val(formatCurrency(subtotal));
            calculateTotal();
        }

        $row.find('.item-quantity, .item-price').on('input', calculateSubtotal);
        calculateSubtotal();
    });

    // Remove row button for existing rows
    $('.remove-product').click(function() {
        $(this).closest('.product-row').remove();
        calculateTotal();
    });

    // Calculate totals
    function calculateTotal() {
        let subtotal = 0;
        $('.item-price').each(function() {
            const price = parseFloat($(this).val()) || 0;
            const quantity = parseInt($(this).closest('.product-row').find('.item-quantity').val()) || 0;
            subtotal += price * quantity;
        });

        const delivery = parseFloat($('#delivery_cost').val()) || 0;
        const tax = parseFloat($('#tax_cost').val()) || 0;
        const total = subtotal + delivery + tax;

        $('#subtotal').val(formatCurrency(subtotal));
        $('#total_price').val(total);
    }

    $('#delivery_cost, #tax_cost').on('input', calculateTotal);

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    // Initial calculation
    calculateTotal();

    // Form validation
    $('#transactionForm').submit(function(e) {
        if ($('.product-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the transaction.');
            return false;
        }

        // Confirm submission
        e.preventDefault();
        Swal.fire({
            title: 'Update Transaction?',
            text: "Are you sure you want to update this transaction?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
