<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand item_type enum to include 'repair'
        DB::statement("ALTER TABLE invoice_items MODIFY COLUMN item_type ENUM('product','service','recharge','manual','repair') NOT NULL");

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('is_linked')->default(false)->after('total');
            $table->unsignedBigInteger('linked_id')->nullable()->after('is_linked');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE invoice_items MODIFY COLUMN item_type ENUM('product','service','recharge','manual') NOT NULL");

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['is_linked', 'linked_id']);
        });
    }
};
