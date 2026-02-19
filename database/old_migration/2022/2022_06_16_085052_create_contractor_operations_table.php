<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contractor_id");
            $table->string("caption");
            $table->string("code");
            $table->foreignId("active_status_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contractor_operations` comment 'لیست انواع عملیات پیمانکاران'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_operations');
    }
}
