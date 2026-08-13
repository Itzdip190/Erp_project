<?php

namespace App\Http\Controllers;

use App\Mail\DemoBookingClientMail;
use App\Mail\DemoBookingSuperAdminMail;
use App\Models\DemoBooking;
use App\Http\Controllers\SuperAdmin\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class DemoBookingController extends Controller
{
    public function store(Request $request)
    {
        // Auto-ensure database table and columns exist
        $this->ensureColumnsExist();

        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:30',
            'institute_name' => 'nullable|string|max:255',
            'student_count'  => 'nullable|string|max:100',
            'role'           => 'nullable|string|max:100',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'booking_date'   => 'nullable|string|max:100',
            'booking_time'   => 'nullable|string|max:100',
            'timezone'       => 'nullable|string|max:100',
            'source'         => 'nullable|string|max:50',
            'message'        => 'required|string|max:2000',
        ]);

        $dateVal = $validated['booking_date'] ?? date('Y-m-d');
        $timeVal = $validated['booking_time'] ?? '10:15 AM';
        $tzVal   = $validated['timezone'] ?? 'India Standard Time';
        $source  = $validated['source'] ?? 'Website';

        // Check for duplicate booking within last 1 minute with automatic schema recovery
        try {
            $existing = DemoBooking::where('email', $validated['email'])
                ->where('booking_date', $dateVal)
                ->where('booking_time', $timeVal)
                ->where('created_at', '>=', now()->subMinute())
                ->first();
        } catch (QueryException $e) {
            $this->ensureColumnsExist();
            try {
                $existing = DemoBooking::where('email', $validated['email'])
                    ->where('booking_date', $dateVal)
                    ->where('booking_time', $timeVal)
                    ->where('created_at', '>=', now()->subMinute())
                    ->first();
            } catch (\Throwable $ex) {
                $existing = null;
            }
        }

        if ($existing) {
            $booking = $existing;
        } else {
            $booking = DemoBooking::create([
                'full_name'      => $validated['full_name'],
                'email'          => $validated['email'],
                'phone'          => $validated['phone'],
                'institute_name' => $validated['institute_name'] ?? null,
                'student_count'  => $validated['student_count'] ?? null,
                'role'           => $validated['role'] ?? 'Prospect',
                'city'           => $validated['city'] ?? 'N/A',
                'state'          => $validated['state'] ?? 'N/A',
                'country'        => $validated['country'] ?? 'India',
                'booking_date'   => $dateVal,
                'booking_time'   => $timeVal,
                'timezone'       => $tzVal,
                'source'         => $source,
                'message'        => $validated['message'],
                'status'         => 'pending',
            ]);
        }

        $superAdminEmail = SettingsController::getSuperAdminEmail();

        // 1. Send Email to Super Admin
        $this->sendSafeEmail(new DemoBookingSuperAdminMail($booking), $superAdminEmail, 'superadmin notification email');

        // 2. Send Email to Client
        $this->sendSafeEmail(new DemoBookingClientMail($booking), $booking->email, 'client confirmation email');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your demo request has been submitted. Our product team will get in touch with you shortly.',
                'booking' => $booking,
            ]);
        }

        return redirect()->back()->with('success', 'Thank you! Your demo request has been submitted. Our product team will get in touch with you shortly.');
    }

    /**
     * Auto-ensure demo_bookings database table and columns exist dynamically.
     */
    private function ensureColumnsExist(): void
    {
        try {
            if (!Schema::hasTable('demo_bookings')) {
                Schema::create('demo_bookings', function ($table) {
                    $table->id();
                    $table->string('full_name');
                    $table->string('email');
                    $table->string('phone');
                    $table->string('institute_name')->nullable();
                    $table->string('student_count')->nullable();
                    $table->string('role')->nullable();
                    $table->string('city')->nullable();
                    $table->string('state')->nullable();
                    $table->string('country')->nullable();
                    $table->string('booking_date')->nullable();
                    $table->string('booking_time')->nullable();
                    $table->string('timezone')->nullable();
                    $table->string('source')->default('Website');
                    $table->text('message')->nullable();
                    $table->string('status')->default('pending');
                    $table->timestamps();
                });
            } else {
                Schema::table('demo_bookings', function ($table) {
                    if (!Schema::hasColumn('demo_bookings', 'booking_date')) {
                        $table->string('booking_date')->nullable();
                    }
                    if (!Schema::hasColumn('demo_bookings', 'booking_time')) {
                        $table->string('booking_time')->nullable();
                    }
                    if (!Schema::hasColumn('demo_bookings', 'timezone')) {
                        $table->string('timezone')->nullable();
                    }
                    if (!Schema::hasColumn('demo_bookings', 'source')) {
                        $table->string('source')->default('Website');
                    }
                });
            }
        } catch (\Throwable $e) {
            Log::warning('DemoBooking schema check warning: ' . $e->getMessage());
        }
    }

    /**
     * Safely attempt email dispatch across configured mailer, sendmail, and log drivers.
     */
    private function sendSafeEmail($mailable, string $recipientEmail, string $mailType): void
    {
        try {
            $smtpUser = config('mail.mailers.smtp.username');
            $smtpPass = config('mail.mailers.smtp.password');
            $defaultMailer = config('mail.default', 'smtp');

            // If SMTP is selected but missing username/password in .env, attempt sendmail or log driver directly
            if ($defaultMailer === 'smtp' && (empty($smtpUser) || empty($smtpPass))) {
                Log::info("Demo booking {$mailType}: SMTP credentials empty in .env. Attempting sendmail driver to {$recipientEmail}.");
                try {
                    Mail::mailer('sendmail')->to($recipientEmail)->send($mailable);
                    Log::info("Demo booking {$mailType} successfully dispatched via sendmail driver to {$recipientEmail}.");
                    return;
                } catch (\Throwable $e2) {
                    Mail::mailer('log')->to($recipientEmail)->send($mailable);
                    Log::info("Demo booking {$mailType} written to log driver for {$recipientEmail}.");
                    return;
                }
            }

            // Primary mail dispatch
            Mail::to($recipientEmail)->send($mailable);
            Log::info("Demo booking {$mailType} successfully dispatched to {$recipientEmail}.");
        } catch (\Throwable $e) {
            Log::warning("Demo booking {$mailType} primary dispatch failed: " . $e->getMessage() . ". Retrying via fallback...");
            try {
                Mail::mailer('sendmail')->to($recipientEmail)->send($mailable);
                Log::info("Demo booking {$mailType} successfully dispatched via fallback sendmail to {$recipientEmail}.");
            } catch (\Throwable $e2) {
                try {
                    Mail::mailer('log')->to($recipientEmail)->send($mailable);
                    Log::info("Demo booking {$mailType} logged to laravel.log for {$recipientEmail}.");
                } catch (\Throwable $e3) {
                    Log::error("Demo booking {$mailType} all mail fallbacks failed: " . $e3->getMessage());
                }
            }
        }
    }
}
