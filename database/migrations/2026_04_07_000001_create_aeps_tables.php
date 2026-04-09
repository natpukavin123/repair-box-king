<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AePS Wallet — single row tracks current balance
        Schema::create('aeps_wallet', function (Blueprint $table) {
            $table->id();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
        });

        // Wallet transactions — every top-up, withdrawal, commission, etc.
        Schema::create('aeps_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['topup', 'withdrawal', 'commission', 'adjustment']);
            $table->decimal('amount', 12, 2);
            $table->enum('direction', ['IN', 'OUT']); // IN = credit, OUT = debit
            $table->decimal('balance_after', 12, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Customer AePS service entries — every customer served
        Schema::create('aeps_customer_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 150)->nullable(); // fallback if no customer record
            $table->string('aadhaar_last4', 4)->nullable();
            $table->enum('service_type', ['cash_withdrawal', 'balance_enquiry', 'mini_statement', 'cash_deposit', 'aadhaar_pay']);
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('commission_earned', 10, 2)->default(0);
            $table->string('bank_name', 100)->nullable();
            $table->string('transaction_ref', 100)->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('success');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed initial wallet row
        DB::table('aeps_wallet')->insert(['balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('aeps_customer_services');
        Schema::dropIfExists('aeps_wallet_transactions');
        Schema::dropIfExists('aeps_wallet');
    }
};
