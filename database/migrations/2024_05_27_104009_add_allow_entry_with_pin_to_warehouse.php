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
        Schema::table('warehouses', function (Blueprint $table) {
            //
            $table->integer("allow_entry_with_pin")->default(0)->
                comment("آیا ورود به انبار می تواند با pin فرم های بسته بندی  انجام شود.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse', function (Blueprint $table) {
            //
        });
    }
};
