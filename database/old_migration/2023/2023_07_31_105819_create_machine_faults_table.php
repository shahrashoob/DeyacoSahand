<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineFaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_faults', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("string");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `machine_faults` comment 'جدول همه عیب هایی که ممکن است در ماشین اتفاق بیفتد'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_faults');
    }
}
