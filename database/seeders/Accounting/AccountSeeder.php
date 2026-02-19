<?php

namespace Database\Seeders\Accounting;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1,"code"=>"1", "caption" => 'دارایی ها', ],
        [ "id" => 2,"code"=>"2", "caption" => 'بدهی ها' ,],
        [ "id" => 3,"code"=>"3" ,"caption" => 'حقوق صاحبان سهام', ],
        [ "id" => 4,"code"=>"4", "caption" => 'درآمد ها', ],
        [ "id" => 5,"code"=>"5", "caption" => 'هزینه ها', ],
        [ "id" => 6,"code"=>"6", "caption" => 'کنترل ها (حساب های انتظامی)', ],

    ];
    private $table = 'accounts';

    public function run() {
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
        }

    }
}
