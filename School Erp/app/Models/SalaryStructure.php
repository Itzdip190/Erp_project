<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SalaryStructure extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'salary_structures';

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'basic_salary',
        'salary_type',
        'hra',
        'da',
        'ta',
        'allowance',
        'pf',
        'esi',
        'tds',
        'prof_tax',
        'effective_from',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'da' => 'decimal:2',
        'ta' => 'decimal:2',
        'allowance' => 'decimal:2',
        'pf' => 'decimal:2',
        'esi' => 'decimal:2',
        'tds' => 'decimal:2',
        'prof_tax' => 'decimal:2',
        'effective_from' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Self-healing runtime schema initializer
     */
    public static function ensureSchemaExists(): void
    {
        try {
            if (!Schema::hasTable('salary_structures')) {
                Schema::create('salary_structures', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('school_id')->index();
                    $table->string('name', 150);
                    $table->text('description')->nullable();
                    $table->decimal('basic_salary', 12, 2)->default(0);
                    $table->string('salary_type', 50)->default('Monthly');
                    $table->decimal('hra', 12, 2)->default(0);
                    $table->decimal('da', 12, 2)->default(0);
                    $table->decimal('ta', 12, 2)->default(0);
                    $table->decimal('allowance', 12, 2)->default(0);
                    $table->decimal('pf', 12, 2)->default(0);
                    $table->decimal('esi', 12, 2)->default(0);
                    $table->decimal('tds', 12, 2)->default(0);
                    $table->decimal('prof_tax', 12, 2)->default(0);
                    $table->date('effective_from')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->unsignedBigInteger('updated_by')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                    $table->index(['school_id', 'is_active']);
                });
            }

            if (Schema::hasTable('staff') && !Schema::hasColumn('staff', 'salary_structure_id')) {
                Schema::table('staff', function (Blueprint $table) {
                    $table->unsignedBigInteger('salary_structure_id')->nullable()->after('basic_salary');
                    $table->index('salary_structure_id');
                });
            }

            if (Schema::hasTable('staff_salary_structures') && !Schema::hasColumn('staff_salary_structures', 'salary_structure_id')) {
                Schema::table('staff_salary_structures', function (Blueprint $table) {
                    $table->unsignedBigInteger('salary_structure_id')->nullable()->after('staff_id');
                    $table->index('salary_structure_id');
                });
            }
        } catch (\Throwable $e) {
            // Fail safely if database user lacks DDL privileges
        }
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'salary_structure_id');
    }

    public function staffSalaryStructures()
    {
        return $this->hasMany(StaffSalaryStructure::class, 'salary_structure_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get total allowances sum
     */
    public function getTotalAllowancesAttribute(): float
    {
        return (float) (($this->hra ?? 0) + ($this->da ?? 0) + ($this->ta ?? 0) + ($this->allowance ?? 0));
    }

    /**
     * Get total deductions sum
     */
    public function getTotalDeductionsAttribute(): float
    {
        return (float) (($this->pf ?? 0) + ($this->esi ?? 0) + ($this->tds ?? 0) + ($this->prof_tax ?? 0));
    }

    /**
     * Get net estimated monthly salary
     */
    public function getNetSalaryAttribute(): float
    {
        return max(0, (float) (($this->basic_salary ?? 0) + $this->total_allowances - $this->total_deductions));
    }
}
