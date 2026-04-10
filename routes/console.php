<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// WhatsApp scheduled messages — runs every minute
// Schedule::command('whatsapp:send')
//     ->everyMinute()
//     ->withoutOverlapping()
//     ->runInBackground();
