<?php

namespace App\Models\Traits;

use App\Models\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new SchoolScope);

        static::creating(function ($model) {
            if (!$model->school_id && auth()->check()) {
                $model->school_id = auth()->user()->school_id;
            }
        });
    }

    // Superadmin scope override
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->withoutGlobalScope(SchoolScope::class)
                     ->where('school_id', $schoolId);
    }
}
