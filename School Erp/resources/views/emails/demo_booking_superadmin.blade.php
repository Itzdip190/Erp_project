<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Demo Booking Request — Super Admin</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #0947ca 0%, #031a61 100%); padding: 36px 40px; text-align: center; color: #ffffff; }
        .header h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 0; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
        .text { font-size: 15px; color: #475569; line-height: 1.7; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #f8fafc; border-radius: 10px; overflow: hidden; }
        .info-table th, .info-table td { padding: 12px 16px; font-size: 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .info-table th { background: #eff6ff; color: #1e3a8a; font-weight: 700; width: 38%; }
        .info-table td { color: #334155; }
        .message-box { background: #f1f5f9; border-left: 4px solid #0947ca; padding: 14px 18px; border-radius: 8px; font-style: italic; color: #334155; margin-top: 15px; font-size: 14px; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>🔔 New Demo Booking Request</h1>
            <p>EducoreERP Super Admin Notification</p>
        </div>

        <div class="body">
            <p class="greeting">Hello SuperAdmin,</p>
            <p class="text">
                A new client has requested a live product demo for <strong>EducoreERP</strong>. Below are the complete submission details:
            </p>

            <table class="info-table">
                <tr>
                    <th>Full Name</th>
                    <td><strong>{{ $booking->full_name }}</strong></td>
                </tr>
                <tr>
                    <th>Email Address</th>
                    <td><a href="mailto:{{ $booking->email }}" style="color:#0947ca;">{{ $booking->email }}</a></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td><a href="tel:{{ $booking->phone }}" style="color:#0947ca;">{{ $booking->phone }}</a></td>
                </tr>
                <tr>
                    <th>Institute Name</th>
                    <td>{{ $booking->institute_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ $booking->role }}</td>
                </tr>
                <tr>
                    <th>Student Count</th>
                    <td>{{ $booking->student_count ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Booking Date</th>
                    <td><strong>{{ $booking->booking_date ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <th>Booking Time</th>
                    <td><strong>{{ $booking->booking_time ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <th>Timezone</th>
                    <td>{{ $booking->timezone ?? 'India Standard Time' }}</td>
                </tr>
                <tr>
                    <th>Booking Source</th>
                    <td><span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:4px; font-weight:bold;">{{ $booking->source ?? 'Website' }}</span></td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td>{{ $booking->city }}, {{ $booking->state }}, {{ $booking->country }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $booking->created_at ? $booking->created_at->format('d M Y, h:i A') : date('d M Y, h:i A') }}</td>
                </tr>
            </table>

            @if($booking->message)
            <p style="font-weight:700; color:#0f172a; margin-bottom:6px;">Prospect Notes / Requirements:</p>
            <div class="message-box">
                "{{ $booking->message }}"
            </div>
            @endif

            <p class="text" style="margin-top:25px;">
                Log into the <a href="{{ url('/superadmin/demo-requests') }}" style="color:#0947ca; font-weight:bold;">Super Admin Panel</a> to view and update this demo request.
            </p>
        </div>

        <div class="footer">
            <p>Automated notification generated by EducoreERP System.</p>
        </div>
    </div>
</body>
</html>
