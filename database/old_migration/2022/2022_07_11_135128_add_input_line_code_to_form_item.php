<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputLineCodeToFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_item', function (Blueprint $table) {
            //

            $table->integer("io_line_code")->nullable()->comment("به ازای هر آیتم درخواست کالا از انبار، باید مشخص باشد برای کدام خط ورودی/خروجی استفاده می گردد. ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_item', function (Blueprint $table) {
            //
        });
    }
}
