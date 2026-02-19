<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_type_id')->comment("نوع نماینده");
            $table->foreignId('user_id')->nullable();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->foreignId('employment_id')->nullable();
            $table->integer('status_id')->default(4642001);
            $table->foreignId('contractor_id')->nullable();
            $table->string('active_code')->nullable();

            $table->foreignId('has_the_right_to_sign')->default(0)->comment(" ایا حق امضا دارد یا خیر");
            $table->timestamps();
        });
        DB::statement('ALTER TABLE agents COMMENT "جدول مرتبط با نمایندگان "');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents');
    }
}
