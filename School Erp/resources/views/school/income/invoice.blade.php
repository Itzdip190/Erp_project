<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $income->receipt_no ?: 'SI-'.$income->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --bg-light: #f8fafc;
            --border-color: #cbd5e1;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background: #fff;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            position: relative;
        }
        .invoice-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 24px;
        }
        .school-info h2 {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .school-info p {
            color: var(--text-muted);
            font-size: 12.5px;
            margin-bottom: 2px;
        }
        .receipt-title {
            text-align: right;
        }
        .receipt-title h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .receipt-badge {
            background: #ecfdf5;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
            text-transform: uppercase;
            border: 1px solid #d1fae5;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-bottom: 30px;
        }
        .meta-block h3 {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .meta-details {
            background: var(--bg-light);
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #e2e8f0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .meta-row:last-child {
            margin-bottom: 0;
        }
        .meta-label {
            color: var(--text-muted);
            font-weight: 500;
        }
        .meta-val {
            font-weight: 600;
            color: var(--text-dark);
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th {
            background: var(--bg-light);
            color: var(--text-dark);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            padding: 12px 16px;
            border-bottom: 2px solid var(--border-color);
            text-align: left;
            letter-spacing: 0.5px;
        }
        .invoice-table td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13.5px;
            color: var(--text-dark);
            vertical-align: top;
        }
        .invoice-table td.amount-col {
            text-align: right;
            font-weight: 700;
        }
        .amount-words {
            background: var(--bg-light);
            padding: 14px 18px;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 40px;
        }
        .amount-words span {
            font-weight: 700;
            color: var(--text-dark);
        }
        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
        }
        .stamp-box {
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            width: 150px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1.5px solid var(--text-dark);
            margin-bottom: 8px;
        }
        .signature-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
        }
        .print-btn-bar {
            max-width: 800px;
            margin: 20px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-print {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-weight: 700;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: var(--primary-dark);
        }
        .btn-close {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 8px 20px;
            font-weight: 700;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-close:hover {
            background: #475569;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .print-btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <div class="header-row" style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="school-logo-container">
                    @if(!empty($school->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->logo))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($school->logo) }}" alt="{{ $school->name }}" style="max-height: 80px; max-width: 180px; object-fit: contain; border-radius: 8px;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 12px; background: #ecfdf5; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 28px; border: 1.5px solid var(--border-color);">
                            <i class="fas fa-school"></i>
                        </div>
                    @endif
                </div>
                <div class="school-info">
                    <h2 style="font-size: 20px; font-weight: 800; color: var(--primary); text-transform: uppercase;">{{ $school->name }}</h2>
                    @if($school->address)
                        <p style="color: var(--text-muted); font-size: 12.5px; margin-top: 4px;">{{ $school->address }}</p>
                    @endif
                    <p style="color: var(--text-muted); font-size: 12.5px; margin-top: 2px;">
                        Phone: {{ $school->phone ?: 'N/A' }} 
                        @if($school->code)
                            | School Code: {{ $school->code }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="receipt-title" style="text-align: right;">
                <h1>RECEIPT</h1>
                <span class="receipt-badge">Official Document</span>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-block">
                <h3>Receipt Information</h3>
                <div class="meta-details">
                    <div class="meta-row">
                        <span class="meta-label">Receipt No:</span>
                        <span class="meta-val">{{ $income->receipt_no ?: 'REC-'.str_pad($income->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Date:</span>
                        <span class="meta-val">{{ $income->income_date ? $income->income_date->format('d M Y') : now()->format('d M Y') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Category:</span>
                        <span class="meta-val">{{ $income->category_label }}</span>
                    </div>
                </div>
            </div>
            
            <div class="meta-block">
                <h3>Payer Details</h3>
                <div class="meta-details">
                    <div class="meta-row">
                        <span class="meta-label">Received From:</span>
                        <span class="meta-val">{{ $income->received_from ?: '—' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Payment Mode:</span>
                        <span class="meta-val">{{ ucwords(str_replace('_', ' ', $income->payment_mode)) }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Reference No:</span>
                        <span class="meta-val">{{ $income->reference_no ?: '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Grid -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Description</th>
                    <th style="text-align: right; width: 30%;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">{{ $income->title }}</div>
                        <div style="color: var(--text-muted); font-size: 12.5px;">{{ $income->description ?: 'Payment received towards account head: ' . $income->category_label }}</div>
                    </td>
                    <td class="amount-col">₹{{ number_format($income->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Amount In Words -->
        <div class="amount-words">
            Amount in Words: <span>{{ $amountInWords }}</span>
        </div>

        <!-- Signatures & Stamp -->
        <div class="footer-row">
            <div class="stamp-box">
                Official Stamp
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <span class="signature-title">Authorized Signatory</span>
            </div>
        </div>
    </div>

    <!-- Print Action Bar -->
    <div class="print-btn-bar">
        <button class="btn-close" onclick="window.close()">Close</button>
        <button class="btn-print" onclick="window.print()">
            Print Receipt <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-printer"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
        </button>
    </div>

    <script>
        // Check if print query parameter is set to auto-print
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            window.addEventListener('load', () => {
                window.print();
            });
        }
    </script>
</body>
</html>
