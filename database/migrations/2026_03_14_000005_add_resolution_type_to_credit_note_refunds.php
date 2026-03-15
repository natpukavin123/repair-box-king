<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_note_refunds', function (Blueprint $table) {
            $table->enum('resolution_type', ['refund', 'new_repair', 'new_invoice'])->default('refund')->after('credit_note_id');
            $table->string('reference_type', 50)->nullable()->after('resolution_type'); // 'repairs', 'invoices'
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
        });
    }

    public function down(): void
    {
        Schema::table('credit_note_refunds', function (Blueprint $table) {
            $table->dropColumn(['resolution_type', 'reference_type', 'reference_id']);
        });
    }
};
