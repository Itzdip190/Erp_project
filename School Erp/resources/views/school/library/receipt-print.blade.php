<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Issue Voucher - {{ $txn->transaction_code }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 20px;
        }

        /* Top Action Bar (Hidden in Print) */
        .print-control-bar {
            max-width: 850px;
            margin: 0 auto 16px auto;
            background: #002266;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0, 34, 102, 0.25);
        }

        .btn-print-action {
            background: linear-gradient(135deg, #0038b8 0%, #1d4ed8 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-print-action:hover {
            background: linear-gradient(135deg, #002b8f 0%, #1e40af 100%);
        }

        .btn-close-action {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-close-action:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Print Container */
        .twin-sheet-wrapper {
            max-width: 850px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .voucher-card {
            background: #ffffff;
            border: 2px solid #002266;
            border-radius: 10px;
            padding: 16px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .voucher-type-strip {
            background: #002266;
            color: #ffffff;
            padding: 5px 14px;
            border-radius: 7px 7px 0 0;
            margin: -16px -20px 14px -20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .voucher-type-student {
            background: #0369a1 !important;
        }

        /* School Header */
        .voucher-school-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            border-bottom: 1.5px dashed #cbd5e1;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .voucher-logo-box {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .voucher-school-logo {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
        }

        .voucher-logo-fallback {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            background: #002266;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .voucher-school-info {
            flex: 1 1 auto;
            text-align: center;
        }

        .voucher-school-name {
            font-size: 18px;
            font-weight: 900;
            color: #002266;
            margin-bottom: 2px;
            letter-spacing: -0.3px;
            text-transform: uppercase;
        }

        .voucher-school-sub {
            font-size: 11px;
            color: #475569;
            margin-bottom: 5px;
            line-height: 1.25;
        }

        .voucher-title-pill {
            display: inline-block;
            background: #f0f5ff;
            color: #0038b8;
            border: 1px solid #bfdbfe;
            font-size: 10.5px;
            font-weight: 800;
            padding: 2px 12px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .voucher-meta-box {
            text-align: right;
            flex-shrink: 0;
        }

        .voucher-date-item {
            font-size: 11.5px;
            color: #334155;
            margin-bottom: 4px;
        }

        .voucher-barcode-box {
            font-family: 'Courier New', monospace;
            background: #f8fafc;
            padding: 4px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            text-align: center;
        }

        .voucher-barcode-box .barcode-lines {
            font-size: 16px;
            letter-spacing: -1px;
            line-height: 1;
            font-weight: bold;
        }

        /* 2-Column Info Grid */
        .voucher-content-grid {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .voucher-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .voucher-panel-title {
            font-size: 10.5px;
            font-weight: 800;
            color: #002266;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .borrower-photo-frame {
            width: 65px;
            height: 75px;
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .borrower-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .borrower-photo-fallback {
            font-size: 28px;
            color: #94a3b8;
        }

        .voucher-field-row {
            font-size: 12px;
            margin-bottom: 3.5px;
            line-height: 1.3;
            display: flex;
        }

        .voucher-field-lbl {
            color: #64748b;
            font-weight: 600;
            width: 95px;
            flex-shrink: 0;
            font-size: 11px;
        }

        .voucher-field-val {
            color: #0f172a;
            font-weight: 700;
            flex: 1 1 auto;
        }

        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            font-size: 10.5px;
            font-weight: 800;
            border-radius: 4px;
        }

        .badge-dark {
            background: #0f172a;
            color: #ffffff;
        }

        .badge-code {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        /* Schedule & Loan Bar */
        .voucher-schedule-bar {
            background: #f0f7ff;
            border: 1.5px solid #bfdbfe;
            border-radius: 8px;
            padding: 8px 12px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 10px;
            text-align: center;
        }

        .sched-item {
            display: flex;
            flex-direction: column;
        }

        .sched-lbl {
            font-size: 9.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .sched-val {
            font-size: 12.5px;
            font-weight: 800;
            color: #0f172a;
        }

        .sched-val-due {
            color: #dc2626 !important;
            font-size: 13.5px;
        }

        /* Footer & Signatures */
        .voucher-footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }

        .voucher-terms {
            font-size: 9.5px;
            color: #64748b;
            max-width: 60%;
            line-height: 1.3;
        }

        .voucher-signatures-group {
            display: flex;
            gap: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .voucher-sig-box {
            width: 130px;
        }

        .voucher-sig-line {
            border-bottom: 1px solid #0f172a;
            height: 24px;
            margin-bottom: 3px;
        }

        .voucher-sig-lbl {
            font-size: 9.5px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
        }

        /* Perforated Divider */
        .perforated-cut-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 4px 0;
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            user-select: none;
        }

        .cut-line-rule {
            flex: 1;
            border-bottom: 2px dashed #94a3b8;
        }

        /* Print Media Stylesheet */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .print-control-bar {
                display: none !important;
            }

            .twin-sheet-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                gap: 12px !important;
            }

            .voucher-card {
                box-shadow: none !important;
                border: 1.5px solid #000000 !important;
                page-break-inside: avoid !important;
            }

            .voucher-type-strip {
                background: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .voucher-schedule-bar {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Hidden during Print) -->
    <div class="print-control-bar">
        <div style="font-weight: 800; font-size: 14px;">
            <i class="fas fa-receipt text-warning me-2"></i> Library Book Issue Twin-Voucher (Double Copy)
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="btn-print-action" onclick="window.print()">
                <i class="fas fa-print"></i> PRINT VOUCHER (A4)
            </button>
            <button type="button" class="btn-close-action" onclick="window.close()">
                <i class="fas fa-times"></i> Close Tab
            </button>
        </div>
    </div>

    <!-- Twin Sheet Container -->
    <div class="twin-sheet-wrapper">
        
        @php
            $copies = [
                ['title' => 'LIBRARY / COUNTER COPY (Office Record)', 'is_student' => false, 'icon' => 'fa-building-columns'],
                ['title' => 'STUDENT / BORROWER COPY (Keep with Book)', 'is_student' => true, 'icon' => 'fa-book-bookmark']
            ];
        @endphp

        @foreach($copies as $copy)
        <div class="voucher-card">
            <!-- Top Strip -->
            <div class="voucher-type-strip {{ $copy['is_student'] ? 'voucher-type-student' : '' }}">
                <span><i class="fas {{ $copy['icon'] }} me-1"></i> {{ $copy['title'] }}</span>
                <span>TXN NO: <strong>{{ $txn->transaction_code }}</strong></span>
            </div>

            <!-- School Header -->
            <div class="voucher-school-header">
                <div class="voucher-logo-box">
                    @if($schoolLogo)
                        <img src="{{ $schoolLogo }}" class="voucher-school-logo" alt="School Logo">
                    @else
                        <div class="voucher-logo-fallback"><i class="fas fa-school"></i></div>
                    @endif
                </div>

                <div class="voucher-school-info">
                    <h4 class="voucher-school-name">{{ $school->name ?? 'Educore International School' }}</h4>
                    <div class="voucher-school-sub">
                        {{ $school->address ?? 'School Campus, Main Road' }}
                        @if(!empty($school->phone)) • Tel: {{ $school->phone }} @endif
                        @if(!empty($school->email)) • {{ $school->email }} @endif
                    </div>
                    <div class="voucher-title-pill">LIBRARY BOOK ISSUE VOUCHER</div>
                </div>

                <div class="voucher-meta-box">
                    <div class="voucher-date-item"><strong>Date:</strong> {{ $txn->issue_date ? \Carbon\Carbon::parse($txn->issue_date)->format('d M Y') : date('d M Y') }}</div>
                    <div class="voucher-barcode-box">
                        <div class="barcode-lines">||| | |||| | || ||| | |||</div>
                        <div style="font-size: 9.5px; font-weight: bold;">{{ $txn->transaction_code }}</div>
                    </div>
                </div>
            </div>

            <!-- Main Info Grid (Borrower + Book) -->
            <div class="voucher-content-grid">
                <!-- Left: Borrower Panel -->
                <div class="voucher-panel">
                    <div class="voucher-panel-title">
                        <span><i class="fas fa-user-graduate me-1" style="color: #0038b8;"></i> Borrower Details ({{ ucfirst($txn->member_type) }})</span>
                        <span class="badge-pill badge-code">{{ $borrowerCode }}</span>
                    </div>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div class="borrower-photo-frame">
                            @if($borrowerPhoto && !str_contains($borrowerPhoto, 'avatar-student.png'))
                                <img src="{{ $borrowerPhoto }}" alt="Borrower Photo">
                            @else
                                <div class="borrower-photo-fallback"><i class="fas fa-user"></i></div>
                            @endif
                        </div>

                        <div style="flex: 1 1 auto; min-width: 0;">
                            <div class="voucher-field-row">
                                <span class="voucher-field-lbl">Full Name:</span>
                                <span class="voucher-field-val">{{ $borrowerName }}</span>
                            </div>
                            <div class="voucher-field-row">
                                <span class="voucher-field-lbl">{{ $txn->member_type === 'staff' ? 'Designation:' : 'Class & Sec:' }}</span>
                                <span class="voucher-field-val">{{ $borrowerClassDept }}</span>
                            </div>
                            @if($borrower && !empty($borrower->roll_number))
                            <div class="voucher-field-row">
                                <span class="voucher-field-lbl">Roll Number:</span>
                                <span class="voucher-field-val">{{ $borrower->roll_number }}</span>
                            </div>
                            @endif
                            <div class="voucher-field-row">
                                <span class="voucher-field-lbl">Contact No:</span>
                                <span class="voucher-field-val">{{ $borrower->phone ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Book Panel -->
                <div class="voucher-panel">
                    <div class="voucher-panel-title">
                        <span><i class="fas fa-book me-1" style="color: #0038b8;"></i> Book Particulars</span>
                        <span class="badge-pill badge-dark">{{ $txn->book?->accession_no ?? ('ACC-' . str_pad($txn->book_id, 5, '0', STR_PAD_LEFT)) }}</span>
                    </div>

                    <div>
                        <div class="voucher-field-row">
                            <span class="voucher-field-lbl">Book Title:</span>
                            <span class="voucher-field-val" style="color: #0038b8;">{{ $txn->book?->title ?? 'Untitled Book' }}</span>
                        </div>
                        <div class="voucher-field-row">
                            <span class="voucher-field-lbl">Book Code:</span>
                            <span class="voucher-field-val font-monospace">BK-{{ str_pad($txn->book_id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="voucher-field-row">
                            <span class="voucher-field-lbl">Author / Pub:</span>
                            <span class="voucher-field-val">{{ $txn->book?->author ?? '—' }} @if($txn->book?->publisher) / {{ $txn->book->publisher }} @endif</span>
                        </div>
                        <div class="voucher-field-row">
                            <span class="voucher-field-lbl">Category/Rack:</span>
                            <span class="voucher-field-val">{{ $txn->book?->section?->name ?? 'General' }} (Rack: {{ $txn->book?->rack_location ?? 'Main' }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule & Loan Bar -->
            <div class="voucher-schedule-bar">
                <div class="sched-item">
                    <span class="sched-lbl">Issue Date</span>
                    <span class="sched-val" style="color: #16a34a;"><i class="fas fa-calendar-check me-1"></i> {{ $txn->issue_date ? \Carbon\Carbon::parse($txn->issue_date)->format('d M Y') : date('d M Y') }}</span>
                </div>
                <div class="sched-item">
                    <span class="sched-lbl">Return Due Date</span>
                    <span class="sched-val sched-val-due"><i class="fas fa-calendar-xmark me-1"></i> {{ $txn->due_date ? \Carbon\Carbon::parse($txn->due_date)->format('d M Y') : date('d M Y', strtotime('+14 days')) }}</span>
                </div>
                <div class="sched-item">
                    <span class="sched-lbl">Loan Limit</span>
                    <span class="sched-val">{{ (int)($rule->borrow_period_days ?? 14) }} Days</span>
                </div>
                <div class="sched-item">
                    <span class="sched-lbl">Late Penalty</span>
                    <span class="sched-val" style="color: #dc2626;">₹{{ number_format((float)($rule->late_fine_amount ?? 5.00), 2) }}/Day</span>
                </div>
            </div>

            <!-- Terms & Signatures -->
            <div class="voucher-footer">
                <div class="voucher-terms">
                    <i class="fas fa-info-circle me-1" style="color: #0038b8;"></i>
                    <strong>Terms & Rules:</strong> Handle book with care. Return or renew on or before due date to avoid late charges. Lost or damaged books will be charged as per library regulations.
                </div>
                <div class="voucher-signatures-group">
                    <div class="voucher-sig-box">
                        <div class="voucher-sig-line"></div>
                        <div class="voucher-sig-lbl">Librarian Signature</div>
                    </div>
                    <div class="voucher-sig-box">
                        <div class="voucher-sig-line"></div>
                        <div class="voucher-sig-lbl">Borrower Signature</div>
                    </div>
                </div>
            </div>
        </div>

        @if(!$copy['is_student'])
        <!-- Perforated Cut-Line Divider -->
        <div class="perforated-cut-line">
            <div class="cut-line-rule"></div>
            <div><i class="fas fa-scissors"></i> CUT ALONG DOTTED LINE (TOP: LIBRARY RECORD COPY / BOTTOM: STUDENT BOOK COPY)</div>
            <div class="cut-line-rule"></div>
        </div>
        @endif

        @endforeach

    </div>

    <!-- Auto trigger browser print -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
