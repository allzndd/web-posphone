@extends('layouts.app')

@section('title', 'Customers')

@push('style')
<!-- CSS Libraries -->
<style>
    .badge.badge-success {
        background-color: #47c363;
    }
    .badge.badge-warning {
        background-color: #ffa426;
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Customers</h1>
            <div class="section-header-button">
                <a href="{{ route('customer.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Customer
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Customers</h4>
                        </div>
                        <div class="card-body">
                            {{ $customers->total() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Customer List</h4>
                        <div class="card-header-form">
                            <form action="{{ route('customer.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control"
                                           name="search"
                                           placeholder="Search by name, email, or phone..."
                                           value="{{ request('search') }}">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Contact Info</th>
                                        <th>Address</th>
                                        <th>Join Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->id }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td>
                                                @if($customer->email)
                                                    <div>
                                                        <i class="far fa-envelope mr-1"></i>
                                                        {{ $customer->email }}
                                                    </div>
                                                @endif
                                                @if($customer->phone)
                                                    <div>
                                                        <i class="fas fa-phone mr-1"></i>
                                                        {{ $customer->phone }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($customer->address, 50) }}</td>
                                            <td>
                                                @if($customer->join_date)
                                                    {{ $customer->join_date->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('customer.show', $customer->id) }}"
                                                   class="btn btn-info btn-sm"
                                                   data-toggle="tooltip"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('customer.edit', $customer->id) }}"
                                                   class="btn btn-warning btn-sm"
                                                   data-toggle="tooltip"
                                                   title="Edit Customer">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ route('customer.destroy', $customer->id) }}"
                                                      method="POST"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm delete-customer"
                                                            data-toggle="tooltip"
                                                            title="Delete Customer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-3">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon">
                                                        <i class="fas fa-users"></i>
                                                    </div>
                                                    <h2>No Customers Found</h2>
                                                    <p class="lead">
                                                        No customers have been added yet.
                                                    </p>
                                                    <a href="{{ route('customer.create') }}" class="btn btn-primary mt-4">
                                                        Add New Customer
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
                        {{ $customers->links() }}
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

    // Delete confirmation
    $('.delete-customer').click(function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this customer. This action cannot be undone!",
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
