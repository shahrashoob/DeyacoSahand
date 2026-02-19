<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineAllocationIdToImportPackingForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('import_package_form', function (Blueprint $table) {
//            // چون در بروز رسانی یکی از نسخه های جدول آن وجود نداشت، این مایگریشن حذف شد.
//            $table->foreignId("machine_allocation_id");
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('import_packing_form', function (Blueprint $table) {
            //
        });
    }
}
