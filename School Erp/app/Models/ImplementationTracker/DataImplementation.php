<?php

namespace App\Models\ImplementationTracker;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class DataImplementation extends Model
{
    use BelongsToSchool, LogsActivity;

    protected $table = 'impl_data_implementation';

    protected $fillable = [
        'school_id',
        'module_name',
        'attachments',
        'uploaded_by',
        'data_received_date',
        'data_implemented_on',
        'tat',
        'owner_school_side',
        'confirmation_school_side',
        'status',
        'comment',
    ];

    protected $casts = [
        'data_received_date' => 'datetime',
        'data_implemented_on' => 'datetime',
    ];
}
