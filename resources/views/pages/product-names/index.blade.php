@extends('layouts.app')

@section('title', 'Nama Produk')

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Nama Produk</h1>
      <div class="section-header-button">
        <a href="{{ route('product-name.create') }}" class="btn btn-primary">Tambah Nama Produk</a>
      </div>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item"><a href="#">Products</a></div>
        <div class="breadcrumb-item">Nama Produk</div>
      </div>
    </div>

    <div class="section-body">
      @if(session('success'))
      <div class="alert alert-success alert-dismissible show fade">
        <div class="alert-body">
          <button class="close" data-dismiss="alert">
            <span>×</span>
          </button>
          {{ session('success') }}
        </div>
      </div>
      @endif
      @if(session('error'))
      <div class="alert alert-danger alert-dismissible show fade">
        <div class="alert-body">
          <button class="close" data-dismiss="alert">
            <span>×</span>
          </button>
          {{ session('error') }}
        </div>
      </div>
      @endif

      <div class="card">
        <div class="card-body">
          <form method="GET" class="mb-3">
            <div class="input-group">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama produk...">
              <div class="input-group-append">
                <button class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="{{ route('product-name.index') }}" class="btn btn-secondary">Reset</a>
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Slug</th>
                  <th>Deskripsi</th>
                  <th>Sisa Stok</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($names as $index => $n)
                  <tr>
                    <td>{{ $names->firstItem() + $index }}</td>
                    <td><strong>{{ $n->name }}</strong></td>
                    <td>{{ $n->slug }}</td>
                    <td>{{ $n->description ?? '-' }}</td>
                    <td>
                      <span class="badge badge-primary">{{ $stocks[$n->name] ?? 0 }} stok</span>
                    </td>
                    <td>
                      <a href="{{ route('product-name.edit', $n->id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <form action="{{ route('product-name.destroy', $n->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus nama produk ini? Jika sedang dipakai produk lain, penghapusan akan diblokir.')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">
                          <i class="fas fa-trash"></i> Hapus
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted">Belum ada data nama produk</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="float-right">{{ $names->withQueryString()->links() }}</div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
