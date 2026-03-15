<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_returns', function (Blueprint $table) {
            $table->foreignId('credit_note_id')->nullable()->after('created_by')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('repair_returns', function (Blueprint $table) {
            $table->dropForeign(['credit_note_id']);
            $table->dropColumn('credit_note_id');
        });
    }
};
