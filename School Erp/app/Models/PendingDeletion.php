<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingDeletion extends Model
{
    use \App\Models\Traits\BelongsToSchool;

    protected $fillable = [
        'school_id',
        'type',
        'target_id',
        'item_name',
        'requested_by',
    ];
}
