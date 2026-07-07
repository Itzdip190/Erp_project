<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseHead extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'expense_heads';

    protected $fillable = [
        'school_id',
        'name',
        'created_by',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function vouchers()
    {
        return $this->hasMany(ExpenseVoucher::class, 'expense_head_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
