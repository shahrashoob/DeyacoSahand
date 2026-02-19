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
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
            $table->integer("allow_show_sub_amount_in_exit_forms")->default(1)->comment("آیا ستون واحد فرعی در برگ خروج ها نمایش داده شود");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_kinds', function (Blueprint $table) {
            //
        });
    }
};
