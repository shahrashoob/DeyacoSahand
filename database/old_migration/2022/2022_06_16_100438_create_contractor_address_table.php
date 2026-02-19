<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_address', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contractor_id");
            $table->foreignId("address_id");
            $table->integer("is_default")->default(0);
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contractor_address` comment 'آدرس های پیمانکاران'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_address');
    }
}
