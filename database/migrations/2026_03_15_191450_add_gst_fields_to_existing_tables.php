<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Customers: add GSTIN and billing state ──
        Schema::table('customers', function (Blueprint $table) {
            $table->string('gstin', 15)->nullable()->after('address');
            $table->string('billing_state')->nullable()->after('gstin');
        });

        // ── Vendors: add GSTIN ──
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('gstin', 15)->nullable()->after('address');
        });

        // ── Parts: add HSN code and tax rate ──
        Schema::table('parts', function (Blueprint $table) {
            $table->string('hsn_code', 10)->nullable()->after('selling_price');
            $table->foreignId('tax_rate_id')->nullable()->after('hsn_code')->constrained('tax_rates')->nullOnDelete();
        });

        // ── Products: add HSN code and tax rate ──
        Schema::table('products', function (Blueprint $table) {
            $table->string('hsn_code', 10)->nullable()->after('selling_price');
            $table->foreignId('tax_rate_id')->nullable()->after('hsn_code')->constrained('tax_rates')->nullOnDelete();
        });

        // ── Service Types: add SAC code and tax rate ──
        Schema::table('service_types', function (Blueprint $table) {
            $table->string('sac_code', 10)->nullable()->after('default_price');
            $table->foreignId('tax_rate_id')->nullable()->after('sac_code')->constrained('tax_rates')->nullOnDelete();
        });

        // ── Invoices: add tax totals ──
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->default(0)->after('discount');
            $table->decimal('cgst_amount', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('sgst_amount', 10, 2)->default(0)->after('cgst_amount');
            $table->decimal('igst_amount', 10, 2)->default(0)->after('sgst_amount');
            $table->boolean('is_igst')->default(false)->after('igst_amount');
        });

        // ── Invoice Items: add per-item tax info ──
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('hsn_code', 10)->nullable()->after('total');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('hsn_code');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            $table->decimal('cgst_amount', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('sgst_amount', 10, 2)->default(0)->after('cgst_amount');
            $table->decimal('igst_amount', 10, 2)->default(0)->after('sgst_amount');
        });

        // ── Repair Parts: add per-part tax info ──
        Schema::table('repair_parts', function (Blueprint $table) {
            $table->string('hsn_code', 10)->nullable()->after('cost_price');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('hsn_code');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
        });

        // ── Repair Services: add per-service tax info ──
        Schema::table('repair_services', function (Blueprint $table) {
            $table->string('sac_code', 10)->nullable()->after('vendor_charge');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('sac_code');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
        });
    }

    public function down(): void
    {
        Schema::table('repair_services', function (Blueprint $table) {
            $table->dropColumn(['sac_code', 'tax_rate', 'tax_amount']);
        });
        Schema::table('repair_parts', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'tax_rate', 'tax_amount']);
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'tax_rate', 'tax_amount', 'cgst_amount', 'sgst_amount', 'igst_amount']);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'cgst_amount', 'sgst_amount', 'igst_amount', 'is_igst']);
        });
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropForeign(['tax_rate_id']);
            $table->dropColumn(['sac_code', 'tax_rate_id']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['tax_rate_id']);
            $table->dropColumn(['hsn_code', 'tax_rate_id']);
        });
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['tax_rate_id']);
            $table->dropColumn(['hsn_code', 'tax_rate_id']);
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['gstin']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'billing_state']);
        });
    }
};
