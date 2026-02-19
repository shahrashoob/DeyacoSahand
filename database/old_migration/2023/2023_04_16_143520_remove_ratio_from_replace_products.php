<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRatioFromReplaceProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replace_products', function (Blueprint $table) {
            //
            $table->dropColumn("ratio");
            $table->dropColumn("replace_type_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replace_products', function (Blueprint $table) {
            //
        });
    }
}
