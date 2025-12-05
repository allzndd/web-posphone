@extends('layouts.app')

@section('title', 'Edit Paket Layanan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-edit"></i> Edit Paket Layanan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('paket-layanan.update', $paket['id']) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Paket</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ $paket['nama'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ $paket['deskripsi'] }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" value="{{ $paket['harga'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="durasi" class="form-label">Durasi</label>
                    <input type="text" class="form-control" id="durasi" name="durasi" value="{{ $paket['durasi'] }}" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif" {{ $paket['status'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $paket['status'] == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('paket-layanan.index') }}'">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
