<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * SAFE migration — run this on production.
 * 1. Converts any in-flight 'completed' or 'payment' records to 'in_progress'
 *    so no live order is lost.
 * 2. Narrows the status ENUM to the three statuses we actually use now:
 *    received | in_progress | closed | cancelled
 */
return new class extends Migration
{
    public function up(): void
    {
        // Move any transitional statuses into in_progress before we drop them from the ENUM
        DB::table('repairs')
            ->whereIn('status', ['completed', 'payment', 'diagnosing', 'waiting_parts', 'outsourced', 'delivered'])
            ->update(['status' => 'in_progress']);

        // Alter the column (MySQL / MariaDB ENUM syntax)
        DB::statement("ALTER TABLE repairs MODIFY COLUMN status ENUM('received','in_progress','closed','cancelled') NOT NULL DEFAULT 'received'");
    }

    public function down(): void
    {
        // Restore the original wider ENUM (data cannot be recovered, only the schema)
        DB::statement("ALTER TABLE repairs MODIFY COLUMN status ENUM('received','diagnosing','waiting_parts','in_progress','outsourced','completed','delivered','cancelled','payment','closed') NOT NULL DEFAULT 'received'");
    }
};
