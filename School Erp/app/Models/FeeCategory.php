<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'description',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function structures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}
