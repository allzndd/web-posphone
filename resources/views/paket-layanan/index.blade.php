@extends('layouts.app')

@section('title', 'Paket Layanan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-box"></i> Daftar Paket Layanan</h4>
            <a href="{{ route('paket-layanan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Paket
            </a>
        </div>
        
        <div class="p-6">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Paket</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paket as $item)
                        <tr>
                            <td>{{ $item['id'] }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['deskripsi'] }}</td>
                            <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                            <td>{{ $item['durasi'] }}</td>
                            <td>
                                <span class="badge bg-{{ $item['status'] == 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($item['status']) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('paket-layanan.show', $item['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <a href="{{ route('paket-layanan.edit', $item['id']) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete('{{ route('paket-layanan.destroy', $item['id']) }}')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Yakin ingin menghapus data ini?')) {
        window.location.href = url;
    }
}
</script>
@endsection
