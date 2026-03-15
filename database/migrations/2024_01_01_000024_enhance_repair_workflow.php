<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Convert status enum to varchar(30) using raw SQL (enum can't be changed via Schema::table ->change without DBAL)
        DB::statement("ALTER TABLE `repairs` MODIFY `status` VARCHAR(30) NOT NULL DEFAULT 'received'");

        // 2. Normalize old status values
        DB::table('repairs')->where('status', 'diagnosing')->update(['status' => 'in_progress']);
        DB::table('repairs')->where('status', 'waiting_parts')->update(['status' => 'in_progress']);
        DB::table('repairs')->where('status', 'outsourced')->update(['status' => 'in_progress']);
        DB::table('repairs')->where('status', 'delivered')->update(['status' => 'closed']);

        // 3. Add new columns to repairs
        Schema::table('repairs', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->boolean('is_locked')->default(false)->after('status');
            $table->string('record_type', 20)->default('original')->after('is_locked');
            $table->text('cancel_reason')->nullable()->after('record_type');
            $table->timestamp('completed_at')->nullable()->after('cancel_reason');
            $table->timestamp('closed_at')->nullable()->after('completed_at');

            $table->foreign('parent_id')->references('id')->on('repairs')->nullOnDelete();
        });

        // 4. Convert payment_type enum to varchar(30) and add direction + notes
        DB::statement("ALTER TABLE `repair_payments` MODIFY `payment_type` VARCHAR(30) NOT NULL DEFAULT 'advance'");

        Schema::table('repair_payments', function (Blueprint $table) {
            $table->string('direction', 3)->default('IN')->after('payment_method');
            $table->text('notes')->nullable()->after('reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_locked', 'record_type', 'cancel_reason', 'completed_at', 'closed_at']);
        });

        Schema::table('repair_payments', function (Blueprint $table) {
            $table->dropColumn(['direction', 'notes']);
        });
    }
};
