<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Saved WhatsApp groups / phone numbers
        Schema::create('wa_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('wa_id');                  // group-id like xxxxx@g.us  or  +91xxxxxxxxxx@c.us
            $table->enum('type', ['group', 'number'])->default('group');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Message schedules
        Schema::create('wa_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('group_ids');                // array of wa_groups.id
            $table->text('message_template');         // e.g. "{name} GAVE MONEY TO {amount} TRUST"
            $table->json('data_rows');                // [{name:"Raj",amount:"5000"}, ...]
            $table->enum('schedule_type', ['once', 'daily', 'weekly', 'cron'])->default('once');
            $table->string('cron_expression')->nullable();  // * * * * *  (cron type only)
            $table->timestamp('scheduled_at')->nullable();  // once type: exact datetime
            $table->string('schedule_time', 5)->nullable(); // HH:MM  (daily / weekly)
            $table->tinyInteger('schedule_day')->nullable();// 0-6 (weekly: 0=Sun)
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->unsignedInteger('sent_count')->default(0);
            $table->timestamps();
        });

        // History of every sent attempt
        Schema::create('wa_message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->nullable()->constrained('wa_schedules')->nullOnDelete();
            $table->string('schedule_name')->nullable();
            $table->string('group_wa_id');
            $table->string('group_name');
            $table->text('message');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_message_logs');
        Schema::dropIfExists('wa_schedules');
        Schema::dropIfExists('wa_groups');
    }
};
