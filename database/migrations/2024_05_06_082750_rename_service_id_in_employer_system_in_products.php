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

            $table->renameColumn("service_id_in_employer_system_remove","service_id_in_employer_system");
        });

        DB::statement("ALTER TABLE `products` 	CHANGE COLUMN `service_id_in_employer_system` `service_id_in_employer_system` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'کد خدمت در سامانه کارفرما (کالاهای پیمانکاری)' ");

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
