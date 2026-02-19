<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialSoftwareTransKindTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_software_trans_kind', function (Blueprint $table) {
            $table->id();
            $table->foreignId("financial_software_id");
            $table->foreignId("warehouse_id");
            $table->foreignId("trans_kind_id");
            $table->integer("has_accounting_document")->default(0)->comment("ثبت سند حسابداری");
            $table->integer("has_warehouse_transaction")->default(0)->comment("ثبت تراکنش انبار");
            $table->integer("has_sale_invoice")->default(0)->comment("ثبت فاکتور انبار");
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
        Schema::dropIfExists('financial_software_trans_kind');
    }
}
