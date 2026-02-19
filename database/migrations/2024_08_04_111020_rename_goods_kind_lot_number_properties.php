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
        Schema::table('goods_kind_lot_number_properties', function (Blueprint $table) {
            $table->dropColumn("goods_kind_id");
        });
        Schema::table('goods_kind_lot_number_properties', function (Blueprint $table) {
            $table->rename("lot_number_properties");
        });

        DB::statement("ALTER TABLE `lot_number_properties` comment 'لات ها یک سری مشخصات دارند'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
