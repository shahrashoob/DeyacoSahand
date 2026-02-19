<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePackingFormItemStatusToPackingItemImportantStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_form_item_status', function (Blueprint $table) {
            //

            $table->dropColumn("create_exit_form_at");
            $table->dropColumn("confirm_draft_form_at");
            $table->dropColumn("confirm_final_form_at");
            $table->dropColumn("loading_at");
            $table->dropColumn("confirm_guarding_at");
            $table->dropColumn("confirm_applicant_at");
            $table->dropColumn("applicant_type_id");
            $table->dropColumn("applicant_id");
            $table->foreignId("exit_form_id")->after("goods_kind_id")->nullable()->comment("شماره برگ خروج از انبار");

            $table->rename("packing_form_item_important_status");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_item_important_status', function (Blueprint $table) {
            //
        });
    }
}
