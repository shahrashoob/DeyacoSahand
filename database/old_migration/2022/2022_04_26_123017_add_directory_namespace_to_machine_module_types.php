<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDirectoryNamespaceToMachineModuleTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
            $table->string("directory_namespace")->comment("نام پوشه ای که کنترلرهای ماژول داخل آن هستند.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('machine_module_types', function (Blueprint $table) {
            //
        });
    }
}
