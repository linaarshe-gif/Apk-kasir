<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text small mb-1 opacity-75">Total Produk</p>
                        <h2 class="card-title mb-0">{{ $totalProduk ?? 0 }}</h2>
                    </div>
                    <div class="fs-3 opacity-75"><i class="fas fa-box"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text small mb-1 opacity-75">Total Stok</p>
                        <h2 class="card-title mb-0">{{ $totalStok ?? 0 }}</h2>
                    </div>
                    <div class="fs-3 opacity-75"><i class="fas fa-cubes"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text small mb-1 opacity-75">Transaksi Selesai</p>
                        <h2 class="card-title mb-0">{{ $totalTransaksi ?? 0 }}</h2>
                    </div>
                    <div class="fs-3 opacity-75"><i class="fas fa-shopping-cart"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text small mb-1 opacity-75">Omzet</p>
                        <h2 class="card-title mb-0">Rp {{ number_format($omzet ?? 0, 0, ',', '.') }}</h2>
                    </div>
                    <div class="fs-3 opacity-75"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-success mt-4">Selamat datang, {{ auth()->user()->name }}</div>
    <p>Gunakan menu {{ auth()->user()->isAdmin() ? 'Kategori, Produk, dan Transaksi' : 'Transaksi' }} untuk menggunakan sistem.</p>
</div>

