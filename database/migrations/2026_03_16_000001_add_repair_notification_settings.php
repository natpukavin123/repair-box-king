<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Email Templates ──────────────────────────────────────────────────────
        $receivedBody = <<<'HTML'
<p style="font-size:16px;color:#374151;margin:0 0 12px 0;">Hi <strong>{customer_name}</strong>,</p>
<p style="color:#6b7280;margin:0 0 20px 0;">We have received your device for repair. Here are your ticket details:</p>
<table style="width:100%;border-collapse:collapse;margin:0 0 24px 0;">
  <tr style="background:#f9fafb;">
    <td style="padding:10px 14px;font-weight:600;color:#111827;width:45%;border:1px solid #e5e7eb;">Ticket Number</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{ticket_number}</td>
  </tr>
  <tr>
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Tracking ID</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{tracking_id}</td>
  </tr>
  <tr style="background:#f9fafb;">
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Device</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{device_brand} {device_model}</td>
  </tr>
  <tr>
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Estimated Cost</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{estimated_cost}</td>
  </tr>
  <tr style="background:#f9fafb;">
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Expected Delivery</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{expected_delivery_date}</td>
  </tr>
</table>
<p style="color:#6b7280;margin:0 0 16px 0;">You can track your repair status anytime using the link below:</p>
<p style="margin:0 0 20px 0;">
  <a href="{tracking_url}" style="display:inline-block;background:#4f46e5;color:#ffffff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;font-size:14px;">Track My Repair</a>
</p>
<p style="color:#9ca3af;font-size:13px;margin:0;">If you have any questions, feel free to contact us at <strong>{shop_phone}</strong>.</p>
HTML;

        $completedBody = <<<'HTML'
<p style="font-size:16px;color:#374151;margin:0 0 12px 0;">Hi <strong>{customer_name}</strong>,</p>
<p style="color:#6b7280;margin:0 0 20px 0;">Great news! Your device repair has been <strong style="color:#059669;">completed</strong> and is ready for pickup. Here is a summary:</p>
<table style="width:100%;border-collapse:collapse;margin:0 0 24px 0;">
  <tr style="background:#f9fafb;">
    <td style="padding:10px 14px;font-weight:600;color:#111827;width:45%;border:1px solid #e5e7eb;">Ticket Number</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{ticket_number}</td>
  </tr>
  <tr>
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Device</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{device_brand} {device_model}</td>
  </tr>
  <tr style="background:#f9fafb;">
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Technician</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{technician_name}</td>
  </tr>
  <tr>
    <td style="padding:10px 14px;font-weight:600;color:#111827;border:1px solid #e5e7eb;">Service Charge</td>
    <td style="padding:10px 14px;color:#374151;border:1px solid #e5e7eb;">{service_charge}</td>
  </tr>
  <tr style="background:#059669;color:#ffffff;">
    <td style="padding:10px 14px;font-weight:700;border:1px solid #059669;">Total Amount</td>
    <td style="padding:10px 14px;font-weight:700;border:1px solid #059669;">{grand_total}</td>
  </tr>
</table>
<div style="background:#ecfdf5;border-left:4px solid #059669;padding:14px 16px;border-radius:4px;margin:0 0 24px 0;">
  <p style="margin:0;color:#065f46;font-weight:600;">Please visit our store for pickup & payment.</p>
  <p style="margin:6px 0 0 0;color:#047857;font-size:13px;">{shop_name} — {shop_phone}</p>
</div>
<p style="color:#6b7280;margin:0 0 16px 0;">You can also check the repair status online:</p>
<p style="margin:0 0 20px 0;">
  <a href="{tracking_url}" style="display:inline-block;background:#059669;color:#ffffff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:600;font-size:14px;">View Repair Details</a>
</p>
<p style="color:#9ca3af;font-size:13px;margin:0;">Thank you for trusting us with your device!</p>
HTML;

        $templates = [
            [
                'template_name' => 'repair_received',
                'subject'       => 'Your repair ticket #{ticket_number} has been received — {shop_name}',
                'body'          => $receivedBody,
                'status'        => 'active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'template_name' => 'repair_completed',
                'subject'       => 'Your device is ready for pickup! Ticket #{ticket_number} — {shop_name}',
                'body'          => $completedBody,
                'status'        => 'active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        foreach ($templates as $tpl) {
            $exists = DB::table('email_templates')
                ->where('template_name', $tpl['template_name'])
                ->exists();
            if (! $exists) {
                DB::table('email_templates')->insert($tpl);
            }
        }

        // ── WhatsApp / Notification Settings ────────────────────────────────────
        $defaults = [
            'notify_email_received'    => '1',
            'notify_email_completed'   => '1',
            'notify_whatsapp_received' => '0',
            'notify_whatsapp_completed'=> '0',
            'whatsapp_api_url'         => '',
            'whatsapp_api_token'       => '',
            'whatsapp_from_number'     => '',
            'whatsapp_template_received' =>
                "Hello {customer_name}! 👋\n\nYour device has been received at {shop_name}.\n\n📋 Ticket: {ticket_number}\n📱 Device: {device_brand} {device_model}\n💰 Est. Cost: {estimated_cost}\n📅 Expected Delivery: {expected_delivery_date}\n\nTrack your repair here:\n{tracking_url}\n\nQueries? Call us: {shop_phone}",
            'whatsapp_template_completed' =>
                "Hello {customer_name}! 🎉\n\nYour device repair is COMPLETED and ready for pickup!\n\n📋 Ticket: {ticket_number}\n📱 Device: {device_brand} {device_model}\n💰 Amount Due: {grand_total}\n\nPlease visit {shop_name} to collect your device.\n📞 {shop_phone}\n\nTrack: {tracking_url}",
        ];

        foreach ($defaults as $key => $value) {
            $exists = DB::table('settings')->where('setting_key', $key)->exists();
            if (! $exists) {
                DB::table('settings')->insert([
                    'setting_key'   => $key,
                    'setting_value' => $value,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('email_templates')
            ->whereIn('template_name', ['repair_received', 'repair_completed'])
            ->delete();

        DB::table('settings')
            ->whereIn('setting_key', [
                'notify_email_received', 'notify_email_completed',
                'notify_whatsapp_received', 'notify_whatsapp_completed',
                'whatsapp_api_url', 'whatsapp_api_token', 'whatsapp_from_number',
                'whatsapp_template_received', 'whatsapp_template_completed',
            ])->delete();
    }
};
