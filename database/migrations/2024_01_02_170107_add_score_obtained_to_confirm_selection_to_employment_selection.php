<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScoreObtainedToConfirmSelectionToEmploymentSelection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_selection', function (Blueprint $table) {
            $table->float('score_obtained_to_confirm_selection')->nullable()->comment('نمره کسب شده برای تایید گزینش');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_selection', function (Blueprint $table) {
            //
        });
    }
}
