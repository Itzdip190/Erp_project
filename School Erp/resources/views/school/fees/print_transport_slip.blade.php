@php
    $isCancelled = (isset($invoice) && ($invoice->status === 'cancelled' || in_array($invoice->type, ['cancel_payment', 'cancel_refund']))) ||
                   ($type === 'payment' && isset($receipt) && $receipt->status === 'cancelled') || 
                   $items->contains(function($item) {
                       return isset($item->invoice_status) && $item->invoice_status === 'cancelled';
                   });

    // Resolve Academic Session
    $sessionName = '—';
    $session = null;
    if (isset($invoice) && $invoice->school_id) {
        $session = \App\Models\AcademicSession::where('school_id', $invoice->school_id)->where('is_current', true)->first();
    } elseif (auth()->check()) {
        $session = \App\Models\AcademicSession::where('school_id', auth()->user()->school_id)->where('is_current', true)->first();
    }
    if ($session) {
        $sessionName = $session->name;
    }

    // Resolve date and time
    $txDate = $date ? \Carbon\Carbon::parse($date)->format('d-M-Y') : now()->format('d-M-Y');
    $txTime = '—';
    if (isset($invoice) && $invoice->created_at) {
        $txTime = \Carbon\Carbon::parse($invoice->created_at)->format('h:i A');
    } elseif (isset($invoice) && $invoice->payment_date) {
        $txTime = \Carbon\Carbon::parse($invoice->payment_date)->format('h:i A');
    } else {
        $txTime = now()->format('h:i A');
    }

    // Resolve Student Category
    $studentCategory = 'STUDENT';
    if (isset($student) && $student->category_id) {
        $cat = \App\Models\StudentCategory::find($student->category_id);
        if ($cat) {
            $studentCategory = strtoupper($cat->name);
        }
    }

    // Resolve Paid By
    $paidBy = 'Cash';
    if ($mode) {
        $formattedMode = str_replace('_', ' ', $mode);
        $paidBy = ucwords(strtolower($formattedMode));
    }

    // Resolve Cheque No
    $chequeNo = '';
    $paymentDetails = isset($invoice) && !empty($invoice->payment_details) ? json_decode($invoice->payment_details, true) : null;
    if (!$paymentDetails && isset($receipt) && !empty($receipt->payment_details)) {
        $paymentDetails = json_decode($receipt->payment_details, true);
    }
    if ($paymentDetails) {
        $chequeNo = $paymentDetails['cheque_number'] ?? '';
    }
    if (empty($chequeNo) && isset($invoice) && !empty($invoice->remarks) && preg_match('/(?:No:|Cheque No:)\s*([0-9]+)/i', $invoice->remarks, $matches)) {
        $chequeNo = $matches[1];
    }
    if (empty($chequeNo) && isset($receipt) && !empty($receipt->remarks) && preg_match('/(?:No:|Cheque No:)\s*([0-9]+)/i', $receipt->remarks, $matches)) {
        $chequeNo = $matches[1];
    }

    // Resolve Received By
    $receivedBy = 'Staff';
    if (isset($invoice) && $invoice->creator) {
        $receivedBy = $invoice->creator->name;
    } elseif (auth()->check()) {
        $receivedBy = auth()->user()->name;
    }

    // Resolve Transport Month
    $months = [];
    foreach ($items as $item) {
        if (preg_match('/—\s*([A-Za-z]+(?:\s+\d{4})?)/', $item->description, $matches)) {
            $months[] = $matches[1];
        }
    }
    $transportMonth = !empty($months) ? implode(', ', array_unique($months)) : now()->format('F');

    // Calculations for the table
    $paidAmount = $amount;
    $totalAmountVal = 0;
    $dueAmountVal = 0;

    // Get discount information
    $discountInfo = isset($paymentDetails['instant_discount_info']) ? $paymentDetails['instant_discount_info'] : null;
    $discountValue = 0;
    if ($discountInfo) {
        $totalAmountVal = floatval($discountInfo['total_amount']);
        $discountValue = floatval($discountInfo['discount_value']);
        $dueAmountVal = floatval($discountInfo['final_remaining_amount']);
    } else {
        // Fallback calculation
        $totalAmountVal = $items->sum('amount');
        $discountValue = isset($invoice) ? floatval($invoice->discount_amount) : (isset($receipt) ? floatval($receipt->discount_amount) : 0);
        $dueAmountVal = max(0, $totalAmountVal - $paidAmount - $discountValue);
    }

    $reason = '';
    if (isset($invoice) && !empty($invoice->remarks)) {
        $reason = $invoice->remarks;
    } elseif (isset($receipt) && !empty($receipt->remarks)) {
        $reason = $receipt->remarks;
    }

    // Fetch transport assignment details
    $vehicle = null;
    if ($student && $student->transport_vehicle_code) {
        $vehicle = \App\Models\Vehicle::where('school_id', $school->id)
            ->where(function($q) use ($student) {
                $q->where('id', $student->transport_vehicle_code)
                  ->orWhere('vehicle_no', $student->transport_vehicle_code);
            })->first();
    }
    
    $boardingPoint = $student->transport_pickup_location ?? $student->transport_stop ?? '';
    $pickupPoint = $student->transport_stop ?? $student->transport_pickup_location ?? '';
    $vehicleNo = $vehicle ? $vehicle->vehicle_no : ($student->transport_vehicle_code ?? '');
    $driverInfo = '';
    if ($vehicle) {
        $dName = $vehicle->driver_name ?? '';
        $dPhone = $vehicle->driver_phone ?? '';
        if ($dName || $dPhone) {
            $driverInfo = $dName . ($dPhone ? ' / ' . $dPhone : '');
        }
    }
    if (empty($driverInfo)) {
        $driverInfo = '/';
    }
    $pickupTiming = $student->transport_pickup_time ?? '';
    $dropTiming = $student->transport_drop_time ?? '';

    // Amount to words helper
    if (!function_exists('convertNumberToWordsHelper')) {
        function convertNumberToWordsHelper($number) {
            $no = (int)floor($number);
            $decimal = round($number - $no, 2) * 100;
            
            $words = array(
                0 => '', 1 => 'One', 2 => 'Two',
                3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
                7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
                13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
                16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
                19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
                40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
                70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
            );
            
            $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
            
            $str = array();
            $i = 0;
            $length = strlen((string)$no);
            while ($i < $length) {
                $divider = ($i == 2) ? 10 : 100;
                $currentNumber = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                $counter = count($str);
                if ($currentNumber) {
                    $plural = '';
                    $hundred = ($counter == 1 && isset($str[0]) && $str[0]) ? ' and ' : null;
                    $str[] = ($currentNumber < 21) 
                        ? $words[$currentNumber] . ' ' . $digits[$counter] . $plural . ' ' . $hundred
                        : $words[floor($currentNumber / 10) * 10] . ' ' . $words[$currentNumber % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                } else {
                    $str[] = null;
                }
            }
            
            $result = implode('', array_reverse(array_filter($str)));
            $result = trim(preg_replace('/\s+/', ' ', $result));
            
            $paise = '';
            if ($decimal > 0) {
                $paiseVal = (int)$decimal;
                if ($paiseVal < 21) {
                    $paise = ' and ' . $words[$paiseVal] . ' Paise';
                } else {
                    $paise = ' and ' . $words[floor($paiseVal / 10) * 10] . ' ' . $words[$paiseVal % 10] . ' Paise';
                }
            }
            
            return 'Rupees ' . ($result ? $result : 'Zero') . $paise . ' Only';
        }
    }

    $showSchoolName = \App\Services\SettingService::get('show_school_name_transport_invoice', '1') == '1';
    $showSchoolLogo = \App\Services\SettingService::get('show_school_logo_transport_invoice', '1') == '1';
    $showRouteVehicle = \App\Services\SettingService::get('show_route_vehicle_on_transport_invoice', '1') == '1';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Invoice - {{ $number }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            color: #000;
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
            background-color: #d97706;
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

        /* Side by Side Layout Container */
        .invoice-wrapper {
            max-width: 1300px;
            margin: 15px auto;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            box-sizing: border-box;
            padding: 0 15px;
        }

        .invoice-card {
            width: 49%;
            background: white;
            padding: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            position: relative;
        }

        /* Branding Header */
        .header-section {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 8px;
        }

        .logo-img {
            max-height: 50px;
            max-width: 50px;
            margin-right: 12px;
            object-fit: contain;
        }

        .school-info-block {
            flex-grow: 1;
        }

        .school-name-text {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
            color: #000;
        }

        .school-address-text {
            font-size: 10px;
            margin: 3px 0 0 0;
            color: #334155;
            line-height: 1.3;
        }

        /* Metadata Information Grid */
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 10px;
            font-size: 11px;
            line-height: 1.4;
        }

        .meta-item {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .meta-label {
            font-weight: 500;
            color: #475569;
            margin-right: 4px;
        }

        .meta-value {
            font-weight: 700;
            color: #000;
        }

        /* Items Table */
        .table-section {
            margin-bottom: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .data-table th {
            font-weight: 700;
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right !important;
        }

        /* Financial Summaries */
        .summary-section {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .summary-block {
            width: 48%;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
        }

        .summary-label {
            color: #475569;
        }

        .summary-value {
            font-weight: 700;
        }

        .words-block {
            font-size: 11px;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        /* Transport Details Table */
        .details-section {
            margin-bottom: 8px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .details-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            width: 25%;
        }

        .details-label {
            font-weight: 600;
            color: #000;
            background-color: #f8fafc;
        }

        .details-val {
            font-weight: 600;
            color: #000;
        }

        /* Note Line */
        .note-block {
            font-size: 10px;
            text-align: center;
            font-weight: 600;
            margin-bottom: 12px;
            color: #000;
        }

        /* Footer section */
        .card-footer-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            font-weight: 700;
            margin-top: auto;
        }

        .footer-left {
            text-transform: uppercase;
        }

        .footer-center {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .footer-right {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .cancelled-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 60px;
            color: rgba(239, 68, 68, 0.15);
            border: 6px solid rgba(239, 68, 68, 0.15);
            padding: 10px 30px;
            border-radius: 12px;
            font-weight: 900;
            letter-spacing: 4px;
            pointer-events: none;
            z-index: 99;
            text-transform: uppercase;
            white-space: nowrap;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 4mm 6mm;
            }
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .invoice-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                gap: 15px !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
                border-radius: 0 !important;
                padding: 10px !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar no-print">
        <span>Print Preview - Transport Invoice</span>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Slip
        </button>
    </div>

    <div class="invoice-wrapper">
        
        <!-- LEFT COPY: STUDENT COPY -->
        <div class="invoice-card">
            @if($isCancelled)
                <div class="cancelled-watermark">CANCELLED</div>
            @endif

            <div class="header-section">
                @if($showSchoolLogo && !empty($school->logo) && Storage::disk('public')->exists($school->logo))
                    <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="School Logo" class="logo-img">
                @endif
                <div class="school-info-block">
                    @if($showSchoolName)
                        <h1 class="school-name-text">{{ $school->name }}</h1>
                    @endif
                    <p class="school-address-text">{{ $school->address }}</p>
                </div>
            </div>

            <div class="metadata-grid">
                <div class="meta-item"><span class="meta-label">Session</span><span class="meta-value">{{ $sessionName }}</span></div>
                <div class="meta-item"><span class="meta-label">Date</span><span class="meta-value">{{ $txDate }}</span></div>
                <div class="meta-item">
                    @if($config?->details_receipt_no ?? true)
                        <span class="meta-label">Receipt No:</span>
                        <span class="meta-value">
                            @if(isset($invoice))
                                {{ $invoice->receipt_number }}
                            @elseif(isset($receipt))
                                {{ $receipt->receipt_number }}
                            @else
                                {{ $number }}
                            @endif
                        </span>
                    @else
                        <span class="meta-label">Receipt No.</span>
                        <span class="meta-value">{{ $number }}</span>
                    @endif
                </div>
                
                <div class="meta-item"><span class="meta-label">Class</span><span class="meta-value">{{ strtoupper(optional($student->class)->name ?? '') }}</span></div>
                <div class="meta-item"><span class="meta-label">Section</span><span class="meta-value">{{ strtoupper(optional($student->section)->name ?? '') }}</span></div>
                <div class="meta-item"><span class="meta-label">SR No.</span><span class="meta-value">{{ $student->admission_number ?? '—' }}</span></div>
                
                <div class="meta-item"><span class="meta-label">Month</span><span class="meta-value">{{ $transportMonth }}</span></div>
                <div class="meta-item" style="grid-column: span 2;"><span class="meta-label">Student's Name</span><span class="meta-value">{{ strtoupper($student->full_name) }}</span></div>
                
                <div class="meta-item" style="grid-column: span 2;"><span class="meta-label">Father's Name</span><span class="meta-value">{{ strtoupper($student->father_name ?? '—') }}</span></div>
                <div class="meta-item"><span class="meta-label">Category</span><span class="meta-value">{{ $studentCategory }}</span></div>
                
                <div class="meta-item"><span class="meta-label">Paid By</span><span class="meta-value">{{ $paidBy }}</span></div>
                <div class="meta-item" style="grid-column: span 2;"><span class="meta-label">Cheque no.</span><span class="meta-value">{{ $chequeNo ?: '—' }}</span></div>
            </div>

            <div class="table-section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="text-right" style="width: 25%;">Total Amount</th>
                            @if($config?->add_fee_due ?? true)
                            <th class="text-right" style="width: 25%;">Due Amount</th>
                            @endif
                            <th class="text-right" style="width: 25%;">Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Transport fee</td>
                            <td class="text-right">{{ number_format($totalAmountVal, 2, '.', '') }}</td>
                            @if($config?->add_fee_due ?? true)
                            <td class="text-right">{{ number_format($dueAmountVal, 2, '.', '') }}</td>
                            @endif
                            <td class="text-right" style="font-weight: 700;">{{ number_format($paidAmount, 2, '.', '') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="summary-section">
                <div class="summary-block">
                    <div class="summary-row"><span class="summary-label">Paid Amount:</span><span class="summary-value">{{ number_format($paidAmount, 2, '.', '') }}</span></div>
                    @if(($config?->add_fee_due ?? true) || ($config?->add_fee_balance ?? true))
                    <div class="summary-row"><span class="summary-label">Balance/Due:</span><span class="summary-value">{{ number_format($dueAmountVal, 2, '.', '') }}</span></div>
                    @endif
                    @if(($config?->add_fee_discount ?? true) && $discountValue > 0)
                    <div class="summary-row"><span class="summary-label">Discount:</span><span class="summary-value">{{ number_format($discountValue, 2, '.', '') }}</span></div>
                    @endif
                </div>
                <div class="summary-block">
                    <div class="summary-row" style="flex-direction: column;"><span class="summary-label">Reason:</span><span class="summary-value" style="word-break: break-all;">{{ $reason ?: '—' }}</span></div>
                </div>
            </div>

            <div class="words-block">
                Amount In Words &nbsp; <strong>{{ convertNumberToWordsHelper($paidAmount) }}</strong>
            </div>

            @if($showRouteVehicle)
            <div class="details-section">
                <table class="details-table">
                    <tr>
                        <td class="details-label">1. Boarding Point</td>
                        <td class="details-val">{{ $boardingPoint ?: '—' }}</td>
                        <td class="details-label">2. Pickup Point</td>
                        <td class="details-val">{{ $pickupPoint ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">3. Vehicle No.</td>
                        <td class="details-val">{{ $vehicleNo ?: '—' }}</td>
                        <td class="details-label">4. Driver Name / Mobile No.</td>
                        <td class="details-val">{{ $driverInfo }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">5. Pickup Timing</td>
                        <td class="details-val">{{ $pickupTiming ?: '—' }}</td>
                        <td class="details-label">6. Drop Timing</td>
                        <td class="details-val">{{ $dropTiming ?: '—' }}</td>
                    </tr>
                </table>
            </div>
            @endif

            <div class="note-block">
                Note: Fees Once Paid Will Not Be Refundable In Any Case
            </div>

            <div class="card-footer-info">
                <div class="footer-left">Student Copy</div>
                <div class="footer-center">
                    <span>Received By</span>
                    <span style="font-weight: 500;">{{ $receivedBy }}</span>
                </div>
                <div class="footer-right">
                    <span>Time</span>
                    <span style="font-weight: 500;">{{ $txTime }}</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COPY: SCHOOL COPY -->
        <div class="invoice-card">
            @if($isCancelled)
                <div class="cancelled-watermark">CANCELLED</div>
            @endif

            <div class="header-section">
                @if($showSchoolLogo && !empty($school->logo) && Storage::disk('public')->exists($school->logo))
                    <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="School Logo" class="logo-img">
                @endif
                <div class="school-info-block">
                    @if($showSchoolName)
                        <h1 class="school-name-text">{{ $school->name }}</h1>
                    @endif
                    <p class="school-address-text">{{ $school->address }}</p>
                </div>
            </div>

            <div class="metadata-grid">
                <div class="meta-item"><span class="meta-label">Session</span><span class="meta-value">{{ $sessionName }}</span></div>
                <div class="meta-item"><span class="meta-label">Date</span><span class="meta-value">{{ $txDate }}</span></div>
                <div class="meta-item">
                    @if($config?->details_receipt_no ?? true)
                        <span class="meta-label">Receipt No:</span>
                        <span class="meta-value">
                            @if(isset($invoice))
                                {{ $invoice->receipt_number }}
                            @elseif(isset($receipt))
                                {{ $receipt->receipt_number }}
                            @else
                                {{ $number }}
                            @endif
                        </span>
                    @else
                        <span class="meta-label">Receipt No.</span>
                        <span class="meta-value">{{ $number }}</span>
                    @endif
                </div>
                
                <div class="meta-item"><span class="meta-label">Class</span><span class="meta-value">{{ strtoupper(optional($student->class)->name ?? '') }}</span></div>
                <div class="meta-item"><span class="meta-label">Section</span><span class="meta-value">{{ strtoupper(optional($student->section)->name ?? '') }}</span></div>
                <div class="meta-item"><span class="meta-label">SR No.</span><span class="meta-value">{{ $student->admission_number ?? '—' }}</span></div>
                
                <div class="meta-item"><span class="meta-label">Month</span><span class="meta-value">{{ $transportMonth }}</span></div>
                <div class="meta-item" style="grid-column: span 2;"><span class="meta-label">Student's Name</span><span class="meta-value">{{ strtoupper($student->full_name) }}</span></div>
                
                <div class="meta-item" style="grid-column: span 2;"><span class="meta-label">Father's Name</span><span class="meta-value">{{ strtoupper($student->father_name ?? '—') }}</span></div>
                <div class="meta-item"><span class="meta-label">Category</span><span class="meta-value">{{ $studentCategory }}</span></div>
                
                <div class="meta-item"><span class="meta-label">Paid By</span><span class="meta-value">{{ $paidBy }}</span></div>
                <div class="meta-item" style="grid-column: span 2;"><span class="meta-label">Cheque no.</span><span class="meta-value">{{ $chequeNo ?: '—' }}</span></div>
            </div>

            <div class="table-section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="text-right" style="width: 25%;">Total Amount</th>
                            @if($config?->add_fee_due ?? true)
                            <th class="text-right" style="width: 25%;">Due Amount</th>
                            @endif
                            <th class="text-right" style="width: 25%;">Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Transport fee</td>
                            <td class="text-right">{{ number_format($totalAmountVal, 2, '.', '') }}</td>
                            @if($config?->add_fee_due ?? true)
                            <td class="text-right">{{ number_format($dueAmountVal, 2, '.', '') }}</td>
                            @endif
                            <td class="text-right" style="font-weight: 700;">{{ number_format($paidAmount, 2, '.', '') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="summary-section">
                <div class="summary-block">
                    <div class="summary-row"><span class="summary-label">Paid Amount:</span><span class="summary-value">{{ number_format($paidAmount, 2, '.', '') }}</span></div>
                    @if(($config?->add_fee_due ?? true) || ($config?->add_fee_balance ?? true))
                    <div class="summary-row"><span class="summary-label">Balance/Due:</span><span class="summary-value">{{ number_format($dueAmountVal, 2, '.', '') }}</span></div>
                    @endif
                    @if(($config?->add_fee_discount ?? true) && $discountValue > 0)
                    <div class="summary-row"><span class="summary-label">Discount:</span><span class="summary-value">{{ number_format($discountValue, 2, '.', '') }}</span></div>
                    @endif
                </div>
                <div class="summary-block">
                    <div class="summary-row" style="flex-direction: column;"><span class="summary-label">Reason:</span><span class="summary-value" style="word-break: break-all;">{{ $reason ?: '—' }}</span></div>
                </div>
            </div>

            <div class="words-block">
                Amount In Words &nbsp; <strong>{{ convertNumberToWordsHelper($paidAmount) }}</strong>
            </div>

            @if($showRouteVehicle)
            <div class="details-section">
                <table class="details-table">
                    <tr>
                        <td class="details-label">1. Boarding Point</td>
                        <td class="details-val">{{ $boardingPoint ?: '—' }}</td>
                        <td class="details-label">2. Pickup Point</td>
                        <td class="details-val">{{ $pickupPoint ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">3. Vehicle No.</td>
                        <td class="details-val">{{ $vehicleNo ?: '—' }}</td>
                        <td class="details-label">4. Driver Name / Mobile No.</td>
                        <td class="details-val">{{ $driverInfo }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">5. Pickup Timing</td>
                        <td class="details-val">{{ $pickupTiming ?: '—' }}</td>
                        <td class="details-label">6. Drop Timing</td>
                        <td class="details-val">{{ $dropTiming ?: '—' }}</td>
                    </tr>
                </table>
            </div>
            @endif

            <div class="note-block">
                Note: Fees Once Paid Will Not Be Refundable In Any Case
            </div>

            <div class="card-footer-info">
                <div class="footer-left">School Copy</div>
                <div class="footer-center">
                    <span>Received By</span>
                    <span style="font-weight: 500;">{{ $receivedBy }}</span>
                </div>
                <div class="footer-right">
                    <span>Time</span>
                    <span style="font-weight: 500;">{{ $txTime }}</span>
                </div>
            </div>
        </div>

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
