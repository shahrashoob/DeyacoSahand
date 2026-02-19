<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationImportantStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_important_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->dateTime("allocation_created_at")->nullable()->comment("تاریخ تخصیص");
            $table->dateTime("allocation_coordinating_for_delivery_at")->nullable()->comment("تاریخ هماهنگی جهت ارسال مواد اولیه");
            $table->dateTime("allocation_raw_delivery_at")->nullable()->comment("تاریخ هماهنگی جهت ارسال مواد اولیه");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `allocation_important_status` comment 'تاریخ های مهم تخصیص'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocation_important_status');
    }
}
