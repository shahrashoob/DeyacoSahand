<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlgorithmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('algorithms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("algorithm_type_id");
            $table->string("caption");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `algorithms` comment 'لیست همه الگوریتم هایی که در سامانه طراحی شده است.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('algorithms');
    }
}
