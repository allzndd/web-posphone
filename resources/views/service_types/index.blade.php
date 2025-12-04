@extends('layouts.app')

@section('title', 'Tipe Servis')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Tipe Servis</h1>
      <div class="section-header-button">
        <a href="{{ route('service-types.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Tipe</a>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible show fade">
        <div class="alert-body">
          <button class="close" data-dismiss="alert"><span>×</span></button>
          {{ session('success') }}
        </div>
      </div>
    @endif

    <div class="card">
      <div class="card-header">
        <h4>Daftar Tipe Servis</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($types as $type)
                <tr>
                  <td>{{ $type->name }}</td>
                  <td class="text-muted">{{ $type->description ?: '-' }}</td>
                  <td>
                    @if($type->is_active)
                      <span class="badge badge-success">Aktif</span>
                    @else
                      <span class="badge badge-secondary">Nonaktif</span>
                    @endif
                  </td>
                  <td class="text-right">
                    <a href="{{ route('service-types.edit', $type->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>
                    <form action="{{ route('service-types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus tipe ini?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger btn-sm" type="submit"><i class="fas fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Belum ada tipe servis</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        {{ $types->links() }}
      </div>
    </div>
  </section>
</div>
@endsection
