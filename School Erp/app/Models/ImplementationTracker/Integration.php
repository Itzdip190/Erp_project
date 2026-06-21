<?php

namespace App\Models\ImplementationTracker;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class Integration extends Model
{
    use BelongsToSchool, LogsActivity;

    protected $table = 'impl_integrations';

    protected $fillable = [
        'school_id',
        'integration_name',
        'company',
        'serial_number',
        'vendor_contact_details',
        'api_received_on',
        'implemented_on',
        'tat',
        'owner_school_side',
        'confirmation_school_side',
        'status',
        'comment',
    ];

    protected $casts = [
        'api_received_on' => 'datetime',
        'implemented_on' => 'datetime',
    ];
}
