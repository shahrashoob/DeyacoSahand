<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->comment("کاربر");
            $table->foreignId("post_id")->comment("پست");
            $table->foreignId("shift_work_group_type_id")->comment("شیفت")->nullable();
            $table->foreignId("current_selection_id")->nullable();
            $table->foreignId("status_id")->comment("وضعیت");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `employments` comment 'جدول لیست در خواست های همکاری با ما'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employments');
    }
}
