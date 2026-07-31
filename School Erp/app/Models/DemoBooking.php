<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoBooking extends Model
{
    use HasFactory;

    protected $table = 'demo_bookings';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'institute_name',
        'student_count',
        'role',
        'city',
        'state',
        'country',
        'message',
        'status',
    ];
}
