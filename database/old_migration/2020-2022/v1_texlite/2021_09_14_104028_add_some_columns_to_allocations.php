<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsToAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
            $table->boolean("has_product_change")->nullable()->comment("آیا تخصیص تغییر کالا داشته است.");
            $table->boolean("has_design_change")->nullable()->comment("آیا تخصیص تغییر طراحی داشته است.");
            $table->boolean("has_weft_density_change")->nullable()->comment("آیا تخصیص تغییر تراکم پود داشته است.");
            $table->boolean("has_yarn_weft_change")->nullable()->comment("آیا تخصیص تغییر نخ پود داشته است.");
            $table->boolean("has_warps_change")->nullable()->comment("آیا تخصیص تغییر چله داشته است.");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
        });
    }
}
