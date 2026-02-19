<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingDocumentsWarehouseTransactionSaleInvoiceToTransKind extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trans_kinds', function (Blueprint $table) {
            //
            $table->integer("has_accounting_document")->default(0)->comment("آیا این نوع رخداد، ثبت سند حساب داری دارد؟");
            $table->integer("has_warehouse_transaction")->default(0)->comment("آیا این نوع رخداد، ثبت تراکنش انبار داری دارد؟");
            $table->integer("has_sale_invoice")->default(0)->comment("آیا این نوع رخداد، ثبت فاکتور فروش دارد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trans_kind', function (Blueprint $table) {
            //
        });
    }
}
