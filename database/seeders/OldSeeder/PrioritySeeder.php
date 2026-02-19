<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritySeeder extends Seeder
{
    private $data = [
        //تولید
        [ "id" => 1, "caption" => ' خیلی فوری ',"priority_type_id"=>1 ],
        [ "id" => 2, "caption" => ' فوری ' ,"priority_type_id"=>1],
        [ "id" => 3, "caption" => ' عادی ' ,"priority_type_id"=>1],
        [ "id" => 7, "caption" => ' عادی (دستور تولید) ',"priority_type_id"=>1 ],
        [ "id" => 9, "caption" => ' خیلی فوری (دستور تولید) ',"priority_type_id"=>1 ],
        [ "id" => 10, "caption" => ' فوری (دستور تولید) ',"priority_type_id"=>1 ],

        //اتوماسیون اداری
        [ "id" => 201, "caption" => ' عادی ' ,"priority_type_id"=>2],
        [ "id" => 202, "caption" => ' فوری ' ,"priority_type_id"=>2],
        [ "id" => 203, "caption" => ' خیلی فوری ',"priority_type_id"=>2 ],
        [ "id" => 204, "caption" => ' آنی ' ,"priority_type_id"=>2],

    ];
    private $table = 'priorities';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
