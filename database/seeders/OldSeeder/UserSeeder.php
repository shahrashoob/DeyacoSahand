<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1,"lastname"=>"مدیر سیستم","national_code"=>1,"firstname"=>"مدیریت","name"=>"diyako","mobile"=>"9134516608","email"=>"admin_system","password"=>"@dmin","cooperation_type_id"=>4],
        [ "id" => 2,"lastname"=>"دیاکو","national_code"=>1,"firstname"=>"دستیار دیجیتال","user_type_id"=>3,"name"=>"digital_robot","mobile"=>"9134516608","email"=>"digital_robot","password"=>"@deyako_digit","cooperation_type_id"=>4],
        [ "id" => 4,"lastname"=>"انباردار","national_code"=>1,"firstname"=>"دستیار دیجیتال","user_type_id"=>3,"name"=>"digit_storekeeper_robot","mobile"=>"9134516608","email"=>"digit_storekeeper","password"=>"@digit_storekeeper","cooperation_type_id"=>4]

    ];
    private $table = 'users';




    public function run() {

        foreach ( $this->data as $item ) {
            if(!DB::table( $this->table )->where(["id"=>$item["id"]])->exists()){
                $item["password"] = Hash::make($item['password']);
                DB::table( $this->table )->insert( $item );
            }
        }

    }


}
