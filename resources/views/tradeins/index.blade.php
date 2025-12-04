@extends('layouts.app')

@section('title', 'Trade In')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Trade In</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">Trade In Statistics</div>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Trade In</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalTradeIns }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">Trade In Statistics</div>
                    </div>
                    <div class="card-icon shadow-primary bg-success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Profit</h4>
                        </div>
                        <div class="card-body">
                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Trade In List</h4>
                    <div class="card-header-form">
                        <form method="GET" action="{{ route('tradein.index') }}">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Cari produk, IMEI, atau old phone..." value="{{ request('q') }}">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Old Phone</th>
                                    <th>Old IMEI</th>
                                    <th>Old Value</th>
                                    <th>New Product</th>
                                    <th>New Price</th>
                                    <th>Profit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tradeins as $tradein)
                                @php
                                    $newPrice = $tradein->newProduct->sell_price ?? 0;
                                    $profit = $newPrice - $tradein->old_value;
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($tradein->date)->format('d/m/Y') }}</td>
                                    <td>{{ $tradein->customer->name ?? '-' }}</td>
                                    <td>{{ $tradein->old_phone }}</td>
                                    <td>{{ $tradein->old_imei ?? '-' }}</td>
                                    <td>Rp {{ number_format($tradein->old_value, 0, ',', '.') }}</td>
                                    <td>{{ $tradein->newProduct->name ?? '-' }}</td>
                                    <td>Rp {{ number_format($newPrice, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $profit >= 0 ? 'success' : 'danger' }}">
                                            Rp {{ number_format($profit, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('tradein.edit', $tradein->id) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('tradein.destroy', $tradein->id) }}" method="POST" class="d-inline form-delete-tradein">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="float-right">{{ $tradeins->links() }}</div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // SweetAlert confirmation for delete
    $(document).on('submit', '.form-delete-tradein', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Hapus Trade-In?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection
