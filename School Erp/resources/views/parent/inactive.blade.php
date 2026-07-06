<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Inactive — SchoolCloud ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --navy: #1a1f3c;
            --navy2: #12172e;
            --gold: #f59e0b;
            --gold-bg: rgba(245, 158, 11, 0.15);
            --red: #ef4444;
            --red-bg: rgba(239, 68, 68, 0.12);
            --page: #f8f7f4;
            --white: #fff;
            --t1: #111827;
            --t2: #6b7280;
            --border: #e5e7eb;
            --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--page);
            color: var(--t1);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 480px;
            width: 100%;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }
        .icon-box {
            font-size: 64px;
            color: var(--red);
            background: var(--red-bg);
            width: 120px;
            height: 120px;
            line-height: 120px;
            border-radius: 50%;
            margin: 0 auto 30px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 24px;
            color: var(--navy);
            margin-bottom: 12px;
        }
        p {
            font-size: 15px;
            color: var(--t2);
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .student-details {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 30px;
            text-align: left;
        }
        .student-details div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13.5px;
        }
        .student-details div:last-child {
            margin-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: var(--t2);
        }
        .val {
            font-weight: 700;
            color: var(--navy);
        }
        .btn {
            display: block;
            width: 100%;
            padding: 12px 24px;
            background: var(--navy);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
            margin-bottom: 12px;
            text-align: center;
        }
        .btn:hover {
            background: var(--navy2);
        }
        .btn-outline {
            display: block;
            width: 100%;
            padding: 12px 24px;
            background: transparent;
            color: var(--navy);
            border: 2px solid var(--navy);
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-outline:hover {
            background: rgba(26, 31, 60, 0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-box">
            <i class="fas fa-user-slash"></i>
        </div>
        <h1>Account Inactive</h1>
        <p>Your child's student account is currently marked as <strong>Inactive</strong>. You will not receive any school notifications, assignments, report cards, fee alerts, or transport tracking updates for this student.</p>
        
        <div class="student-details">
            <div>
                <span class="label">Student Name:</span>
                <span class="val">{{ $student->full_name }}</span>
            </div>
            <div>
                <span class="label">Admission ID:</span>
                <span class="val">{{ $student->admission_number }}</span>
            </div>
            <div>
                <span class="label">Status:</span>
                <span class="val" style="color: var(--red);">Inactive</span>
            </div>
        </div>

        <a href="{{ route('logout') }}" class="btn">Logout</a>
        <p style="font-size: 12px; color: var(--t2); margin-top: 15px; margin-bottom: 0;">If you believe this is an error, please contact the school administration.</p>
    </div>
</body>
</html>
