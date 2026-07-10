@php
    $isCancelled = (isset($invoice) && ($invoice->status === 'cancelled' || in_array($invoice->type, ['cancel_payment', 'cancel_refund']))) ||
                   ($type === 'payment' && isset($receipt) && $receipt->status === 'cancelled') || 
                   $items->contains(function($item) {
                       return isset($item->invoice_status) && $item->invoice_status === 'cancelled';
                   });

    $isInvoiceOrPayment = ($type === 'invoice' || $type === 'payment' || (isset($invoice) && ($invoice->type === 'invoice' || $invoice->type === 'payment')));
    $showDetailed = ($isInvoiceOrPayment && ($config?->show_installment_components_on_invoice ?? false));

    $renderItems = collect();
    if ($type === 'payment' || $type === 'invoice') {
        // Separate transport items from others
        $transportItems = collect();
        $otherItems = collect();
        
        foreach ($items as $item) {
            $desc = strtolower($item->description ?? '');
            if (strpos($desc, 'transport') !== false || strpos($desc, 'bus') !== false) {
                $transportItems->push($item);
            } else {
                $otherItems->push($item);
            }
        }

        // 1. Render other items (normal logic)
        if ($otherItems->isNotEmpty()) {
            if ($showDetailed) {
                foreach ($otherItems as $item) {
                    $renderItems->push((object)[
                        'description' => $item->description,
                        'amount' => $item->amount,
                        'instant_discount_amount' => $item->instant_discount_amount,
                        'paid_amount' => $item->paid_amount,
                    ]);
                }
            } else {
                $grouped = $otherItems->groupBy('installment_no');
                foreach ($grouped as $instNo => $group) {
                    $totalAmount = $group->sum('amount');
                    $totalDiscount = $group->sum('instant_discount_amount');
                    $totalPaid = $group->sum(function($f) use ($type) {
                        if (isset($f->paid_amount)) return $f->paid_amount;
                        return $type === 'payment' ? $f->paid_amount : ($f->amount - $f->instant_discount_amount);
                    });
                    
                    $renderItems->push((object)[
                        'description' => 'Installment ' . $instNo,
                        'amount' => $totalAmount,
                        'instant_discount_amount' => $totalDiscount,
                        'paid_amount' => $totalPaid,
                    ]);
                }
            }
        }

        // 2. Render transport items dynamically with daily deductions
        if ($transportItems->isNotEmpty() && isset($student)) {
            $pickFare = (float) ($student->transport_pick_fare ?? 0);
            $dropFare = (float) ($student->transport_drop_fare ?? 0);
            $monthStr = $student->transport_month ?: date('F Y');

            $targetDate = now();
            if (!empty($monthStr)) {
                try {
                    $targetDate = \Carbon\Carbon::parse($monthStr);
                } catch (\Exception $e) {
                    $targetDate = now();
                }
            }

            $year = $targetDate->year;
            $month = $targetDate->month;
            $totalDays = $targetDate->daysInMonth;

            // Count Sundays in this month
            $sundays = 0;
            for ($d = 1; $d <= $totalDays; $d++) {
                $checkDate = \Carbon\Carbon::create($year, $month, $d);
                if ($checkDate->isSunday()) {
                    $sundays++;
                }
            }

            $billableDays = $totalDays - $sundays;
            if ($billableDays <= 0) $billableDays = 26;

            $dailyPickCost = $pickFare / $billableDays;
            $dailyDropCost = $dropFare / $billableDays;

            $startOfMonth = $targetDate->copy()->startOfMonth()->toDateString();
            $endOfMonth = $targetDate->copy()->endOfMonth()->toDateString();

            $attendances = \App\Models\BusAttendance::where('school_id', $student->school_id)
                ->where('student_id', $student->id)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->get();

            $absentPickCount = $attendances->where('trip_type', 'pickup')->where('status', 'absent')->count();
            $absentDropCount = $attendances->where('trip_type', 'drop')->where('status', 'absent')->count();

            $pickDeduction = $absentPickCount * $dailyPickCost;
            $dropDeduction = $absentDropCount * $dailyDropCost;

            $finalPickFare = max(0, $pickFare - $pickDeduction);
            $finalDropFare = max(0, $dropFare - $dropDeduction);

            if ($pickFare > 0) {
                $pDesc = "Transport Pickup Cost - Route: " . ($student->transport_route ?? 'N/A') . " (" . $monthStr . ")";
                if ($absentPickCount > 0) {
                    $pDesc .= " [Deducted {$absentPickCount} absent days]";
                }
                $renderItems->push((object)[
                    'description' => $pDesc,
                    'amount' => $pickFare,
                    'instant_discount_amount' => $pickDeduction,
                    'paid_amount' => $finalPickFare,
                ]);
            }
            if ($dropFare > 0) {
                $dDesc = "Transport Drop Cost - Route: " . ($student->transport_route ?? 'N/A') . " (" . $monthStr . ")";
                if ($absentDropCount > 0) {
                    $dDesc .= " [Deducted {$absentDropCount} absent days]";
                }
                $renderItems->push((object)[
                    'description' => $dDesc,
                    'amount' => $dropFare,
                    'instant_discount_amount' => $dropDeduction,
                    'paid_amount' => $finalDropFare,
                ]);
            }
        }
    } else {
        // Refund
        foreach ($items as $item) {
            $desc = isset($item->description) ? $item->description : (isset($item->reason) ? $item->reason : '');
            if (strpos($desc, ' (Refunded: ') !== false) {
                $desc = str_replace(' (Refunded: ', '', strstr($desc, ' (Refunded: '));
                $desc = rtrim($desc, ')');
            }
            $renderItems->push((object)[
                'description' => $desc ?: ('Refund - Installment ' . ($item->installment_no ?? 1)),
                'amount' => $item->amount,
                'instant_discount_amount' => 0.00,
                'paid_amount' => $item->amount,
            ]);
        }
    }
