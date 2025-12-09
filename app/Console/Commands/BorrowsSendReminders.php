<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;
use App\Mail\DueReminderMail;
use App\Mail\OverdueNoticeMail;
use Illuminate\Support\Facades\Mail;

class BorrowsSendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrows:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due date reminders (3 days, 1 day, due today) and overdue notices for borrows.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->startOfDay();

        // Reminders: 3 days before, 1 day before, due today
        $offsets = [3, 1, 0];
        foreach ($offsets as $days) {
            $targetDate = $today->copy()->addDays($days);
            $borrows = Borrow::with(['user','book'])
                ->whereNull('returned_at')
                ->where('status', 'borrowed')
                ->whereDate('due_at', $targetDate)
                ->get();

            foreach ($borrows as $borrow) {
                if (!$borrow->user || !$borrow->user->email) continue;
                Mail::to($borrow->user->email)->queue(new DueReminderMail($borrow));
            }

            $this->info("Sent reminders for due_at = {$targetDate->toDateString()} ({$borrows->count()} items)");
        }

        // Overdue: due_at in the past, status borrowed
        $overdues = Borrow::with(['user','book'])
            ->whereNull('returned_at')
            ->where('status', 'borrowed')
            ->whereDate('due_at', '<', $today)
            ->get();

        foreach ($overdues as $borrow) {
            if (!$borrow->user || !$borrow->user->email) continue;
            Mail::to($borrow->user->email)->queue(new OverdueNoticeMail($borrow));
        }

        $this->info("Sent overdue notices: {$overdues->count()} items");

        return self::SUCCESS;
    }
}
