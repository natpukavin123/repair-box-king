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
            'purchases' => 'Purchases',
            'suppliers' => 'Suppliers',
            'pos' => 'POS Billing',
            'invoices' => 'Invoices',
            'repairs' => 'Repairs',
            'recharges' => 'Recharges',
            'services' => 'Services',
            'customers' => 'Customers',
            'returns' => 'Returns & Refunds',
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
    }
}
