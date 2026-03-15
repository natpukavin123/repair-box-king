<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 30)->unique();
            $table->foreignId('repair_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->text('reason');
            $table->decimal('total_return_amount', 10, 2)->default(0);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->string('refund_method', 50)->nullable();
            $table->string('refund_reference', 100)->nullable();
            $table->text('refund_notes')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'refunded'])->default('draft');
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('repair_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_return_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['part', 'service']);
            $table->unsignedBigInteger('repair_part_id')->nullable();
            $table->unsignedBigInteger('repair_service_id')->nullable();
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('return_amount', 10, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('repair_part_id')->references('id')->on('repair_parts')->nullOnDelete();
            $table->foreign('repair_service_id')->references('id')->on('repair_services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_return_items');
        Schema::dropIfExists('repair_returns');
    }
};
