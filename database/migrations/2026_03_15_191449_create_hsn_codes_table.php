<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hsn_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');                // HSN code (goods) or SAC code (services)
            $table->enum('type', ['hsn', 'sac']);  // hsn = goods, sac = services
            $table->string('description');
            $table->foreignId('tax_rate_id')->constrained('tax_rates')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hsn_codes');
    }
};
