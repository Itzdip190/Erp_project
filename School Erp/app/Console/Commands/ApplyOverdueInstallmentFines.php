<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FeeSchedule;
use App\Models\TransportFeeSchedule;
use App\Models\StudentFee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ApplyOverdueInstallmentFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apply-overdue-installment-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply late fee fines automatically to overdue unpaid student fee installments.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting overdue installment fine application...');

        $today = now()->startOfDay();
        $schoolsProcessed = [];
        $finesAppliedCount = 0;
        $totalFineAmountApplied = 0.00;

        // 1. Process Tuition Fee Schedules
        $tuitionSchedules = FeeSchedule::whereNotNull('fine_id')->with('fine')->get();
        foreach ($tuitionSchedules as $sched) {
            $finePolicy = $sched->fine;
            if (!$finePolicy || !$finePolicy->status) {
                continue;
            }

            $schoolsProcessed[$sched->school_id] = true;

            $installments = $sched->installments ?? [];
            foreach ($installments as $inst) {
                if (empty($inst['due_date']) || empty($inst['installment_no'])) {
                    continue;
                }

                $dueDate = Carbon::parse($inst['due_date'])->startOfDay();
                $graceDays = (int) ($inst['grace_days'] ?? $finePolicy->default_grace_days ?? 0);
                $graceDate = $dueDate->copy()->addDays($graceDays);

                if ($today->gt($graceDate)) {
                    // Installment is overdue past the grace period!
                    $studentFees = StudentFee::where('school_id', $sched->school_id)
                        ->where('fee_schedule_id', $sched->id)
                        ->where('installment_no', $inst['installment_no'])
                        ->where('status', '!=', 'paid')
                        ->whereNull('fine_applied_at')
                        ->get();

                    foreach ($studentFees as $sf) {
                        if ($finePolicy->fee_component_id !== null && $finePolicy->fee_component_id !== $sf->fee_component_id) {
                            continue;
                        }
                        $fineAmount = $finePolicy->calculateFor($sf, $inst['due_date'], $graceDays);
                        if ($fineAmount > 0) {
                            $sf->fine_amount_applied = $fineAmount;
                            $sf->fine_applied_at = now();
                            $sf->save();

                            $finesAppliedCount++;
                            $totalFineAmountApplied += $fineAmount;
                        }
                    }
                }
            }
        }

        // 2. Process Transport Fee Schedules
        $transportSchedules = TransportFeeSchedule::whereNotNull('fine_id')
            ->where('is_active', true)
            ->with('fine')
            ->get();
        foreach ($transportSchedules as $sched) {
            $finePolicy = $sched->fine;
            if (!$finePolicy || !$finePolicy->status) {
                continue;
            }

            $schoolsProcessed[$sched->school_id] = true;

            $installments = $sched->installments ?? [];
            foreach ($installments as $inst) {
                if (empty($inst['due_date']) || empty($inst['installment_no'])) {
                    continue;
                }

                $dueDate = Carbon::parse($inst['due_date'])->startOfDay();
                $graceDays = (int) ($inst['grace_days'] ?? $finePolicy->default_grace_days ?? 0);
                $graceDate = $dueDate->copy()->addDays($graceDays);

                if ($today->gt($graceDate)) {
                    // Installment is overdue past the grace period!
                    $studentFees = StudentFee::where('school_id', $sched->school_id)
                        ->where('transport_fee_schedule_id', $sched->id)
                        ->where('installment_no', $inst['installment_no'])
                        ->where('status', '!=', 'paid')
                        ->whereNull('fine_applied_at')
                        ->get();

                    foreach ($studentFees as $sf) {
                        if ($finePolicy->fee_component_id !== null && $finePolicy->fee_component_id !== $sf->fee_component_id) {
                            continue;
                        }
                        $fineAmount = $finePolicy->calculateFor($sf, $inst['due_date'], $graceDays);
                        if ($fineAmount > 0) {
                            $sf->fine_amount_applied = $fineAmount;
                            $sf->fine_applied_at = now();
                            $sf->save();

                            $finesAppliedCount++;
                            $totalFineAmountApplied += $fineAmount;
                        }
                    }
                }
            }
        }

        $schoolsCount = count($schoolsProcessed);
        $summary = "Fine Job Summary: Processed {$schoolsCount} schools, applied {$finesAppliedCount} fines, total amount ₹" . number_format($totalFineAmountApplied, 2);
        
        $this->info($summary);
        Log::info($summary);

        return Command::SUCCESS;
    }

    public function info($string, $verbosity = null)
    {
        if ($this->output) {
            parent::info($string, $verbosity);
        }
    }
}
