<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostUserSeeder extends Seeder
{
    private $data = [
        //
        [ "post_id" => 2000, "user_id" => 1 ],

    ];
    private $table = 'post_user';

    public function run() {

        foreach ( $this->data as $item ) {
           if(!DB::table( $this->table )->where(["post_id"=>$item["post_id"],"user_id"=>$item["user_id"]])->exists())
                DB::table( $this->table )->insert( $item );
        }
    }
}
