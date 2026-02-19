<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckInventoryForCalculateActualConsumptionToMachineAllocationModifications extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
            $table->integer( "check_inventory_for_calculate_actual_consumption" )->
            comment( "برای محاسبه مصرف واقعی، آیا موجودی کالا چک شود؟ بله: موجودی انبارک چک می شود و اگر صفر نشده بود، خطا می دهد." )->
            default( 1 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'machine_allocation_modifications', function ( Blueprint $table ) {
            //
        } );
    }
}
