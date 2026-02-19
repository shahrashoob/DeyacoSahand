<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCreationProcessPriorityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_creation_process_priority', function (Blueprint $table) {
            $table->id();
            $table->foreignId("button_id")->comment("شناسه عملیات در بخش طراحی کالا");
            $table->foreignId("next_status_id")->nullable()->comment(" وضعیت بعدی در صورت تایید گام");
            $table->foreignId("before_status_id")->nullable()->comment(" وضعیت بعدی در صورت عدم تایید گام");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_creation_process_priority` comment 'گام های فرایند طراحی کالا در این جدول مشخص می گردد.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_creation_processes_priority');
    }
}
