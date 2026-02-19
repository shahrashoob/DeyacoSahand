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
        Schema::table('machine_type_input_band_goods_kind', function (Blueprint $table) {
            //
            $table->integer("warehouse_entry_confirmation_in_altogether")->default(0)->comment("تایید ورود به انبار  به صورت تجمیعی می باشد ");
            $table->integer("injection_into_machine_in_altogether")->default(0)->comment(" تزریق به مایشن به صورت تجمیعی می باشد ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_type_input_band_goods_kind', function (Blueprint $table) {
            //
        });
    }
};
