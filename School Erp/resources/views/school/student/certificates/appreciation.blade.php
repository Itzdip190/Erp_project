<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Appreciation - {{ $student->admission_number }}</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            color: #1E293B;
            background: #FFFFFF;
            padding: 30px;
        }
        .border-outer {
            border: 15px solid #1E3A8A;
            padding: 5px;
            height: 90%;
        }
        .border-inner {
            border: 3px solid #F59E0B;
            padding: 40px;
            text-align: center;
            height: 80%;
        }
        .school-header {
            font-size: 28px;
            font-weight: bold;
            color: #1E3A8A;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .school-sub {
            font-size: 13px;
            color: #6B7280;
            font-style: italic;
            margin-bottom: 25px;
        }
        .certificate-title {
            font-size: 24px;
            font-weight: bold;
            color: #F59E0B;
            margin-bottom: 35px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            font-size: 16px;
            line-height: 1.8;
            margin: 0 auto;
            max-width: 680px;
            text-align: justify;
            margin-bottom: 50px;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .footer-table td {
            font-size: 14px;
            vertical-align: bottom;
        }
    </style>
</head>
<body>
    <div class="border-outer">
        <div class="border-inner">
            <div class="school-header">Yash International School</div>
            <div class="school-sub">Affiliated to National Council, Education Block, Delhi</div>
            
            <div class="certificate-title">Certificate of Appreciation</div>
            
            <div class="content">
                This Certificate of Appreciation is proudly awarded to <strong>{{ $student->full_name }}</strong>, bearing Admission Number <strong>{{ $student->admission_number }}</strong>, in recognition of their outstanding contribution, service, and exemplary attitude during the academic term. Their dedication and helpful behavior have set a positive example for peers and enriched our school community.
            </div>
            
            <table class="footer-table">
                <tr>
                    <td style="text-align: left; width: 40%;">
                        <strong>Date:</strong> {{ $date }}
                    </td>
                    <td style="text-align: right; width: 60%;">
                        <div style="width: 180px; border-bottom: 1.5px solid #1E293B; margin-left: auto; margin-bottom: 5px;"></div>
                        <strong>Principal Signature</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
