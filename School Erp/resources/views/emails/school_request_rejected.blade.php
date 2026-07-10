<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Update</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 36px 40px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 28px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 0; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #1e1b4b; margin-bottom: 16px; }
        .text { font-size: 15px; color: #4b5563; line-height: 1.7; margin-bottom: 20px; }
        .status-badge { display: inline-block; background: #fee2e2; color: #991b1b; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 700; margin-bottom: 24px; }
        .reason-card { background: #fff7ed; border-left: 4px solid #f97316; border-radius: 10px; padding: 18px 20px; margin: 20px 0; }
        .reason-card p { margin: 0; font-size: 14px; color: #374151; line-height: 1.6; }
        .reason-card strong { color: #c2410c; display: block; margin-bottom: 6px; }
        .info-card { background: #f0f4ff; border-left: 4px solid #0252D9; border-radius: 10px; padding: 14px 18px; margin: 20px 0; font-size: 14px; color: #374151; }
        .btn-retry { display: block; text-align: center; background: linear-gradient(135deg, #0252D9, #00287A); color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-size: 15px; font-weight: 700; margin: 28px 0; }
        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 28px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.6; }
        .footer a { color: #0252D9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header-icon">❌</div>
            <h1>Registration Not Approved</h1>
            <p>SchoolCloud ERP — School Registration</p>
        </div>

        <div class="body">
            <p class="greeting">Hello {{ $schoolRequest->name }},</p>
            <p class="text">
                Thank you for your interest in <strong>SchoolCloud ERP</strong>. After reviewing your school
                registration request, we regret to inform you that we are unable to approve your application at this time.
            </p>

            <span class="status-badge">❌ Not Approved</span>

            @if($rejectedReason)
            <div class="reason-card">
                <p><strong>Reason for Rejection:</strong> {{ $rejectedReason }}</p>
            </div>
            @else
            <div class="reason-card">
                <p><strong>Reason:</strong> No specific reason was provided. Please contact our support team for more details.</p>
            </div>
            @endif

            <div class="info-card">
                <strong>📋 Request Details</strong><br>
                School: {{ $schoolRequest->name }}<br>
                Email: {{ $schoolRequest->email }}<br>
                Submitted: {{ $schoolRequest->created_at->format('d M Y') }}
            </div>

            <p class="text">
                If you believe this decision was made in error, or if you would like to address the reason
                mentioned above and re-apply, please feel free to submit a new request or contact our support team.
            </p>

            <a href="{{ url('/school/signup') }}" class="btn-retry">
                Submit a New Request
            </a>

            <hr class="divider">

            <p class="text" style="font-size: 13px; color: #9ca3af;">
                We appreciate your interest and hope to work with you in the future.
                If you have any questions, please reach out to our support team.
            </p>
        </div>

        <div class="footer">
            <p>
                This is an automated email from <a href="#">SchoolCloud ERP</a>.<br>
                Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
