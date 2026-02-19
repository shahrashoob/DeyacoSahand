<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationModificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_modification_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption")->comment("نوع اصلاح موجودی: برگشت مواد اولیه یا انبارگردانی ");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_allocation_modification_types');
    }
}
