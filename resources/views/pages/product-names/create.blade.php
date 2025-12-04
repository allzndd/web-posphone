@extends('layouts.app')

@section('title', 'Tambah Nama Produk')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Tambah Nama Produk</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="{{ route('product-name.index') }}">Nama Produk</a></div>
        <div class="breadcrumb-item">Tambah</div>
      </div>
    </div>

    <div class="section-body">
      <div class="card">
        <form action="{{ route('product-name.store') }}" method="POST">
          @csrf
          <div class="card-header">
            <h4>Form Tambah Nama Produk</h4>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Nama Produk <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: iPhone 13 Pro Max" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label>Deskripsi</label>
              <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="{{ route('product-name.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection
