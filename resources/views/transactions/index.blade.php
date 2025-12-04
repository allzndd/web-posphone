@extends('layouts.app')

@section('title', 'Sales Transaction')

@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
<style>
    .badge.badge-success {
        background-color: #47c363;
    }
    /* UI polish for filters and summary */
    .filter-bar { background: #f8f9fa; border: 1px solid #e4e6ea; border-radius: 8px; padding: 12px; }
    .summary-alert { background: #fefefe; border: 1px dashed #dfe3e7; }
    .badge.badge-warning {
        background-color: #ffa426;
    }
    .badge.badge-danger {
        background-color: #fc544b;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Transactions</h1>
            <div class="section-header-button">
                <a href="{{ route('transaction.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Transaction
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible show fade">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>×</span>
                    </button>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible show fade">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>×</span>
                    </button>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET">
                            <div class="form-row align-items-end">
                                <div class="col-md-3 mb-2">
                                    <label class="mb-1">Kata Kunci</label>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search transactions...">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="mb-1">Tampilan</label>
                                    <select class="form-control" name="view" id="viewSelect">
                                        <option value="">Semua</option>
                                        <option value="day" {{ request('view')=='day' ? 'selected' : '' }}>Per Hari</option>
                                        <option value="week" {{ request('view')=='week' ? 'selected' : '' }}>Per Minggu</option>
                                        <option value="range" {{ request('view')=='range' ? 'selected' : '' }}>Rentang Tanggal</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 view-field view-day" style="display:none;">
                                    <label class="mb-1">Tanggal</label>
                                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                                </div>
                                <div class="col-md-2 mb-2 view-field view-week" style="display:none;">
                                    <label class="mb-1">Minggu</label>
                                    <input type="week" class="form-control" name="week" value="{{ request('week') }}">
                                </div>
                                <div class="col-md-2 mb-2 view-field view-range" style="display:none;">
                                    <label class="mb-1">Dari</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2 mb-2 view-field view-range" style="display:none;">
                                    <label class="mb-1">Sampai</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-1 mb-2">
                                    <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-filter"></i></button>
                                </div>
                                <div class="col-md-1 mb-2">
                                    <a class="btn btn-secondary btn-block" href="{{ route('transaction.index') }}"><i class="fas fa-undo"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Transactions</h4>
                        </div>
                        <div class="card-body">
                            {{ $transactions->total() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Completed Payments</h4>
                        </div>
                        <div class="card-body">
                            {{ $transactions->where('payment.status', 'paid')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending Payments</h4>
                        </div>
                        <div class="card-body">
                            {{ $transactions->where('payment.status', 'pending')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isOwner())
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Pendapatan (Filter)</h4>
                        </div>
                        <div class="card-body">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Profit (Filter)</h4>
                        </div>
                        <div class="card-body">
                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Margin (Filter)</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($totalMargin, 1, ',', '.') }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Transaction Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Transaction List</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Products</th>
                                        @if(auth()->user()->isOwner())
                                        <th class="text-right">Price/Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Subtotal</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">Profit</th>
                                        <th class="text-right">Margin</th>
                                        @else
                                        <th class="text-center">Qty</th>
                                        @endif
                                        <th>Status</th>
                                        <th>Garansi</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                            <td>{{ $transaction->customer?->name ?? 'Umum' }}</td>
                                            <td>
                                                @foreach($transaction->items as $item)
                                                    @if($item->type === 'product' && $item->product)
                                                        {{ $item->product->name }}<br>
                                                    @else
                                                        <span class="text-muted">Unknown Item</span><br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            @if(auth()->user()->isOwner())
                                                <td class="text-right">
                                                    @foreach($transaction->items as $item)
                                                        Rp {{ number_format($item->price_per_item, 0, ',', '.') }}<br>
                                                    @endforeach
                                                </td>
                                                <td class="text-center">
                                                    @foreach($transaction->items as $item)
                                                        {{ $item->quantity }}<br>
                                                    @endforeach
                                                </td>
                                                <td class="text-right">
                                                    @foreach($transaction->items as $item)
                                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}<br>
                                                    @endforeach
                                                </td>
                                                <td class="text-right">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                                                @php
                                                    $__txProfit = $transaction->items->sum(function($item) {
                                                        return ($item->type === 'product' && $item->product)
                                                            ? ($item->product->profit * $item->quantity)
                                                            : 0;
                                                    });
                                                    $__margin = $transaction->total_price > 0 ? ($__txProfit / $transaction->total_price * 100) : 0;
                                                @endphp
                                                <td class="text-right">
                                                    <span class="badge badge-success">
                                                        Rp {{ number_format($__txProfit, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="badge badge-info">
                                                        {{ number_format($__margin, 1, ',', '.') }}%
                                                    </span>
                                                </td>
                                            @else
                                                <td class="text-center">
                                                    @foreach($transaction->items as $item)
                                                        {{ $item->quantity }}<br>
                                                    @endforeach
                                                </td>
                                            @endif
                                            <td>
                                                @php
                                                    $payStatus = $transaction->payment?->status ?? 'pending';
                                                @endphp
                                                <span class="badge badge-{{ $payStatus === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($payStatus) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->warranty_expires_at)
                                                    @php
                                                        $isExpired = now()->isAfter($transaction->warranty_expires_at);
                                                    @endphp
                                                    <span class="badge badge-{{ $isExpired ? 'danger' : 'success' }}">
                                                        @if($isExpired)
                                                            OFF
                                                        @else
                                                            {{ $transaction->warranty_expires_at->format('d/m/Y') }}
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('transaction.show', $transaction->id) }}"
                                                   class="btn btn-info btn-sm"
                                                   data-toggle="tooltip"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('transaction.edit', $transaction->id) }}"
                                                   class="btn btn-warning btn-sm"
                                                   data-toggle="tooltip"
                                                   title="Edit Transaction">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ route('transaction.destroy', $transaction->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      id="delete-form-{{ $transaction->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm delete-transaction"
                                                            data-id="{{ $transaction->id }}"
                                                            data-invoice="{{ $transaction->invoice_number }}"
                                                            data-toggle="tooltip"
                                                            title="Delete Transaction">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-3">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </div>
                                                    <h2>No Transactions Found</h2>
                                                    <p class="lead">
                                                        No transactions have been recorded yet.
                                                    </p>
                                                    <a href="{{ route('transaction.create') }}" class="btn btn-primary mt-4">
                                                        Create New Transaction
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<!-- JS Libraries -->
<script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Toggle filter fields by view selection
    function toggleViewFields() {
        const v = $('#viewSelect').val();
        $('.view-field').hide();
        if (v === 'day') {
            $('.view-day').show();
        } else if (v === 'week') {
            $('.view-week').show();
        } else if (v === 'range') {
            $('.view-range').show();
        }
    }
    toggleViewFields();
    $('#viewSelect').on('change', toggleViewFields);
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Delete transaction confirmation
    $('.delete-transaction').on('click', function(e) {
        e.preventDefault();
        const transactionId = $(this).data('id');
        const invoiceNumber = $(this).data('invoice');

        swal({
            title: 'Hapus Transaksi?',
            text: `Apakah Anda yakin ingin menghapus transaksi ${invoiceNumber}? Stok produk akan dikembalikan.`,
            icon: 'warning',
            buttons: {
                cancel: {
                    text: 'Batal',
                    value: null,
                    visible: true,
                    className: 'btn btn-secondary',
                    closeModal: true,
                },
                confirm: {
                    text: 'Ya, Hapus!',
                    value: true,
                    visible: true,
                    className: 'btn btn-danger',
                    closeModal: true
                }
            },
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // Submit the form
                $(`#delete-form-${transactionId}`).submit();
            }
        });
    });
});
</script>
@endpush
