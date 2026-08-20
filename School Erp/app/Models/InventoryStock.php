<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'inventory_stocks';

    protected $fillable = [
        'school_id',
        'product_id',
        'size',
        'stock',
        'price',
        'mrp',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
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
