@extends('layouts.app')

@section('title', 'Customer Details')

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
    .customer-info dl {
        margin-bottom: 0;
    }
    .customer-info dt {
        font-weight: 600;
    }
    .customer-info dd {
        margin-bottom: 0.5rem;
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
            <h1>Customer Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customers</a></div>
                <div class="breadcrumb-item active">Details</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 col-lg-4 order-2 order-lg-1">
                    <div class="card">
                        <div class="card-header">
                            <h4>Customer Information</h4>
                            <div class="card-header-action">
                                <span class="badge badge-{{ $customer->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ($customer->transactions ?? collect())->filter(function($t){ return optional($t->payment)->status === 'paid'; })->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="customer-info">
                                <dl>
                                    <dt>Customer ID</dt>
                                            {{ ($customer->transactions ?? collect())->filter(function($t){ return optional($t->payment)->status === 'pending'; })->count() }}

                                    <dt>Name</dt>
                                    <dd>{{ $customer->name }}</dd>

                                    @if($customer->email)
                                        <dt>Email</dt>
                                        <dd>
                                            <a href="mailto:{{ $customer->email }}">
                                                {{ $customer->email }}
                                            </a>
                                        </dd>
                                    @endif

                                    @if($customer->phone)
                                        <dt>Phone</dt>
                                        <dd>
                                            <a href="tel:{{ $customer->phone }}">
                                                {{ $customer->phone }}
                                            </a>
                                        </dd>
                                    @endif

                                    @if($customer->address)
                                        <dt>Address</dt>
                                        <dd>{{ $customer->address }}</dd>
                                    @endif

                                    <dt>Created On</dt>
                                    <dd>{{ $customer->created_at->format('d F Y, H:i') }}</dd>

                                    <dt>Last Updated</dt>
                                    <dd>{{ $customer->updated_at->format('d F Y, H:i') }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="card-footer bg-whitesmoke">
                            <div class="buttons">
                                <a href="{{ route('customer.edit', $customer->id) }}"
                                   class="btn btn-warning">
                                    <i class="fas fa-pencil-alt"></i> Edit Customer
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Transaction Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center mb-4">
                                        <div class="text-small text-muted">Total Transactions</div>
                                        <div class="font-weight-bold h4">{{ $customer->transactions_count ?? ($customer->transactions ? $customer->transactions->count() : 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center mb-4">
                                        <div class="text-small text-muted">Total Spent</div>
                                        <div class="font-weight-bold h4">
                                            Rp {{ number_format($customer->transactions_sum_total_price ?? ($customer->transactions ? $customer->transactions->sum('total_price') : 0), 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="text-small text-muted">Completed Payments</div>
                                        <div class="font-weight-bold h4">
                                            {{ ($customer->transactions ?? collect())->filter(function($t){ return optional($t->payment)->status === 'paid'; })->count() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="text-small text-muted">Pending Payments</div>
                                        <div class="font-weight-bold h4">
                                            {{ ($customer->transactions ?? collect())->filter(function($t){ return optional($t->payment)->status === 'pending'; })->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8 order-1 order-lg-2">
                    <!-- Transaction History -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Transaction History</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Products</th>
                                            <th class="text-right">Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($customer->transactions ?? collect())->sortByDesc('created_at') as $transaction)
                                            <tr>
                                                <td>{{ $transaction->invoice_number ?? '#'.$transaction->id }}</td>
                                                <td>{{ optional($transaction->date)->format('d/m/Y') }}</td>
                                                <td>
                                                    @foreach(($transaction->items ?? collect()) as $item)
                                                        <div>{{ optional($item->product)->name ?? 'Unknown' }} (x{{ $item->quantity }})</div>
                                                    @endforeach
                                                </td>
                                                <td class="text-right">
                                                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                                </td>
                                                    <td>
                                                        @php $status = optional($transaction->payment)->status ?? 'unknown'; @endphp
                                                        <span class="badge badge-{{ $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    </td>
                                                <td>
                                                    <a href="{{ route('transaction.show', $transaction->id) }}"
                                                       class="btn btn-info btn-sm"
                                                       data-toggle="tooltip"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-3">
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon">
                                                            <i class="fas fa-receipt"></i>
                                                        </div>
                                                        <h2>No Transactions Found</h2>
                                                        <p class="lead">
                                                            This customer hasn't made any transactions yet.
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
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
