@extends('layouts.app')

@section('title', 'Detail Pemberitahuan')

@section('main')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-info-circle"></i> Detail Pemberitahuan</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="200">ID</th>
                    <td>{{ $item['id'] }}</td>
                </tr>
                <tr>
                    <th>Judul</th>
                    <td>{{ $item['judul'] }}</td>
                </tr>
                <tr>
                    <th>Pesan</th>
                    <td>{{ $item['pesan'] }}</td>
                </tr>
                <tr>
                    <th>Tujuan</th>
                    <td>{{ $item['tujuan'] }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ date('d/m/Y', strtotime($item['tanggal'])) }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $item['status'] == 'terkirim' ? 'success' : 'warning' }}">
                            {{ ucfirst($item['status']) }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('pemberitahuan.edit', $item['id']) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('pemberitahuan.index') }}'">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
