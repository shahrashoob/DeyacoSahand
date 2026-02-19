<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmploymentSelectionIdInEmploymentSelectionIndicatorValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_selection_indicator_values', function (Blueprint $table) {
            $table->foreignId('employment_selection_id')->after('selection_indicator_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_selection_indicator_values', function (Blueprint $table) {
            //
        });
    }
}
