@php
    $isCancelled = (isset($invoice) && ($invoice->status === 'cancelled' || in_array($invoice->type, ['cancel_payment', 'cancel_refund']))) ||
                   ($type === 'payment' && isset($receipt) && $receipt->status === 'cancelled') || 
                   $items->contains(function($item) {
                       return isset($item->invoice_status) && $item->invoice_status === 'cancelled';
                   });

    $isBouncedChequeInvoice = false;
    if (isset($invoice)) {
        if ($invoice->status === 'bounced' || $invoice->type === 'bounced_cheque') {
            $isBouncedChequeInvoice = true;
        } else {
            $cheque = null;
            if (!empty($invoice->cheque_id_raw)) {
                $cheque = \App\Models\PendingCheque::find($invoice->cheque_id_raw);
            } else {
                $details = is_string($invoice->payment_details) ? json_decode($invoice->payment_details, true) : null;
                $chequeNumber = is_array($details) ? ($details['cheque_number'] ?? null) : null;
                if (!$chequeNumber && !empty($invoice->remarks) && preg_match('/(?:No:|Cheque No:)\s*([0-9]+)/i', $invoice->remarks, $matches)) {
                    $chequeNumber = $matches[1];
                }
                if ($chequeNumber) {
                    $cheque = \App\Models\PendingCheque::where('school_id', $invoice->school_id)
                        ->where('student_id', $invoice->student_id)
                        ->where('cheque_number', $chequeNumber)
                        ->first();
                }
            }
            if ($cheque && $cheque->status === 'bounced') {
                $isBouncedChequeInvoice = true;
            }
        }
    }
    if (!$isBouncedChequeInvoice && isset($receipt)) {
        $details = is_string($receipt->payment_details) ? json_decode($receipt->payment_details, true) : null;
        $chequeNumber = is_array($details) ? ($details['cheque_number'] ?? null) : null;
        if (!$chequeNumber && !empty($receipt->remarks) && preg_match('/(?:No:|Cheque No:)\s*([0-9]+)/i', $receipt->remarks, $matches)) {
            $chequeNumber = $matches[1];
        }
        if ($chequeNumber) {
            $cheque = \App\Models\PendingCheque::where('school_id', $receipt->school_id)
                ->where('student_id', $receipt->student_id)
                ->where('cheque_number', $chequeNumber)
                ->first();
            if ($cheque && $cheque->status === 'bounced') {
                $isBouncedChequeInvoice = true;
            }
        }
    }

    $isInvoiceOrPayment = ($type === 'invoice' || $type === 'payment' || (isset($invoice) && ($invoice->type === 'invoice' || $invoice->type === 'payment')));
    $showDetailed = ($isInvoiceOrPayment && ($config?->show_installment_components_on_invoice ?? false));

    // Decode payment details if available to fetch computed discount info
    $paymentDetails = isset($invoice) && !empty($invoice->payment_details) ? json_decode($invoice->payment_details, true) : null;
    if (!$paymentDetails && isset($receipt) && !empty($receipt->payment_details)) {
        $paymentDetails = json_decode($receipt->payment_details, true);
    }

    $discountInfo = isset($paymentDetails['instant_discount_info']) ? $paymentDetails['instant_discount_info'] : null;

    $totalAmount = 0;
    $amountPaidThisPayment = 0;
    $totalPaidAmount = 0;
    $discountValue = 0;
    $discountPercent = 0;
    $finalRemainingAmount = 0;
    $priorDiscountAmount = 0;
    $priorDiscountNames = [];

    $totalOriginalFallback = 0;
    foreach ($items as $item) {
        $totalOriginalFallback += floatval($item->amount);
    }

    if ($discountInfo) {
        $totalAmount = floatval($discountInfo['total_amount']);
        $amountPaidThisPayment = floatval($discountInfo['amount_paid_this_transaction'] ?? $amount);
        $totalPaidAmount = floatval($discountInfo['amount_paid_prior']) + $amountPaidThisPayment;
        $remainingBeforeDiscount = floatval($discountInfo['remaining_amount_before_discount']);
        $discountValue = floatval($discountInfo['discount_value']);
        $discountPercent = floatval($discountInfo['discount_percent'] ?? 0);
        if ($discountPercent <= 0 && $remainingBeforeDiscount > 0) {
            $discountPercent = round(($discountValue / $remainingBeforeDiscount) * 100, 2);
        }
        $finalRemainingAmount = floatval($discountInfo['final_remaining_amount']);
        $remainingAmount = $remainingBeforeDiscount;
        $priorDiscountAmount = floatval($discountInfo['prior_discount_amount'] ?? 0);
        $priorDiscountNamesRaw = $discountInfo['prior_discount_names'] ?? '';
        if (is_array($priorDiscountNamesRaw)) {
            $priorDiscountNames = $priorDiscountNamesRaw;
        } elseif (!empty($priorDiscountNamesRaw)) {
            $priorDiscountNames = array_filter(array_map('trim', explode(',', $priorDiscountNamesRaw)));
        }
    } else {
        // Fallback for legacy
        $feeIds = [];
        if ($type === 'combined' && isset($number)) {
            $feeIds = array_filter(array_map('trim', explode(',', $number)));
        } elseif (isset($paymentDetails)) {
            $compList = $paymentDetails['components'] ?? $paymentDetails;
            if (is_array($compList)) {
                foreach ($compList as $comp) {
                    if (isset($comp['student_fee_id'])) {
                        $feeIds[] = $comp['student_fee_id'];
                    }
                }
            }
        }
        
        $feesList = collect();
        if (!empty($feeIds)) {
            $feesList = \App\Models\StudentFee::withoutGlobalScopes()->whereIn('id', $feeIds)->get();
        }
        
        if ($feesList->isEmpty() && isset($invoice)) {
            $feesList = \App\Models\StudentFee::withoutGlobalScope('active')
                ->where('school_id', $invoice->school_id)
                ->where('student_id', $invoice->student_id)
                ->where('invoice_no', $invoice->invoice_number)
                ->get();
            if ($feesList->isEmpty()) {
                $feesList = \App\Models\StudentFee::withoutGlobalScope('active')
                    ->where('school_id', $invoice->school_id)
                    ->where('student_id', $invoice->student_id)
                    ->where('installment_no', $invoice->installment_no)
                    ->get();
            }
        }
        
        if ($feesList->isNotEmpty()) {
            $totalAmount = $feesList->sum(fn($f) => floatval($f->amount));
            
            $thisPaid = 0;
            $thisDiscount = 0;
            if (isset($invoice)) {
                $thisPaid = floatval($invoice->amount);
                $thisDiscount = floatval($invoice->discount_amount);
            } elseif (isset($receipt)) {
                $thisPaid = floatval($receipt->amount_paid);
                $thisDiscount = floatval($receipt->discount_amount);
            } else {
                $thisPaid = $feesList->sum('paid_amount');
                $thisDiscount = $feesList->sum('instant_discount_amount');
            }
            
            // Extract from details breakdown if available
            $thisPaidFromDetails = 0;
            $thisDiscountFromDetails = 0;
            if (isset($paymentDetails)) {
                $compList = $paymentDetails['components'] ?? $paymentDetails;
                if (is_array($compList)) {
                    foreach ($compList as $comp) {
                        $thisPaidFromDetails += floatval($comp['amount_paid'] ?? 0);
                        $thisDiscountFromDetails += floatval($comp['transaction_discount'] ?? ($comp['discount_amount'] ?? 0));
                    }
                }
            }
            if ($thisPaidFromDetails > 0) {
                $thisPaid = $thisPaidFromDetails;
            }
            if ($thisDiscountFromDetails > 0) {
                $thisDiscount = $thisDiscountFromDetails;
            }
            
            $amountPaidThisPayment = $thisPaid;
            $amountPaidPrior = max(0.00, $feesList->sum('paid_amount') - $thisPaid);
            $totalPaidAmount = $amountPaidPrior + $thisPaid;
            
            $priorDiscountAmount = max(0.00, $feesList->sum('instant_discount_amount') - $thisDiscount);
            $totalAmountDue = max(0.00, $totalAmount - $amountPaidPrior - $priorDiscountAmount);
            $remainingBeforeDiscount = max(0.00, $totalAmountDue - $thisPaid);
            
            $discountValue = $thisDiscount;
            if ($remainingBeforeDiscount > 0) {
                $discountPercent = round(($discountValue / $remainingBeforeDiscount) * 100, 2);
            } else {
                $discountPercent = 0;
            }
            $finalRemainingAmount = max(0.00, $remainingBeforeDiscount - $discountValue);
            $remainingAmount = $remainingBeforeDiscount;

            // Fetch prior discount names dynamically for the student
            $schoolId = isset($invoice) ? $invoice->school_id : (isset($receipt) ? $receipt->school_id : null);
            $studentId = isset($invoice) ? $invoice->student_id : (isset($receipt) ? $receipt->student_id : null);
            if ($schoolId && $studentId) {
                $session = \App\Models\AcademicSession::where('school_id', $schoolId)->where('is_current', true)->first();
                if ($session) {
                    $sib = \App\Models\Student::where('school_id', $schoolId)->find($studentId);
                    if ($sib) {
                        $discounts = \App\Models\FeeDiscount::where('school_id', $schoolId)
                            ->where('academic_session_id', $session->id)
                            ->get()
                            ->filter(function ($d) use ($sib) {
                                return \App\Http\Controllers\School\FeeManagementController::isDiscountApplicableForStudent($d, $sib);
                            });
                        $priorDiscountNames = $discounts->pluck('name')->unique()->toArray();
                    }
                }
            }
        } else {
            // Ultimate fallback
            $thisPaid = isset($invoice) ? floatval($invoice->amount) : (isset($receipt) ? floatval($receipt->amount_paid) : 0);
            $thisDiscount = isset($invoice) ? floatval($invoice->discount_amount) : (isset($receipt) ? floatval($receipt->discount_amount) : 0);
            
            $totalAmount = $totalOriginalFallback;
            $amountPaidThisPayment = $thisPaid;
            $totalPaidAmount = $thisPaid;
            $remainingBeforeDiscount = max(0.00, $totalAmount - $thisPaid);
            $discountValue = $thisDiscount;
            if ($remainingBeforeDiscount > 0) {
                $discountPercent = round(($discountValue / $remainingBeforeDiscount) * 100, 2);
            } else {
                $discountPercent = 0;
            }
            $finalRemainingAmount = max(0.00, $remainingBeforeDiscount - $discountValue);
            $remainingAmount = $remainingBeforeDiscount;
        }
    }

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
                    $isItemMisc = !empty($item->is_misc);
                    
                    $sf = null;
                    if (isset($item->student_fee_id)) {
                        $sf = \App\Models\StudentFee::withoutGlobalScopes()->find($item->student_fee_id);
                    }
                    if (!$sf && isset($student) && isset($item->installment_no)) {
                        $sf = \App\Models\StudentFee::withoutGlobalScopes()
                            ->where('student_id', $student->id)
                            ->where('installment_no', $item->installment_no)
                            ->first();
                    }

                    if ($sf && floatval($sf->fine_amount_applied) > 0) {
                        $F = floatval($sf->fine_amount_applied);
                        $P_trans = floatval($item->paid_amount);
                        $P_total = floatval($sf->paid_amount);
                        $P_prior = max(0.00, $P_total - $P_trans);
                        
                        $F_paid_total = min($F, $P_total);
                        $F_paid_prior = min($F, $P_prior);
                        $F_paid_trans = max(0.00, $F_paid_total - $F_paid_prior);
                        $Base_paid_trans = max(0.00, $P_trans - $F_paid_trans);

                        $renderItems->push((object)[
                            'student_fee_id' => $item->student_fee_id ?? null,
                            'installment_no' => $item->installment_no ?? null,
                            'description' => $item->description,
                            'amount' => $item->amount,
                            'misc_amount' => $isItemMisc ? $item->amount : 0,
                            'instant_discount_amount' => $item->instant_discount_amount,
                            'paid_amount' => $Base_paid_trans,
                            'is_misc' => $isItemMisc,
                        ]);

                        $renderItems->push((object)[
                            'installment_no' => $item->installment_no ?? null,
                            'description' => 'Late Fine - Installment ' . ($item->installment_no ?? 1),
                            'amount' => $F,
                            'misc_amount' => 0,
                            'instant_discount_amount' => 0,
                            'paid_amount' => $F_paid_trans,
                            'total_paid_till_now' => $F_paid_total,
                            'is_misc' => false,
                            'is_fine' => true,
                        ]);
                    } else {
                        $renderItems->push((object)[
                            'student_fee_id' => $item->student_fee_id ?? null,
                            'installment_no' => $item->installment_no ?? null,
                            'description' => $item->description,
                            'amount' => $item->amount,
                            'misc_amount' => $isItemMisc ? $item->amount : 0,
                            'instant_discount_amount' => $item->instant_discount_amount,
                            'paid_amount' => $item->paid_amount,
                            'is_misc' => $isItemMisc,
                        ]);
                    }
                }
            } else {
                $grouped = $otherItems->groupBy('installment_no');
                foreach ($grouped as $instNo => $group) {
                    $totalAmount = $group->filter(fn($x) => empty($x->is_misc))->sum('amount');
                    $miscAmount = $group->filter(fn($x) => !empty($x->is_misc))->sum('amount');
                    $totalDiscount = $group->sum('instant_discount_amount');
                    $totalPaid = $group->sum(function($f) use ($type) {
                        if (isset($f->paid_amount)) return $f->paid_amount;
                        return $type === 'payment' ? $f->paid_amount : ($f->amount - $f->instant_discount_amount);
                    });
                    
                    // Look up fine for this installment group
                    $fineAmount = 0;
                    $finePaidTrans = 0;
                    $finePaidTotalSum = 0;

                    foreach ($group as $item) {
                        $sf = null;
                        if (isset($item->student_fee_id)) {
                            $sf = \App\Models\StudentFee::withoutGlobalScopes()->find($item->student_fee_id);
                        }
                        if (!$sf && isset($student)) {
                            $sf = \App\Models\StudentFee::withoutGlobalScopes()
                                ->where('student_id', $student->id)
                                ->where('installment_no', $instNo)
                                ->first();
                        }
                        if ($sf && floatval($sf->fine_amount_applied) > 0) {
                            $F = floatval($sf->fine_amount_applied);
                            $P_trans = floatval($item->paid_amount);
                            $P_total = floatval($sf->paid_amount);
                            $P_prior = max(0.00, $P_total - $P_trans);

                            $F_paid_total = min($F, $P_total);
                            $F_paid_prior = min($F, $P_prior);
                            $F_paid_trans = max(0.00, $F_paid_total - $F_paid_prior);

                            $fineAmount += $F;
                            $finePaidTrans += $F_paid_trans;
                            $finePaidTotalSum += $F_paid_total;
                        }
                    }

                    $basePaidTrans = max(0.00, $totalPaid - $finePaidTrans);

                    $renderItems->push((object)[
                        'installment_no' => $instNo,
                        'description' => 'Installment ' . $instNo,
                        'amount' => $totalAmount,
                        'misc_amount' => $miscAmount,
                        'instant_discount_amount' => $totalDiscount,
                        'paid_amount' => $basePaidTrans,
                    ]);

                    if ($fineAmount > 0) {
                        $renderItems->push((object)[
                            'installment_no' => $instNo,
                            'description' => 'Late Fine - Installment ' . $instNo,
                            'amount' => $fineAmount,
                            'misc_amount' => 0,
                            'instant_discount_amount' => 0,
                            'paid_amount' => $finePaidTrans,
                            'total_paid_till_now' => $finePaidTotalSum,
                            'is_misc' => false,
                            'is_fine' => true,
                        ]);
                    }
                }
            }
        }

        // 2. Render transport items - each item already has the correct description with month from the controller
        if ($transportItems->isNotEmpty() && isset($student)) {
            $pickFare = (float) ($student->transport_pick_fare ?? 0);
            $dropFare = (float) ($student->transport_drop_fare ?? 0);

            // Group by installment_no so we can render pick+drop per installment month
            $byInstallment = $transportItems->groupBy('installment_no');

            foreach ($byInstallment as $instNo => $instItems) {
                foreach ($instItems as $item) {
                    $renderItems->push((object)[
                        'description' => $item->description,
                        'amount'      => $item->amount,
                        'misc_amount' => 0.00,
                        'instant_discount_amount' => $item->instant_discount_amount,
                        'paid_amount' => $item->paid_amount,
                    ]);
                }
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
                'misc_amount' => 0.00,
                'instant_discount_amount' => 0.00,
                'paid_amount' => isset($item->paid_amount) ? $item->paid_amount : $item->amount,
            ]);
        }
    }
    
    // Consolidate all Late Fine items into a single row positioned immediately above Total
    $nonFineRenderItems = collect();
    $fineRenderItems = collect();

    foreach ($renderItems as $item) {
        $desc = strtolower($item->description ?? '');
        if (!empty($item->is_fine) || strpos($desc, 'late fine') !== false) {
            $fineRenderItems->push($item);
        } else {
            $nonFineRenderItems->push($item);
        }
    }

    if ($fineRenderItems->isNotEmpty()) {
        $consolidatedFine = (object)[
            'student_fee_id' => null,
            'installment_no' => null,
            'description' => 'Late Fine',
            'amount' => $fineRenderItems->sum(fn($i) => floatval($i->amount ?? 0)),
            'misc_amount' => 0.00,
            'instant_discount_amount' => $fineRenderItems->sum(fn($i) => floatval($i->instant_discount_amount ?? 0)),
            'paid_amount' => $fineRenderItems->sum(fn($i) => floatval($i->paid_amount ?? 0)),
            'total_paid_till_now' => $fineRenderItems->sum(fn($i) => isset($i->total_paid_till_now) ? floatval($i->total_paid_till_now) : floatval($i->paid_amount ?? 0)),
            'is_misc' => false,
            'is_fine' => true,
        ];
        $nonFineRenderItems->push($consolidatedFine);
    }

    $renderItems = $nonFineRenderItems;

    // Check if there is any miscellaneous fee in the items
    $hasMiscFees = false;
    foreach ($renderItems as $item) {
        if (!empty($item->misc_amount) && $item->misc_amount > 0) {
            $hasMiscFees = true;
        }
    }
