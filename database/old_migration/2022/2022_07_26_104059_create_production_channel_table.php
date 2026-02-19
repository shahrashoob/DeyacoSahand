<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_channels', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `production_channels` comment 'در این جدول کانال های تولید نمایش داده می شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_channel');
    }
}
