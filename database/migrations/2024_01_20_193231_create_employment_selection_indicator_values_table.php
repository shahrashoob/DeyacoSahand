<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentSelectionIndicatorValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_selection_indicator_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employment_id");
            $table->foreignId("selection_id");
            $table->foreignId("selection_indicator_id");
            $table->foreignId("post_id");
            $table->float("value");
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
        Schema::dropIfExists('employment_selection_indicator_values');
    }
}
