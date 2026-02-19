<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSelectionIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('caption');
            $table->foreignId("selection_id");
            $table->foreignId("field_type_id");
            $table->integer("weight");
            $table->float("min_score");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `selection_indicators` comment 'محتوای گزینش(شاخص-گزینش)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selection_indicators');
    }
}
