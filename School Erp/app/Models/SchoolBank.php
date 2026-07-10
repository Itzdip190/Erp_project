<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolBank extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'school_banks';

    protected $fillable = [
        'school_id',
        'bank_name',
        'account_no',
        'branch',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
