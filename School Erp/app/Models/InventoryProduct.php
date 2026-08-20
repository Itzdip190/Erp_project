<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'inventory_products';

    protected $fillable = [
        'school_id',
        'category_id',
        'name',
        'price',
        'mrp',
        'tax',
        'status',
        'size_type',
        'selected_sizes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'tax' => 'decimal:2',
        'status' => 'boolean',
        'selected_sizes' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(InventoryStock::class, 'product_id');
    }

    public function logs()
    {
        return $this->hasMany(InventoryStockLog::class, 'product_id');
    }

    /**
     * Get formatted sizes string (e.g. "Free", "M, S, XXL", "1, 2, 3")
     */
    public function getSizesDisplayAttribute(): string
    {
        if ($this->size_type === 'none' || empty($this->selected_sizes)) {
            return 'Free';
        }

        if (is_array($this->selected_sizes)) {
            return implode(', ', $this->selected_sizes);
        }

        return (string) $this->selected_sizes;
    }

    /**
     * Get total stock across all size variants
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->sum('stock');
        }

        return (int) $this->stocks()->sum('stock');
    }
}
