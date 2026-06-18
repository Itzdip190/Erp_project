<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'title_text',
        'body_text',
        'background_image',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
