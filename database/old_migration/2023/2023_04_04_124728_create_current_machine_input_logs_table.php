<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentMachineInputLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_machine_input_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("current_machine_input_id");
            $table->foreignId("machine_id");
            $table->foreignId("allocation_id");
            $table->foreignId("production_id");
            $table->foreignId("production_form_id")->nullable();
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->foreignId("lot_number_id");
            $table->foreignId("entry_packing_form_id")->comment("کد بسته بندی که اپراتور وارد نموده است.");
            $table->foreignId("packing_form_id")->comment("کد بسته بندی که سیستم انتخاب کرده");
            $table->foreignId("user_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `current_machine_input_logs` comment 'هر زمان مواد اولیه به ماشین تزریق می شود، اطلاعات ورودی ها ماشین بروز می شود و لاگ آن در این جدول نگهداری می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_machine_input_logs');
    }
}
