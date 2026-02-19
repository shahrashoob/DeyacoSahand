<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseHandlingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_handling', function (Blueprint $table) {
            $table->id();
            $table->foreignId("warehouse_id");
            $table->foreignId("user_id");
            $table->foreignId("status_id")->comment("524000 انبارگردانی");
            $table->date("start_datetime")->comment("تاریخ شروع انبارگردانی");
            $table->date("end_datetime")->comment("تاریخ پایان انبارگردانی");
            $table->timestamps();

            $table->index('user_id');
            $table->index('status_id');
        });

        DB::statement("ALTER TABLE `warehouse_handling` comment 'جدول لیست انبار گردانی ها'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_handling');
    }
}
