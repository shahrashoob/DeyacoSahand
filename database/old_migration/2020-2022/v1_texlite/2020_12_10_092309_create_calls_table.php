<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {

            $table->id();
            $table->integer("user_id");
            $table->integer("warehouse_import_status_id")->default(3340);
            $table->integer("nosa_import_status_id")->default(3340);

            $table->datetime("start_datetime")->nullable();
            $table->datetime("end_datetime")->nullable();
            $table->integer("run_time")->default(0)->comment(" run time in secounds");
            $table->integer("status_id")->default(3300);
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
        Schema::dropIfExists('calls');
    }
}
