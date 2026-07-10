<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $number }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #004d5a;
            --primary-light: #eff6ff;
            --accent-color: #d97706;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: #f1f5f9;
        }

        .no-print-bar {
            background-color: #1e293b;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-print {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.2s;
        }
        .btn-print:hover {
            opacity: 0.9;
        }

        .receipt-page {
            max-width: 800px;
            margin: 10px auto;
            background: white;
            padding: 20px 30px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            box-sizing: border-box;
        }

        .copy-divider {
            border-top: 1.5px dashed #94a3b8;
            margin: 15px 0;
            position: relative;
        }
        .copy-divider::after {
            content: "\f0c4";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            top: -9px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            color: #64748b;
            font-size: 12px;
        }

        /* Invoice Structure */
        .school-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .school-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .school-logo {
            width: 38px;
            height: 38px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .school-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 15px;
            color: var(--primary-color);
            margin: 0;
        }

        .school-details {
            font-size: 10px;
            color: var(--text-muted);
            margin: 2px 0 0 0;
            line-height: 1.3;
        }

        .doc-title-area {
            text-align: right;
        }

        .doc-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 13px;
            color: var(--accent-color);
            text-transform: uppercase;
            margin: 0 0 2px 0;
        }

        .doc-ref {
            font-size: 10px;
            color: var(--text-muted);
            margin: 0;
        }

        .metadata-box {
            display: flex;
            justify-content: space-between;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 6px 12px;
            background-color: var(--primary-light);
            font-size: 11px;
            margin-bottom: 8px;
        }

        .metadata-section {
            line-height: 1.4;
        }

        .metadata-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .student-name {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary-color);
        }

        .particulars-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 8px;
            border: 1px solid var(--border-color);
        }

        .particulars-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 8px;
            text-align: left;
        }

        .particulars-table td {
            padding: 5px 8px;
            border-bottom: 1px solid var(--border-color);
        }

        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }

        .total-table {
            width: 220px;
            font-size: 11px;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 3px 0;
        }

        .total-row {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary-color);
            border-top: 1px solid var(--border-color);
        }

        .footer-note {
            font-size: 10px;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm 10mm;
            }
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .receipt-page {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
                width: 100%;
            }
            .school-header {
                padding-bottom: 4px !important;
                margin-bottom: 4px !important;
            }
            .school-logo {
                width: 32px !important;
                height: 32px !important;
                font-size: 15px !important;
            }
            .school-name {
                font-size: 13px !important;
            }
            .school-details {
                font-size: 9px !important;
            }
            .doc-title {
                font-size: 12px !important;
            }
            .metadata-box {
                padding: 4px 8px !important;
                margin-bottom: 6px !important;
                font-size: 10px !important;
            }
            .particulars-table {
                margin-bottom: 6px !important;
                font-size: 10px !important;
            }
            .particulars-table th {
                padding: 4px 6px !important;
            }
            .particulars-table td {
                padding: 4px 6px !important;
            }
            .total-table {
                font-size: 10px !important;
            }
            .total-table td {
                padding: 2px 0 !important;
            }
            .footer-note {
                padding-top: 4px !important;
                font-size: 9px !important;
            }
            .copy-divider {
                margin: 8px 0 !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar no-print">
        <span>Print Preview - {{ $title }}</span>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Slip
        </button>
    </div>

    <div class="receipt-page">
        @if(request('copy') !== 'student')
        <!-- Render copy 1: Office Copy -->
        @include('school.fees._slip_layout', ['copy_label' => 'Office Copy'])
        
        <div class="copy-divider"></div>
        @endif
        
        <!-- Render copy 2: Student Copy -->
        @include('school.fees._slip_layout', ['copy_label' => 'Student Copy'])
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
