<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStockLog extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'inventory_stock_logs';

    protected $fillable = [
        'school_id',
        'product_id',
        'size',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function product()
    {
        return $this->belongsTo(InventoryProduct::class, 'product_id');
    }
}
