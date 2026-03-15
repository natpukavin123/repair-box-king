<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // e.g. "GST 5%", "GST 18%"
            $table->decimal('percentage', 5, 2); // e.g. 5.00, 12.00, 18.00, 28.00
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default Indian GST slabs
        DB::table('tax_rates')->insert([
            ['name' => 'GST 0%',  'percentage' => 0,  'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 5%',  'percentage' => 5,  'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 12%', 'percentage' => 12, 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 18%', 'percentage' => 18, 'is_default' => true,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GST 28%', 'percentage' => 28, 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
