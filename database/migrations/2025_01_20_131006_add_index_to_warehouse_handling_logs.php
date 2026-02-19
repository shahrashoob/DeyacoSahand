<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouse_handling_logs', function (Blueprint $table) {
            //
            $table->index("warehouse_handling_id");
            $table->index("status_id");
            $table->index("event_id");
            $table->index("user_id");
            $table->index("message_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_handling_logs', function (Blueprint $table) {
            //
        });
    }
};
