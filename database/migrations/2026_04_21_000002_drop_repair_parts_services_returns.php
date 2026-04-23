<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * SAFE migration — run this AFTER 2026_04_21_000001_simplify_repair_statuses.php
 *
 * Drops tables that are no longer used:
 *   repair_return_items  (FK → repair_parts, repair_services)
 *   repair_returns
 *   repair_services
 *   repair_parts
 *   repair_vendors
 *
 * Order matters: child tables with FK constraints must be dropped first.
 * All data in these tables will be permanently removed.
 * Run `php artisan migrate` to apply.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Disable FK checks so we can drop in any order if needed
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('repair_return_items');
        Schema::dropIfExists('repair_returns');
        Schema::dropIfExists('repair_services');
        Schema::dropIfExists('repair_parts');
        Schema::dropIfExists('repair_vendors');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Recreating these tables is intentionally not implemented.
        // Restore from a database backup if a rollback is truly needed.
    }
};
