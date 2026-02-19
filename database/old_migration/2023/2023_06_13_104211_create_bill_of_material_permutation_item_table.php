<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialPermutationItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_material_permutation_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bill_of_material_permutation_id");
            $table->foreignId("bill_of_material_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `bill_of_material_permutation_item` comment 'هر محصولی که از جایگردی مواد اولیه به دست می آید، ترکیب مواد اولیه و مواد اولیه جایگزین آنها را در این جدول ذخیره می گینم.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_of_material_permutation_item');
    }
}
