<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibrarySection extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'library_sections';

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'rack_location',
        'material_scope',
        'description',
        'status',
    ];

    /**
     * Books belonging to this section.
     */
    public function books(): HasMany
    {
        return $this->hasMany(LibraryBook::class, 'section_id');
    }
}
