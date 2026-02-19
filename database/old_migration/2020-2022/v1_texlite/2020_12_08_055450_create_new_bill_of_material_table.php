<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNewBillOfMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('new_bill_of_material', function (Blueprint $table) {
            $table->id();
            $table->string("product_code");
            $table->string("product_id");
            $table->string("material_code");
            $table->string("material_id");
            $table->float("amount");
            $table->string("error")->nullable();
            
            
            
            $table->datetime("created_at")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime("updated_at")->default(DB::raw('CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_bill_of_material');
    }
}
