<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPermissionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_permission_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->integer("order_status_id")->comment("ریز وضعیت نوع 304 برای سفارش ها");
            $table->integer("priority_order")->comment("ترتیب اولویت ");
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
        Schema::dropIfExists('order_permission_types');
    }
}
