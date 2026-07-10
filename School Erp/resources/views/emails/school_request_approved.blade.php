<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Approved</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #059669 0%, #047857 100%); padding: 36px 40px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 28px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0 0 6px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 14px; margin: 0; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; font-weight: 700; color: #1e1b4b; margin-bottom: 16px; }
        .text { font-size: 15px; color: #4b5563; line-height: 1.7; margin-bottom: 20px; }
        .status-badge { display: inline-block; background: #d1fae5; color: #065f46; border-radius: 20px; padding: 6px 16px; font-size: 13px; font-weight: 700; margin-bottom: 24px; }
        .credentials-card { background: #1e1b4b; border-radius: 12px; padding: 24px 28px; margin: 24px 0; }
        .credentials-card h3 { color: #a5b4fc; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 16px; }
        .cred-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .cred-label { color: #94a3b8; font-size: 13px; }
        .cred-value { color: #f1f5f9; font-size: 14px; font-weight: 700; font-family: monospace; background: rgba(255,255,255,0.08); padding: 4px 12px; border-radius: 6px; }
        .warning-box { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 10px; padding: 14px 18px; margin: 16px 0; font-size: 13px; color: #92400e; }
        .btn-login { display: block; text-align: center; background: linear-gradient(135deg, #0252D9, #00287A); color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-size: 15px; font-weight: 700; margin: 28px 0; }
        .divider { border: none; border-top: 1px solid #f1f5f9; margin: 28px 0; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.6; }
        .footer a { color: #0252D9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header-icon">✅</div>
            <h1>Congratulations! You're Approved</h1>
            <p>SchoolCloud ERP — School Registration</p>
        </div>

        <div class="body">
            <p class="greeting">Hello {{ $schoolRequest->name }},</p>
            <p class="text">
                Great news! Your school registration request on <strong>SchoolCloud ERP</strong> has been
                <strong>approved</strong>. Your school account has been set up and is ready to use.
            </p>

            <span class="status-badge">✅ Approved</span>

            <div class="credentials-card">
                <h3>🔐 Your Login Credentials</h3>
                <div class="cred-row">
                    <span class="cred-label">Login Email</span>
                    <span class="cred-value">{{ $schoolRequest->email }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-value">{{ $generatedPassword }}</span>
                </div>
            </div>

            <div class="warning-box">
                ⚠️ <strong>Important:</strong> Please log in and change your password immediately after your first login for security purposes.
            </div>

            <a href="{{ url('/login') }}" class="btn-login">
                🚀 Login to Your School Dashboard
            </a>

            <hr class="divider">

            <p class="text" style="font-size: 14px; color: #6b7280;">
                <strong>Getting Started:</strong><br>
                ① Log in using the credentials above<br>
                ② Change your password from the Profile settings<br>
                ③ Set up your school's academic sessions, classes, and staff<br>
                ④ Start managing your school efficiently!
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
