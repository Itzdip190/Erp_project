<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryBookType extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'library_book_types';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'default_borrow_days_override',
        'fine_multiplier',
        'icon',
        'description',
        'status',
    ];

    protected $casts = [
        'default_borrow_days_override' => 'integer',
        'fine_multiplier' => 'decimal:2',
    ];

    /**
     * Books belonging to this book type.
     */
    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'book_type_id');
    }
}
