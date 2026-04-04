<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_requests', function (Blueprint $table) {
            $table->string('order_type', 20)->default('customer')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('po_requests', function (Blueprint $table) {
            $table->dropColumn('order_type');
        });
    }
};
