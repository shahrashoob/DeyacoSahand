<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_property_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contractor_id");
            $table->foreignId("contractor_property_id");
            $table->double("value");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contractor_property_value` comment 'جدول نگهداری مقادیر مشخصه های پیمانکاران'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_property_value');
    }
}
