<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    private $data = [
        //

        ["id"=>1000,"caption"=>"مجمع سهام داران - بالاترین سطح سازمان","parent_id"=>0,"organization_category_id"=>1],

        ["id" => 1001, "caption" => 'مدیر عامل',"parent_id"=>1000,"organization_category_id"=>100],

        ["id" => 2000, "caption" => 'مدیر سیستم ',"parent_id"=>1001,"organization_category_id"=>100,"shift_id"=>1],
        ["id" => 1002, "caption" => 'مدیر برنامه ریزی تولید',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1003, "caption" => 'مدیر پروژه',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1004, "caption" => 'مدیر برنامه ریزی فروش',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1005, "caption" => ' مدیر فروش',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1006, "caption" => ' مدیر بازرگانی',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1007, "caption" => ' مدیر انبار',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1008, "caption" => ' مدیر منابع انسانی ',"parent_id"=>1001,"organization_category_id"=>100],
        ["id" => 1016, "caption" => "مدیر تولید","parent_id"=>1001,"organization_category_id"=>100],



        ["id" => 1100, "caption" => "گروه مشتریان"],
        ["id" => 1200, "caption" => "گروه پیمانکاران"],
        ["id" => 1300, "caption" => "گروه تامین کنندگان"],

    ];
    private $table = 'posts';

    public function run()
    {

        foreach ($this->data as $item) {

           if(! DB::table($this->table)->where("id", $item["id"])->first()) {
               DB::table( $this->table )->insert( $item );
           }

        }
    }
}
