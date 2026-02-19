<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservoirsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservoirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("reservoir_type_id")->comment("نوع مخزن");
            $table->foreignId("unit_id");
            $table->foreignId("product_id");
            $table->double("capacity")->default(0)->comment("حداکثر ظرفیت مخزن");
            $table->double("amount", 15, 7)->default(0)->comment("مقدار موجودی مخزن");
            $table->double("sub_amount", 15, 7)->default(0)->comment("مقدار  فرعی موجودی مخزن");
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
        Schema::dropIfExists('reservoirs');
    }
}
