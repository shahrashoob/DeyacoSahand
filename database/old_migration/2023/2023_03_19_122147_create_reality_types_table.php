<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reality_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
        });
        DB::statement("ALTER TABLE `reality_types` comment 'نوع واقعیت: حقیقی| مجازی'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reality_types');
    }
}
