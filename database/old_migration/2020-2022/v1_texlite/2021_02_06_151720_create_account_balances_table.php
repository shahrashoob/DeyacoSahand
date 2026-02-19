<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->integer("customer_id");

            $table->double("debtor_1401",15,2)->default(0)->comment("مانده بدهکار حساب های دریافتنی");
            $table->double("creditor_1401")->default(0)->comment("مانده بستانکار حساب های دریافتنی");


            $table->double("debtor_1301",15,2)->default(0)->comment("مانده بدهکار اسناد در گردش صندوق");
            $table->double("creditor_1301",15,2)->default(0)->comment("مانده بستانکار اسناد در گردش صندوق");

            $table->double("debtor_1302",15,2)->default(0)->comment("مانده بدهکار اسناد در جریان وصول");
            $table->double("creditor_1302",15,2)->default(0)->comment("مانده بستانکار اسناد در جریان وصول");

            $table->double("debtor_1304",15,2)->default(0)->comment("مانده بدهکار اسناد در جریان وصول بابت دین");
            $table->double("creditor_1304",15,2)->default(0)->comment("مانده بستانکار اسناد در جریان وصول بابت دین");

            $table->double("debtor_1303",15,2)->default(0)->comment("مانده بدهکار اسناد برگشتنی");
            $table->double("creditor_1303",15,2)->default(0)->comment("مانده بستانکار اسناد برگشتنی");

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
        Schema::dropIfExists('account_balances');
    }
}
