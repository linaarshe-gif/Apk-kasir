<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'kategori_id',
        'harga',
        'stok',
        'gambar',
    ];

    /**
     * Get the kategori that owns the produk.
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
