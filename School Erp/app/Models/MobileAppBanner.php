<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileAppBanner extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'mobile_app_banners';

    protected $fillable = [
        'school_id',
        'title',
        'subtitle',
        'badge_text',
        'image_url',
        'target_type', // 'none', 'screen', 'url', 'notice', 'fee', 'event', 'exam'
        'target_route',
        'target_role',  // 'all', 'student', 'parent', 'teacher', 'staff'
        'bg_color',
        'display_order',
        'status',       // 'active', 'inactive'
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        $today = Carbon::today()->toDateString();
        return $query->where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            })
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'desc');
    }

    public function scopeForRole($query, ?string $role = null)
    {
        if (!$role || $role === 'all') {
            return $query;
        }

        return $query->where(function ($q) use ($role) {
            $q->where('target_role', 'all')
              ->orWhere('target_role', $role);
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
