<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserDependentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();;
            $table->foreignId('dependent_type_id')->nullable()->comment('نوع ارتباط با فرد');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();;
            $table->string('national_code')->nullable();;
            $table->date('date_of_birth')->nullable();;
            $table->timestamps();
        });
        DB::statement('ALTER TABLE user_dependents COMMENT "جدول مرتبط با افراد تحت تکفل کارمند"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_dependents');
    }
}