@endphp
@php
    if (!function_exists('convertNumberToWordsHelper')) {
        function convertNumberToWordsHelper($number) {
            $no = (int)floor($number);
            $decimal = round($number - $no, 2) * 100;
            
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
                    $hundred = ($counter == 1 && isset($str[0]) && $str[0]) ? ' AND ' : null;
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
                    $paise = ' AND ' . $words[$paiseVal] . ' PAISE';
                } else {
                    $paise = ' AND ' . $words[floor($paiseVal / 10) * 10] . ' ' . $words[$paiseVal % 10] . ' PAISE';
                }
            }
            
            return ($result ? $result : 'ZERO') . $paise . ' ONLY';
        }
    }

    // Resolve website dynamically
    $website = 'https://www.educorerp.com';

    // Resolve board name dynamically
    $boardName = !empty($school->udise_data['board_name']) ? $school->udise_data['board_name'] : ($school->school_type ?? 'CBSE');

    // Resolve transaction details and payment mode dynamically
    $transactionId = null;
    if (isset($invoice)) {
        $receiptObj = \App\Models\FeeReceipt::withoutGlobalScope('active')
            ->where('school_id', $school->id)
            ->where('receipt_number', $invoice->invoice_number)
            ->first();
        if ($receiptObj) {
            $transactionId = $receiptObj->transaction_id;
        }
        if (!$transactionId && preg_match('/Transaction ID:\s*([^\s]+)/i', $invoice->remarks, $matches)) {
            $transactionId = $matches[1];
        }
    } elseif (isset($receipt)) {
        $transactionId = $receipt->transaction_id;
    }
    
    $paymentModeFormatted = str_replace('_', ' ', $mode);
    $paymentModeFormatted = ucwords(strtolower($paymentModeFormatted));
    $transactionDetails = '';
    if ($transactionId) {
        $transactionDetails = '( ' . $transactionId . ' )';
    } elseif (!empty($bankName)) {
        $transactionDetails = '( ' . $bankName . ($bankDate ? ' ' . \Carbon\Carbon::parse($bankDate)->format('d-M-Y') : '') . ' )';
    }
    $paymentModeDisplay = $paymentModeFormatted . ($transactionDetails ? ' ' . $transactionDetails : '');

    // Resolve installment label
    $installmentLabels = [];
    foreach ($items as $item) {
        if (isset($item->installment_no)) {
            $instNo = $item->installment_no;
            $label = null;
            $sf = \App\Models\StudentFee::withoutGlobalScopes()
                ->where('student_id', $student->id)
                ->where('installment_no', $instNo)
                ->first();
            if ($sf) {
                if ($sf->feeSchedule && is_array($sf->feeSchedule->installments)) {
                    $instIdx = $instNo - 1;
                    $label = $sf->feeSchedule->installments[$instIdx]['name'] ?? null;
                } elseif ($sf->transportFeeSchedule && is_array($sf->transportFeeSchedule->installments)) {
                    $instIdx = $instNo - 1;
                    $label = $sf->transportFeeSchedule->installments[$instIdx]['name'] ?? null;
                }
            }
            if (!$label) {
                if ($instNo >= 1 && $instNo <= 12) {
                    $label = \Carbon\Carbon::create()->month($instNo)->format('F');
                } else {
                    $label = 'Installment ' . $instNo;
                }
            }
            $installmentLabels[] = $label;
        }
    }
    $installmentLabel = !empty($installmentLabels) ? implode(', ', array_unique($installmentLabels)) : '—';

    // Resolve Fee Type dynamically
    $feeTypeDisplay = '';
    $hasTransport = false;
    foreach ($items as $item) {
        $desc = strtolower($item->description ?? '');
        if (strpos($desc, 'transport') !== false || strpos($desc, 'bus') !== false) {
            $hasTransport = true;
        }
    }
    if ($hasTransport) {
        $feeTypeDisplay = '-Transport Fee';
    } else {
        $feeTypeDisplay = 'School Fees';
    }
