<?php

namespace App\Services;

use App\Mail\RepairMail;
use App\Models\{EmailTemplate, Setting, Repair};
use Illuminate\Support\Facades\{Log, Mail};
use Illuminate\Support\Facades\Http;

class NotificationService
{
    // ───────────────────────────────────────────────────
    //  Public entry points
    // ───────────────────────────────────────────────────

    public function sendRepairReceived(Repair $repair): void
    {
        $repair->loadMissing('customer', 'technician');
        $vars = $this->buildVars($repair);

        if (Setting::getValue('notify_email_received', '1') === '1') {
            $this->sendEmail('repair_received', $vars, $repair->customer?->email);
        }

        if (Setting::getValue('notify_whatsapp_received', '0') === '1') {
            $tpl = Setting::getValue('whatsapp_template_received', '');
            $this->sendWhatsApp($repair->customer?->mobile_number, $this->interpolate($tpl, $vars));
        }
    }

    public function sendRepairCompleted(Repair $repair): void
    {
        $repair->loadMissing('customer', 'technician');
        $vars = $this->buildVars($repair);

        if (Setting::getValue('notify_email_completed', '1') === '1') {
            $this->sendEmail('repair_completed', $vars, $repair->customer?->email);
        }

        if (Setting::getValue('notify_whatsapp_completed', '0') === '1') {
            $tpl = Setting::getValue('whatsapp_template_completed', '');
            $this->sendWhatsApp($repair->customer?->mobile_number, $this->interpolate($tpl, $vars));
        }
    }

    // ───────────────────────────────────────────────────
    //  Email
    // ───────────────────────────────────────────────────

    private function sendEmail(string $templateName, array $vars, ?string $recipientEmail): void
    {
        if (empty($recipientEmail)) {
            Log::info("[NotificationService] Skipping email ({$templateName}) — no customer email.");
            return;
        }

        $template = EmailTemplate::where('template_name', $templateName)
            ->where('status', 'active')
            ->first();

        if (! $template) {
            Log::warning("[NotificationService] Email template '{$templateName}' not found or inactive.");
            return;
        }

        $subject = $this->interpolate($template->subject ?? '', $vars);
        $body    = $this->interpolate($template->body   ?? '', $vars);

        // Build a clean preview snippet from the body (strip tags, limit length)
        $preview = mb_substr(strip_tags($body), 0, 100);

        try {
            Mail::to($recipientEmail)
                ->send(new RepairMail($subject, $body, $preview));

            Log::info("[NotificationService] Email '{$templateName}' sent to {$recipientEmail}.");
        } catch (\Throwable $e) {
            Log::error("[NotificationService] Email '{$templateName}' failed: " . $e->getMessage());
        }
    }

    // ───────────────────────────────────────────────────
    //  WhatsApp (generic HTTP API — works with Ultramsg,
    //  2chat, WA-Gateway, etc.)
    // ───────────────────────────────────────────────────

    private function sendWhatsApp(?string $mobile, string $message): void
    {
        if (empty($mobile) || empty(trim($message))) {
            Log::info('[NotificationService] Skipping WhatsApp — no mobile or empty message.');
            return;
        }

        $apiUrl   = rtrim(Setting::getValue('whatsapp_api_url', ''), '/');
        $token    = Setting::getValue('whatsapp_api_token', '');
        $fromNum  = Setting::getValue('whatsapp_from_number', '');

        if (empty($apiUrl) || empty($token)) {
            Log::warning('[NotificationService] WhatsApp API URL or token not configured.');
            return;
        }

        // Normalise phone: strip non-digits, remove leading zeroes, keep country code
        $phone = preg_replace('/\D/', '', $mobile);
        if (! str_starts_with($phone, '+')) {
            // Default to India (+91) if phone looks local (10 digits)
            if (strlen($phone) === 10) {
                $phone = '91' . $phone;
            }
        } else {
            $phone = ltrim($phone, '+');
        }

        try {
            $payload = [
                'token'   => $token,
                'to'      => $phone,
                'body'    => $message,
            ];

            // Some providers use "instance_id" + "access_token"
            if ($fromNum) {
                $payload['instance_id'] = $fromNum;
            }

            $response = Http::timeout(15)
                ->post($apiUrl . '/sendMessage', $payload);

            if ($response->successful()) {
                Log::info("[NotificationService] WhatsApp sent to {$phone}.");
            } else {
                Log::warning("[NotificationService] WhatsApp API returned {$response->status()}: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('[NotificationService] WhatsApp send failed: ' . $e->getMessage());
        }
    }

    // ───────────────────────────────────────────────────
    //  Helpers
    // ───────────────────────────────────────────────────

    /** Replace {placeholder} tokens in a template string. */
    public function interpolate(string $text, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $text = str_replace('{' . $key . '}', (string) $value, $text);
        }
        return $text;
    }

    /** Build the variable map from a Repair model. */
    private function buildVars(Repair $repair): array
    {
        $currency    = Setting::getValue('currency_symbol', '₹');
        $shopName    = Setting::getValue('shop_name', 'RepairBox');
        $shopPhone   = Setting::getValue('shop_phone', '');
        $trackingUrl = url('/track/' . $repair->tracking_id);

        $formatMoney = fn(?float $v) => $currency . number_format((float) $v, 2);

        return [
            'customer_name'       => $repair->customer?->name ?? 'Customer',
            'customer_email'      => $repair->customer?->email ?? '',
            'customer_mobile'     => $repair->customer?->mobile_number ?? '',
            'ticket_number'       => $repair->ticket_number ?? '',
            'tracking_id'         => $repair->tracking_id ?? '',
            'tracking_url'        => $trackingUrl,
            'device_brand'        => $repair->device_brand ?? '',
            'device_model'        => $repair->device_model ?? '',
            'imei'                => $repair->imei ?? '',
            'problem_description' => $repair->problem_description ?? '',
            'estimated_cost'      => $formatMoney($repair->estimated_cost),
            'service_charge'      => $formatMoney($repair->service_charge ?? 0),
            'grand_total'         => $formatMoney($repair->grand_total ?? 0),
            'expected_delivery_date' => $repair->expected_delivery_date
                ? $repair->expected_delivery_date->format('d M Y')
                : 'TBD',
            'technician_name'     => $repair->technician?->name ?? 'Our Technician',
            'status'              => ucfirst(str_replace('_', ' ', $repair->status ?? '')),
            'shop_name'           => $shopName,
            'shop_phone'          => $shopPhone,
        ];
    }

    /** Return the same variable map for preview/test purposes. */
    public function previewVars(Repair $repair): array
    {
        return $this->buildVars($repair);
    }
}
