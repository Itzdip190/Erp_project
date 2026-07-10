<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Request Received</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #0252D9 0%, #00287A 100%); padding: 36px 40px; text-align: center; }
        .header-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; margin: 0; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #1e1b4b; margin-bottom: 16px; }
        .text { font-size: 15px; color: #4b5563; line-height: 1.7; margin-bottom: 20px; }
        .info-card { background: #f0f4ff; border-left: 4px solid #0252D9; border-radius: 10px; padding: 18px 20px; margin: 24px 0; }
        .info-card p { margin: 4px 0; font-size: 14px; color: #374151; }
        .info-card strong { color: #0252D9; }
        .status-badge { display: inline-block; background: #fef3c7; color: #92400e; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 700; margin-bottom: 24px; }
        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 28px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.6; }
        .footer a { color: #0252D9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header-icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><path fill="#fff" d="M12 2L2 7l10 5 10-5-10-5zm0 7.5L4.5 6 12 2.5 19.5 6 12 9.5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <h1>Request Received!</h1>
            <p>SchoolCloud ERP — School Registration</p>
        </div>

        <div class="body">
            <p class="greeting">Hello {{ $schoolRequest->name }},</p>
            <p class="text">
                Thank you for submitting your school registration request on <strong>SchoolCloud ERP</strong>.
                We have received your details and your request is currently under review.
            </p>

            <span class="status-badge">⏳ Pending Review</span>

            <div class="info-card">
                <p><strong>School Name:</strong> {{ $schoolRequest->name }}</p>
                <p><strong>Contact Email:</strong> {{ $schoolRequest->email }}</p>
                @if($schoolRequest->phone)
                <p><strong>Phone:</strong> {{ $schoolRequest->phone }}</p>
                @endif
                <p><strong>Submitted On:</strong> {{ $schoolRequest->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <p class="text">
                Our team will carefully review your request and one of our agents will reach out to you shortly.
                You will receive another email notification once a decision has been made on your application.
            </p>

            <p class="text">
                If you have any questions in the meantime, please feel free to contact us.
            </p>

            <hr class="divider">

            <p class="text" style="font-size: 14px; color: #6b7280;">
                <strong>What happens next?</strong><br>
                ① Our team reviews your request<br>
                ② An agent contacts you to discuss your needs<br>
                ③ Once approved, you receive your login credentials via email
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
