<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AdminTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $transaksiQuery = Transaksi::with(['user', 'items.produk']);
        if ($user->isUser()) {
            // User hanya bisa melihat transaksi milik sendiri.
            $transaksiQuery->where('user_id', $user->id);
        }

        $data = [
            'transaksis' => $transaksiQuery->get(),
            'content' => 'admin.transaksi.index',
        ];

        return view('admin.layouts.wrapper', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $data = [
            'produks' => Produk::get(),
            'users' => $user->isUser() ? User::where('id', $user->id)->get() : User::get(),
            'cart' => Session::get('transaksi_cart', []),
            'content' => 'admin.transaksi.create',
        ];

        return view('admin.layouts.wrapper', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cart = Session::get('transaksi_cart', []);
        if (empty($cart)) {
            return redirect('/admin/transaksi/create')->with('error', 'Keranjang masih kosong!');
        }

        $user = auth()->user();
        if ($user->isUser()) {
            // User hanya bisa membuat transaksi untuk dirinya sendiri.
            $request->merge(['user_id' => $user->id]);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_transaksi' => 'required|date',
            'uang_bayar' => 'required|integer|min:0',
        ]);

        $tanggal = $request->tanggal_transaksi;
        $uangBayar = (int) $request->uang_bayar;

        DB::transaction(function () use ($cart, $request, $tanggal, $uangBayar) {
            $quantities = [];

            foreach ($cart as $item) {
                $produkId = (int) ($item['produk_id'] ?? 0);
                $qty = (int) ($item['qty'] ?? 0);

                if ($produkId < 1 || $qty < 1) {
                    throw ValidationException::withMessages(['cart' => 'Item keranjang tidak valid.']);
                }

                $quantities[$produkId] = ($quantities[$produkId] ?? 0) + $qty;
            }

            $produks = Produk::whereIn('id', array_keys($quantities))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($produks->count() !== count($quantities)) {
                throw ValidationException::withMessages(['cart' => 'Produk di keranjang sudah tidak tersedia.']);
            }

            $items = [];
            $grandTotal = 0;

            foreach ($quantities as $produkId => $qty) {
                $produk = $produks->get($produkId);

                if ($qty > $produk->stok) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$produk->name} tidak mencukupi. Stok tersedia: {$produk->stok}.",
                    ]);
                }

                $subtotal = $produk->harga * $qty;
                $grandTotal += $subtotal;
                $items[] = [
                    'produk_id' => $produkId,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            if ($uangBayar < $grandTotal) {
                throw ValidationException::withMessages(['uang_bayar' => 'Uang bayar kurang dari total transaksi.']);
            }

            $transaksi = Transaksi::create([
                'user_id' => $request->user_id,
                'tanggal_transaksi' => $tanggal,
                'uang_bayar' => $uangBayar,
                'kembalian' => $uangBayar - $grandTotal,
                'stock_deducted' => true,
            ]);

            foreach ($items as $item) {
                $transaksi->items()->create($item);
                Produk::whereKey($item['produk_id'])->decrement('stok', $item['qty']);
            }
        });

        Session::forget('transaksi_cart');

        return redirect('/admin/transaksi')->with('success', 'Transaksi Berhasil Disimpan dan Struk Dicetak!!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $transaksiQuery = Transaksi::with('items.produk')->where('id', $id);
        if ($user->isUser()) {
            $transaksiQuery->where('user_id', $user->id);
        }
        $transaksi = $transaksiQuery->firstOrFail();
        $data = [
            'transaksi' => $transaksi,
            'produks' => Produk::get(),
            'users' => $user->isUser() ? User::where('id', $user->id)->get() : User::get(),
            'cart' => $transaksi->items->map(function ($item) {
                return [
                    'produk_id' => $item->produk_id,
                    'produk_name' => optional($item->produk)->name,
                    'harga' => optional($item->produk)->harga,
                    'qty' => $item->qty,
                    'subtotal' => $item->subtotal,
                ];
            })->values()->all(),
            'content' => 'admin.transaksi.create',
        ];

        return view('admin.layouts.wrapper', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $transaksiQuery = Transaksi::where('id', $id);
        if ($user->isUser()) {
            $transaksiQuery->where('user_id', $user->id);
        }
        $transaksi = $transaksiQuery->firstOrFail();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_transaksi' => 'required|date',
            'uang_bayar' => 'required|integer|min:0',
        ]);

        $uangBayar = (int) $request->uang_bayar;
        $grandTotal = $transaksi->items->sum('subtotal');
        $kembalian = max(0, $uangBayar - $grandTotal);

        $transaksi->update([
            'user_id' => $user->isUser() ? $user->id : $request->user_id,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'uang_bayar' => $uangBayar,
            'kembalian' => $kembalian,
        ]);

        return redirect('/admin/transaksi')->with('success', 'Data Transaksi Telah diedit!!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $transaksiQuery = Transaksi::where('id', $id);
        if ($user->isUser()) {
            $transaksiQuery->where('user_id', $user->id);
        }
        $transaksi = $transaksiQuery->firstOrFail();

        DB::transaction(function () use ($transaksi) {
            $transaksi->load('items');

            if ($transaksi->stock_deducted) {
                foreach ($transaksi->items as $item) {
                    Produk::whereKey($item->produk_id)->increment('stok', $item->qty);
                }
            }

            $transaksi->delete();
        });

        return redirect('/admin/transaksi')->with('success', 'Data Transaksi Telah dihapus!!');
    }

    /** * Cetak struk transaksi. */
    public function struk(string $id)
    {
        $user = auth()->user();
        $transaksiQuery = Transaksi::with(['user', 'items.produk'])->where('id', $id);
        if ($user->isUser()) {
            $transaksiQuery->where('user_id', $user->id);
        }
        $transaksi = $transaksiQuery->firstOrFail();
        $data = [
            'transaksi' => $transaksi,
            'content' => 'admin.transaksi.struk',
        ];

        return view('admin.layouts.wrapper', $data);
    }

    /** * Add item to cart (session). */
    public function addToCart(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'qty' => 'required|integer|min:1',
        ]);

        $produk = Produk::find($request->produk_id);
        $qty = (int) $request->qty;
        $subtotal = $produk->harga * $qty;

        $cart = Session::get('transaksi_cart', []);
        $currentQty = 0;

        foreach ($cart as $item) {
            if ((int) $item['produk_id'] === $produk->id) {
                $currentQty += (int) $item['qty'];
            }
        }

        if ($currentQty + $qty > $produk->stok) {
            return response()->json([
                'success' => false,
                'message' => "Stok {$produk->name} tidak mencukupi. Stok tersedia: {$produk->stok}.",
            ], 422);
        }

        $found = false;
        foreach ($cart as $key => $item) {
            if ($item['produk_id'] == $produk->id) {
                $cart[$key]['qty'] += $qty;
                $cart[$key]['subtotal'] = $cart[$key]['qty'] * $produk->harga;
                $found = true;
                break;
            }
        }

        if (! $found) {
            $cart[] = [
                'produk_id' => $produk->id,
                'produk_name' => $produk->name,
                'harga' => $produk->harga,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        Session::put('transaksi_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => array_sum(array_column($cart, 'subtotal')),
        ]);
    }

    /** * Remove item from cart. */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|integer',
        ]);

        $cart = Session::get('transaksi_cart', []);
        $cart = array_values(array_filter($cart, function ($item) use ($request) {
            return $item['produk_id'] != $request->produk_id;
        }));

        Session::put('transaksi_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => array_sum(array_column($cart, 'subtotal')),
        ]);
    }

    /** * Clear all items from cart. */
    public function clearCart()
    {
        Session::forget('transaksi_cart');

        return response()->json(['success' => true]);
    }
}
