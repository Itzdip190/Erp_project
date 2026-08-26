<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $maintTitle ?? 'Under Scheduled Maintenance' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .maint-card {
            background: #ffffff;
            border-radius: 24px;
            max-width: 420px;
            width: 100%;
            padding: 36px 28px;
            text-align: center;
            box-shadow: 0 20px 40px -10px rgba(0, 34, 102, 0.12), 0 0 0 1px #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        .maint-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        }
        .maint-icon-circle {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: #fef3c7;
            color: #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px auto;
            box-shadow: 0 0 35px rgba(217, 119, 6, 0.25);
            animation: pulseGlow 2.5s infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); box-shadow: 0 0 20px rgba(217, 119, 6, 0.2); }
            50% { transform: scale(1.05); box-shadow: 0 0 35px rgba(217, 119, 6, 0.35); }
        }
        .maint-badge {
            font-size: 11px;
            font-weight: 800;
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 14px;
        }
        .maint-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.4px;
            line-height: 1.3;
            margin-bottom: 12px;
        }
        .maint-desc {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .maint-status-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 12.5px;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .maint-btn {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(0, 34, 102, 0.25);
            transition: all 0.2s ease;
        }
        .maint-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 34, 102, 0.35);
            color: #ffffff;
        }
        .maint-footer {
            margin-top: 20px;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="maint-card">
        <div class="maint-icon-circle">
            <i class="fas fa-screwdriver-wrench"></i>
        </div>

        <span class="maint-badge">
            <i class="fas fa-circle-exclamation me-1"></i> Maintenance Active
        </span>

        <h1 class="maint-title">{{ $maintTitle ?? 'Under Scheduled Maintenance' }}</h1>

        <p class="maint-desc">
            {{ $maintMsg ?? 'We are performing regular server optimizations. The mobile app will be back online shortly.' }}
        </p>

        <div class="maint-status-box">
            <span><i class="fas fa-satellite-dish me-2 text-primary"></i> App Service Status</span>
            <strong style="color:#d97706;"><i class="fas fa-circle text-warning me-1" style="font-size:8px;"></i> Paused</strong>
        </div>

        <button type="button" class="maint-btn" onclick="window.location.reload()">
            <i class="fas fa-arrows-rotate me-2"></i> Check Connection Again
        </button>

        <div class="maint-footer">
            <i class="fas fa-shield-halved me-1"></i> School Cloud Security & System Maintenance
        </div>
    </div>
</body>
</html>
