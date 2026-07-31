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
        .header h1 { font-size: 24px; font-weight: 700; margin: 0 0 6px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 0; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
        .text { font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 20px; }
        .badge { display: inline-block; background: #dbeafe; color: #1e40af; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 700; margin-bottom: 20px; }
        .summary-card { background: #f8fafc; border-left: 4px solid #0947ca; border-radius: 8px; padding: 18px 20px; margin: 20px 0; }
        .summary-card p { margin: 6px 0; font-size: 14px; color: #334155; }
        .summary-card strong { color: #0f172a; }
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
                Thank you for requesting a demo with <strong>EducoreERP</strong>! We have successfully received your booking request.
            </p>

            <span class="badge">🚀 Demo Request Received</span>

            <div class="summary-card">
                <p><strong>Name:</strong> {{ $booking->full_name }}</p>
                <p><strong>Email:</strong> {{ $booking->email }}</p>
                <p><strong>Phone:</strong> {{ $booking->phone }}</p>
                @if($booking->institute_name)
                <p><strong>Institute:</strong> {{ $booking->institute_name }}</p>
                @endif
                <p><strong>Role:</strong> {{ $booking->role }}</p>
            </div>

            <p class="text">
                One of our product specialists will reach out to you shortly via phone or email to schedule your personalized live demo session.
            </p>

            <p class="text" style="color: #64748b; font-size: 14px;">
                If you have any urgent questions in the meantime, feel free to reply to this email or contact our support team.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} EducoreERP. All rights reserved.<br>Smart School & College Management System.</p>
        </div>
    </div>
</body>
</html>
