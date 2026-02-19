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
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->foreignId("material_packing_type_id_dependent_to_batch")->nullable()->
            after("material_id_dependent_to_batch")->
            comment("در صورتی که واحد بچ تعداد بسته بندی باشد، نوع بسته بندی را انتخاب می کنیم");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
};
