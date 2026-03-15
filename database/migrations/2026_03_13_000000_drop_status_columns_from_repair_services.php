<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_services', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('repair_services', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'completed'])->default('pending')->after('vendor_charge');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending')->after('payment_status');
        });
    }
};
