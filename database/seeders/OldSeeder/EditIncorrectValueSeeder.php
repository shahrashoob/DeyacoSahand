<?php

namespace Database\Seeders\OldSeeder;

use App\Http\Controllers\GoodsKindProcess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EditIncorrectValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table( "lines" )->whereNotIN("active_status_id",[1200,1210])->orWhereNull("active_status_id")->update(["active_status_id"=>1210]);
        DB::table( "stations" )->whereNotIN("active_status_id",[1200,1210])->orWhereNull("active_status_id")->update(["active_status_id"=>1210]);
        DB::table( "machine_types" )->whereNotIN("active_status_id",[1200,1210])->orWhereNull("active_status_id")->update(["active_status_id"=>1210]);
        DB::table( "machines" )->whereNotIN("active_status_id",[1200,1210])->orWhereNull("active_status_id")
        ->update(["active_status_id"=>1210, "production_status_id"=>null]);
        DB::table( "machines" )->whereNotIN("on_status_id",[53001,53002])->orWhereNull("on_status_id")
        ->update(["on_status_id"=>53001]);
    }
}
