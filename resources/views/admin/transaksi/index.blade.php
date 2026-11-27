<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <a href="/admin/transaksi/create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Transaksi</a>
                        </div>
                        <span class="badge badge-success">{{ count($transaksis) }} Transaksi</span>
                    </div>

                    @if(session()->has('success'))
                        <div class="alert alert-success mt-2"><i class="fas fa-check"></i> {{ session('success') }}</div>
                    @endif

                    <div class="row">
                        @foreach ($transaksis as $item)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card struk-card h-100 shadow-sm" id="struk-{{ $item->id }}" onclick="window.location='/admin/transaksi/{{$item->id}}/struk'" style="cursor:pointer;">
                                <div class="card-header bg-dark text-white py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-receipt mr-1"></i> STRUK #{{ $item->id }}</span>
                                        <span class="badge badge-light text-dark">{{ $item->tanggal_transaksi }}</span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <small class="text-muted d-block">Kasir</small>
                                            <span class="font-weight-bold">{{ optional($item->user)->name }}</span>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 45%">Produk</th>
                                                <th style="width: 20%" class="text-center">Qty</th>
                                                <th style="width: 35%" class="text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->items as $detail)
                                            <tr>
                                                <td>{{ optional($detail->produk)->name }}</td>
                                                <td class="text-center">{{ $detail->qty }}</td>
                                                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="2" class="text-right">Total</td>
                                                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right">Bayar</td>
                                                <td class="text-right">Rp {{ number_format($item->uang_bayar, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr class="text-success">
                                                <td colspan="2" class="text-right">Kembalian</td>
                                                <td class="text-right">Rp {{ number_format($item->kembalian, 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer bg-white py-2 d-flex justify-content-between align-items-center">
                                    <form action="/admin/transaksi/{{$item->id}}" method="POST" style="margin:0;display:inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation()"><i class="fas fa-trash"></i></button>
                                    </form>
                                    <span class="small text-muted">Klik kartu untuk cetak struk</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if(count($transaksis) === 0)
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data transaksi.</p>
                        <a href="/admin/transaksi/create" class="btn btn-primary">Buat Transaksi Pertama</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.struk-card {
    border: 1px solid #dee2e6;
    border-left: 4px solid #007bff;
    transition: transform 0.2s;
}
.struk-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.struk-card .card-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.struk-card .card-footer {
    border-top: 1px solid #f8f9fa;
}
</style>

