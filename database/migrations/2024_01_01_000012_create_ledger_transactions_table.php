<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['sale', 'repair', 'recharge', 'expense', 'refund', 'purchase']);
            $table->string('reference_module', 100);
            $table->unsignedBigInteger('reference_id');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->nullable();
            $table->enum('direction', ['IN', 'OUT']);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['reference_module', 'reference_id']);
            $table->index('transaction_type');
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_transactions');
    }
};
