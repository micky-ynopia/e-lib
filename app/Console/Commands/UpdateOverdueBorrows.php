<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrow;

class UpdateOverdueBorrows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrows:update-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update borrow status to overdue for items past due date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->startOfDay();

        $borrows = Borrow::where('status', 'borrowed')
            ->whereNull('returned_at')
            ->where('due_at', '<', $today)
            ->get();

        $count = 0;
        foreach ($borrows as $borrow) {
            $borrow->status = 'overdue';
            $borrow->save();
            $count++;
        }

        $this->info("Updated {$count} borrows to overdue status.");

        return self::SUCCESS;
    }
}

