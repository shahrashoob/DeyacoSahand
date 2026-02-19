<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('machine_allocation_modification_consumed', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_allocation_modification_form_id")->index("machine_allocation_modification_index");
            $table->foreignId("allocation_id")->index();
            $table->foreignId("product_id")->index();
            $table->double("consumed_amount",15,7)->comment("مقدار مصرف کالا در تخصیص (در برگشت مواد اولیه)");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `machine_allocation_modification_consumed` comment 'به ازای هر تخصیص در برگشت مواد اولیه مشخص می کنیم چه مقدار از کالا مصرف شده است.'");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_allocation_modification_consumed');
    }
};
