<?php

namespace App\Models\ImplementationTracker;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class TemplateImplementation extends Model
{
    use BelongsToSchool, LogsActivity;

    protected $table = 'impl_template_implementation';

    protected $fillable = [
        'school_id',
        'template_name',
        'important_dates',
        'template_received_attachment',
        'uploaded_by_1',
        'template_received_on',
        'implemented_template_attachment',
        'uploaded_by_2',
        'template_implemented_on',
        'owner_school_side',
        'confirmation_school_side',
        'status',
        'comment',
    ];

    protected $casts = [
        'important_dates' => 'date',
        'template_received_on' => 'datetime',
        'template_implemented_on' => 'datetime',
    ];
}
