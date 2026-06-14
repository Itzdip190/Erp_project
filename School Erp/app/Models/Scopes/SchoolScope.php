<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) return;

        // Security Fix: bypass by ROLE not by null school_id
        if (auth()->user()->hasRole('superadmin')) return;

        $builder->where(
            $model->getTable() . '.school_id',
            auth()->user()->school_id
        );
    }
}
