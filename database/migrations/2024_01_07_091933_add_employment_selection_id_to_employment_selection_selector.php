<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmploymentSelectionIdToEmploymentSelectionSelector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_selection_selector', function (Blueprint $table) {
            //
            $table->foreignId("employment_selection_id")->comment("شناسه گزینش در فرایند استخدام");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_selection_selector', function (Blueprint $table) {
            //
        });
    }
}
