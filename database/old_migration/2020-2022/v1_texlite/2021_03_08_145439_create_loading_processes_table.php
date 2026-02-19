<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadingProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loading_processes', function (Blueprint $table) {
            $table->id();
            $table->integer("code")->nullable();
            $table->integer("order_id");
            $table->integer("status_id");
            $table->integer("supervisor_collect_post_id")->comment(" تیم جمع آوری بار");
            $table->integer("supervisor_loading_post_id")->comment(" تیم بارگیری");
            $table->integer("car_type_id");
            $table->integer("car_id");
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
        Schema::dropIfExists('loading_processes');
    }
}
