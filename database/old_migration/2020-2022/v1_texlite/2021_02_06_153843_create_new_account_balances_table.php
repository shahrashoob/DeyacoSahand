<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewAccountBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_account_balances', function (Blueprint $table) {
            $table->id();
            $table->integer("customer_id");
            $table->string("caption");
            $table->integer("detailed_code");

            $table->double("debtor",15,2)->default(0)->comment("مانده بدهکار ");
            $table->double("creditor",15,2)->default(0)->comment("مانده بستانکار ");

            $table->string("error");
            $table->integer("code");
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
        Schema::dropIfExists('new_account_balances');
    }
}
