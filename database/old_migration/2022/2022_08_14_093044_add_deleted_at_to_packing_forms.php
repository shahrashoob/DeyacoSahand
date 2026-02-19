<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToPackingForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
            $table->dateTime("deleted_at")->nullable();
        });
        Schema::table('packing_form_item', function (Blueprint $table) {
            //
            $table->dateTime("deleted_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
}
