<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTaskHead extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'daily_task_heads';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(DailyTaskQuestion::class, 'daily_task_head_id');
    }
}
