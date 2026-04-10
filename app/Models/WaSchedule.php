<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaSchedule extends Model
{
    protected $table = 'wa_schedules';

    protected $fillable = [
        'name', 'group_ids', 'message_template', 'data_rows',
        'schedule_type', 'cron_expression', 'scheduled_at',
        'schedule_time', 'schedule_day', 'is_active',
        'last_sent_at', 'sent_count',
    ];

    protected $casts = [
        'group_ids'    => 'array',
        'data_rows'    => 'array',
        'is_active'    => 'boolean',
        'scheduled_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(WaMessageLog::class, 'schedule_id');
    }

    /**
     * Determine if this schedule should run right now.
     */
    public function isDue(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        return match ($this->schedule_type) {
            'once'   => $this->isDueOnce($now),
            'daily'  => $this->isDueDaily($now),
            'weekly' => $this->isDueWeekly($now),
            'cron'   => $this->isDueCron($now),
            default  => false,
        };
    }

    private function isDueOnce($now): bool
    {
        if (! $this->scheduled_at || $this->last_sent_at) {
            return false;
        }
        return $now->gte($this->scheduled_at);
    }

    private function isDueDaily($now): bool
    {
        if (! $this->schedule_time) {
            return false;
        }
        [$h, $m] = explode(':', $this->schedule_time);
        if ((int) $h !== $now->hour || (int) $m !== $now->minute) {
            return false;
        }
        // Already ran today?
        return ! ($this->last_sent_at && $this->last_sent_at->isToday());
    }

    private function isDueWeekly($now): bool
    {
        if (! $this->schedule_time || $this->schedule_day === null) {
            return false;
        }
        if ($now->dayOfWeek !== (int) $this->schedule_day) {
            return false;
        }
        [$h, $m] = explode(':', $this->schedule_time);
        if ((int) $h !== $now->hour || (int) $m !== $now->minute) {
            return false;
        }
        // Already ran this week?
        return ! ($this->last_sent_at && $this->last_sent_at->isCurrentWeek());
    }

    private function isDueCron($now): bool
    {
        if (! $this->cron_expression) {
            return false;
        }
        try {
            $cron = \Cron\CronExpression::factory($this->cron_expression);
            return $cron->isDue($now->toDateTimeString());
        } catch (\Throwable) {
            return false;
        }
    }
}
