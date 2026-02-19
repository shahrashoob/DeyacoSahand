<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationActualCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_actual_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->foreignId("product_id");
            $table->double("total_price",15,2)->comment("مبلغ کل بدون مالیات")->default(0);
            $table->double("tax_price",15,2)->comment("مبلغ مالیات")->default(0);
            $table->double("transportation_fare_price",15,2)->comment("مبلغ کرایه حمل و نقل")->default(0);

            $table->double("amount",15,2)->comment("مقدار کل کالا");

            $table->double("cost_of_one_unit",15,2)->comment("بهای تمام شده یک واحد کالا");

            $table->timestamps();
        });
        DB::statement("ALTER TABLE `machine_allocation_actual_costs` comment 'جدول محاسبات بهای تمام شده به ازای هر تخصیص کالا'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_allocation_actual_cost');
    }
}
