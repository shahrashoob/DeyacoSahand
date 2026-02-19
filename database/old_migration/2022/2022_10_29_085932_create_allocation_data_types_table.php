<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationDataTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_data_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");

        });
        DB::statement("ALTER TABLE `allocation_data_types` comment 'در این جدول داده های متفرقه مربوط به تخصیصی ذخیره می گرد.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocation_data_types');
    }
}