@endphp
<div class="slip-container" style="position: relative; overflow: hidden; padding: 15px 0; font-family: Arial, Helvetica, sans-serif;">
@if($isCancelled)
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); font-size: 80px; color: rgba(239, 68, 68, 0.16); border: 8px solid rgba(239, 68, 68, 0.16); padding: 15px 40px; border-radius: 16px; font-weight: 900; letter-spacing: 6px; pointer-events: none; z-index: 999; text-transform: uppercase; white-space: nowrap;">
        CANCELLED
    </div>
@endif
@if($isBouncedChequeInvoice)
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); font-size: 80px; color: rgba(220, 38, 38, 0.16); border: 8px solid rgba(220, 38, 38, 0.16); padding: 15px 40px; border-radius: 16px; font-weight: 900; letter-spacing: 6px; pointer-events: none; z-index: 999; text-transform: uppercase; white-space: nowrap;">
        BOUNCED
    </div>
@endif

    <!-- School Header -->
    <div class="school-header" style="display: flex; align-items: center; justify-content: center; position: relative; margin-bottom: 20px;">
        @if(!empty($school->logo) && Storage::disk('public')->exists($school->logo))
            <div class="school-logo-container" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); display: flex; align-items: center; justify-content: center;">
                <img src="{{ Storage::disk('public')->url($school->logo) }}" alt="School Logo" style="max-height: 80px; max-width: 80px; object-fit: contain; display: block;">
            </div>
        @endif
        <div style="text-align: center; max-width: 85%;">
            <h1 class="school-name" style="font-size: 26px; font-weight: bold; margin: 0; color: #000; font-family: Arial, sans-serif;">{{ $school->name }}</h1>
            <p class="school-details" style="font-size: 14px; margin: 4px 0 0 0; color: #000; font-weight: bold;">{{ $school->address }}</p>
            @if(!empty($school->udise_data['affiliation_number']))
                <p class="school-details" style="font-size: 14px; margin: 4px 0 0 0; color: #000; font-weight: bold;">School Affiliation No : {{ $school->udise_data['affiliation_number'] }}</p>
            @endif
            <p class="school-details" style="font-size: 14px; margin: 4px 0 0 0; color: #000; font-weight: bold;">
                Website:{{ $website }} | Contact:{{ $school->phone }}
            </p>
        </div>
    </div>

    <!-- Title and Copy Badge -->
    <h2 class="doc-title" style="text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0 2px 0; color: #000;">{{ strtoupper($title) }}</h2>
    <div class="copy-label-badge" style="text-align: center; font-size: 10px; font-weight: bold; color: #854d0e; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px;">
        {{ $copy_label }}
    </div>
    @if($isBouncedChequeInvoice)
    <div style="text-align: center; margin: 5px 0 15px 0;">
        <span style="display: inline-block; border: 3px solid #dc2626; color: #dc2626; padding: 6px 20px; font-size: 24px; font-weight: 900; text-transform: uppercase; letter-spacing: 3px; border-radius: 6px; transform: rotate(-3deg); background: #fff5f5;">
            BOUNCED
        </span>
    </div>
    @endif

    <!-- Metadata Fields (Two Columns) -->
    <div class="metadata-box" style="display: flex; justify-content: space-between; font-size: 14px; line-height: 1.6; margin-bottom: 20px; color: #000;">
        <div style="width: 48%;">
            <table style="border-collapse: collapse; border: none; width: 100%;">
                <tr style="border: none;">
                    @if($config?->details_receipt_no ?? true)
                        <td style="padding: 2px 0; border: none; width: 30%; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Receipt No:</td>
                        <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>
                            @if(isset($invoice))
                                {{ $invoice->receipt_number }}
                            @elseif(isset($receipt))
                                {{ $receipt->receipt_number }}
                            @else
                                {{ $number }}
                            @endif
                        </strong></td>
                    @else
                        <td style="padding: 2px 0; border: none; width: 30%; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Bill No :</td>
                        <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ $number }}</strong></td>
                    @endif
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Name:</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ strtoupper($student->full_name) }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Father's Name :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ strtoupper($student->father_name ?? '—') }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Mother's Name :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ strtoupper($student->mother_name ?? '—') }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Board :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ strtoupper($boardName) }}</strong></td>
                </tr>
            </table>
        </div>
        <div style="width: 48%;">
            <table style="border-collapse: collapse; border: none; width: 100%;">
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; width: 35%; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Date :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Class :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ strtoupper(optional($student->class)->name) }}{{ optional($student->section)->name ? '-' . strtoupper(optional($student->section)->name) : '' }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Installment:</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ $installmentLabel }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Payment Mode :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ $paymentModeDisplay }}</strong></td>
                </tr>
                <tr style="border: none;">
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;">Fee Type :</td>
                    <td style="padding: 2px 0; border: none; color: #000; font-size: 14px; font-family: Arial, sans-serif; vertical-align: top;"><strong>{{ $feeTypeDisplay }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Particulars Table -->
    <table class="particulars-table" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; margin-bottom: 25px; border: 1.5px solid #c09553;">
        <thead>
            <tr style="background-color: #faf2e2; border-bottom: 1.5px solid #c09553;">
                <th style="padding: 10px; text-align: left; font-weight: bold; border-right: 1.5px solid #c09553; color: #000; font-size: 14px;">HEAD NAME</th>
                <th style="padding: 10px; text-align: right; font-weight: bold; border-right: 1.5px solid #c09553; color: #000; width: 15%; font-size: 14px;">ACTUAL AMOUNT</th>
                <th style="padding: 10px; text-align: right; font-weight: bold; border-right: 1.5px solid #c09553; color: #000; width: 18%; font-size: 14px;">CONCESSION AMOUNT</th>
                <th style="padding: 10px; text-align: right; font-weight: bold; border-right: 1.5px solid #c09553; color: #000; width: 15%; font-size: 14px;">PAID AMOUNT</th>
                <th style="padding: 10px; text-align: right; font-weight: bold; color: #000; width: 20%; font-size: 14px;">BALANCE</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalActual = 0;
                $totalConcession = 0;
                $totalDue = 0;
                $totalLastPaid = 0;
                $totalPaid = 0;
            @endphp
            @foreach($renderItems as $item)
                @php
                    $sf = null;
                    if (empty($item->is_fine) && isset($item->student_fee_id)) {
                        $sf = \App\Models\StudentFee::withoutGlobalScopes()->find($item->student_fee_id);
                    }
                    
                    if (empty($item->is_fine) && !$sf && isset($student) && isset($item->installment_no)) {
                        $installmentFees = \App\Models\StudentFee::withoutGlobalScopes()
                            ->where('student_id', $student->id)
                            ->where('installment_no', $item->installment_no)
                            ->get();
                        
                        if ($installmentFees->isNotEmpty()) {
                            $firstFee = $installmentFees->first();
                            $sf = clone $firstFee;
                            $sf->amount = $installmentFees->sum('amount');
                            $sf->instant_discount_amount = $installmentFees->sum('instant_discount_amount');
                            $sf->paid_amount = $installmentFees->sum('paid_amount');
                            $sf->fine_amount_applied = $installmentFees->sum('fine_amount_applied');
                        }
                    }

                    if ($sf && empty($item->is_fine)) {
                        if (floatval($sf->fine_amount_applied) > 0) {
                            $sf = clone $sf;
                            $sfFine = floatval($sf->fine_amount_applied);
                            $sfPaid = floatval($sf->paid_amount);
                            $finePaidTotal = min($sfFine, $sfPaid);
                            $sf->paid_amount = max(0.00, $sfPaid - $finePaidTotal);
                        }
                    }
                    
                    $actualVal = $sf ? floatval($sf->amount) : floatval($item->amount);
                    $concessionVal = $sf ? floatval($sf->instant_discount_amount) : floatval($item->instant_discount_amount ?? 0);
                    if ($isCancelled && isset($item->instant_discount_amount)) {
                        $concessionVal = floatval($item->instant_discount_amount);
                    }
                    
                    $isRefund = (isset($invoice) && ($invoice->type === 'refund' || $invoice->type === 'cancel_refund')) || ($type === 'refund');
                    
                    if ($isRefund) {
                        $actualVal = floatval($item->amount);
                        $concessionVal = 0.00;
                        $lastPaidVal = 0.00;
                        $paidVal = floatval($item->paid_amount);
                    } else {
                        $paidVal = floatval($item->paid_amount);
                        $totalPaidTillNow = isset($item->total_paid_till_now) ? floatval($item->total_paid_till_now) : ($sf ? floatval($sf->paid_amount) : $paidVal);
                        if ($isCancelled && $sf) {
                            $totalPaidTillNow = floatval($sf->paid_amount) + $paidVal;
                        }
                        $lastPaidVal = max(0.00, $totalPaidTillNow - $paidVal);
                    }
                    
                    $totalActual += $actualVal;
                    $totalConcession += $concessionVal;
                    $dueVal = max(0.00, $actualVal - $concessionVal - $lastPaidVal - $paidVal);
                    $totalDue += $dueVal;
                    $totalLastPaid += $lastPaidVal;
                    $totalPaid += $paidVal;
                @endphp
                <tr style="border-bottom: 1.5px solid #c09553;">
                    <td style="padding: 10px; text-align: left; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; vertical-align: middle;">
                        {{ ($sf && $sf->misc_fee_id && $sf->miscFee) ? $sf->miscFee->fee_head_name : $item->description }}
                    </td>
                    <td style="padding: 10px; text-align: right; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; vertical-align: middle;">
                        {{ number_format($actualVal, 2, '.', '') }}
                    </td>
                    <td style="padding: 10px; text-align: right; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; vertical-align: middle;">
                        {{ number_format($concessionVal, 2, '.', '') }}
                    </td>
                    <td style="padding: 10px; text-align: right; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; font-weight: bold; vertical-align: middle;">
                        {{ number_format($paidVal, 2, '.', '') }}
                    </td>
                    <td style="padding: 10px; text-align: right; color: #000; font-size: 14px; vertical-align: middle;">
                        {{ number_format($dueVal, 2, '.', '') }}
                    </td>
                </tr>
            @endforeach
            
            <!-- Total Row -->
            <tr style="background-color: #ffffff; font-weight: bold;">
                <td style="padding: 10px; text-align: left; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; vertical-align: middle;">Total</td>
                <td style="padding: 10px; text-align: right; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; vertical-align: middle;">
                    {{ number_format($totalActual, 2, '.', '') }}
                </td>
                <td style="padding: 10px; text-align: right; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; vertical-align: middle;">
                    {{ number_format($totalConcession, 2, '.', '') }}
                </td>
                <td style="padding: 10px; text-align: right; border-right: 1.5px solid #c09553; color: #000; font-size: 14px; font-weight: bold; vertical-align: middle;">
                    {{ number_format($totalPaid, 2, '.', '') }}
                </td>
                <td style="padding: 10px; text-align: right; color: #000; font-size: 14px; font-weight: bold; vertical-align: middle;">
                    {{ number_format($totalDue, 2, '.', '') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer Area (Amount in words & Total Paid & Cashier) -->
    <div class="footer-area" style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 15px;">
        <div class="amount-in-words" style="width: 60%; font-size: 14px; line-height: 1.4; color: #000; padding-top: 10px; font-family: Arial, sans-serif;">
            Amount(in words): <strong>{{ convertNumberToWordsHelper($totalPaid) }}</strong>
        </div>
        <div class="cashier-section" style="width: 35%; display: flex; flex-direction: column; align-items: stretch;">
            <div class="total-paid-badge" style="background-color: #c09553; color: #000; padding: 12px 15px; font-size: 15px; font-weight: bold; text-align: center; width: 100%; box-sizing: border-box; margin-bottom: 25px; font-family: Arial, sans-serif;">
                Total Paid : {{ number_format($totalPaid, 2, '.', '') }}
            </div>
            <div class="cashier-label" style="font-size: 15px; font-weight: bold; color: #000; margin-top: 5px; font-family: Arial, sans-serif; text-align: center;">
                Cashier
            </div>
        </div>
    </div>
</div>
