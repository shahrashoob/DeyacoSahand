<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void 
    {
        Schema::table('order_packing_form', function (Blueprint $table) {
            //
            $table->foreignId("packing_type_id")->after("packing_form_code");
            $table->double("weight", 15, 7)->after("amount");
            $table->double("gross_weight", 15, 7)->after("weight");
            $table->double("sub_amount", 15, 7)->after("amount");
            $table->foreignId("degree_id")->change();
            $table->integer("sub_packing_form_number")->default(0);
            $table->foreignId("lot_number_id")->index();

        });
        DB::statement("ALTER TABLE `order_packing_form` CHANGE COLUMN `amount` `amount` DOUBLE(15,7) NOT NULL DEFAULT 0 COLLATE 'utf8mb4_unicode_ci' AFTER `lot_number_code`;");

//        "ALTER TABLE `order_packing_form`
//	CHANGE COLUMN `amount` `amount` DOUBLE(15,7) NOT NULL DEFAULT 0 COLLATE 'utf8mb4_unicode_ci' AFTER `lot_number_code`;"
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
