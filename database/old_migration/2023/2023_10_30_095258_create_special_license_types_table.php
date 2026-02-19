<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialLicenseTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_license_types', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->text("description");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `special_license_types` comment 'لیست مجوزهای خاص در کل سامانه'");
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
