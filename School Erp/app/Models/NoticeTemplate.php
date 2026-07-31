<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeTemplate extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'notice_templates';

    protected $fillable = [
        'school_id',
        'title',
        'target_audience',
        'category',
        'content',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
