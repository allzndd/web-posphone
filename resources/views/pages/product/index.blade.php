@extends('layouts.app')

@section('title', 'Product')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Products</h1>
                <div class="section-header-button">
                    <a href="{{ route('product.create') }}" class="btn btn-primary">Add New</a>
                </div>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Product</a></div>
                    <div class="breadcrumb-item">All Product</div>
                </div>
            </div>
            <div class="section-body">
                {{-- <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div> --}}

                <!-- Filter Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-filter"></i> Filter Produk</h4>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('product.index') }}" class="row">
                                    <div class="col-md-3 mb-3">
                                        <label>Nama Produk</label>
                                        <input type="text" class="form-control" name="name" placeholder="Cari nama produk..." value="{{ request('name') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label>Kategori</label>
                                        <select class="form-control" name="category_id">
                                            <option value="">Semua Kategori</option>
                                            @if(isset($categories))
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label>Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <div>
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                            <a href="{{ route('product.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Data Produk</h4>
                                <div class="card-header-action">
                                    <span class="badge badge-primary">Total: {{ $products->total() }} produk</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table-striped table">
                                        <tr>
                                            <th>Action</th>
                                            <th>Nama</th>
                                            <th>Kategori</th>
                                            <th>Kategori (Auto)</th>
                                            <th>Color</th>
                                            <th>Storage</th>
                                            <th>Battery Health</th>
                                            <th>Sell Price</th>
                                            <th>Cost</th>
                                            <th>Profit</th>
                                            <th>IMEI</th>
                                            <th>Stock</th>
                                            <th>Created At</th>
                                        </tr>
                                        @foreach ($products as $product)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('product.edit', $product) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="{{ route('product.show', $product) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk {{ $product->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>{{ $product->name }}</td>
                                                <td>
                                                    @if($product->category)
                                                        <span class="badge badge-primary">{{ $product->category->name }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $product->auto_category }}</td>
                                                <td>{{ $product->color ?? '-' }}</td>
                                                <td>{{ $product->storage ?? '-' }}</td>
                                                <td>{{ $product->barre_health ?? '-' }}</td>
                                                <td>Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $costs = is_string($product->costs) ? json_decode($product->costs, true) : $product->costs;
                                                    @endphp
                                                    @if($costs && is_array($costs) && count($costs))
                                                        <ul class="pl-3 mb-0">
                                                            @foreach($costs as $c)
                                                                <li>{{ $c['description'] ?? '-' }}: Rp {{ number_format($c['amount'] ?? 0, 0, ',', '.') }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($product->profit, 0, ',', '.') }}</td>
                                                <td>{{ $product->imei ?? '-' }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>{{ $product->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>
@endpush
