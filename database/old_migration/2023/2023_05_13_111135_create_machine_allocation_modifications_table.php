<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationModificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_id");
            $table->foreignId("machine_log_id");
            $table->foreignId("status_id");
            $table->foreignId("user_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `machine_allocation_modifications` comment 'در زمان ثبت برگشت مواد اولیه، اطلاعات فرم برگشت شده در این جدول ذخیره می گردد'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_allocation_modifications');
    }
}
