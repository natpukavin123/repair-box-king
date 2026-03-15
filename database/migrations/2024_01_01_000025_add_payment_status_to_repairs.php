<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update any 'delivered' status records to 'closed'
        DB::table('repairs')->where('status', 'delivered')->update(['status' => 'closed']);
    }

    public function down(): void
    {
        DB::table('repairs')->where('status', 'payment')->update(['status' => 'completed']);
    }
};
