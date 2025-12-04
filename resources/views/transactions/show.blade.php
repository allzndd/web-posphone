@extends('layouts.app')

@section('title', 'Transaction Details')

@push('style')
<style>
    .badge.badge-success {
        background-color: #47c363;
    }
    .badge.badge-warning {
        background-color: #ffa426;
    }
    .badge.badge-danger {
        background-color: #fc544b;
    }
    .transaction-info dl {
        margin-bottom: 0;
    }
    .transaction-info dt {
        font-weight: 600;
    }
    .transaction-info dd {
        margin-bottom: 0.5rem;
    }
    .table-items th,
    .table-items td {
        padding: 1rem;
    }
    .table-items tbody tr:last-child td {
        border-bottom: none;
    }
    .status-timeline {
        position: relative;
        padding-left: 3rem;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        left: 1.15rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .status-timeline .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .status-timeline .timeline-item:last-child {
        padding-bottom: 0;
    }
    .status-timeline .timeline-item::before {
        content: '';
        position: absolute;
        left: -3rem;
        top: 0.25rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 2px solid #6777ef;
        background: #fff;
    }
    .status-timeline .timeline-item.completed::before {
        background: #47c363;
        border-color: #47c363;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Transaction Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('transaction.index') }}">Transactions</a></div>
                <div class="breadcrumb-item">Details</div>
                <div class="breadcrumb-item active">#{{ $transaction->id }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-lg-8 order-2 order-lg-1">
                    <div class="card">
                        <div class="card-header">
                            <h4>Items</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-items">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-right">Price/Item</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transaction->items as $item)
                                            <tr>
                                                <td>
                                                    @if($item->type === 'product' && $item->product)
                                                        <div class="font-weight-bold">{{ $item->product->name }}</div>
                                                        <div class="small text-muted">{{ $item->product->slug ?? '-' }}</div>
                                                    @else
                                                        <div class="text-muted">Unknown Item</div>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    Rp {{ number_format($item->price_per_item, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-right">
                                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-whitesmoke">
                                        <tr>
                                            <td colspan="3" class="text-right font-weight-bold">Subtotal</td>
                                            <td class="text-right">
                                                Rp {{ number_format($transaction->items->sum('subtotal'), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @if($transaction->delivery_cost)
                                            <tr>
                                                <td colspan="3" class="text-right">Delivery Cost</td>
                                                <td class="text-right">
                                                    Rp {{ number_format($transaction->delivery_cost, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if($transaction->tax_cost)
                                            <tr>
                                                <td colspan="3" class="text-right">Tax</td>
                                                <td class="text-right">
                                                    Rp {{ number_format($transaction->tax_cost, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" class="text-right font-weight-bold">Total</td>
                                            <td class="text-right font-weight-bold">
                                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($transaction->notes)
                        <div class="card">
                            <div class="card-header">
                                <h4>Notes</h4>
                            </div>
                            <div class="card-body">
                                {{ $transaction->notes }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-12 col-lg-4 order-1 order-lg-2">
                    <!-- Transaction Info -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Transaction Information</h4>
                            @if($transaction->type === 'purchase')
                                <span class="badge badge-primary">Purchase</span>
                            @else
                                <span class="badge badge-info">Trade-in</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="transaction-info">
                                <dl>
                                    <dt>Transaction ID</dt>
                                    <dd>#{{ $transaction->id }}</dd>

                                    <dt>Date</dt>
                                    <dd>{{ $transaction->date->format('d F Y') }}</dd>

                                    <dt>Customer</dt>
                                    <dd>
                                        <div>{{ $transaction->customer->name }}</div>
                                        @if($transaction->customer->phone)
                                            <div class="text-muted">{{ $transaction->customer->phone }}</div>
                                        @endif
                                    </dd>

                                    <dt>Payment Method</dt>
                                    <dd>{{ ucfirst(str_replace('_', ' ', $transaction->payment->method)) }}</dd>

                                    <dt>Payment Status</dt>
                                    <dd>
                                        <span class="badge badge-{{ $transaction->payment->status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($transaction->payment->status) }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="card-footer bg-whitesmoke">
                            <div class="buttons">
                                <a href="{{ route('transaction.edit', $transaction->id) }}"
                                   class="btn btn-warning">
                                    <i class="fas fa-pencil-alt"></i> Edit Transaction
                                </a>
                                <a href="{{ route('transaction.invoice', $transaction->id) }}" target="_blank" rel="noopener" class="btn btn-primary">
                                    <i class="fas fa-file-invoice"></i> Invoice
                                </a>
                                <a href="{{ route('transaction.print', $transaction->id) }}" target="_blank" rel="noopener" class="btn btn-info">
                                    <i class="fas fa-print"></i> Print Receipt
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Status Timeline</h4>
                        </div>
                        <div class="card-body">
                            <div class="status-timeline">
                                <div class="timeline-item completed">
                                    <h6 class="mb-1">Transaction Created</h6>
                                    <small class="text-muted">
                                        {{ $transaction->created_at->format('d M Y, H:i') }}
                                    </small>
                                </div>

                                @if($transaction->updated_at->gt($transaction->created_at))
                                    <div class="timeline-item completed">
                                        <h6 class="mb-1">Transaction Updated</h6>
                                        <small class="text-muted">
                                            {{ $transaction->updated_at->format('d M Y, H:i') }}
                                        </small>
                                    </div>
                                @endif

                                <div class="timeline-item {{ $transaction->payment->status === 'paid' ? 'completed' : '' }}">
                                    <h6 class="mb-1">Payment {{ ucfirst($transaction->payment->status) }}</h6>
                                    <small class="text-muted">
                                        {{ $transaction->payment->updated_at->format('d M Y, H:i') }}
                                    </small>
                                </div>
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
<script>
$(document).ready(function() {
    // Delete confirmation
    $('.delete-transaction').click(function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this transaction and restore product stock levels.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
