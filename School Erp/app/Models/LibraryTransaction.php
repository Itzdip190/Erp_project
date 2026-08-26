<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryTransaction extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'library_transactions';

    protected $fillable = [
        'school_id',
        'transaction_code',
        'book_id',
        'member_type',
        'student_id',
        'staff_id',
        'issue_date',
        'due_date',
        'return_date',
        'renewed_count',
        'status',
        'late_days',
        'late_fine_amount',
        'damage_lost_fine',
        'total_fine',
        'fine_status',
        'payment_date',
        'remarks',
        'issued_by',
        'received_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'payment_date' => 'date',
        'renewed_count' => 'integer',
        'late_days' => 'integer',
        'late_fine_amount' => 'decimal:2',
        'damage_lost_fine' => 'decimal:2',
        'total_fine' => 'decimal:2',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
