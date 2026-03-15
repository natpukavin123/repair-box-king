<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recharge_providers', function (Blueprint $table) {
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('provider_type');
        });
    }

    public function down(): void
    {
        Schema::table('recharge_providers', function (Blueprint $table) {
            $table->dropColumn('commission_percentage');
        });
    }
};
