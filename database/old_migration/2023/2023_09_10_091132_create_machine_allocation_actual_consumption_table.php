<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationActualConsumptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_actual_consumption', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->double("predictive_amount",15,7)->comment("مقدار پیش بینی به ازای یک واحد کالا");
            $table->double("actual_amount",15,7)->comment("مقدار مصرف واقعی به ازای یک واحد کالا");
            $table->double("change_degree_amount",15,7)->comment("مقدار ضایعات به ازای یک واحد کالا");
            $table->double("waste_amount",15,7)->comment("مقدار تغییر درجه داده شده به ازای یک واحد کالا");
            $table->double("calculated_amount_till_now",15,7)->default(0)->comment("مقدار محاسبه شده از تخصیص تاکنون ");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `machine_allocation_actual_consumption` comment 'جدول نگهداری مقدار واقعی مصرف به ازای هر تخصیص'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_allocation_actual_consumption');
    }
}
