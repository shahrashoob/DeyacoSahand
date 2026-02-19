<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineModuleTypePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_module_type_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_module_type_id");
            $table->foreignId("button_id")->comment("به ازای هر ماژول یک دکمه معادل در جدول button ثبت می گردد.");
            $table->string("caption")->comment("نام مشخصه تنظیمات");
            $table->foreignId("field_type_id");
            $table->foreignId("special_unit_id")->nullable();
            $table->integer("min_value");
            $table->integer("max_value");
            $table->integer("priority_number")->default(1);
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `machine_module_type_properties` comment 'در این جدول به ازای هر ماژول ماشین، مشخصاتی ذخیره می گردد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_module_type_properties');
    }
}
