<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExpense extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'vehicle_id',
        'school_expense_id',
        'expense_type',
        'amount',
        'date',
        'description',
        'attachment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function schoolExpense()
    {
        return $this->belongsTo(SchoolExpense::class, 'school_expense_id');
    }
}
