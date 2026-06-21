<?php

namespace App\Models\ImplementationTracker;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class ImplActivityLog extends Model
{
    use BelongsToSchool;

    protected $table = 'impl_activity_logs';

    protected $fillable = [
        'school_id',
        'tab_name',
        'row_reference',
        'field_changed',
        'old_value',
        'new_value',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];
}
