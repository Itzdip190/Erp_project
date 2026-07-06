<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id', 'module_key', 'feature_key', 'view_access', 'edit_access',
    ];

    protected $casts = [
        'view_access' => 'boolean',
        'edit_access' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
