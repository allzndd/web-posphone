@extends('layouts.app')

@section('title', 'Layanan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-tools"></i> Daftar Layanan</h4>
            <a href="{{ route('layanan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Layanan
            </a>
        </div>
        
        <div class="p-6">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Layanan</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($layanan as $item)
                        <tr>
                            <td>{{ $item['id'] }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['deskripsi'] }}</td>
                            <td>Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $item['status'] == 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($item['status']) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('layanan.show', $item['id']) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <a href="{{ route('layanan.edit', $item['id']) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete('{{ route('layanan.destroy', $item['id']) }}')" class="btn btn-sm btn-danger">
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
