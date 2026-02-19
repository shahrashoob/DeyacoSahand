<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellingTypeIdInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('selling_type_id')->default(2)->comment("نوع جذب در استخدام");
            $table->string('internet_account_username')->nullable()->comment("نام کاربری اینترنت");
            $table->string('detailed_code')->nullable()->comment("حساب تفضیلی");
            $table->foreignId('cost_center_id')->nullable()->comment("مرکز هزینه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
