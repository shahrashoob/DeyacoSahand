<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("special_license_type_id");
            $table->foreignId("reference_id");
            $table->foreignId("user_id")->comment("درخواست دهنده");
            $table->foreignId("status_id")->comment("گروه وضعیت با کد 6040");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `special_licenses` comment 'لیست همه مجوزهایی که توسط افراد درخواست شده است.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_licenses');
    }
}
