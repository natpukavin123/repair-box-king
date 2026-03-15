<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->string('tracking_id', 50)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_brand', 100)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('imei', 50)->nullable();
            $table->text('problem_description')->nullable();
            $table->decimal('estimated_cost', 10, 2)->default(0);
            $table->date('expected_delivery_date')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', [
                'received', 'diagnosing', 'waiting_parts', 'in_progress',
                'outsourced', 'completed', 'delivered', 'cancelled'
            ])->default('received');
            $table->timestamps();
        });

        Schema::create('repair_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->string('status', 50);
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('repair_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('repair_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_type', ['advance', 'final']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->string('reference_number', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('repair_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('vendor_cost', 10, 2)->default(0);
            $table->date('sent_date')->nullable();
            $table->date('return_date')->nullable();
            $table->enum('status', ['sent', 'in_progress', 'completed', 'returned'])->default('sent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_vendors');
        Schema::dropIfExists('repair_payments');
        Schema::dropIfExists('repair_parts');
        Schema::dropIfExists('repair_status_history');
        Schema::dropIfExists('repairs');
    }
};
