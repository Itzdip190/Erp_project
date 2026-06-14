<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'job_id',
        'file_path',
        'total_rows',
        'success_rows',
        'failed_rows',
        'errors',
        'status',
    ];

    protected $casts = [
        'errors' => 'array',
        'total_rows' => 'integer',
        'success_rows' => 'integer',
        'failed_rows' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
