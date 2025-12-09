<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily reminder emails at 08:00 server time
Schedule::command('borrows:send-reminders')->dailyAt('08:00');

// Schedule overdue status update daily at 09:00 server time
Schedule::command('borrows:update-overdue')->dailyAt('09:00');

// Schedule fine calculation daily at 10:00 server time
Schedule::command('fines:calculate')->dailyAt('10:00');
