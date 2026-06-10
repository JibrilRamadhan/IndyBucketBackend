<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'features',
        'price',
        'stock_status',
        'label',
        'home_section',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'features' => 'array',
    ];

    /**
     * Relasi ke gambar produk (satu produk bisa punya banyak gambar).
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Ambil gambar utama produk.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
}
