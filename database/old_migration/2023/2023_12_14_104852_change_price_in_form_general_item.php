<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePriceInFormGeneralItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_general_item', function (Blueprint $table) {
            //
            DB::statement('alter table form_general_item modify price DOUBLE(15,2) Null');
            DB::statement('alter table form_general_item modify tax_price DOUBLE(15,2) Null');
            DB::statement('alter table form_general_item modify total_price_with_tax DOUBLE(15,2) Null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_general_item', function (Blueprint $table) {
            //
        });
    }
}
