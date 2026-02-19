<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineModuleType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_module_types', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `machine_module_types` comment 'نوع دسته ماژول هایی که باید برای تغییر وضعیت های ماشین اجرا شود.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_madule_type');
    }
}
