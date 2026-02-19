<?php

namespace Database\Seeders\HR;

use App\Models\Post\PostEntryStatus;
use App\Models\Utility\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostEntryStatusSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $table = 'post_entry_status';
    public function run() {
        //
        $status = Status::where( "status_type_id", 4620 )->get();
        if ( DB::table( $this->table )->count() == 0 ) {
            foreach ( $status as $item ) {
                DB::table( $this->table )->insert( [ "post_id" => 2000, "status_id" => $item->id,"allow_show_personal_menu"=>1 ,"allow_show_all_menu"=>1] );
            }
        }
    }
}
