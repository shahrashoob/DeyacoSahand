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
        Schema::table('order_packing_form', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE `order_packing_form` CHANGE COLUMN `packing_form_code` `packing_form_code` VARCHAR(191) NOT NULL COMMENT 'کد بسته بندی مشتری' COLLATE 'utf8mb4_unicode_ci' AFTER `material_id`;");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_packing_form', function (Blueprint $table) {
            //
        });
    }
};
