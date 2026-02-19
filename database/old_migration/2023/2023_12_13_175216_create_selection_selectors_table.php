<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectionSelectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_selectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId("selection_id")->comment("گزینش");
            $table->foreignId("post_id")->nullable()->comment("پست");
            $table->foreignId("post_selection_id")->nullable()->comment("پست که در گزینش انتخاب می شود.");
            $table->foreignId("committee_id")->nullable()->comment("کمیته");
            $table->integer("minimum_percent_of_committee")->nullable()->comment("حداقل درصد کمیته تایید کمیته");
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
        Schema::dropIfExists('selection_selectors');
    }
}
