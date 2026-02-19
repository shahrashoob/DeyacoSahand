<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChengePriceInMachineAllocationActualCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_allocation_actual_costs', function (Blueprint $table) {
            //
            DB::statement('alter table machine_allocation_actual_costs modify total_price DOUBLE(15,2) Null');
            DB::statement('alter table machine_allocation_actual_costs modify tax_price DOUBLE(15,2) Null');
            DB::statement('alter table machine_allocation_actual_costs modify transportation_fare_price DOUBLE(15,2) Null');
            DB::statement('alter table machine_allocation_actual_costs modify cost_of_one_unit DOUBLE(15,2) Null');
            DB::statement('alter table machine_allocation_actual_costs modify price DOUBLE(15,2) Null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_allocation_actual_costs', function (Blueprint $table) {
            //
        });
    }
}
