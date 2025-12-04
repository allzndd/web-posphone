@extends('layouts.app')

@section('title', 'New Trade In')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Create Trade In</h1>
        </div>
        <div class="section-body">
            <div class="card">
                <form action="{{ route('tradein.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Customer</label>
                            <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Old Phone</label>
                            <input type="text" name="old_phone" class="form-control @error('old_phone') is-invalid @enderror" value="{{ old('old_phone') }}">
                            @error('old_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Old IMEI</label>
                            <input type="text" name="old_imei" class="form-control @error('old_imei') is-invalid @enderror" value="{{ old('old_imei') }}" placeholder="Optional">
                            @error('old_imei')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Old Value</label>
                            <input type="number" step="0.01" name="old_value" class="form-control @error('old_value') is-invalid @enderror" value="{{ old('old_value') }}">
                            @error('old_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>New Product</label>
                            <select name="new_product_id" class="form-control @error('new_product_id') is-invalid @enderror">
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('new_product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}">
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
