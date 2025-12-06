@extends('layouts.app')

@section('title', 'Buat Pemberitahuan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-plus-circle"></i> Buat Pemberitahuan Baru</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('pemberitahuan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="judul" name="judul" required>
                </div>

                <div class="mb-3">
                    <label for="pesan" class="form-label">Pesan</label>
                    <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="tujuan" class="form-label">Tujuan</label>
                    <select class="form-select" id="tujuan" name="tujuan" required>
                        <option value="">Pilih Tujuan</option>
                        <option value="Semua Customer">Semua Customer</option>
                        <option value="Customer Premium">Customer Premium</option>
                        <option value="Staff">Staff</option>
                        <option value="Manager">Manager</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="draft">Draft</option>
                        <option value="terkirim">Terkirim</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
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
