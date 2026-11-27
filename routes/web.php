<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminKategoriController;
use App\Http\Controllers\AdminProdukController;
use App\Http\Controllers\AdminTransaksiController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AdminAuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login/do', [AdminAuthController::class, 'doLogin'])->middleware('guest');
Route::get('logout', [AdminAuthController::class, 'logout'])->middleware('auth');

// Dashboard & Transaksi dapat diakses oleh admin dan user.
Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'role:admin,user']);
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'role:admin,user']);

Route::prefix('/admin')->middleware(['auth', 'role:admin,user'])->group(function () {
    // Manajemen master data hanya boleh admin.
    Route::middleware('role:admin')->group(function () {
        Route::resource('/user', AdminUserController::class);
        Route::resource('/kategori', AdminKategoriController::class);
        Route::resource('/produk', AdminProdukController::class);
    });

    // Transaksi dapat diakses oleh admin dan user.
    Route::resource('/transaksi', AdminTransaksiController::class);
    Route::post('/transaksi/cart/add', [AdminTransaksiController::class, 'addToCart'])->name('transaksi.cart.add');
    Route::post('/transaksi/cart/remove', [AdminTransaksiController::class, 'removeFromCart'])->name('transaksi.cart.remove');
    Route::post('/transaksi/cart/clear', [AdminTransaksiController::class, 'clearCart'])->name('transaksi.cart.clear');
    Route::get('/transaksi/{id}/struk', [AdminTransaksiController::class, 'struk'])->name('transaksi.struk');
});
