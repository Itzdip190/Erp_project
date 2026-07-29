<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VehicleDocument extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'vehicle_id',
        'document_type',
        'document_number',
        'valid_from',
        'valid_to',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    protected $casts = [
        'valid_from' => 'date:Y-m-d',
        'valid_to' => 'date:Y-m-d',
        'file_size' => 'integer',
    ];

    protected $appends = [
        'file_url',
        'formatted_file_size',
    ];

    public function getFileUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '';
        }
        return Storage::disk(config('filesystems.default', 'public'))->url($this->file_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '—';
        }
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
