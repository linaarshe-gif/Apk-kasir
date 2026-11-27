<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">
                    <h4><b>Tambah Data Kategori</b></h4>

                    @isset($kategori)
                        <form action="/admin/kategori/{{$kategori->id}}" method="POST">
                            @method('put')
                    @else
                    <form action="/admin/kategori" method="POST">
                    @endisset

                    @csrf
                    <div class="form-group">
                        <label for=""><b>Nama Kategori</b></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Nama Kategori" value="{{ isset ($kategori) ? $kategori->name : ''}}">

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for=""><b>Slug</b></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" placeholder="contoh: berita-tekno" value="{{ isset ($kategori) ? $kategori->slug : ''}}">

                        @error('slug')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for=""><b>Deskripsi</b></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Deskripsi singkat">{{ isset ($kategori) ? $kategori->description : ''}}</textarea>

                        @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <a href="/admin/kategori" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>