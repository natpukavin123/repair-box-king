<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE repairs MODIFY COLUMN status ENUM('received','in_progress','completed','closed','cancelled') NOT NULL DEFAULT 'received'");
    }

    public function down(): void
    {
        // Move any 'completed' back to 'in_progress' before removing from enum
        DB::table('repairs')->where('status', 'completed')->update(['status' => 'in_progress']);
        DB::statement("ALTER TABLE repairs MODIFY COLUMN status ENUM('received','in_progress','closed','cancelled') NOT NULL DEFAULT 'received'");
    }
};
