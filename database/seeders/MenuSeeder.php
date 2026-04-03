<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();

        $order = 0;

        // Dashboard (no section)
        Menu::create([
            'name' => 'Dashboard',
            'route' => '/dashboard',
            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'module' => 'dashboard',
            'section' => null,
            'sort_order' => $order++,
        ]);

        // Main Modules
        Menu::create([
            'name' => 'POS Billing',
            'route' => '/pos',
            'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
            'module' => 'pos',
            'section' => 'Main',
            'sort_order' => $order++,
        ]);
        Menu::create([
            'name' => 'Repairs',
            'route' => '/repairs',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'module' => 'repairs',
            'section' => 'Main',
            'sort_order' => $order++,
        ]);
        Menu::create([
            'name' => 'Recharges',
            'route' => '/recharges',
            'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
            'module' => 'recharges',
            'section' => 'Main',
            'sort_order' => $order++,
        ]);
        Menu::create([
            'name' => 'Expenses',
            'route' => '/expenses',
            'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            'module' => 'expenses',
            'section' => 'Main',
            'sort_order' => $order++,
        ]);
        Menu::create([
            'name' => 'Invoices',
            'route' => '/invoices',
            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'module' => 'invoices',
            'section' => 'Main',
            'sort_order' => $order++,
        ]);
        Menu::create([
            'name' => 'PO',
            'route' => '/po',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'module' => 'po',
            'section' => 'Main',
            'sort_order' => $order++,
        ]);

        // Settings & Master Data (accessible via Settings > Master Data tab)
        Menu::create([
            'name' => 'Settings',
            'route' => '/settings',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'module' => 'settings',
            'section' => 'System',
            'sort_order' => $order++,
        ]);

        // Hidden menu items (for RBAC permissions, accessed via Settings > Master Data)
        $masterDataItems = [
            ['name' => 'Categories', 'route' => '/categories', 'module' => 'categories'],
            ['name' => 'Brands', 'route' => '/brands', 'module' => 'categories'],
            ['name' => 'Products', 'route' => '/products', 'module' => 'products'],
            ['name' => 'Inventory', 'route' => '/inventory', 'module' => 'inventory'],
            ['name' => 'Parts', 'route' => '/parts', 'module' => 'products'],
            ['name' => 'Customers', 'route' => '/customers', 'module' => 'customers'],
            ['name' => 'Ledger', 'route' => '/ledger', 'module' => 'ledger'],
            ['name' => 'Reports', 'route' => '/reports', 'module' => 'reports'],
            ['name' => 'Users', 'route' => '/users', 'module' => 'users'],
            ['name' => 'Roles & Permissions', 'route' => '/roles', 'module' => 'users'],
            ['name' => 'Vendors', 'route' => '/vendors', 'module' => 'settings'],
            ['name' => 'Menus', 'route' => '/menus', 'module' => 'settings'],
            ['name' => 'Activity Logs', 'route' => '/activity-logs', 'module' => 'settings'],
            ['name' => 'Service Types', 'route' => '/service-types', 'module' => 'settings'],
            ['name' => 'Blog', 'route' => '/blog', 'module' => 'settings'],
            ['name' => 'FAQs', 'route' => '/faqs', 'module' => 'settings'],
            ['name' => 'SEO Pages', 'route' => '/seo-pages', 'module' => 'settings'],
            ['name' => 'SEO Settings', 'route' => '/seo-settings', 'module' => 'settings'],
        ];

        foreach ($masterDataItems as $item) {
            Menu::create([
                'name' => $item['name'],
                'route' => $item['route'],
                'icon' => 'M4 6h16M4 12h16M4 18h16',
                'module' => $item['module'],
                'section' => 'Master Data',
                'is_active' => false,
                'sort_order' => $order++,
            ]);
        }
    }
}
