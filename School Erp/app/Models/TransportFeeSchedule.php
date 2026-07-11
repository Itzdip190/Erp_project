<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportFeeSchedule extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'academic_session_id',
        'route_id',
        'name',
        'installments',
        'months',          // virtual alias — handled by setMonthsAttribute mutator → installments column
        'installment_type',
        'fine_id',
        'is_active',
    ];

    protected $casts = [
        'installments' => 'array',
        'is_active' => 'boolean',
    ];

    public function getMonthsAttribute()
    {
        return $this->installments;
    }

    public function setMonthsAttribute($value)
    {
        // Map the legacy 'months' key to the 'installments' column.
        // If items use old {label, due_date} format, normalise them so
        // generateTransportInstallments can iterate with installment_no / start_date / end_date.
        if (is_array($value)) {
            $normalised = [];
            foreach ($value as $idx => $item) {
                if (is_array($item) && !isset($item['installment_no'])) {
                    // Old format: {label, due_date}
                    $dueDate  = $item['due_date']  ?? null;
                    $label    = $item['label']     ?? ($dueDate ? \Carbon\Carbon::parse($dueDate)->format('F Y') : ('Month ' . ($idx + 1)));
                    $monthDate = $dueDate ? \Carbon\Carbon::parse($dueDate) : \Carbon\Carbon::now()->addMonths($idx);
                    $normalised[] = [
                        'installment_no' => $idx + 1,
                        'name'           => $label,
                        'start_date'     => $monthDate->copy()->startOfMonth()->toDateString(),
                        'end_date'       => $monthDate->copy()->endOfMonth()->toDateString(),
                        'due_date'       => $dueDate,
                        'grace_days'     => 5,
                    ];
                } else {
                    $normalised[] = $item;
                }
            }
            $value = $normalised;
        }
        $this->attributes['installments'] = json_encode($value);
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function route()
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function fine()
    {
        return $this->belongsTo(FeeFine::class, 'fine_id');
    }

    public function getNoOfInstallmentsAttribute(): int
    {
        return is_array($this->installments) ? count($this->installments) : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Resolve the active transport fee schedule for a given route,
     * falling back to the school-wide default if no route-specific override exists.
     */
    public static function resolveFor($schoolId, $academicSessionId, $routeId)
    {
        if ($routeId) {
            $routeSchedule = self::where('school_id', $schoolId)
                ->where('academic_session_id', $academicSessionId)
                ->where('route_id', $routeId)
                ->where('is_active', true)
                ->first();

            if ($routeSchedule) {
                return $routeSchedule;
            }
        }

        return self::where('school_id', $schoolId)
            ->where('academic_session_id', $academicSessionId)
            ->whereNull('route_id')
            ->where('is_active', true)
            ->first();
    }
}
