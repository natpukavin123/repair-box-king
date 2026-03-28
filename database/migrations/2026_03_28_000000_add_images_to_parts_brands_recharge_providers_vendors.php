<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->string('image')->nullable()->after('selling_price');
            $table->string('thumbnail')->nullable()->after('image');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->string('image')->nullable()->after('name');
            $table->string('thumbnail')->nullable()->after('image');
        });

        Schema::table('recharge_providers', function (Blueprint $table) {
            $table->string('image')->nullable()->after('commission_percentage');
            $table->string('thumbnail')->nullable()->after('image');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('image')->nullable()->after('specialization');
            $table->string('thumbnail')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn(['image', 'thumbnail']);
        });
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['image', 'thumbnail']);
        });
        Schema::table('recharge_providers', function (Blueprint $table) {
            $table->dropColumn(['image', 'thumbnail']);
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['image', 'thumbnail']);
        });
    }
};
