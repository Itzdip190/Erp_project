<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryPost extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'posted_by',
        'type',
        'academic_year',
        'title',
        'description',
        'attachments',
        'recipients',
        'is_scheduled',
        'scheduled_at',
        'show_popup',
    ];

    protected $casts = [
        'attachments'  => 'array',
        'is_scheduled' => 'boolean',
        'show_popup'   => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
