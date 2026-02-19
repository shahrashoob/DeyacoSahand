<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->integer("is_default")->comment("آدرس پیش فرض")->default(0);
            $table->integer("country_id");
            $table->integer("province_id");
            $table->integer("state_id")->comment("شهرستان");
            $table->integer("city_id");
            $table->string("fax")->nullable();
            $table->string("phone")->nullable();
            $table->string("website")->nullable();
            $table->string("whatsapp")->nullable();
            $table->string("instagram")->nullable();
            $table->string("address")->nullable();
            $table->string("postal_code")->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
