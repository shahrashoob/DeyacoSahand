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
        Schema::table('machine_product_properties', function (Blueprint $table) {
            $table->integer("code")->nullable()->after("id")->comment('کد تشیخص ویژگی کالا در مسیر محصول');
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `machine_product_properties` COMMENT='ویژگی های مسیر محصول به ازای هر استگاه کاری';");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_product_properties', function (Blueprint $table) {
            //
        });
    }
};
