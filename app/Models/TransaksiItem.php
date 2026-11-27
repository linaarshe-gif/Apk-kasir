<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    use HasFactory;

    /** * The attributes that are mass assignable. * * @var array<int, string> */ protected $fillable = [
        'transaksi_id',
        'produk_id',
        'qty',
        'subtotal',
    ];

    /** * Get the transaksi that owns the item. */ public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    /** * Get the produk that owns the item. */ public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
