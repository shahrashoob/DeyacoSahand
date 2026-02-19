<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricRawDesignFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fabric_raw_design_forms', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->foreignId("machine_id");
            $table->boolean("design_available")->nullable()->comment("آیا طراحی موجود است");
            $table->boolean("it_has_pinning")->nullable()->comment("آیا لامل ریزی دارد");
            $table->boolean("warps_is_in_warehouse")->nullable()->comment("آیا چله در انبار هست؟");
            $table->boolean("need_to_convert")->nullable()->comment("نیار به تبدیل دارد؟");

            $table->foreignId("status_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fabric_raw_design_forms');
    }
}
