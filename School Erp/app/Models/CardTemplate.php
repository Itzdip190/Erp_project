<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'background_color',
        'text_color',
        'layout_style',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
