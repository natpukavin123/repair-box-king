<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_type_name', 150); // store name for custom entries too
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('customer_charge', 10, 2)->default(0);
            $table->decimal('vendor_charge', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'completed'])->default('pending');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->string('reference_no', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_services');
    }
};
