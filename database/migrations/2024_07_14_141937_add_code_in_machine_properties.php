<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('machine_properties', function (Blueprint $table) {
            $table->integer("code")->nullable()->after("id")->comment('کد تشیخص ویژگی ماشین در ایستگاه');
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `machine_properties` 	COMMENT='مشخصات گروه ماشین های به ازای هر ایستگاه کاری';");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_properties', function (Blueprint $table) {
            //
        });
    }
};
