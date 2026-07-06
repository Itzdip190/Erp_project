<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'address',
        'admin_name',
        'admin_email',
        'admin_password',
        'plan_id',
        'status',
        'rejected_reason',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
