@extends('layouts.app')

@section('title', 'Tambah Paket Layanan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-plus-circle"></i> Tambah Paket Layanan Baru</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('paket-layanan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Paket</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="harga" name="harga" required>
                </div>

                <div class="mb-3">
                    <label for="durasi" class="form-label">Durasi</label>
                    <input type="text" class="form-control" id="durasi" name="durasi" placeholder="Contoh: 1 hari, 2 hari" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
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
