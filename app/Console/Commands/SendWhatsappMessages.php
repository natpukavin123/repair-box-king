<?php

namespace App\Console\Commands;

use App\Models\WaSchedule;
use App\Services\WhatsappService;
use Illuminate\Console\Command;

class SendWhatsappMessages extends Command
{
    protected $signature   = 'whatsapp:send {--schedule= : Run a specific schedule ID}';
    protected $description = 'Send due WhatsApp scheduled messages';

    public function handle(WhatsappService $wa): int
    {
        // Check WA device is connected
        $status = $wa->getStatus();
        if (($status['status'] ?? '') !== 'connected') {
            $this->warn('[WA] Device not connected – skipping.');
            return self::FAILURE;
        }

        $specificId = $this->option('schedule');

        $query = WaSchedule::where('is_active', true);
        if ($specificId) {
            $query->where('id', $specificId);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            $this->info('[WA] No active schedules found.');
            return self::SUCCESS;
        }

        $totalSent   = 0;
        $totalFailed = 0;

        foreach ($schedules as $schedule) {
            if ($specificId || $schedule->isDue()) {
                $this->info("[WA] Running: {$schedule->name}");
                $result       = $wa->processSchedule($schedule);
                $totalSent   += $result['sent'];
                $totalFailed += $result['failed'];
                $this->line("  → sent:{$result['sent']} failed:{$result['failed']}");
            }
        }

        $this->info("[WA] Done — total sent:{$totalSent} failed:{$totalFailed}");
        return self::SUCCESS;
    }
}
