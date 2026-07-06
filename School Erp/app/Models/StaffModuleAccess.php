<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;

class StaffModuleAccess extends Model
{
    use BelongsToSchool;

    protected $table = 'staff_module_access';

    protected $fillable = [
        'school_id', 'user_id', 'module_key', 'feature_key', 'view_access', 'edit_access',
    ];

    protected $casts = [
        'view_access' => 'boolean',
        'edit_access' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
