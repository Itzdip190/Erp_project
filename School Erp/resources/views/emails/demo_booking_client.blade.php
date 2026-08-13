<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Booking Confirmation — EducoreERP</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #0947ca 0%, #031a61 100%); padding: 36px 40px; text-align: center; color: #ffffff; }
        .header h1 { font-size: 26px; font-weight: 800; margin: 0 0 6px; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 0; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
        .text { font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 20px; }
        .badge { display: inline-block; background: #d1fae5; color: #065f46; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 700; margin-bottom: 20px; }
        .summary-card { background: #f8fafc; border-left: 4px solid #0947ca; border-radius: 8px; padding: 18px 20px; margin: 20px 0; }
        .summary-card p { margin: 8px 0; font-size: 14px; color: #334155; }
        .summary-card strong { color: #0f172a; min-width: 120px; display: inline-block; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>EducoreERP</h1>
            <p>Empowering Education. Enriching Future.</p>
        </div>

        <div class="body">
            <p class="greeting">Hello {{ $booking->full_name }},</p>
            
            <p class="text">
                Thank you for scheduling a live product walkthrough with <strong>EducoreERP</strong>! Your demo request has been successfully registered.
            </p>

            <span class="badge">✔ Demo Request Confirmed</span>

            <div class="summary-card">
                <p><strong>Scheduled Date:</strong> {{ $booking->booking_date ?? date('Y-m-d') }}</p>
                <p><strong>Scheduled Time:</strong> {{ $booking->booking_time ?? '10:15 AM' }}</p>
                <p><strong>Timezone:</strong> {{ $booking->timezone ?? 'India Standard Time' }}</p>
                @if($booking->institute_name)
                <p><strong>Institution:</strong> {{ $booking->institute_name }}</p>
                @endif
                <p><strong>Contact Phone:</strong> {{ $booking->phone }}</p>
                <p><strong>Official Email:</strong> {{ $booking->email }}</p>
                <p><strong>Role:</strong> {{ $booking->role }}</p>
                <p><strong>Support Email:</strong> <a href="mailto:{{ config('mail.from.address', 'businesshead@bloombyte.in') }}" style="color:#0947ca;">{{ config('mail.from.address', 'businesshead@bloombyte.in') }}</a></p>
            </div>

            <p class="text">
                Our Senior Product Specialist will join the session at the scheduled time or connect via call to guide you through the system modules.
            </p>

            <p class="text" style="color: #64748b; font-size: 14px;">
                If you need to reschedule or have any questions beforehand, please feel free to reply directly to this email or reach us at <strong>+91-9451805575</strong>.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} EducoreERP. All rights reserved.<br>Smart School & College Management System.</p>
        </div>
    </div>
</body>
</html>
