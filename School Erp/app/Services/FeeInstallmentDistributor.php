<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\AcademicSession;

class FeeInstallmentDistributor
{
    /**
     * Generate installment segments based on frequency.
     */
    public static function generate(Carbon $sessionStart, Carbon $sessionEnd, string $installmentType, ?int $customCount = null): array
    {
        $installments = [];

        switch ($installmentType) {
            case 'monthly':
                $current = $sessionStart->copy();
                $instNo = 1;
                while ($current->lt($sessionEnd) || $current->format('Y-m') === $sessionEnd->format('Y-m')) {
                    $start = $current->copy();
                    if ($instNo > 1) {
                        $start->startOfMonth();
                    }

                    $end = $start->copy()->endOfMonth();
                    if ($end->gt($sessionEnd)) {
                        $end = $sessionEnd->copy();
                    }

                    $installments[] = [
                        'installment_no' => $instNo,
                        'name' => $start->format('F Y'),
                        'start_date' => $start->toDateString(),
                        'end_date' => $end->toDateString(),
                        'due_date' => $end->toDateString(),
                        'grace_days' => 5,
                    ];

                    $current->addMonth()->startOfMonth();
                    $instNo++;
                }
                break;

            case 'quarterly':
                // Group monthly segments into chunks of 3
                $monthly = self::generate($sessionStart, $sessionEnd, 'monthly');
                $chunks = array_chunk($monthly, 3);
                foreach ($chunks as $index => $chunk) {
                    $instNo = $index + 1;
                    $first = $chunk[0];
                    $last = $chunk[count($chunk) - 1];

                    $firstStart = Carbon::parse($first['start_date']);
                    $lastEnd = Carbon::parse($last['end_date']);

                    $installments[] = [
                        'installment_no' => $instNo,
                        'name' => "Q" . $instNo . " (" . $firstStart->format('M') . "-" . $lastEnd->format('M') . " " . $firstStart->format('Y') . ")",
                        'start_date' => $first['start_date'],
                        'end_date' => $last['end_date'],
                        'due_date' => $last['end_date'],
                        'grace_days' => 5,
                    ];
                }
                break;

            case 'yearly':
                $sessionName = \App\Models\AcademicSession::where('start_date', $sessionStart->toDateString())
                    ->where('end_date', $sessionEnd->toDateString())
                    ->value('name');

                if (!$sessionName) {
                    $sessionName = "Session " . $sessionStart->format('Y') . "-" . $sessionEnd->format('y');
                }

                $installments[] = [
                    'installment_no' => 1,
                    'name' => $sessionName,
                    'start_date' => $sessionStart->toDateString(),
                    'end_date' => $sessionEnd->toDateString(),
                    'due_date' => $sessionEnd->toDateString(),
                    'grace_days' => 5,
                ];
                break;

            case 'custom':
            default:
                $count = $customCount ?: 4;
                $totalDays = $sessionStart->diffInDays($sessionEnd) + 1;
                $segmentLength = (int) floor($totalDays / $count);
                $currentStart = $sessionStart->copy();

                for ($i = 1; $i <= $count; $i++) {
                    $start = $currentStart->copy();
                    if ($i === $count) {
                        $end = $sessionEnd->copy();
                    } else {
                        $end = $start->copy()->addDays($segmentLength - 1);
                    }

                    $installments[] = [
                        'installment_no' => $i,
                        'name' => "Installment " . $i,
                        'start_date' => $start->toDateString(),
                        'end_date' => $end->toDateString(),
                        'due_date' => $end->toDateString(),
                        'grace_days' => 5,
                    ];

                    $currentStart = $end->copy()->addDay();
                }
                break;
        }

        return $installments;
    }

    /**
     * Validate manual edits to installments list.
     * Returns null if valid, or a string error message if invalid.
     */
    public static function validateInstallments(array $installments, AcademicSession $session): ?string
    {
        $sessionStart = Carbon::parse($session->start_date);
        $sessionEnd = Carbon::parse($session->end_date);

        // Sort by start_date to check for overlaps
        usort($installments, function ($a, $b) {
            return strcmp($a['start_date'], $b['start_date']);
        });

        foreach ($installments as $idx => $inst) {
            $name = $inst['name'] ?? ('Installment ' . ($idx + 1));

            if (empty($inst['start_date']) || empty($inst['end_date']) || empty($inst['due_date'])) {
                return "Dates are required for all installments.";
            }

            $start = Carbon::parse($inst['start_date']);
            $end = Carbon::parse($inst['end_date']);
            $due = Carbon::parse($inst['due_date']);

            if ($start->lt($sessionStart) || $start->gt($sessionEnd)) {
                return "The start date of installment '{$name}' ({$inst['start_date']}) must be within the academic session bounds ({$session->start_date} to {$session->end_date}).";
            }

            if ($end->lt($sessionStart) || $end->gt($sessionEnd)) {
                return "The end date of installment '{$name}' ({$inst['end_date']}) must be within the academic session bounds ({$session->start_date} to {$session->end_date}).";
            }

            if ($due->lt($sessionStart) || $due->gt($sessionEnd)) {
                return "The due date of installment '{$name}' ({$inst['due_date']}) must be within the academic session bounds ({$session->start_date} to {$session->end_date}).";
            }

            if ($end->lt($start)) {
                return "The end date of installment '{$name}' cannot be before the start date.";
            }

            // Check overlap with next installment
            if ($idx < count($installments) - 1) {
                $nextInst = $installments[$idx + 1];
                $nextName = $nextInst['name'] ?? ('Installment ' . ($idx + 2));
                $nextStart = Carbon::parse($nextInst['start_date']);

                if ($nextStart->lte($end)) {
                    return "Installments overlap: '{$name}' ends on {$inst['end_date']}, which overlaps with '{$nextName}' starting on {$nextInst['start_date']}.";
                }
            }
        }

        return null;
    }
}
