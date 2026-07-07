<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeHead extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'income_heads';

    protected $fillable = [
        'school_id',
        'name',
        'budget_target',
        'created_by',
    ];

    protected $casts = [
        'budget_target' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function vouchers()
    {
        return $this->hasMany(IncomeVoucher::class, 'income_head_id');
    }

    public function incomes()
    {
        return $this->hasMany(SchoolIncome::class, 'income_head_id');
    }

    public function getActualRevenueAttribute(): float
    {
        return (float) $this->incomes()->where('status', '!=', 'cancelled')->sum('amount');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
