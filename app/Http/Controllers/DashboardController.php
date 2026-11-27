<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isUser = $user->isUser();

        $totalProduk = Produk::count();
        $totalStok = Produk::sum('stok');

        $transaksiQuery = Transaksi::query();
        if ($isUser) {
            // User hanya melihat statistik transaksi milik sendiri.
            $transaksiQuery->where('user_id', $user->id);
        }

        $totalTransaksi = $transaksiQuery->count();
        $omzet = $transaksiQuery
            ->join('transaksi_items', 'transaksis.id', '=', 'transaksi_items.transaksi_id')
            ->sum('transaksi_items.subtotal');

        $data = [
            'content' => 'admin.dashboard.index',
            'totalProduk' => $totalProduk,
            'totalStok' => $totalStok,
            'totalTransaksi' => $totalTransaksi,
            'omzet' => $omzet,
            'isUser' => $isUser,
        ];

        return view('admin.layouts.wrapper', $data);
    }
}
