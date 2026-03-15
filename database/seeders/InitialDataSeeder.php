<?php

namespace Database\Seeders;

use App\Models\{Role, Permission, Setting, EmailTemplate, ServiceType};
use Illuminate\Database\Seeder;

/**
 * InitialDataSeeder — Production only.
 * Seeds essential system data: roles, permissions, settings, service types, email templates.
 * Does NOT insert any fake/demo customers, repairs, invoices, products, etc.
 */
class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(MenuSeeder::class);
        $this->seedSettings();
        $this->seedServiceTypes();
        $this->seedEmailTemplates();
    }

    private function seedSettings(): void
    {
        $defaults = [
            'shop_name'       => 'My Repair Shop',
            'shop_phone'      => '',
            'shop_email'      => '',
            'shop_address'    => '',
            'currency'        => 'INR',
            'currency_symbol' => '₹',
            'tax_rate'        => '0',
            'invoice_prefix'  => 'INV-',
            'repair_prefix'   => 'RPR-',
            'tracking_prefix' => 'TRK-',
        ];

        foreach ($defaults as $key => $value) {
            Setting::setValue($key, $value);
        }
    }

    private function seedServiceTypes(): void
    {
        $types = [
            ['name' => 'Screen Replacement',   'default_price' => 0, 'description' => 'LCD/AMOLED screen replacement'],
            ['name' => 'Battery Replacement',  'default_price' => 0, 'description' => 'Battery swap'],
            ['name' => 'Software Update',      'default_price' => 0, 'description' => 'OS update/flash'],
            ['name' => 'Data Recovery',        'default_price' => 0, 'description' => 'Recover data from device'],
            ['name' => 'Water Damage Repair',  'default_price' => 0, 'description' => 'Water damage diagnosis and repair'],
            ['name' => 'Charging Port Repair', 'default_price' => 0, 'description' => 'Fix charging port issues'],
        ];

        foreach ($types as $type) {
            ServiceType::firstOrCreate(['name' => $type['name']], $type);
        }
    }

    private function seedEmailTemplates(): void
    {
        $templates = [
            [
                'template_name' => 'Invoice Created',
                'subject'       => 'Your Invoice #{invoice_number}',
                'body'          => 'Dear {customer_name}, your invoice #{invoice_number} of {amount} has been generated. Thank you for your purchase!',
            ],
            [
                'template_name' => 'Repair Status Update',
                'subject'       => 'Repair Update - {ticket_number}',
                'body'          => 'Dear {customer_name}, your repair ticket {ticket_number} status has been updated to: {status}. Tracking ID: {tracking_id}',
            ],
            [
                'template_name' => 'Repair Completed',
                'subject'       => 'Repair Completed - {ticket_number}',
                'body'          => 'Dear {customer_name}, your device repair ({ticket_number}) is completed. Please collect from our shop.',
            ],
        ];

        foreach ($templates as $tpl) {
            EmailTemplate::firstOrCreate(['template_name' => $tpl['template_name']], $tpl);
        }
    }
}
