<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmploymentSelectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employment_selection', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->comment('ایدی جدول کارمند');
            $table->foreignId("selection_id")->comment("گزینش");
            $table->foreignId('status_id')->comment('وضعیت ');
            $table->integer("minimum_score_to_confirm_selection")->comment("حداقل امتیاز برای تایید مصاحبه");
            $table->integer("priority_number")->comment("اولویت");
            $table->datetime('coordination_time')->nullable()->comment('زمان هماهنگی ');

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
        Schema::dropIfExists('employment_selection');
    }
}
