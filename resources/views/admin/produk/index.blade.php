<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <a href="/admin/produk/create" class="btn btn-primary"><i class="fas fa-plus"></i>Tambah</a>

                    @if(session()->has('success'))
                        <div class="alert alert-success mt-2"><i class="fas fa-check"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table mt-1">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Action</th>
                        </tr>

                        @foreach ($produks as $item)

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ optional($item->kategori)->name }}</td>
                            <td>{{ $item->harga }}</td>
                            <td>{{ $item->stok }}</td>
                            <td>
                                @if($item->gambar)
                                    <img src="{{ asset($item->gambar) }}" alt="Gambar Produk" class="img-thumbnail" style="max-width: 80px; height: auto;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="/admin/produk/{{$item->id}}/edit" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="/admin/produk/{{$item->id}}" method="POST">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm ml-1"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
