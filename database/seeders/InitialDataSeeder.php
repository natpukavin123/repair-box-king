<?php

namespace Database\Seeders;

use App\Models\{Role, Permission, User, Setting, EmailTemplate, ServiceType};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * InitialDataSeeder — Production only.
 * Seeds: roles, permissions, settings, service types, email templates, default admin user.
 * All operations are idempotent (safe to run multiple times).
 */
class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->seedRoles();
        $this->call(MenuSeeder::class);
        $this->seedSettings();
        $this->seedServiceTypes();
        $this->seedEmailTemplates();
        $this->seedAdminUser();
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'Admin',         'description' => 'Full system access'],
            ['name' => 'Stock Manager', 'description' => 'Manage inventory and purchases'],
            ['name' => 'Technician',    'description' => 'Handle repairs'],
            ['name' => 'Billing Staff', 'description' => 'POS and billing'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // Give Admin role all permissions
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::pluck('id'));
        }
    }

    private function seedAdminUser(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        $email    = env('ADMIN_EMAIL',    'admin@repairbox.com');
        $password = env('ADMIN_PASSWORD', 'password');
        $name     = env('ADMIN_NAME',     'Administrator');

        User::withoutEvents(function () use ($email, $password, $name, $adminRole) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'           => $name,
                    'password'       => Hash::make($password),
                    'role_id'        => $adminRole?->id,
                    'status'         => 'active',
                    'is_super_admin' => true,
                ]
            );
        });
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
            'shop_gstin'      => '',
            'shop_state'      => '',
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
            ['name' => 'Screen Replacement',   'default_price' => 0, 'description' => 'LCD/AMOLED screen replacement', 'sac_code' => '998314'],
            ['name' => 'Battery Replacement',  'default_price' => 0, 'description' => 'Battery swap', 'sac_code' => '998314'],
            ['name' => 'Software Update',      'default_price' => 0, 'description' => 'OS update/flash', 'sac_code' => '998316'],
            ['name' => 'Data Recovery',        'default_price' => 0, 'description' => 'Recover data from device', 'sac_code' => '998392'],
            ['name' => 'Water Damage Repair',  'default_price' => 0, 'description' => 'Water damage diagnosis and repair', 'sac_code' => '998319'],
            ['name' => 'Charging Port Repair', 'default_price' => 0, 'description' => 'Fix charging port issues', 'sac_code' => '998314'],
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
