<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        ["id"=>1,"caption"=>"آقا","caption2"=>"جناب آقای"],
        ["id"=>2,"caption"=>"خانم","caption2"=>"سرکار خانم"],
        ["id"=>3,"caption"=>"جنس سوم","caption2"=>"آقا/خانم"],
    ];
    private $table = 'genders';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
