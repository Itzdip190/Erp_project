@php
    if (!function_exists('convertNumberToWordsInventory')) {
        function convertNumberToWordsInventory($number) {
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null;
            $digits_length = strlen($no);
            $i = 0;
            $str = array();
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
            while ($i < $digits_length) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : '';
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : '';
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $paise = ($decimal > 0) ? " and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
            return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise . " Only";
        }
    }

    $customerName = $sale->customer_name ?? ($sale->student?->full_name ?? '—');
    $admissionNo = $sale->admission_no ?? ($sale->student?->admission_number ?? '—');
    $mobile = $sale->customer_mobile ?? ($sale->student?->phone ?? '—');
    $address = $sale->customer_address ?? '—';
    $invoiceNo = $sale->invoice_number ?? ('INV-' . $sale->id);
    $receiptNo = $sale->receipt_number ?? $invoiceNo;
    $saleDate = !empty($sale->sale_date) ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : date('d/m/Y');
    $paymentMode = is_object($sale) && isset($sale->payment_mode_label) ? $sale->payment_mode_label : (ucfirst($sale->payment_mode ?? 'Cash'));

    $items = $sale->items ?? collect();
    $totalMrp = (float)($sale->total_mrp ?? 0);
    $subTotal = (float)($sale->sub_total ?? 0);
    $totalTax = (float)($sale->total_tax ?? 0);
    $totalDiscount = (float)($sale->total_discount ?? 0);
    $grandTotal = (float)($sale->grand_total ?? ($subTotal - $totalDiscount + $totalTax));
    $paidAmount = (float)($sale->paid_amount ?? $grandTotal);
    $dueAmount = (float)($sale->due_amount ?? max(0, $grandTotal - $paidAmount));
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
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 24px;
            font-size: 12px;
        }
        .invoice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px 35px;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        /* Action Toolbar (Non-printable) */
        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-print {
            background: #1a3a4b;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #122b39;
            box-shadow: 0 4px 12px rgba(26, 58, 75, 0.25);
        }
        .btn-back {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }
        .btn-back:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        /* School Header (Minimal Template Style) */
        .school-header {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 16px;
            position: relative;
        }
        .school-logo {
            max-height: 65px;
            max-width: 65px;
            object-fit: contain;
            margin-right: 16px;
        }
        .school-info {
            text-align: center;
            flex-grow: 1;
        }
        .school-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .school-sub {
            font-size: 11.5px;
            color: #334155;
            margin: 2px 0;
            line-height: 1.3;
        }

        /* Metadata Grid */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11.5px;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: 700;
            color: #1e293b;
        }
        .meta-val {
            color: #0f172a;
        }

        /* Product Details Section Divider (Image 4 Style) */
        .section-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 18px 0 12px;
        }
        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #d97706;
            opacity: 0.7;
        }
        .section-title {
            padding: 0 16px;
            font-size: 12px;
            font-weight: 800;
            color: #d97706;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }

        /* Product Table (Matching Image 4) */
        .product-table-wrapper {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 18px;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            border: 1px solid #e2e8f0;
        }
        .product-table thead th {
            background-color: #1a3a4b;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 10px;
            text-align: right;
            border: 1px solid #2d4e61;
            white-space: nowrap;
        }
        .product-table thead th:first-child,
        .product-table thead th:nth-child(2) {
            text-align: left;
        }
        .product-table tbody td {
            padding: 8px 10px;
            text-align: right;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            white-space: nowrap;
        }
        .product-table tbody td:first-child,
        .product-table tbody td:nth-child(2) {
            text-align: left;
            font-weight: 600;
        }
        .product-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .product-table tfoot td {
            padding: 9px 10px;
            font-weight: 800;
            border-top: 2px solid #1a3a4b;
            border-bottom: 2px solid #1a3a4b;
            text-align: right;
            background-color: #f8fafc;
            color: #0f172a;
            white-space: nowrap;
        }
        .product-table tfoot td:first-child {
            text-align: left;
            font-size: 12.5px;
            font-weight: 800;
        }

        /* 6 Summary Badge Boxes (Matching Image 4) */
        .summary-badges-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 22px;
            margin-top: 10px;
        }
        .badge-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-top: 3.5px solid #d97706;
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .badge-box .badge-icon {
            font-size: 16px;
            margin-bottom: 4px;
        }
        .badge-box.icon-sub .badge-icon { color: #0284c7; }
        .badge-box.icon-disc .badge-icon { color: #ef4444; }
        .badge-box.icon-tax .badge-icon { color: #d97706; }
        .badge-box.icon-paid .badge-icon { color: #d97706; }
        .badge-box.icon-due .badge-icon { color: #d97706; }

        .badge-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #d97706;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .badge-val {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
        }

        /* Grand Total Box Solid Style */
        .badge-box.badge-grand-total {
            background: #1a3a4b;
            border-color: #1a3a4b;
            border-top: 3.5px solid #fbbf24;
            color: #ffffff;
        }
        .badge-box.badge-grand-total .badge-icon {
            color: #fbbf24;
        }
        .badge-box.badge-grand-total .badge-title {
            color: #fbbf24;
        }
        .badge-box.badge-grand-total .badge-val {
            color: #ffffff;
            font-size: 14.5px;
        }

        /* Footer & Signatures (Fee Minimal Template Style) */
        .invoice-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            margin-top: 10px;
            font-size: 11px;
            line-height: 1.5;
        }
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 35px;
            padding-top: 10px;
        }
        .sig-block {
            text-align: center;
            width: 140px;
        }
        .sig-line {
            border-top: 1px solid #0f172a;
            padding-top: 4px;
            font-weight: 700;
            color: #1e293b;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .invoice-wrapper {
                padding: 16px 14px;
            }
            .summary-badges-container {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
        }
        @media (max-width: 480px) {
            .summary-badges-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Print Media */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .invoice-wrapper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .summary-badges-container {
                grid-template-columns: repeat(6, 1fr) !important;
                gap: 5px !important;
            }
            .badge-box {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .badge-box.badge-grand-total {
                background: #1a3a4b !important;
                color: #ffffff !important;
            }
            .product-table thead th {
                background-color: #1a3a4b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Non-printable top action bar -->
    <div class="no-print-bar">
        <a href="{{ route('school.inventory.billing') }}" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Back to Product Cart
        </a>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('school.inventory.sales-history') }}" class="btn-action btn-back">
                <i class="fas fa-list"></i> Sales History
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>

    <!-- Invoice / Receipt Paper Layout (Matching Image 4 & Fee Minimal Template) -->
    <div class="invoice-wrapper">

        <!-- School Header -->
        <div class="school-header">
            @if(!empty($school->logo_url))
                <img src="{{ $school->logo_url }}" alt="School Logo" class="school-logo">
            @elseif(!empty($school->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->logo))
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($school->logo) }}" alt="School Logo" class="school-logo">
            @endif
            <div class="school-info">
                <h1 class="school-name">{{ $school->name ?? 'Demo International School' }}</h1>
                <p class="school-sub">{{ $school->address ?? '' }}</p>
                <p class="school-sub">
                    @if(!empty($school->email)) Email: {{ $school->email }} &nbsp;|&nbsp; @endif
                    Phone: {{ $school->phone ?? '—' }}
                </p>
            </div>
        </div>

        <!-- Student & Bill Metadata -->
        <table class="meta-table">
            <tr>
                <td style="width: 15%;" class="meta-label">Receipt No.:</td>
                <td style="width: 35%;" class="meta-val"><strong>{{ $receiptNo }}</strong></td>
                <td style="width: 18%;" class="meta-label">Receipt Date:</td>
                <td style="width: 32%;" class="meta-val">{{ $saleDate }}</td>
            </tr>
            <tr>
                <td class="meta-label">Customer/Student:</td>
                <td class="meta-val" style="text-transform: uppercase;"><strong>{{ $customerName }}</strong></td>
                <td class="meta-label">Admission No.:</td>
                <td class="meta-val">{{ $admissionNo }}</td>
            </tr>
            <tr>
                <td class="meta-label">Contact / Mobile:</td>
                <td class="meta-val">{{ $mobile }}</td>
                <td class="meta-label">Payment Mode:</td>
                <td class="meta-val"><strong>{{ $paymentMode }}</strong></td>
            </tr>
            @if(!empty($address) && $address !== '—')
            <tr>
                <td class="meta-label">Address:</td>
                <td class="meta-val" colspan="3">{{ $address }}</td>
            </tr>
            @endif
            @if(!empty($sale->reference_no))
            <tr>
                <td class="meta-label">Reference No.:</td>
                <td class="meta-val" colspan="3">{{ $sale->reference_no }}</td>
            </tr>
            @endif
        </table>

        <!-- PRODUCT DETAILS Section Divider (Image 4) -->
        <div class="section-divider">
            <span class="section-title">Product Details</span>
        </div>

        <!-- Product Table (11 Columns matching Image 4) -->
        <div class="product-table-wrapper">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>MRP</th>
                        <th>Price</th>
                        <th>Tax(%)</th>
                        <th>Quantity</th>
                        <th>Total MRP</th>
                        <th>Total Price</th>
                        <th>Total Tax</th>
                        <th>Discount</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $iName = $item->product_name ?? ($item['product_name'] ?? ($item['name'] ?? 'Item'));
                            $iSize = $item->size ?? ($item['size'] ?? 'Free');
                            $iMrp = (float)($item->mrp ?? ($item['mrp'] ?? 0));
                            $iPrice = (float)($item->price ?? ($item['price'] ?? 0));
                            $iTaxPct = (float)($item->tax_percent ?? ($item['tax_percent'] ?? ($item['tax'] ?? 0)));
                            $iQty = (int)($item->quantity ?? ($item['quantity'] ?? 1));
                            $iTotMrp = (float)($item->total_mrp ?? ($item['total_mrp'] ?? ($iMrp * $iQty)));
                            $iTotPrice = (float)($item->total_price ?? ($item['total_price'] ?? ($iPrice * $iQty)));
                            $iTotTax = (float)($item->total_tax ?? ($item['total_tax'] ?? ($item->tax_amount ?? 0)));
                            $iDiscount = (float)($item->discount ?? ($item['discount'] ?? 0));
                            $iTotAmount = (float)($item->total_amount ?? ($item['total_amount'] ?? ($iTotPrice - $iDiscount + $iTotTax)));
                        @endphp
                        <tr>
                            <td>{{ $iName }}</td>
                            <td>{{ $iSize }}</td>
                            <td>{{ number_format($iMrp, 2) }}</td>
                            <td>{{ number_format($iPrice, 2) }}</td>
                            <td>{{ number_format($iTaxPct, 2) }}</td>
                            <td>{{ $iQty }}</td>
                            <td>{{ number_format($iTotMrp, 2) }}</td>
                            <td>{{ number_format($iTotPrice, 2) }}</td>
                            <td>{{ number_format($iTotTax, 2) }}</td>
                            <td>{{ number_format($iDiscount, 2) }}</td>
                            <td><strong>{{ number_format($iTotAmount, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; color: #94a3b8; padding: 15px;">No products in this order.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($totalMrp, 2) }}</td>
                        <td>{{ number_format($subTotal, 2) }}</td>
                        <td>{{ number_format($totalTax, 2) }}</td>
                        <td>{{ number_format($totalDiscount, 2) }}</td>
                        <td>{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 6 Summary Badge Boxes (Exact Image 4 Replication) -->
        <div class="summary-badges-container">
            <!-- Box 1: Sub Total -->
            <div class="badge-box icon-sub">
                <i class="fas fa-hourglass-half badge-icon"></i>
                <div class="badge-title">Sub Total</div>
                <div class="badge-val">{{ number_format($subTotal, 2) }}</div>
            </div>

            <!-- Box 2: Total Discount -->
            <div class="badge-box icon-disc">
                <i class="fas fa-gift badge-icon"></i>
                <div class="badge-title">Total Discount</div>
                <div class="badge-val">{{ number_format($totalDiscount, 2) }}</div>
            </div>

            <!-- Box 3: Total Tax -->
            <div class="badge-box icon-tax">
                <i class="fas fa-indian-rupee-sign badge-icon"></i>
                <div class="badge-title">Total Tax</div>
                <div class="badge-val">{{ number_format($totalTax, 2) }}</div>
            </div>

            <!-- Box 4: Grand Total (Teal/Navy Solid Card) -->
            <div class="badge-box badge-grand-total">
                <i class="fas fa-circle-check badge-icon"></i>
                <div class="badge-title">Grand Total</div>
                <div class="badge-val">{{ number_format($grandTotal, 2) }}</div>
            </div>

            <!-- Box 5: Paid Amount -->
            <div class="badge-box icon-paid">
                <i class="fas fa-indian-rupee-sign badge-icon"></i>
                <div class="badge-title">Paid Amount</div>
                <div class="badge-val">{{ number_format($paidAmount, 2) }}</div>
            </div>

            <!-- Box 6: Due Amount -->
            <div class="badge-box icon-due">
                <i class="fas fa-indian-rupee-sign badge-icon"></i>
                <div class="badge-title">Due Amount</div>
                <div class="badge-val">{{ number_format($dueAmount, 2) }}</div>
            </div>
        </div>

        <!-- Footer / Words Amount / Signatures -->
        <div class="invoice-footer">
            <div style="margin-bottom: 5px;">
                Total Amount Paid: <strong>{{ convertNumberToWordsInventory($paidAmount) }}</strong>
            </div>
            <div style="margin-bottom: 5px;">
                Mode of Payment: <strong>{{ $paymentMode }}</strong>
                @if(!empty($sale->reference_no)) | Ref No: <strong>{{ $sale->reference_no }}</strong> @endif
            </div>
            @if(!empty($sale->remarks))
                <div>Remarks: <strong>{{ $sale->remarks }}</strong></div>
            @endif

            <div class="footer-signatures">
                <div style="font-size: 10px; color: #64748b; font-style: italic;">
                    * Computer Generated Inventory Invoice
                </div>
                <div class="sig-block">
                    <div class="sig-line">Authorized Signatory</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
