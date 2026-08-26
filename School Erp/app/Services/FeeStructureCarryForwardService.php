<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\ClassWiseFee;
use App\Models\FeeComponent;
use App\Models\FeeDiscount;
use App\Models\FeeFine;
use App\Models\FeeSchedule;
use App\Models\MiscFee;
use App\Models\TransportFeeSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeStructureCarryForwardService
{
    /**
     * Carry forward all fee structure configurations from source session to target session.
     *
     * @param int $schoolId
     * @param int|null $sourceSessionId
     * @param int $targetSessionId
     * @param bool $force
     * @return array
     */
    public static function carryForward(int $schoolId, ?int $sourceSessionId, int $targetSessionId, bool $force = false): array
    {
        $targetSession = AcademicSession::where('school_id', $schoolId)->find($targetSessionId);
        if (!$targetSession) {
            return [
                'success' => false,
                'message' => 'Target academic session not found.',
                'counts' => []
            ];
        }

        // If source session not explicitly provided, find the most relevant previous academic session
        if (!$sourceSessionId) {
            $sourceSession = AcademicSession::where('school_id', $schoolId)
                ->where('id', '!=', $targetSessionId)
                ->where('start_date', '<=', $targetSession->start_date)
                ->orderBy('start_date', 'desc')
                ->first();

            if (!$sourceSession) {
                $sourceSession = AcademicSession::where('school_id', $schoolId)
                    ->where('id', '!=', $targetSessionId)
                    ->orderBy('id', 'desc')
                    ->first();
            }

            $sourceSessionId = $sourceSession ? $sourceSession->id : null;
        } else {
            $sourceSession = AcademicSession::where('school_id', $schoolId)->find($sourceSessionId);
        }

        if (!$sourceSession || $sourceSession->id === $targetSession->id) {
            return [
                'success' => true,
                'message' => 'No distinct source academic session found to carry forward from.',
                'counts' => []
            ];
        }

        // Guard against duplicate creation if target session already has fee structure records
        if (!$force) {
            $hasExistingRecords = FeeSchedule::where('school_id', $schoolId)->where('academic_session_id', $targetSessionId)->exists()
                || FeeComponent::where('school_id', $schoolId)->where('academic_session_id', $targetSessionId)->exists()
                || FeeDiscount::where('school_id', $schoolId)->where('academic_session_id', $targetSessionId)->exists()
                || MiscFee::where('school_id', $schoolId)->where('academic_session_id', $targetSessionId)->exists()
                || FeeFine::where('school_id', $schoolId)->where('academic_session_id', $targetSessionId)->exists();

            if ($hasExistingRecords) {
                return [
                    'success' => true,
                    'message' => 'Fee structure already exists in the target academic year. Carry-forward skipped to prevent duplication.',
                    'counts' => []
                ];
            }
        }

        return DB::transaction(function () use ($schoolId, $sourceSession, $targetSession) {
            $scheduleMap = [];   // [old_id => new_id]
            $componentMap = [];  // [old_id => new_id]
            $fineMap = [];       // [old_id => new_id]

            $counts = [
                'schedules' => 0,
                'components' => 0,
                'discounts' => 0,
                'misc_fees' => 0,
                'fines' => 0,
                'class_wise_fees' => 0,
                'transport_schedules' => 0,
            ];

            $srcStart = Carbon::parse($sourceSession->start_date);
            $tarStart = Carbon::parse($targetSession->start_date);
            $tarEnd   = Carbon::parse($targetSession->end_date);

            $diffMonths = ($tarStart->year - $srcStart->year) * 12 + ($tarStart->month - $srcStart->month);

            // =========================================================================
            // 1. CARRY FORWARD FEE SCHEDULES (fine_id initially null to resolve cycle)
            // =========================================================================
            $sourceSchedules = FeeSchedule::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceSchedules as $srcSched) {
                $rawInstallments = is_array($srcSched->installments)
                    ? $srcSched->installments
                    : json_decode($srcSched->installments ?? '[]', true);

                $newInstallments = [];
                if (!empty($rawInstallments)) {
                    foreach ($rawInstallments as $inst) {
                        $instStart = isset($inst['start_date']) ? Carbon::parse($inst['start_date'])->addMonths($diffMonths) : $tarStart->copy();
                        $instEnd   = isset($inst['end_date'])   ? Carbon::parse($inst['end_date'])->addMonths($diffMonths)   : $tarEnd->copy();
                        $instDue   = isset($inst['due_date'])   ? Carbon::parse($inst['due_date'])->addMonths($diffMonths)   : $instEnd->copy();

                        // Clamp within target session bounds
                        if ($instStart->lt($tarStart)) $instStart = $tarStart->copy();
                        if ($instStart->gt($tarEnd))   $instStart = $tarEnd->copy();
                        if ($instEnd->lt($instStart))  $instEnd   = $instStart->copy();
                        if ($instEnd->gt($tarEnd))     $instEnd   = $tarEnd->copy();
                        if ($instDue->lt($instStart))  $instDue   = $instStart->copy();
                        if ($instDue->gt($tarEnd))     $instDue   = $tarEnd->copy();

                        $name = $inst['name'] ?? ('Installment ' . ($inst['installment_no'] ?? 1));
                        $name = self::updateInstallmentNameYear($name, $instStart, $tarStart, $tarEnd);

                        $newInstallments[] = [
                            'installment_no' => (int) ($inst['installment_no'] ?? (count($newInstallments) + 1)),
                            'name'           => $name,
                            'start_date'     => $instStart->toDateString(),
                            'end_date'       => $instEnd->toDateString(),
                            'due_date'       => $instDue->toDateString(),
                            'grace_days'     => (int) ($inst['grace_days'] ?? 5),
                        ];
                    }

                    // Validate installments; fallback to clean distributor generation if dates overlap/fail
                    $validationError = FeeInstallmentDistributor::validateInstallments($newInstallments, $targetSession);
                    if ($validationError) {
                        $newInstallments = FeeInstallmentDistributor::generate(
                            $tarStart,
                            $tarEnd,
                            $srcSched->installment_type ?: 'custom',
                            $srcSched->no_of_installments ?: count($rawInstallments)
                        );
                    }
                } else {
                    $newInstallments = FeeInstallmentDistributor::generate(
                        $tarStart,
                        $tarEnd,
                        $srcSched->installment_type ?: 'monthly',
                        $srcSched->no_of_installments ?: 12
                    );
                }

                $newSched = FeeSchedule::create([
                    'school_id'           => $schoolId,
                    'academic_session_id' => $targetSession->id,
                    'classes'             => $srcSched->classes,
                    'sections'            => $srcSched->sections,
                    'no_of_installments'  => count($newInstallments),
                    'name'                => $srcSched->name,
                    'start_date'          => $targetSession->start_date,
                    'end_date'            => $targetSession->end_date,
                    'installment_type'    => $srcSched->installment_type,
                    'installments'        => $newInstallments,
                    'fine_id'             => null, // Resolved in step 4
                ]);

                $scheduleMap[$srcSched->id] = $newSched->id;
                $counts['schedules']++;
            }

            // =========================================================================
            // 2. CARRY FORWARD FEE COMPONENTS
            // =========================================================================
            $sourceComponents = FeeComponent::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceComponents as $srcComp) {
                $newSchedId = ($srcComp->fee_schedule_id && isset($scheduleMap[$srcComp->fee_schedule_id]))
                    ? $scheduleMap[$srcComp->fee_schedule_id]
                    : null;

                $newComp = FeeComponent::create([
                    'school_id'           => $schoolId,
                    'academic_session_id' => $targetSession->id,
                    'fee_schedule_id'     => $newSchedId,
                    'head_name'           => $srcComp->head_name,
                    'component_name'      => $srcComp->component_name,
                    'admission_type'      => $srcComp->admission_type,
                    'gender'              => $srcComp->gender,
                    'fee_category_id'     => $srcComp->fee_category_id,
                ]);

                $componentMap[$srcComp->id] = $newComp->id;
                $counts['components']++;
            }

            // =========================================================================
            // 3. CARRY FORWARD FEE FINES
            // =========================================================================
            $sourceFines = FeeFine::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceFines as $srcFine) {
                $newCompId = ($srcFine->fee_component_id && isset($componentMap[$srcFine->fee_component_id]))
                    ? $componentMap[$srcFine->fee_component_id]
                    : null;

                $newFine = FeeFine::create([
                    'school_id'           => $schoolId,
                    'academic_session_id' => $targetSession->id,
                    'fee_component_id'    => $newCompId,
                    'name'                => $srcFine->name,
                    'fine_type'           => $srcFine->fine_type,
                    'fine_amount'         => $srcFine->fine_amount,
                    'default_grace_days'  => $srcFine->default_grace_days ?? 5,
                    'status'              => $srcFine->status ?? true,
                ]);

                $fineMap[$srcFine->id] = $newFine->id;
                $counts['fines']++;
            }

            // =========================================================================
            // 4. UPDATE FEE SCHEDULES WITH REMAPPED FINE IDs
            // =========================================================================
            foreach ($sourceSchedules as $srcSched) {
                if ($srcSched->fine_id && isset($fineMap[$srcSched->fine_id]) && isset($scheduleMap[$srcSched->id])) {
                    FeeSchedule::where('id', $scheduleMap[$srcSched->id])
                        ->update(['fine_id' => $fineMap[$srcSched->fine_id]]);
                }
            }

            // =========================================================================
            // 5. CARRY FORWARD FEE DISCOUNTS
            // =========================================================================
            $sourceDiscounts = FeeDiscount::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceDiscounts as $srcDisc) {
                $newCompIdsJson = null;
                if (!empty($srcDisc->fee_component_ids)) {
                    $rawCompIds = is_array($srcDisc->fee_component_ids)
                        ? $srcDisc->fee_component_ids
                        : json_decode($srcDisc->fee_component_ids, true);

                    if (is_array($rawCompIds)) {
                        $remappedCompIds = [];
                        foreach ($rawCompIds as $cid) {
                            if (isset($componentMap[$cid])) {
                                $remappedCompIds[] = (string) $componentMap[$cid];
                            }
                        }
                        $newCompIdsJson = !empty($remappedCompIds) ? json_encode($remappedCompIds) : null;
                    }
                }

                FeeDiscount::create([
                    'school_id'            => $schoolId,
                    'academic_session_id'  => $targetSession->id,
                    'name'                 => $srcDisc->name,
                    'remarks'              => $srcDisc->remarks,
                    'classes_installments' => $srcDisc->classes_installments,
                    'sections'             => $srcDisc->sections,
                    'amount'               => $srcDisc->amount,
                    'type'                 => $srcDisc->type ?? 'flat',
                    'student_ids'          => $srcDisc->student_ids,
                    'installment_no'       => $srcDisc->installment_no,
                    'target_group'         => $srcDisc->target_group ?: 'all',
                    'fee_component_ids'    => $newCompIdsJson,
                ]);

                $counts['discounts']++;
            }

            // =========================================================================
            // 6. CARRY FORWARD MISC FEES
            // =========================================================================
            $sourceMiscFees = MiscFee::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceMiscFees as $srcMisc) {
                MiscFee::create([
                    'school_id'            => $schoolId,
                    'academic_session_id'  => $targetSession->id,
                    'fee_head_name'        => $srcMisc->fee_head_name,
                    'name'                 => $srcMisc->name,
                    'remarks'              => $srcMisc->remarks,
                    'classes_installments' => $srcMisc->classes_installments,
                    'amount'               => $srcMisc->amount,
                    'student_ids'          => $srcMisc->student_ids,
                ]);

                $counts['misc_fees']++;
            }

            // =========================================================================
            // 7. CARRY FORWARD CLASS-WISE FEES
            // =========================================================================
            $sourceClassWiseFees = ClassWiseFee::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceClassWiseFees as $srcCw) {
                $newSchedId = ($srcCw->fee_schedule_id && isset($scheduleMap[$srcCw->fee_schedule_id]))
                    ? $scheduleMap[$srcCw->fee_schedule_id]
                    : null;

                $newCompId = ($srcCw->fee_component_id && isset($componentMap[$srcCw->fee_component_id]))
                    ? $componentMap[$srcCw->fee_component_id]
                    : null;

                if ($newSchedId && $newCompId) {
                    $newSched = FeeSchedule::find($newSchedId);
                    $schedInstallments = $newSched ? ($newSched->installments ?? []) : [];

                    $cwInstallments = is_array($srcCw->installments)
                        ? $srcCw->installments
                        : json_decode($srcCw->installments ?? '[]', true);

                    $newCwInstallments = [];
                    if (!empty($schedInstallments)) {
                        foreach ($schedInstallments as $sInst) {
                            $instNo = $sInst['installment_no'] ?? null;
                            $existingCwInst = collect($cwInstallments)->firstWhere('installment_no', $instNo);
                            $amount = $existingCwInst ? floatval($existingCwInst['amount'] ?? 0) : 0.00;

                            $newCwInstallments[] = [
                                'installment_no' => $instNo,
                                'name'           => $sInst['name'] ?? '',
                                'amount'         => $amount,
                                'due_date'       => $sInst['due_date'] ?? '',
                                'start_date'     => $sInst['start_date'] ?? '',
                                'end_date'       => $sInst['end_date'] ?? '',
                            ];
                        }
                    }

                    ClassWiseFee::create([
                        'school_id'           => $schoolId,
                        'academic_session_id' => $targetSession->id,
                        'class_id'            => $srcCw->class_id,
                        'section_id'          => $srcCw->section_id,
                        'fee_schedule_id'     => $newSchedId,
                        'student_category_id' => $srcCw->student_category_id,
                        'fee_component_id'    => $newCompId,
                        'is_active'           => $srcCw->is_active,
                        'amount'              => $srcCw->amount,
                        'installments'        => $newCwInstallments,
                    ]);

                    $counts['class_wise_fees']++;
                }
            }

            // =========================================================================
            // 8. CARRY FORWARD TRANSPORT FEE SCHEDULES
            // =========================================================================
            $sourceTransportSchedules = TransportFeeSchedule::where('school_id', $schoolId)
                ->where('academic_session_id', $sourceSession->id)
                ->get();

            foreach ($sourceTransportSchedules as $srcTr) {
                $rawTrInst = is_array($srcTr->installments)
                    ? $srcTr->installments
                    : json_decode($srcTr->installments ?? '[]', true);

                $newTrInst = [];
                if (!empty($rawTrInst)) {
                    foreach ($rawTrInst as $inst) {
                        $instStart = isset($inst['start_date']) ? Carbon::parse($inst['start_date'])->addMonths($diffMonths) : $tarStart->copy();
                        $instEnd   = isset($inst['end_date'])   ? Carbon::parse($inst['end_date'])->addMonths($diffMonths)   : $tarEnd->copy();
                        $instDue   = isset($inst['due_date'])   ? Carbon::parse($inst['due_date'])->addMonths($diffMonths)   : $instEnd->copy();

                        if ($instStart->lt($tarStart)) $instStart = $tarStart->copy();
                        if ($instStart->gt($tarEnd))   $instStart = $tarEnd->copy();
                        if ($instEnd->lt($instStart))  $instEnd   = $instStart->copy();
                        if ($instEnd->gt($tarEnd))     $instEnd   = $tarEnd->copy();
                        if ($instDue->lt($instStart))  $instDue   = $instStart->copy();
                        if ($instDue->gt($tarEnd))     $instDue   = $tarEnd->copy();

                        $name = $inst['name'] ?? ('Installment ' . ($inst['installment_no'] ?? 1));
                        $name = self::updateInstallmentNameYear($name, $instStart, $tarStart, $tarEnd);

                        $newTrInst[] = [
                            'installment_no' => (int) ($inst['installment_no'] ?? (count($newTrInst) + 1)),
                            'name'           => $name,
                            'start_date'     => $instStart->toDateString(),
                            'end_date'       => $instEnd->toDateString(),
                            'due_date'       => $instDue->toDateString(),
                            'grace_days'     => (int) ($inst['grace_days'] ?? 5),
                        ];
                    }
                }

                $newFineId = ($srcTr->fine_id && isset($fineMap[$srcTr->fine_id]))
                    ? $fineMap[$srcTr->fine_id]
                    : null;

                TransportFeeSchedule::create([
                    'school_id'           => $schoolId,
                    'academic_session_id' => $targetSession->id,
                    'route_id'            => $srcTr->route_id,
                    'name'                => $srcTr->name,
                    'installments'        => $newTrInst,
                    'installment_type'    => $srcTr->installment_type,
                    'fine_id'             => $newFineId,
                    'is_active'           => $srcTr->is_active ?? true,
                ]);

                $counts['transport_schedules']++;
            }

            Log::info("Fee Structure carried forward for school {$schoolId} from session {$sourceSession->id} to {$targetSession->id}", $counts);

            return [
                'success' => true,
                'message' => 'Fee structure carried forward successfully.',
                'counts'  => $counts,
            ];
        });
    }

    /**
     * Update year in installment name when shifting dates across academic years.
     *
     * @param string $name
     * @param Carbon $instStart
     * @param Carbon $tarStart
     * @param Carbon $tarEnd
     * @return string
     */
    private static function updateInstallmentNameYear(string $name, Carbon $instStart, Carbon $tarStart, Carbon $tarEnd): string
    {
        $name = trim($name);

        // Format: "Installment N" -> keep as is
        if (preg_match('/^Installment\s+[1-9]\d*$/i', $name)) {
            return $name;
        }

        // Format: "Month YYYY" (e.g. "April 2026") -> format with new installment date
        $months = 'January|February|March|April|May|June|July|August|September|October|November|December|Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec';
        if (preg_match('/^(' . $months . ')\s+\d{4}$/i', $name)) {
            return $instStart->format('F Y');
        }

        // Format: Quarter format e.g. "Q1 (Apr-Jun 2026)" -> update year
        if (preg_match('/^(Q\d+\s*\([A-Za-z]+-[A-Za-z]+)\s+\d{4}(\))$/i', $name, $matches)) {
            return $matches[1] . ' ' . $instStart->format('Y') . $matches[2];
        }

        // Format: Session format e.g. "Session 2026-27" -> update to target session year
        if (preg_match('/^Session\s+\d{4}(-\d{2,4})?$/i', $name)) {
            return "Session " . $tarStart->format('Y') . "-" . $tarEnd->format('y');
        }

        return $name;
    }
}
