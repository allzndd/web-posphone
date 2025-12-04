@extends('layouts.app')

@section('title', 'Product Detail')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Product Detail</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('product.index') }}">Products</a></div>
                    <div class="breadcrumb-item">Detail</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Product Information</h4>
                            </div>
                            <div class="card-body">
                                @if($product->image_url)
                                    <div class="text-center mb-4">
                                        <img src="{{ asset('storage/products/' . $product->image_url) }}"
                                             alt="{{ $product->name }}"
                                             class="img-fluid rounded"
                                             style="max-height: 300px;">
                                    </div>
                                @endif

                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">Product Name</th>
                                            <td>: {{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <td>: {{ $product->category->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Color</th>
                                            <td>: {{ $product->color ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Storage</th>
                                            <td>: {{ $product->storage ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>IMEI</th>
                                            <td>: {{ $product->imei ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Battery Health (BH)</th>
                                            <td>: {{ $product->barre_health ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Stock</th>
                                            <td>: <span class="badge badge-{{ $product->stock > 0 ? 'success' : 'danger' }}">{{ $product->stock }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>View Count</th>
                                            <td>: <span class="badge badge-info">{{ $product->view_count ?? 0 }} views</span></td>
                                        </tr>
                                    </tbody>
                                </table>

                                @if($product->description)
                                    <div class="mt-3">
                                        <h6>Description:</h6>
                                        <p class="text-muted">{{ $product->description }}</p>
                                    </div>
                                @endif

                                @if($product->assessoris)
                                    <div class="mt-3">
                                        <h6>Accessories:</h6>
                                        <p class="text-muted">{{ $product->assessoris }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <!-- Pricing & Cost Information -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Pricing & Cost</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">Selling Price</th>
                                            <td>: <strong class="text-success">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Purchase Price</th>
                                            <td>: Rp {{ number_format($product->buy_price, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                @php
                                    $costs = is_string($product->costs) ? json_decode($product->costs, true) : $product->costs;
                                    $totalCostItems = 0;
                                    if ($costs && is_array($costs)) {
                                        foreach ($costs as $item) {
                                            $totalCostItems += isset($item['amount']) ? (float)$item['amount'] : 0;
                                        }
                                    }
                                @endphp

                                @if($costs && is_array($costs) && count($costs) > 0)
                                    <div class="mt-3">
                                        <h6>Cost Items:</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($costs as $cost)
                                                    @if(isset($cost['description']) && isset($cost['amount']))
                                                        <tr>
                                                            <td>{{ $cost['description'] }}</td>
                                                            <td class="text-right">Rp {{ number_format($cost['amount'], 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                <tr class="table-active">
                                                    <td><strong>Total Cost Items</strong></td>
                                                    <td class="text-right"><strong>Rp {{ number_format($totalCostItems, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <p class="text-muted">No cost items added.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Profit Calculation -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Profit Analysis</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">Gross Profit</th>
                                            <td>: Rp {{ number_format($product->gross_profit ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Net Profit</th>
                                            <td>: <strong class="text-primary">Rp {{ number_format($product->net_profit ?? 0, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Calculated Profit</th>
                                            <td>: <strong class="text-success">Rp {{ number_format($product->profit, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="alert alert-info mt-3">
                                    <strong>Formula:</strong><br>
                                    Profit = Selling Price - (Purchase Price + Total Cost Items)<br>
                                    <small class="text-muted">
                                        = Rp {{ number_format($product->sell_price, 0, ',', '.') }} -
                                        (Rp {{ number_format($product->buy_price, 0, ',', '.') }} +
                                        Rp {{ number_format($totalCostItems ?? 0, 0, ',', '.') }})<br>
                                        = Rp {{ number_format($product->profit, 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-primary btn-lg mr-2">
                                    <i class="fas fa-edit"></i> Edit Product
                                </a>
                                <a href="{{ route('product.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
@endpush
