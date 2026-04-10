<?php

namespace App\Services;

use App\Models\WaGroup;
use App\Models\WaMessageLog;
use App\Models\WaSchedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('whatsapp.service_url', 'http://localhost:3001'), '/');
        $this->token   = config('whatsapp.service_token', '');
    }

    private function headers(): array
    {
        $h = ['Accept' => 'application/json'];
        if ($this->token) {
            $h['X-WA-Token'] = $this->token;
        }
        return $h;
    }

    /** Get device connection status + QR data URL */
    public function getStatus(): array
    {
        try {
            $resp = Http::withHeaders($this->headers())
                ->timeout(8)
                ->get("{$this->baseUrl}/status");

            return $resp->json() ?? ['success' => false, 'status' => 'disconnected'];
        } catch (\Throwable $e) {
            return ['success' => false, 'status' => 'disconnected', 'error' => $e->getMessage()];
        }
    }

    /** Fetch all WhatsApp groups the account is in */
    public function fetchGroups(): array
    {
        try {
            $resp = Http::withHeaders($this->headers())
                ->timeout(15)
                ->get("{$this->baseUrl}/groups");

            return $resp->json('data', []);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Send a single message to a WA id (group or number) */
    public function sendMessage(string $to, string $message): array
    {
        try {
            $resp = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post("{$this->baseUrl}/send", ['to' => $to, 'message' => $message]);

            $data = $resp->json();
            return [
                'success' => $data['success'] ?? false,
                'error'   => $data['message']  ?? null,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /** Replace template placeholders with row values */
    public function renderMessage(string $template, array $row): string
    {
        $message = $template;
        foreach ($row as $key => $value) {
            $message = str_ireplace(
                ['{' . $key . '}', '{{' . $key . '}}'],
                $value,
                $message
            );
        }
        return $message;
    }

    /**
     * Process a schedule: render each data row, send to each group, log results.
     * Returns ['sent' => int, 'failed' => int]
     */
    public function processSchedule(WaSchedule $schedule): array
    {
        $sent   = 0;
        $failed = 0;
        $now    = now();

        $groups = WaGroup::whereIn('id', $schedule->group_ids)
            ->where('is_active', true)
            ->get();

        foreach ($schedule->data_rows as $row) {
            $message = $this->renderMessage($schedule->message_template, $row);

            foreach ($groups as $group) {
                $result = $this->sendMessage($group->wa_id, $message);

                WaMessageLog::create([
                    'schedule_id'   => $schedule->id,
                    'schedule_name' => $schedule->name,
                    'group_wa_id'   => $group->wa_id,
                    'group_name'    => $group->name,
                    'message'       => $message,
                    'status'        => $result['success'] ? 'sent' : 'failed',
                    'error'         => $result['error'] ?? null,
                    'sent_at'       => $now,
                ]);

                $result['success'] ? $sent++ : $failed++;
            }
        }

        $schedule->update([
            'last_sent_at' => $now,
            'sent_count'   => $schedule->sent_count + $sent,
        ]);

        Log::info("[WA] Schedule [{$schedule->name}] done — sent:{$sent} failed:{$failed}");

        return ['sent' => $sent, 'failed' => $failed];
    }

    /** Logout / disconnect the WA device */
    public function logout(): bool
    {
        try {
            $resp = Http::withHeaders($this->headers())
                ->timeout(10)
                ->post("{$this->baseUrl}/logout");
            return $resp->json('success', false);
        } catch (\Throwable) {
            return false;
        }
    }
}
