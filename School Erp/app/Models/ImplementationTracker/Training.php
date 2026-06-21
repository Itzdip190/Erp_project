<?php

namespace App\Models\ImplementationTracker;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Training extends Model
{
    use BelongsToSchool, LogsActivity;

    protected $table = 'impl_training';

    protected $fillable = [
        'school_id',
        'module_name',
        'training_done_on',
        'training_given_to',
        'minutes_of_meeting',
        'attachments',
        'uploaded_by',
        'owner_school_side',
        'confirmation_school_side',
        'status',
        'comment',
    ];

    protected $casts = [
        'training_done_on' => 'datetime',
    ];
}
