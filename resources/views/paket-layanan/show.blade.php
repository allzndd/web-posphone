@extends('layouts.app')

@section('title', 'Detail Paket Layanan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-info-circle"></i> Detail Paket Layanan</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="200">ID</th>
                    <td>{{ $paket['id'] }}</td>
                </tr>
                <tr>
                    <th>Nama Paket</th>
                    <td>{{ $paket['nama'] }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $paket['deskripsi'] }}</td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td>Rp {{ number_format($paket['harga'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Durasi</th>
                    <td>{{ $paket['durasi'] }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $paket['status'] == 'aktif' ? 'success' : 'secondary' }}">
                            {{ ucfirst($paket['status']) }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('paket-layanan.edit', $paket['id']) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('paket-layanan.index') }}'">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
