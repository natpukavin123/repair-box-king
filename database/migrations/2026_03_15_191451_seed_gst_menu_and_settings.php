<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\{Menu, Setting};

return new class extends Migration
{
    public function up(): void
    {
        // Add GST & Tax menu if not exists
        if (!Menu::where('route', '/tax')->exists()) {
            $maxOrder = Menu::max('sort_order') ?? 0;

            // Insert before "Users" in System section, or at end
            $usersMenu = Menu::where('name', 'Users')->where('section', 'System')->first();
            $insertOrder = $usersMenu ? $usersMenu->sort_order : $maxOrder + 1;

            // Shift existing items down
            if ($usersMenu) {
                Menu::where('sort_order', '>=', $insertOrder)->increment('sort_order');
            }

            Menu::create([
                'name' => 'GST & Tax',
                'route' => '/tax',
                'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z',
                'module' => 'settings',
                'section' => 'System',
                'sort_order' => $insertOrder,
            ]);
        }

        // Seed GST settings if not present
        Setting::setValue('shop_gstin', Setting::getValue('shop_gstin', ''));
        Setting::setValue('shop_state', Setting::getValue('shop_state', ''));
    }

    public function down(): void
    {
        Menu::where('route', '/tax')->delete();
    }
};
