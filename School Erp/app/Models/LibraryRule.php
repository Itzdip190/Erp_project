<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryRule extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'library_rules';

    protected $fillable = [
        'school_id',
        'member_type',
        'borrow_period_days',
        'max_books_allowed',
        'late_fine_amount',
        'late_fine_type',
        'lost_book_fine_amount',
        'lost_book_fine_type',
        'damaged_book_fine_amount',
        'damaged_book_fine_type',
        'grace_period_days',
        'max_renewal_count',
        'allow_issue_with_fine',
        'max_fine_threshold',
        'extra_config',
    ];

    protected $casts = [
        'borrow_period_days' => 'integer',
        'max_books_allowed' => 'integer',
        'late_fine_amount' => 'decimal:2',
        'lost_book_fine_amount' => 'decimal:2',
        'damaged_book_fine_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
        'max_renewal_count' => 'integer',
        'allow_issue_with_fine' => 'boolean',
        'max_fine_threshold' => 'decimal:2',
        'extra_config' => 'array',
    ];

    /**
     * Get or create standard default rule for student or staff for a school.
     */
    public static function getRuleFor(int $schoolId, string $memberType): self
    {
        $memberType = strtolower($memberType) === 'staff' ? 'staff' : 'student';

        $defaults = $memberType === 'student' ? [
            'borrow_period_days' => 14,
            'max_books_allowed' => 3,
            'late_fine_amount' => 5.00,
            'late_fine_type' => 'per_day',
            'lost_book_fine_amount' => 200.00,
            'lost_book_fine_type' => 'fixed_amount',
            'damaged_book_fine_amount' => 100.00,
            'damaged_book_fine_type' => 'fixed_amount',
            'grace_period_days' => 0,
            'max_renewal_count' => 2,
            'allow_issue_with_fine' => false,
            'max_fine_threshold' => 100.00,
        ] : [
            'borrow_period_days' => 30,
            'max_books_allowed' => 5,
            'late_fine_amount' => 2.00,
            'late_fine_type' => 'per_day',
            'lost_book_fine_amount' => 300.00,
            'lost_book_fine_type' => 'fixed_amount',
            'damaged_book_fine_amount' => 150.00,
            'damaged_book_fine_type' => 'fixed_amount',
            'grace_period_days' => 0,
            'max_renewal_count' => 3,
            'allow_issue_with_fine' => false,
            'max_fine_threshold' => 200.00,
        ];

        return static::firstOrCreate(
            ['school_id' => $schoolId, 'member_type' => $memberType],
            $defaults
        );
    }
}
