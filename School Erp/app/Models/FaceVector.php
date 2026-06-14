<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceVector extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'encoding',
        'photo_path',
    ];

    protected $casts = [
        'encoding' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
