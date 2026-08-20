@php
    if (!function_exists('convertNumberToWordsInventory')) {
        function convertNumberToWordsInventory($number) {
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null;
            $digits_length = strlen($no);
            $i = 0;
            $str = array();
            $words = array(
                0 => '', 1 => 'ONE', 2 => 'TWO',
                3 => 'THREE', 4 => 'FOUR', 5 => 'FIVE', 6 => 'SIX',
                7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE',
                10 => 'TEN', 11 => 'ELEVEN', 12 => 'TWELVE',
                13 => 'THIRTEEN', 14 => 'FOURTEEN', 15 => 'FIFTEEN',
                16 => 'SIXTEEN', 17 => 'SEVENTEEN', 18 => 'EIGHTEEN',
                19 => 'NINETEEN', 20 => 'TWENTY', 30 => 'THIRTY',
                40 => 'FORTY', 50 => 'FIFTY', 60 => 'SIXTY',
                70 => 'SEVENTY', 80 => 'EIGHTY', 90 => 'NINETY'
            );
            $digits = array('', 'HUNDRED','THOUSAND','LAKH', 'CRORE');
            while ($i < $digits_length) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 'S' : '';
                    $hundred = ($counter == 1 && $str[0]) ? ' AND ' : '';
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $paise = ($decimal > 0) ? " AND " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' PAISE' : '';
            return trim(($Rupees ? $Rupees : 'ZERO') . $paise . " ONLY");
        }
    }

    $customerName = $sale->customer_name ?? ($sale->student?->full_name ?? 'ATHARVA DIWEDI');
    $admissionNo = $sale->admission_no ?? ($sale->student?->admission_number ?? 'JPPS06');
    $mobile = $sale->customer_mobile ?? ($sale->student?->phone ?? '—');
    $address = $sale->customer_address ?? '—';
    $invoiceNo = $sale->invoice_number ?? ('INV-' . $sale->id);
    $receiptNo = $sale->receipt_number ?? ($sale->invoice_number ?? 'VPS-000010');
    $saleDate = !empty($sale->sale_date) ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : date('d/m/Y');
    $paymentMode = is_object($sale) && isset($sale->payment_mode_label) ? $sale->payment_mode_label : (ucfirst($sale->payment_mode ?? 'Cash'));

    $classSection = '—';
    if (isset($sale->student) && $sale->student) {
        $className = optional($sale->student->class)->name ?? '';
        $secName = optional($sale->student->section)->name ?? '';
        $classSection = trim($className . ' ' . $secName) ?: '—';
    } elseif (isset($sale->class_name)) {
        $classSection = trim($sale->class_name . ' ' . ($sale->section_name ?? '')) ?: '—';
    }

    $items = $sale->items ?? collect();
    $totalMrp = (float)($sale->total_mrp ?? 0);
    $subTotal = (float)($sale->sub_total ?? 0);
    $totalTax = (float)($sale->total_tax ?? 0);
    $totalDiscount = (float)($sale->total_discount ?? 0);
    $grandTotal = (float)($sale->grand_total ?? ($subTotal - $totalDiscount + $totalTax));
    $paidAmount = (float)($sale->paid_amount ?? $grandTotal);
    $dueAmount = (float)($sale->due_amount ?? max(0, $grandTotal - $paidAmount));

    // Calculate tax percent if available
    $taxRateLabel = 'Tax / GST';
    $firstItemTaxPct = 0;
    foreach ($items as $it) {
        if (!empty($it->tax_percent) && floatval($it->tax_percent) > 0) {
            $firstItemTaxPct = floatval($it->tax_percent);
            break;
        } elseif (!empty($it->tax) && floatval($it->tax) > 0) {
            $firstItemTaxPct = floatval($it->tax);
            break;
        }
    }
    if ($firstItemTaxPct > 0) {
        $taxRateLabel = 'Tax / GST (' . round($firstItemTaxPct, 1) . '%)';
    }

    // Ratio of paid amount for proportional distribution across rows
    $paidRatio = ($grandTotal > 0) ? min(1.0, $paidAmount / $grandTotal) : 1.0;

    $schoolLogo = (!empty($school->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->logo))
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($school->logo)
        : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $receiptNo }} - {{ $school->name ?? 'School ERP' }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            color: #000;
            padding: 20px;
        }

        .no-print-toolbar {
            max-width: 1060px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
            transition: all 0.2s;
        }
        .btn-action:hover {
            background: #f8fafc;
            color: #0f172a;
        }
        .btn-action.btn-primary {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }
        .btn-action.btn-primary:hover {
            background: #1d4ed8;
        }

        /* ─── Dual Copy Container (A4 Landscape / Split Layout) ─────────────── */
        .dual-slip-container {
            max-width: 1060px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            display: flex;
            padding: 16px;
        }

        .single-slip {
            flex: 1;
            padding: 8px 14px;
            position: relative;
        }

        .slip-divider {
            width: 1px;
            border-left: 1px dashed #64748b;
            margin: 0 8px;
        }

        /* ─── Header ───────────────────────────────────────────────────────── */
        .slip-header {
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .slip-logo {
            max-height: 48px;
            max-width: 48px;
            margin-right: 12px;
            object-fit: contain;
        }
        .slip-header-text {
            flex-grow: 1;
            text-align: center;
        }
        .slip-header-text h2 {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .slip-header-text p {
            font-size: 10.5px;
            line-height: 1.25;
            color: #000;
        }

        /* ─── Metadata ─────────────────────────────────────────────────────── */
        .slip-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 10px;
        }
        .slip-meta-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        .slip-meta-table td.lbl {
            font-weight: bold;
            color: #000;
        }
        .slip-meta-table td.val {
            color: #000;
        }

        /* ─── Component Table ──────────────────────────────────────────────── */
        .slip-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 10px;
            border: 1px solid #000;
        }
        .slip-items-table th {
            border: 1px solid #000;
            padding: 5px 6px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
        }
        .slip-items-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 10.5px;
        }
        .slip-items-table tr.total-row td {
            font-weight: 800;
            border-top: 1.5px solid #000;
        }

        /* ─── Paid Box ─────────────────────────────────────────────────────── */
        .slip-paid-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1.5px solid #000;
            padding: 5px 8px;
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* ─── Details ──────────────────────────────────────────────────────── */
        .slip-details-block {
            font-size: 10.5px;
            line-height: 1.45;
            margin-bottom: 24px;
        }
        .slip-details-block strong {
            font-weight: 800;
        }

        /* ─── Footer Signatures ────────────────────────────────────────────── */
        .slip-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10.5px;
            margin-top: 10px;
        }
        .slip-copy-tag {
            font-style: italic;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
        }
        .slip-sign-line {
            width: 130px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: 800;
            font-size: 10.5px;
        }

        /* ─── Print Settings ───────────────────────────────────────────────── */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .dual-slip-container {
                border: none;
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }
            @page {
                size: landscape;
                margin: 6mm 8mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-toolbar">
        <a href="{{ route('school.inventory.billing') }}" class="btn-action">
            <i class="fas fa-arrow-left"></i> Back to Cart
        </a>
        <button type="button" class="btn-action btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <!-- Dual Copy Side-by-Side Container -->
    <div class="dual-slip-container">

        <!-- 1. LEFT COPY: OFFICE COPY -->
        <div class="single-slip">
            <!-- Header -->
            <div class="slip-header">
                @if($schoolLogo)
                    <img src="{{ $schoolLogo }}" class="slip-logo" alt="Logo">
                @else
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; margin-right: 12px;">
                        {{ strtoupper(substr($school->name ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <div class="slip-header-text">
                    <h2>{{ $school->name ?? 'VEDANT PUBLIC SCHOOL' }}</h2>
                    <p>{{ $school->address ?? 'Sctor 88A Gurgaon, Hariyana' }}</p>
                    <p>Email: {{ $school->email ?? 'vedantpublicschool@gmail.com' }} | Phone: {{ $school->phone ?? '9451805575' }}</p>
                </div>
            </div>

            <!-- Metadata Table -->
            <table class="slip-meta-table">
                <tr>
                    <td class="lbl" style="width: 20%;">Receipt No.:</td>
                    <td class="val" style="width: 32%;"><strong>{{ $receiptNo }}</strong></td>
                    <td class="lbl" style="width: 20%;">Receipt Date:</td>
                    <td class="val" style="width: 28%;">{{ $saleDate }}</td>
                </tr>
                <tr>
                    <td class="lbl">Student Name:</td>
                    <td class="val" colspan="3"><strong style="text-transform: uppercase;">{{ $customerName }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Admission No.:</td>
                    <td class="val"><strong>{{ $admissionNo }}</strong></td>
                    <td class="lbl">Class & Section:</td>
                    <td class="val"><strong style="text-transform: uppercase;">{{ $classSection }}</strong></td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="slip-items-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">COMPONENT</th>
                        <th style="text-align: right; width: 22%;">ACTUAL AMOUNT</th>
                        <th style="text-align: right; width: 18%;">PAID</th>
                        <th style="text-align: right; width: 18%;">BALANCE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $iPrice = (float)($item->price ?? 0);
                            $iQty = (int)($item->quantity ?? 1);
                            $iTotBase = (float)($item->total_price ?? ($iPrice * $iQty));
                            $iPaidBase = round($iTotBase * $paidRatio, 2);
                            $iDueBase = max(0, $iTotBase - $iPaidBase);
                        @endphp
                        <tr>
                            <td style="text-align: left;">
                                {{ $item->product_name ?? ($item->name ?? 'Product') }}
                                @if(!empty($item->size) && $item->size !== 'Free')
                                    ({{ $item->size }})
                                @endif
                                @if($iQty > 1)
                                    x {{ $iQty }}
                                @endif
                            </td>
                            <td style="text-align: right;">{{ number_format($iTotBase, 2) }}</td>
                            <td style="text-align: right;">{{ number_format($iPaidBase, 2) }}</td>
                            <td style="text-align: right;">{{ number_format($iDueBase, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="text-align: left;">Sub Total (Products)</td>
                            <td style="text-align: right;">{{ number_format($subTotal, 2) }}</td>
                            <td style="text-align: right;">{{ number_format(round($subTotal * $paidRatio, 2), 2) }}</td>
                            <td style="text-align: right;">{{ number_format(max(0, $subTotal - round($subTotal * $paidRatio, 2)), 2) }}</td>
                        </tr>
                    @endforelse

                    {{-- Explicit Tax / GST Row --}}
                    @if($totalTax > 0)
                        @php
                            $paidTax = round($totalTax * $paidRatio, 2);
                            $dueTax = max(0, $totalTax - $paidTax);
                        @endphp
                        <tr>
                            <td style="text-align: left; font-weight: bold; color: #1e293b;">
                                <i class="fas fa-percent me-1" style="font-size: 9px;"></i> {{ $taxRateLabel }}
                            </td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($totalTax, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($paidTax, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($dueTax, 2) }}</td>
                        </tr>
                    @endif

                    {{-- Explicit Discount Row (If applicable) --}}
                    @if($totalDiscount > 0)
                        <tr>
                            <td style="text-align: left; font-style: italic; color: #dc2626;">
                                Discount (-)
                            </td>
                            <td style="text-align: right; color: #dc2626;">-{{ number_format($totalDiscount, 2) }}</td>
                            <td style="text-align: right;">—</td>
                            <td style="text-align: right;">—</td>
                        </tr>
                    @endif

                    <tr class="total-row">
                        <td style="text-align: left;">TOTAL</td>
                        <td style="text-align: right;">{{ number_format($grandTotal, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($paidAmount, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($dueAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- PAID Box -->
            <div class="slip-paid-box">
                <span>PAID</span>
                <span>Rs {{ number_format($paidAmount, 2) }}</span>
            </div>

            <!-- Details Block -->
            <div class="slip-details-block">
                <div>Total Amount Paid: <strong>{{ convertNumberToWordsInventory($paidAmount) }}</strong></div>
                <div style="margin-top: 2px;">Mode of Payment: <strong>{{ $paymentMode }}</strong></div>
                <div style="margin-top: 2px; font-size: 10px; color: #334155;">
                    Breakdown: Base: <strong>₹{{ number_format($subTotal, 2) }}</strong>
                    @if($totalTax > 0)
                        | Tax: <strong>₹{{ number_format($totalTax, 2) }}</strong>
                    @endif
                    @if($totalDiscount > 0)
                        | Discount: <strong>₹{{ number_format($totalDiscount, 2) }}</strong>
                    @endif
                    | Total: <strong>₹{{ number_format($grandTotal, 2) }}</strong>
                    @if($dueAmount > 0)
                        | <span style="color: #dc2626; font-weight: bold;">Due: ₹{{ number_format($dueAmount, 2) }}</span>
                    @endif
                </div>
                <div style="margin-top: 2px;">Remarks: <strong>Fee Payment / Product Purchase</strong></div>
            </div>

            <!-- Signatures -->
            <div class="slip-footer">
                <div class="slip-copy-tag">OFFICE COPY</div>
                <div class="slip-sign-line">Accountant Sign</div>
            </div>
        </div>

        <!-- Center Dashed Line Divider -->
        <div class="slip-divider"></div>

        <!-- 2. RIGHT COPY: STUDENT COPY -->
        <div class="single-slip">
            <!-- Header -->
            <div class="slip-header">
                @if($schoolLogo)
                    <img src="{{ $schoolLogo }}" class="slip-logo" alt="Logo">
                @else
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; margin-right: 12px;">
                        {{ strtoupper(substr($school->name ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <div class="slip-header-text">
                    <h2>{{ $school->name ?? 'VEDANT PUBLIC SCHOOL' }}</h2>
                    <p>{{ $school->address ?? 'Sctor 88A Gurgaon, Hariyana' }}</p>
                    <p>Email: {{ $school->email ?? 'vedantpublicschool@gmail.com' }} | Phone: {{ $school->phone ?? '9451805575' }}</p>
                </div>
            </div>

            <!-- Metadata Table -->
            <table class="slip-meta-table">
                <tr>
                    <td class="lbl" style="width: 20%;">Receipt No.:</td>
                    <td class="val" style="width: 32%;"><strong>{{ $receiptNo }}</strong></td>
                    <td class="lbl" style="width: 20%;">Receipt Date:</td>
                    <td class="val" style="width: 28%;">{{ $saleDate }}</td>
                </tr>
                <tr>
                    <td class="lbl">Student Name:</td>
                    <td class="val" colspan="3"><strong style="text-transform: uppercase;">{{ $customerName }}</strong></td>
                </tr>
                <tr>
                    <td class="lbl">Admission No.:</td>
                    <td class="val"><strong>{{ $admissionNo }}</strong></td>
                    <td class="lbl">Class & Section:</td>
                    <td class="val"><strong style="text-transform: uppercase;">{{ $classSection }}</strong></td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="slip-items-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">COMPONENT</th>
                        <th style="text-align: right; width: 22%;">ACTUAL AMOUNT</th>
                        <th style="text-align: right; width: 18%;">PAID</th>
                        <th style="text-align: right; width: 18%;">BALANCE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $iPrice = (float)($item->price ?? 0);
                            $iQty = (int)($item->quantity ?? 1);
                            $iTotBase = (float)($item->total_price ?? ($iPrice * $iQty));
                            $iPaidBase = round($iTotBase * $paidRatio, 2);
                            $iDueBase = max(0, $iTotBase - $iPaidBase);
                        @endphp
                        <tr>
                            <td style="text-align: left;">
                                {{ $item->product_name ?? ($item->name ?? 'Product') }}
                                @if(!empty($item->size) && $item->size !== 'Free')
                                    ({{ $item->size }})
                                @endif
                                @if($iQty > 1)
                                    x {{ $iQty }}
                                @endif
                            </td>
                            <td style="text-align: right;">{{ number_format($iTotBase, 2) }}</td>
                            <td style="text-align: right;">{{ number_format($iPaidBase, 2) }}</td>
                            <td style="text-align: right;">{{ number_format($iDueBase, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td style="text-align: left;">Sub Total (Products)</td>
                            <td style="text-align: right;">{{ number_format($subTotal, 2) }}</td>
                            <td style="text-align: right;">{{ number_format(round($subTotal * $paidRatio, 2), 2) }}</td>
                            <td style="text-align: right;">{{ number_format(max(0, $subTotal - round($subTotal * $paidRatio, 2)), 2) }}</td>
                        </tr>
                    @endforelse

                    {{-- Explicit Tax / GST Row --}}
                    @if($totalTax > 0)
                        @php
                            $paidTax = round($totalTax * $paidRatio, 2);
                            $dueTax = max(0, $totalTax - $paidTax);
                        @endphp
                        <tr>
                            <td style="text-align: left; font-weight: bold; color: #1e293b;">
                                <i class="fas fa-percent me-1" style="font-size: 9px;"></i> {{ $taxRateLabel }}
                            </td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($totalTax, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($paidTax, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($dueTax, 2) }}</td>
                        </tr>
                    @endif

                    {{-- Explicit Discount Row (If applicable) --}}
                    @if($totalDiscount > 0)
                        <tr>
                            <td style="text-align: left; font-style: italic; color: #dc2626;">
                                Discount (-)
                            </td>
                            <td style="text-align: right; color: #dc2626;">-{{ number_format($totalDiscount, 2) }}</td>
                            <td style="text-align: right;">—</td>
                            <td style="text-align: right;">—</td>
                        </tr>
                    @endif

                    <tr class="total-row">
                        <td style="text-align: left;">TOTAL</td>
                        <td style="text-align: right;">{{ number_format($grandTotal, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($paidAmount, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($dueAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- PAID Box -->
            <div class="slip-paid-box">
                <span>PAID</span>
                <span>Rs {{ number_format($paidAmount, 2) }}</span>
            </div>

            <!-- Details Block -->
            <div class="slip-details-block">
                <div>Total Amount Paid: <strong>{{ convertNumberToWordsInventory($paidAmount) }}</strong></div>
                <div style="margin-top: 2px;">Mode of Payment: <strong>{{ $paymentMode }}</strong></div>
                <div style="margin-top: 2px; font-size: 10px; color: #334155;">
                    Breakdown: Base: <strong>₹{{ number_format($subTotal, 2) }}</strong>
                    @if($totalTax > 0)
                        | Tax: <strong>₹{{ number_format($totalTax, 2) }}</strong>
                    @endif
                    @if($totalDiscount > 0)
                        | Discount: <strong>₹{{ number_format($totalDiscount, 2) }}</strong>
                    @endif
                    | Total: <strong>₹{{ number_format($grandTotal, 2) }}</strong>
                    @if($dueAmount > 0)
                        | <span style="color: #dc2626; font-weight: bold;">Due: ₹{{ number_format($dueAmount, 2) }}</span>
                    @endif
                </div>
                <div style="margin-top: 2px;">Remarks: <strong>Fee Payment / Product Purchase</strong></div>
            </div>

            <!-- Signatures -->
            <div class="slip-footer">
                <div class="slip-copy-tag">STUDENT COPY</div>
                <div class="slip-sign-line">Accountant Sign</div>
            </div>
        </div>

    </div>

</body>
</html>
