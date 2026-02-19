<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionReplacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_replaces', function (Blueprint $table) {
            $table->id();
            $table->integer("production_id");
            $table->integer("production_replace_id")->comment("کارت تولید جایگزین شده");
            $table->integer("production_log_id")->comment(" سطر لاگ کارت تولید جایگزین شده");
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
        Schema::dropIfExists('production_replaces');
    }
}
