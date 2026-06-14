<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StudentDocument extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'document_type',
        'file_path',
        'original_name',
    ];

    protected $appends = [
        'file_url',
    ];

    public function getFileUrlAttribute(): string
    {
        return Storage::disk(config('filesystems.default'))->url($this->file_path);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
