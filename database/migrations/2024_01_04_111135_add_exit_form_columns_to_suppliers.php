<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExitFormColumnsToSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
            $table->integer("exit_form_require_draft_permission")->default(0)->comment("آیا برگ خروج از انبار نیاز به تایید پیش نویس (واحد مالی) دارد؟");
            $table->foreignId("exit_form_require_draft_permission_post_id")->default(0)->comment("پست مربوطه جهت ارسال پیامک تایید پیش نویس برگ خروج");

            $table->integer("exit_form_require_permission")->default(0)->comment("آیا برگ خروج از انبار نیاز به تایید نهایی (واحد مالی) دارد؟");
            $table->foreignId("exit_form_require_permission_post_id")->default(0)->comment("پست مربوطه جهت ارسال پیامک تایید نهایی برگ خروج");

            $table->integer("exit_form_loading_require_permission")->default(0)->comment("آیا برگ خروج از انبار نیاز به ارسال دارد؟");

            $table->integer("exit_form_guarding_require_permission")->default(0)->comment("آیا برگ خروج از انبار نیاز به تایید نگهبانی دارد؟");
            $table->foreignId("exit_form_guarding_require_permission_post_id")->default(0)->comment("پست مربوطه جهت ارسال پیامک تایید نگهبانی برگ خروج");

            $table->integer("checking_carrier_at_delivery_of_product")->default(0)->comment("آیا کد بسته بندی / حامل در زمان تایید تحویل کالا توسط تامین کننده چک شود؟	");

            $table->integer("can_i_borrow_from_this_supplier")->default(0)->comment("آیا می توان از این تامین کننده قرض گرفت");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
        });
    }
}
