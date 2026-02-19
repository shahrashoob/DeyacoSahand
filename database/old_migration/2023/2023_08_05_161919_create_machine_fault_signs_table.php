<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineFaultSignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_fault_signs', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `machine_fault_signs` comment 'لیست نمودهای برونی برای نقص های ماشین'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_fault_signs');
    }
}
