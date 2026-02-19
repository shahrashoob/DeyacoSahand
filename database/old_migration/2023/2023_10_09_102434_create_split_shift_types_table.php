<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSplitShiftTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('split_shift_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->time("start_time")->comment("زمان شروع حق شیفت");
            $table->time("end_time")->comment("زمان پایان حق شیفت");

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
        Schema::dropIfExists('split_shift_types');
    }
}
