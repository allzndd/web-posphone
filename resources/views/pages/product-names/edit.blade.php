@extends('layouts.app')

@section('title', 'Edit Nama Produk')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Edit Nama Produk</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('product-name.index') }}">Nama Produk</a></div>
        <div class="breadcrumb-item">Edit</div>
      </div>
    </div>

    <div class="section-body">
      <div class="card">
        <form action="{{ route('product-name.update', $nameItem->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="card-header">
            <h4>Form Edit Nama Produk</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Nama Baru <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name', $nameItem->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama produk baru" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label>Deskripsi</label>
              <textarea name="description" rows="3" class="form-control">{{ old('description', $nameItem->description) }}</textarea>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('product-name.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Nama
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection
