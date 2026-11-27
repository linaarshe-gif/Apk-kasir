<div class="container-fluid mt-2">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Kartu Struk Cetak -->
            <div class="card struk-print shadow-sm" id="strukPrintable">
                <div class="card-header bg-dark text-white py-3 text-center">
                    <h5 class="card-title mb-1"><i class="fas fa-store mr-2"></i>TOKO ANDA</h5>
                    <small class="d-block">Jl. Alamat Toko No. 123</small>
                    <small class="d-block">Telp: 0812 - 3456 - 7890</small>
                </div>
                <div class="card-body p-3">
                    <hr class="my-2">
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted">No.</small><br>
                            <span class="font-weight-bold">{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-6 text-right">
                            <small class="text-muted">Tanggal</small><br>
                            <span class="font-weight-bold">{{ $transaksi->tanggal_transaksi }}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted">Kasir</small><br>
                            <span class="font-weight-bold">{{ optional($transaksi->user)->name }}</span>
                        </div>
                        <div class="col-6 text-right">
                            <small class="text-muted">Pembeli</small><br>
                            <span class="font-weight-bold">{{ optional($transaksi->user)->name }}</span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <table class="table table-sm mb-3">
                        <thead>
                            <tr>
                                <th style="width: 40%">Item</th>
                                <th style="width: 15%" class="text-center">Qty</th>
                                <th style="width: 45%" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi->items as $detail)
                            <tr>
                                <td>{{ optional($detail->produk)->name }}</td>
                                <td class="text-center">{{ $detail->qty }}</td>
                                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-weight-bold bg-light">
                                <td colspan="2" class="text-right">Total</td>
                                <td class="text-right">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-right">Bayar</td>
                                <td class="text-right">Rp {{ number_format($transaksi->uang_bayar, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="text-success font-weight-bold">
                                <td colspan="2" class="text-right">Kembalian</td>
                                <td class="text-right">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr class="my-2">
                    <div class="text-center text-muted small mb-0">
                        Terima kasih atas kunjungan Anda<br>
                        Barang yang sudah dibeli tidak dapat ditukar
                    </div>
                </div>
                <div class="card-footer bg-white py-2 d-flex justify-content-between align-items-center">
                    <button onclick="cetakStruk()" class="btn btn-primary btn-sm">
                        <i class="fas fa-print mr-1"></i>Print Struk
                    </button>
                    <button onclick="downloadStruk()" class="btn btn-success btn-sm">
                        <i class="fas fa-download mr-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .struk-print {
        border: 1px solid #dee2e6;
    }
    .struk-print .card-header {
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    @media print {
        .no-print, .btn-no-print { display: none !important; }
        body * { visibility: hidden !important; }
        #strukPrintable, #strukPrintable * { visibility: visible !important; }
        #strukPrintable { position: absolute !important; left: 0; top: 0; width: 100%; }
        .struk-print { border: none !important; box-shadow: none !important; }
        .card-footer { display: none !important; }
        html, body { background: white !important; }
    }
</style>

<script>
    function cetakStruk() {
        window.print();
    }

    function downloadStruk() {
        window.print();
    }
</script>