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
        Schema::table('products', function (Blueprint $table) {
            //
            $table->integer("unit_of_measure_type_id_in_production")->default(1)->after("sub_unit2_id")->comment("نوع نمایش واحد کالا بر اساس اولویت در تولید");
            $table->integer("unit_of_measure_type_id_in_sale")->default(1)->after("sub_unit2_id")->comment("نوع نمایش واحد کالا بر اساس اولویت در فروش");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