@endphp
<div style="position: relative; overflow: hidden; padding: 10px 0;">
@if($isCancelled)
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); font-size: 60px; color: rgba(220, 38, 38, 0.25); border: 6px solid rgba(220, 38, 38, 0.25); padding: 10px 20px; border-radius: 12px; font-weight: 800; letter-spacing: 4px; pointer-events: none; z-index: 999; text-transform: uppercase;">
        CANCELLED
    </div>
@endif

<div class="school-header">
    <div class="school-info">
        @if(!empty($school->logo) && Storage::disk('public')->exists($school->logo))
            <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="Logo" style="max-height:45px; max-width:110px; object-fit:contain;">
        @else
            <div class="school-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
        @endif
        <div>
            <h3 class="school-name">
                {{ $school->name ?? 'School Institution' }}
                @if($config?->inst_board_logo ?? false)
                    <span style="font-size: 8px; border: 1.5px solid #d97706; padding: 1px 4px; border-radius: 3px; background: #fffbeb; font-weight: 800; color: #d97706; margin-left: 6px; vertical-align: middle; display: inline-block;">CBSE AFFILIATED</span>
                @endif
            </h3>
            <p class="school-details">{{ $school->address ?? 'Academic Avenue, Tech City' }}</p>
        </div>
    </div>
    <div class="doc-title-area">
        <div class="doc-title" style="{{ $isCancelled ? 'color:#dc2626;' : '' }}">{{ $title }}</div>
        @if($config?->details_receipt_no ?? true)
            <p class="doc-ref">{{ $type === 'payment' ? 'Receipt No' : ($type === 'invoice' ? 'Invoice No' : 'Slip No') }}: <strong style="color:var(--primary-color);">{{ $number }}</strong></p>
        @endif
        <span style="font-size:10px; font-weight:800; color:var(--accent-color); background:#fffbeb; border:1px solid #fde68a; padding:2px 6px; border-radius:4px; margin-top:4px; display:inline-block;">{{ $copy_label }}</span>
    </div>
</div>

<div class="metadata-box">
    <div class="metadata-section" style="width: 50%;">
        <div class="metadata-label">Bill To / Student Info:</div>
        @if($config?->details_student_name ?? true)
            <div class="student-name">{{ $student->full_name }}</div>
        @endif
        @if($config?->details_class ?? true)
            <div>Class: <strong>{{ optional($student->class)->name }} - {{ optional($student->section)->name }}</strong></div>
        @endif
        @if($config?->details_admission_no ?? true)
            <div>Admission ID: <strong>{{ $student->admission_number }}</strong></div>
        @endif
        @if(($config?->details_father_name ?? false) && $student->father_name)
            <div>Father's Name: {{ $student->father_name }}</div>
        @endif
        @if(($config?->details_mother_name ?? false) && $student->mother_name)
            <div>Mother's Name: {{ $student->mother_name }}</div>
        @endif
        @if(($config?->details_address ?? false) && $student->address)
            <div>Address: {{ $student->address }}</div>
        @endif
        @if(($config?->details_father_phone ?? false) && $student->father_phone)
            <div>Father's Phone: {{ $student->father_phone }}</div>
        @endif
        @if(($config?->details_mother_phone ?? false) && $student->mother_phone)
            <div>Mother's Phone: {{ $student->mother_phone }}</div>
        @endif
    </div>
    <div class="metadata-section" style="width: 50%; text-align: right;">
        <div class="metadata-label">Transaction Details:</div>
        @if($config?->details_receipt_date ?? true)
            <div>Date: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong></div>
        @endif
        @if($config?->details_session ?? true)
            @php
                $sessionName = optional($student->academicSession)->name;
                if(!$sessionName && isset($invoice) && $invoice->academic_session_id) {
                    $sessionName = optional(\App\Models\AcademicSession::find($invoice->academic_session_id))->name;
                }
                if(!$sessionName) {
                    $sessionName = optional(\App\Models\AcademicSession::where('school_id', $school->id)->where('is_current', true)->first())->name;
                }
            @endphp
            @if($sessionName)
                <div>Session: <strong>{{ $sessionName }}</strong></div>
            @endif
        @endif
        <div>Payment/Invoice Mode: <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $mode) }}</strong></div>
        @if($bankName)
            <div>Bank: <strong>{{ $bankName }}</strong></div>
        @endif
        @if($bankDate)
            <div>Bank Date: <strong>{{ \Carbon\Carbon::parse($bankDate)->format('d M Y') }}</strong></div>
        @endif
        @if($remarks)
            <div>Remarks: <em>{{ $remarks }}</em></div>
        @endif
        @if($config?->inst_affiliation_no ?? false)
            <div>Affiliation No: <strong>CBSE/1130092</strong></div>
        @endif
        @if($config?->inst_school_url ?? false)
            <div>Website: <strong>{{ url('/') }}</strong></div>
        @endif
    </div>
