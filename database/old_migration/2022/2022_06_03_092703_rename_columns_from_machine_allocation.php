<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsFromMachineAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
            $table->renameColumn("practical_time","predict_of_production_time_practical");
            $table->renameColumn("theory_time","predict_of_production_time_theory");

            $table->renameColumn("start_practical_time","predict_of_production_start_date_practical");
            $table->renameColumn("start_theory_time","predict_of_production_start_date_theory");

            $table->renameColumn("start_practical_time_updated","predict_of_production_start_date_practical_updated");
            $table->renameColumn("start_theory_time_updated","predict_of_production_start_date_theory_updated");
            $table->renameColumn("start_time","production_start_date");

//

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation', function (Blueprint $table) {
            //
        });
    }
}
