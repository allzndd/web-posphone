@extends('layouts.app')

@section('title', 'Edit Pemberitahuan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-edit"></i> Edit Pemberitahuan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('pemberitahuan.update', $item['id']) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="judul" name="judul" value="{{ $item['judul'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="pesan" class="form-label">Pesan</label>
                    <textarea class="form-control" id="pesan" name="pesan" rows="4" required>{{ $item['pesan'] }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="tujuan" class="form-label">Tujuan</label>
                    <select class="form-select" id="tujuan" name="tujuan" required>
                        <option value="">Pilih Tujuan</option>
                        <option value="Semua Customer" {{ $item['tujuan'] == 'Semua Customer' ? 'selected' : '' }}>Semua Customer</option>
                        <option value="Customer Premium" {{ $item['tujuan'] == 'Customer Premium' ? 'selected' : '' }}>Customer Premium</option>
                        <option value="Staff" {{ $item['tujuan'] == 'Staff' ? 'selected' : '' }}>Staff</option>
                        <option value="Manager" {{ $item['tujuan'] == 'Manager' ? 'selected' : '' }}>Manager</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $item['tanggal'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="draft" {{ $item['status'] == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="terkirim" {{ $item['status'] == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('pemberitahuan.index') }}'">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
