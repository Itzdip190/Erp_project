<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'inventory_categories';

    protected $fillable = [
        'school_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function products()
    {
        return $this->hasMany(InventoryProduct::class, 'category_id');
    }
}
