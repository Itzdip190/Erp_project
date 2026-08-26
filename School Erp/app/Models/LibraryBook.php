<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryBook extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'library_books';

    protected $fillable = [
        'school_id',
        'accession_no',
        'isbn',
        'title',
        'author',
        'publisher',
        'edition',
        'publication_year',
        'section_id',
        'book_type_id',
        'language',
        'pages',
        'rack_location',
        'price',
        'total_copies',
        'available_copies',
        'issued_copies',
        'lost_copies',
        'damaged_copies',
        'cover_image',
        'description',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'issued_copies' => 'integer',
        'lost_copies' => 'integer',
        'damaged_copies' => 'integer',
        'pages' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(LibrarySection::class, 'section_id');
    }

    public function bookType(): BelongsTo
    {
        return $this->belongsTo(LibraryBookType::class, 'book_type_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LibraryTransaction::class, 'book_id');
    }
}
