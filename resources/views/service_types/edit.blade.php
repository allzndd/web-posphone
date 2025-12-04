@extends('layouts.app')

@section('title', 'Edit Tipe Servis')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Edit Tipe Servis</h1>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('service-types.update', $type->id) }}">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label>Nama Tipe</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $type->name) }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $type->description) }}</textarea>
          </div>
          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ $type->is_active ? 'checked' : '' }}>
              <label class="custom-control-label" for="is_active">Aktif</label>
            </div>
          </div>
          <div class="form-group mb-0">
            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Update</button>
            <a href="{{ route('service-types.index') }}" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection
