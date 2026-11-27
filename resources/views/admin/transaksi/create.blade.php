<form method="POST" action="/admin/transaksi">
    @csrf
    <div class="container-fluid pt-2">
        <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-box mr-2"></i>Pilih Produk</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <input type="text" id="searchProduk" class="form-control" placeholder="Cari produk...">
                    </div>
                    <div id="produkList" class="row">
                        @foreach ($produks as $produk)
                        <div class="col-md-4 mb-3">
                            <div class="card produk-card h-100" data-id="{{ $produk->id }}" data-name="{{ $produk->name }}" data-harga="{{ $produk->harga }}" data-stok="{{ $produk->stok }}">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ $produk->name }}</h6>
                                    <p class="text-muted small">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                    <span class="badge badge-success stok-badge">{{ $produk->stok }} Stok</span>
                                    <div class="mt-3">
                                        <input type="number" class="form-control qty-input" value="1" min="1" max="{{ $produk->stok }}">
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100 btn-tambah-ke-keranjang"><i class="fas fa-plus mr-1"></i>Tambah ke Keranjang</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart mr-2"></i>Keranjang</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label><b>Nama Pembeli</b></label>
                        @if(auth()->user()->isUser())
                            <input type="hidden" id="user_id" value="{{ auth()->user()->id }}">
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        @else
                            <select id="user_id" name="user_id" class="form-control" required>
                                <option value="">-- Pilih Kasir --</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm" id="tabelKeranjang">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40%">Produk</th>
                                    <th style="width: 15%">Qty</th>
                                    <th style="width: 20%">Subtotal</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="keranjangBody">
                                @foreach ($cart ?? [] as $item)
                                <tr data-produk-id="{{ $item['produk_id'] }}">
                                    <td>{{ $item['produk_name'] }}</td>
                                    <td>{{ $item['qty'] }}</td>
                                    <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td><button class="btn btn-danger btn-sm btn-hapus-dari-keranjang"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endforeach
                                @if (empty($cart))
                                <tr><td colspan="4" class="text-center text-muted">Belum ada item di keranjang</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><label><b>Total:</b></label></div>
                        <div class="col-6 text-right"><span id="totalDisplay" class="font-weight-bold text-danger">Rp 0</span></div>
                        <input type="hidden" id="totalInput" name="total_harga" value="0">
                    </div>
                    <div class="form-group mb-3">
                        <label><b>Uang Bayar</b></label>
                        <input type="number" id="uangBayar" name="uang_bayar" class="form-control" placeholder="Masukkan uang bayar" min="0">
                    </div>
                    <div class="form-group mb-3">
                        <label><b>Kembalian:</b></label>
                        <span id="kembalianDisplay" class="font-weight-bold text-success">Rp 0</span>
                        <input type="hidden" id="kembalianInput" name="kembalian" value="0">
                    </div>
                    <div class="form-group mb-3">
                        <label><b>Tanggal Transaksi</b></label>
                        <input type="date" name="tanggal_transaksi" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <input type="hidden" name="user_id" id="hiddenUserId">
                    <input type="hidden" name="cart_data" id="cartDataInput">
                    <div class="d-grid gap-2">
                        <a href="/admin/transaksi" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>Kembali</a>
                        <button id="btnSimpanTransaksi" type="button" class="btn btn-success btn-lg"><i class="fas fa-print mr-1"></i>Simpan & Cetak Struk</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@if(session()->has('success'))
<div class="alert alert-success alert-dismissible fade show mt-3"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif
@if(session()->has('error'))
<div class="alert alert-danger alert-dismissible fade show mt-3"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mt-3">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<style>
.produk-card { transition: all 0.3s ease; border: 1px solid #e9ecef; }
.produk-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #007bff; cursor: pointer; }
.produk-card .btn-tambah-ke-keranjang:disabled { opacity: 0.65; cursor: not-allowed; }
.stok-badge { font-size: 0.75rem; }
.cart-item-added { animation: slideIn 0.3s ease; }
@keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<style>
.produk-card { transition: all 0.3s ease; border: 1px solid #e9ecef; }
.produk-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-color: #007bff; }
.produk-card .btn-tambah-ke-keranjang:disabled { opacity: 0.65; cursor: not-allowed; }
.stok-badge { font-size: 0.75rem; }
.cart-item-added { animation: slideIn 0.3s ease; }
@keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let cart = @json($cart ?? []);
    let total = 0;

    cart.forEach(function(item) { total += parseInt(item.subtotal || 0); });
    updateCartUI();

    $('#searchProduk').on('keyup', function() {
        let keyword = $(this).val().toLowerCase();
        $('.produk-card').each(function() {
            let nama = $(this).data('name').toLowerCase();
            $(this).closest('.col-md-4, .col-sm-6').toggle(nama.indexOf(keyword) > -1);
        });
    });

    $('.btn-tambah-ke-keranjang').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let btn = $(this);
        let card = btn.closest('.produk-card');
        let produkId = card.data('id');
        let produkName = card.data('name');
        let harga = parseInt(card.data('harga'));
        let stok = parseInt(card.data('stok'));
        let qty = parseInt(card.find('.qty-input').val());

        if (qty > stok) { alert('Stok tidak mencukupi!'); return; }
        let subtotal = harga * qty;

        let existingRow = $('#tabelKeranjang tbody tr[data-produk-id="' + produkId + '"]');
        if (existingRow.length > 0) {
            let existingQty = parseInt(existingRow.find('td:nth-child(2)').text());
            let newQty = existingQty + qty;
            if (newQty > stok) { alert('Stok tidak mencukupi!'); return; }
            existingRow.find('td:nth-child(2)').text(newQty);
            existingRow.find('td:nth-child(3)').text('Rp ' + formatRupiah(harga * newQty));
            existingRow.addClass('cart-item-added');
            setTimeout(function(){ existingRow.removeClass('cart-item-added'); }, 300);
        } else {
            $('#tabelKeranjang tbody tr:has(td[colspan])').remove();
            let row = '<tr class="cart-item-added" data-produk-id="' + produkId + '">' +
                '<td>' + produkName + '</td>' +
                '<td>' + qty + '</td>' +
                '<td>Rp ' + formatRupiah(subtotal) + '</td>' +
                '<td><button class="btn btn-danger btn-sm btn-hapus-dari-keranjang"><i class="fas fa-trash-alt"></i></button></td>' +
                '</tr>';
            $('#tabelKeranjang tbody').append(row);
            setTimeout(function(){ $('#tabelKeranjang tbody tr:last-child').removeClass('cart-item-added'); }, 300);
        }

        total += subtotal;
        updateCartUI();
        card.find('.qty-input').val(1);

        $.ajax({
            url: '{{ route('transaksi.cart.add') }}',
            method: 'POST',
            data: { produk_id: produkId, qty: qty, _token: '{{ csrf_token() }}' },
            success: function(response) { console.log('Item ditambahkan ke session'); },
            error: function(xhr) {
                total -= subtotal;
                updateCartUI();
                alert('Gagal menambah ke keranjang. Coba lagi.');
            }
        });
    });

    $(document).on('click', '.btn-hapus-dari-keranjang', function(e) {
        e.stopPropagation();
        let row = $(this).closest('tr');
        let produkId = row.data('produk-id');
        let subtotal = parseInt(row.find('td:nth-child(3)').text().replace(/[^0-9]/g, ''));
        total -= subtotal;
        row.fadeOut(200, function() {
            row.remove();
            updateCartUI();
            if ($('#tabelKeranjang tbody tr').length === 0) {
                $('#tabelKeranjang tbody').html('<tr><td colspan="4" class="text-center text-muted py-3">Belum ada item di keranjang</td></tr>');
            }
        });
        $.post('{{ route('transaksi.cart.remove') }}', { produk_id: produkId, _token: '{{ csrf_token() }}' });
    });

    $('#uangBayar').on('input', function() {
        let bayar = parseInt($(this).val()) || 0;
        let kembali = Math.max(0, bayar - total);
        $('#kembalianDisplay').text('Rp ' + formatRupiah(kembali));
        $('#kembalianInput').val(kembali);
    });

    $('#btnSimpanTransaksi').on('click', function() {
        let userId = $('#user_id').val();
        let uangBayar = parseInt($('#uangBayar').val()) || 0;
        if (!userId) { alert('Pilih pengguna terlebih dahulu!'); return; }
        if (total === 0) { alert('Keranjang masih kosong!'); return; }
        if (uangBayar < total) { alert('Uang bayar kurang!'); return; }
        $('#hiddenUserId').val(userId);
        $('#cartDataInput').val(JSON.stringify(getCartItems()));
        $('form').submit();
    });

    function updateCartUI() {
        $('#totalDisplay').text('Rp ' + formatRupiah(total));
        $('#totalInput').val(total);
        let items = getCartItems();
        $('#itemCount').text(items.length);
        cart = items;
    }

    function getCartItems() {
        return $('#tabelKeranjang tbody tr[data-produk-id]').map(function() {
            return {
                produk_id: $(this).data('produk-id'),
                produk_name: $(this).find('td:nth-child(1)').text(),
                qty: parseInt($(this).find('td:nth-child(2)').text()),
                subtotal: parseInt($(this).find('td:nth-child(3)').text().replace(/[^0-9]/g, ''))
            };
        }).get();
    }

    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>
