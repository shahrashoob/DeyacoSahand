<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentSelectionSelectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_selection_selector', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->comment('ایدی جدول کارمند');
            $table->foreignId("selection_id")->comment("گزینش");
            $table->foreignId("post_id")->nullable()->comment("پست که در گزینش انتخاب می شود.");
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
        Schema::dropIfExists('employment_selection_selector');
    }
}
