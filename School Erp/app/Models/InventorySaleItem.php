<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorySaleItem extends Model
{
    use HasFactory;

    protected $table = 'inventory_sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'size',
        'mrp',
        'price',
        'tax_percent',
        'tax_amount',
        'quantity',
        'discount',
        'total_mrp',
        'total_price',
        'total_tax',
        'total_amount',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'price' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'quantity' => 'integer',
        'discount' => 'decimal:2',
        'total_mrp' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(InventorySale::class, 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(InventoryProduct::class, 'product_id');
    }
}
