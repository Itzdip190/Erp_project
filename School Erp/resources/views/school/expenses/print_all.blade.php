<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Expense Invoices</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
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
            padding: 0;
            margin: 0;
        }
        
        .print-page-container {
            width: 100%;
        }
        
        .invoice-card {
            border-bottom: 2px dashed #94a3b8;
            padding: 30px 40px;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .invoice-card:last-child {
            border-bottom: none;
        }
        
        /* Heights and page breaks based on perPage parameter */
        @php
            $cardHeight = '100vh';
            $pageBreakInterval = 1;
            if ($perPage === 1) {
                $cardHeight = '100vh';
                $pageBreakInterval = 1;
            } elseif ($perPage === 2) {
                $cardHeight = '50vh';
                $pageBreakInterval = 2;
            } elseif ($perPage === 3) {
                $cardHeight = '33.3vh';
                $pageBreakInterval = 3;
            } elseif ($perPage === 4) {
                $cardHeight = '25vh';
                $pageBreakInterval = 4;
            }
        @endphp
        
        .invoice-card {
            height: {{ $cardHeight }};
        }
        
        @media print {
            .page-break {
                page-break-after: always;
                height: 0;
                border: none;
            }
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #f1f5f9;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .school-info h2 {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
        }
        .school-info p {
            color: var(--text-muted);
            font-size: 11px;
            margin-top: 2px;
        }
        .receipt-title {
            text-align: right;
        }
        .receipt-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }
        .receipt-badge {
            background: #eff6ff;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 10px;
            border: 1px solid #bfdbfe;
            text-transform: uppercase;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 10px;
        }
        .meta-details {
            background: var(--bg-light);
            border-radius: 6px;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        .meta-label {
            color: var(--text-muted);
        }
        .meta-val {
            font-weight: 600;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .invoice-table th {
            background: var(--bg-light);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 6px 12px;
            border-bottom: 1.5px solid var(--border-color);
            text-align: left;
        }
        .invoice-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11.5px;
        }
        .amount-words {
            background: var(--bg-light);
            padding: 8px 12px;
            border-radius: 6px;
            border-left: 3px solid var(--primary);
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 15px;
        }
        .amount-words span {
            font-weight: 700;
            color: var(--text-dark);
        }
        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 5px;
        }
        .stamp-box {
            border: 1.5px dashed #cbd5e1;
            border-radius: 6px;
            width: 120px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
        }
        .signature-box {
            text-align: center;
            width: 150px;
        }
        .signature-line {
            border-top: 1px solid var(--text-dark);
            margin-bottom: 4px;
        }
        .signature-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
        }

        /* Smaller scale adjustments for 3 or 4 per page */
        @if ($perPage >= 3)
            .invoice-card {
                padding: 15px 30px;
            }
            .header-row { margin-bottom: 5px; padding-bottom: 5px; }
            .meta-grid { margin-bottom: 5px; gap: 10px; }
            .invoice-table { margin-bottom: 5px; }
            .amount-words { margin-bottom: 5px; padding: 6px 10px; }
            .footer-row { margin-top: 0; }
        @endif
        
        .print-btn-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 10000;
        }
        .btn-print {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        .btn-close {
            background: #64748b;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        
        @media print {
            .print-btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-page-container">
        @foreach($expenses as $index => $expense)
            <div class="invoice-card">
                <!-- Header -->
                <div class="header-row">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        @if(!empty($school->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->logo))
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($school->logo) }}" alt="{{ $school->name }}" style="max-height: 45px; max-width: 120px; object-fit: contain; border-radius: 4px;">
                        @else
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: #eff6ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid var(--border-color);">
                                <i class="fas fa-school"></i>
                            </div>
                        @endif
                        <div class="school-info">
                            <h2>{{ $school->name }}</h2>
                            <p>{{ $school->address ?? 'New Delhi' }} | Phone: {{ $school->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="receipt-title">
                        <h1>DISBURSEMENT</h1>
                        <span class="receipt-badge">Expense Voucher</span>
                    </div>
                </div>

                <!-- Meta Grid -->
                <div class="meta-grid">
                    <div class="meta-details">
                        <div class="meta-row">
                            <span class="meta-label">Voucher / Ref No:</span>
                            <span class="meta-val">{{ $expense->reference_no ?: 'EXP-'.str_pad($expense->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Date:</span>
                            <span class="meta-val">{{ $expense->expense_date ? $expense->expense_date->format('d M Y') : now()->format('d M Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="meta-details">
                        <div class="meta-row">
                            <span class="meta-label">Paid To (Payee):</span>
                            <span class="meta-val">{{ $expense->paid_to ?: '—' }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Payment Mode:</span>
                            <span class="meta-val">{{ ucwords(str_replace('_', ' ', $expense->payment_mode)) }}</span>
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
                                <div style="font-weight: 700; font-size: 11px; margin-bottom: 2px;">{{ $expense->title }}</div>
                                <div style="color: var(--text-muted); font-size: 10px;">{{ $expense->description ?: 'Expense disbursement' }}</div>
                            </td>
                            <td style="text-align: right; font-weight: 700; font-size: 11px;">₹{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Amount In Words -->
                <div class="amount-words">
                    Amount in Words: <span>{{ $expense->amount_in_words }}</span>
                </div>

                <!-- Signatures & Stamp -->
                <div class="footer-row">
                    <div class="stamp-box">
                        Receiver Sign
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <span class="signature-title">Authorized Signatory</span>
                    </div>
                </div>
            </div>
            
            {{-- Render page break after intervals --}}
            @if(($index + 1) % $pageBreakInterval === 0 && ($index + 1) < count($expenses))
                <div class="page-break"></div>
            @endif
        @endforeach
    </div>

    <!-- Print Action Bar -->
    <div class="print-btn-bar">
        <button class="btn-close" onclick="window.close()">Close</button>
        <button class="btn-print" onclick="window.print()">Print All Invoices</button>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => {
                    window.print();
                }, 500);
            });
        }
    </script>
</body>
</html>
