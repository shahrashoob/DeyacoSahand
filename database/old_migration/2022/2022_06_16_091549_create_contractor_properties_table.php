<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_properties', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->integer("min_value");
            $table->integer("max_value");
            $table->foreignId("field_type_id");
            $table->integer("priority_number");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contractor_properties` comment 'لیست مشخصه های پیمانکاران'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_properties');
    }
}
