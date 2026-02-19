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
        Schema::table('machine_type_input_algorithm', function (Blueprint $table) {
            //
            $table->integer("dependency_to_other_goods_kind")->default(1)->comment("آیا درخواست های کالا مستقل است یا به دیگر رسته کالایی ها وابسته است.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_input_algorithm', function (Blueprint $table) {
            //
        });
    }
};
