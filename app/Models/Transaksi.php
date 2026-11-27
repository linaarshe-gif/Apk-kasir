<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tanggal_transaksi',
        'uang_bayar',
        'kembalian',
        'stock_deducted',
    ];

    protected function casts(): array
    {
        return [
            'stock_deducted' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the transaksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the detail items of the transaksi.
     */
    public function items()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id');
    }

    /**
     * Get the grand total of all items in this transaksi.
     */
    public function getTotalAttribute()
    {
        return $this->items->sum('subtotal');
    }
}
