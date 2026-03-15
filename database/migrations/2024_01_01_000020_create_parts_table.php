<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('sku', 50)->unique()->nullable();
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Change repair_parts to reference parts table instead of products
        Schema::table('repair_parts', function (Blueprint $table) {
            $table->foreignId('part_id')->nullable()->after('repair_id')->constrained('parts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('repair_parts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('part_id');
        });
        Schema::dropIfExists('parts');
    }
};
