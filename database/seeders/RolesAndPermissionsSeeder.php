<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => 'Dashboard',
            'categories' => 'Categories',
            'products' => 'Products',
            'inventory' => 'Inventory',
            'pos' => 'POS Billing',
            'invoices' => 'Invoices',
            'repairs' => 'Repairs',
            'recharges' => 'Recharges',
            'customers' => 'Customers',
            'po' => 'PO',
            'ledger' => 'Ledger',
            'expenses' => 'Expenses',
            'reports' => 'Reports',
            'users' => 'Users',
            'settings' => 'Settings',
            'backups' => 'Backups',
            'notifications' => 'Notifications',
        ];

        $actions = [
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ];

        foreach ($modules as $moduleKey => $moduleName) {
            foreach ($actions as $actionKey => $actionName) {
                $permName = "{$moduleKey}.{$actionKey}";
                Permission::updateOrCreate(
                    ['name' => $permName],
                    [
                        'module' => $moduleKey,
                        'display_name' => "{$actionName} {$moduleName}",
                        'description' => "Can {$actionKey} {$moduleName}",
                    ]
                );
            }
        }

        // Special POS permissions
        Permission::updateOrCreate(
            ['name' => 'pos.view_cost_price'],
            [
                'module' => 'pos',
                'display_name' => 'View Cost/Purchase Price in POS',
                'description' => 'Can see purchase price, margin and max discount in POS billing',
            ]
        );
    }
}
