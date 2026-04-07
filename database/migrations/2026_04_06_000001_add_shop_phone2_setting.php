<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    public function up(): void
    {
        // Insert the default row only if it doesn't already exist
        if (!Setting::where('setting_key', 'shop_phone2')->exists()) {
            Setting::setValue('shop_phone2', '');
        }
    }

    public function down(): void
    {
        Setting::where('setting_key', 'shop_phone2')->delete();
    }
};
