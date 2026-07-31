<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FeeNotificationService;
use Illuminate\Support\Facades\Log;

class SendFeeDueDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-fee-due-date-reminders {--school_id= : Optional School ID filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically send fee payment due date reminders to Student and Parent portals at 07:00 AM.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated Fee Due Date Reminder notification dispatch...');

        $schoolId = $this->option('school_id') ? (int) $this->option('school_id') : null;

        $count = FeeNotificationService::sendDueDateReminders($schoolId);

        $summary = "Fee Due Date Reminders Job Summary: Sent {$count} reminder notifications for upcoming due dates.";
        $this->info($summary);
        Log::info($summary);

        return Command::SUCCESS;
    }
}
