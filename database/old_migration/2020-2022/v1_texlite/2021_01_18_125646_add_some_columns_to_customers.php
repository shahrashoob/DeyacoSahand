<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsToCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->integer("user_id")->after("id")->nullable();
            $table->integer("gender_id");
            $table->date("birth_date")->nullable();
            $table->integer("customer_type_id");

            $table->integer("tariff_id")->default(0)->comment("نوع تعرفه");
            $table->float("cash_off_percent")->default(0)->comment("درصد تخفیف نقدی");

            $table->double("bail_amount",15,2)->default(0)->comment("میزان وثیقه");
            $table->string("economic_number")->nullable()->comment("شماره اقتصادی");
            $table->string("detailed_code")->nullable()->comment("نوسا کد تفظیلی ");
            $table->string("center_detailed_code")->nullable()->comment("کد مرکز تفظیلی ");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
