<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->foreignId("parent_allocation_id")->index()->nullable()->
            after("allocation_id")->
            comment("شناسه تخصیص اصلی، برای تخصیص های مجدد");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
};
