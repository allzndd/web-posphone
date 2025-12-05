@extends('layouts.app')

@section('title', 'Detail Layanan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-info-circle"></i> Detail Layanan</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="200">ID</th>
                    <td>{{ $item['id'] }}</td>
                </tr>
                <tr>
                    <th>Nama Layanan</th>
                    <td>{{ $item['nama'] }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $item['deskripsi'] }}</td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $item['status'] == 'aktif' ? 'success' : 'secondary' }}">
                            {{ ucfirst($item['status']) }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('layanan.edit', $item['id']) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('layanan.index') }}'">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
