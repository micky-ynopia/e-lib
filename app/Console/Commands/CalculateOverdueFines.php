<?php

namespace App\Console\Commands;

use App\Models\Borrow;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CalculateOverdueFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fines:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate fines for overdue books';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Calculating fines for overdue books...');

        // Get all overdue borrows that haven't been returned and haven't had fines calculated
        $overdueBorrows = Borrow::where('status', 'overdue')
            ->whereNull('returned_at')
            ->whereNull('fine_calculated_at')
            ->where('due_at', '<', now())
            ->get();

        $totalFines = 0;
        $processedCount = 0;
        $finePerDay = 5; // ₱5 per day

        foreach ($overdueBorrows as $borrow) {
            $daysOverdue = max(0, now()->diffInDays($borrow->due_at));
            $fineAmount = $daysOverdue * $finePerDay;

            $borrow->update([
                'fine_amount' => $fineAmount,
                'fine_calculated_at' => now(),
            ]);

            $totalFines += $fineAmount;
            $processedCount++;
        }

        // Also update fines for already calculated ones that are still overdue
        $existingFines = Borrow::where('status', 'overdue')
            ->whereNull('returned_at')
            ->whereNotNull('fine_calculated_at')
            ->whereNull('fine_paid_at')
            ->where('due_at', '<', now())
            ->get();

        foreach ($existingFines as $borrow) {
            $daysOverdue = max(0, now()->diffInDays($borrow->due_at));
            $fineAmount = $daysOverdue * $finePerDay;

            if ($borrow->fine_amount != $fineAmount) {
                $borrow->update([
                    'fine_amount' => $fineAmount,
                    'fine_calculated_at' => now(),
                ]);
                $totalFines += ($fineAmount - $borrow->fine_amount);
            }
        }

        $this->info("✅ Calculated fines for {$processedCount} new overdue books.");
        $this->info("✅ Updated fines for {$existingFines->count()} existing overdue books.");
        $this->info("💰 Total fines: ₱" . number_format($totalFines, 2));

        return Command::SUCCESS;
    }
}
