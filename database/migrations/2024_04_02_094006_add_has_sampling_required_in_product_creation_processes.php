<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasSamplingRequiredInProductCreationProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            $table->integer('has_sampling_required')->default(0)->comment("آیا  نیاز به نمونه گیری برای کالا در طراحی کالا الزامی است؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_creation_processes', function (Blueprint $table) {
            //
        });
    }
}