</div>

<table class="particulars-table">
    <thead>
        <tr>
            <th>Description</th>
            <th style="text-align:right;">Original Amount</th>
            <th style="text-align:right;">Discount Applied</th>
            <th style="text-align:right;">{{ $type === 'payment' ? 'Paid Amount' : ($type === 'invoice' ? 'Net Amount' : 'Refunded Amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @php $subtotal = 0; $discountTotal = 0; @endphp
        @forelse($renderItems as $item)
        @php
            $subtotal += $item->amount;
            $discountTotal += $item->instant_discount_amount;
        @endphp
        <tr>
            <td style="font-weight:600;">{{ $item->description }}</td>
            <td style="text-align:right;">₹{{ number_format($item->amount, 2) }}</td>
            <td style="text-align:right; color:#dc2626;">₹{{ number_format($item->instant_discount_amount, 2) }}</td>
            <td style="text-align:right; font-weight:700; color:{{ $type === 'payment' ? 'var(--primary-color)' : ($type === 'invoice' ? 'var(--primary-color)' : 'var(--accent-color)') }}">₹{{ number_format($item->paid_amount, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align:center; color:var(--text-muted);">No items recorded.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="total-section">
    <table class="total-table">
        <tr>
            <td style="color:var(--text-muted);">Total Original:</td>
            <td style="text-align:right; font-weight:600;">₹{{ number_format($subtotal, 2) }}</td>
        </tr>
        @if($type === 'payment')
        <tr>
            <td style="color:var(--text-muted);">Total Discount:</td>
            <td style="text-align:right; font-weight:600; color:#dc2626;">₹{{ number_format($discountTotal, 2) }}</td>
        </tr>
        @php
            $instNo = $items->first() ? $items->first()->installment_no : null;
            $installmentDue = 0;
            if ($instNo && isset($student)) {
                $installmentDue = \App\Models\StudentFee::where('school_id', $student->school_id)
                    ->where('student_id', $student->id)
                    ->where('installment_no', $instNo)
                    ->get()
                    ->sum(function($sf) {
                        return max(0, $sf->amount - $sf->instant_discount_amount - $sf->paid_amount);
                    });
            }
        @endphp
        <tr>
            <td style="color:var(--text-muted);">Remaining Due:</td>
            <td style="text-align:right; font-weight:600; color:#dc2626;">₹{{ number_format($installmentDue, 2) }}</td>
        </tr>
        @endif
        @if($type === 'invoice')
        @php
            $totalPaid = $items->sum('paid_amount');
            $totalDue = $items->reduce(function($carry, $item) {
                return $carry + max(0, $item->amount - $item->instant_discount_amount - $item->paid_amount);
            }, 0);
        @endphp
        <tr>
            <td style="color:var(--text-muted);">Total Paid:</td>
            <td style="text-align:right; font-weight:600; color:#16a34a;">₹{{ number_format($totalPaid, 2) }}</td>
        </tr>
        @if($config?->show_due_on_invoice ?? true)
        <tr>
            <td style="color:var(--text-muted);">Outstanding Due:</td>
            <td style="text-align:right; font-weight:600; color:#dc2626;">₹{{ number_format($totalDue, 2) }}</td>
        </tr>
        @endif
        @endif
        <tr class="total-row">
            <td>Net Total Amount:</td>
            <td style="text-align:right;">₹{{ number_format($subtotal - $discountTotal, 2) }}</td>
        </tr>
    </table>
</div>

<div class="footer-note">
    <span>Generated by SchoolCloud ERP</span>
    <span style="font-size: 10px;">Signature / Stamp: ______________________</span>
</div>
</div>
