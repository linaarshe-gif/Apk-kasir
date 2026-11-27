<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">
                    <h4><b>Tambah Data Produk</b></h4>

                    @isset($produk)
                        <form action="/admin/produk/{{$produk->id}}" method="POST" enctype="multipart/form-data">
                            @method('put')
                    @else
                    <form action="/admin/produk" method="POST" enctype="multipart/form-data">
                    @endisset

                    @csrf
                    <div class="form-group">
                        <label for=""><b>Nama Produk</b></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama Produk" value="{{ isset ($produk) ? $produk->name : ''}}">

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for=""><b>Kategori</b></label>
                        <select class="form-control @error('kategori_id') is-invalid @enderror" name="kategori_id">
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ isset($produk) && $produk->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->name }}</option>
                            @endforeach
                        </select>

                        @error('kategori_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for=""><b>Harga</b></label>
                        <input type="number" class="form-control @error('harga') is-invalid @enderror" name="harga" placeholder="Harga" value="{{ isset ($produk) ? $produk->harga : ''}}">

                        @error('harga')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for=""><b>Stok</b></label>
                        <input type="number" class="form-control @error('stok') is-invalid @enderror" name="stok" placeholder="Stok" value="{{ isset ($produk) ? $produk->stok : ''}}">

                        @error('stok')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for=""><b>Gambar</b></label>
                        <input type="file" class="form-control @error('gambar') is-invalid @enderror" name="gambar" accept="image/*">

                        @isset($produk)
                            @if($produk->gambar)
                                <div class="mt-2">
                                    <img src="{{ asset($produk->gambar) }}" alt="Gambar Produk" class="img-thumbnail" style="max-width: 200px; height: auto;">
                                </div>
                            @endif
                        @endisset

                        @error('gambar')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <a href="/admin/produk" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
