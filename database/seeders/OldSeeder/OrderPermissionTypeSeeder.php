<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderPermissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    private $data = [
        //
        ["id" => 1, "caption" => 'کارشناس فروش', "order_status_id" => "304020", "priority_order" => 100],
        ["id" => 4, "caption" => 'مدیر فروش', "order_status_id" => "304050", "priority_order" => 200],
        ["id" => 2, "caption" => 'پیش فاکتور توسط مشتری', "order_status_id" => "304030", "priority_order" => 300],
        ["id" => 3, "caption" => 'کارشناس وصول مطالبات', "order_status_id" => "304040", "priority_order" => 400],
        ["id" => 5, "caption" => 'مدیر مالی', "order_status_id" => "304060", "priority_order" => 500],
        ["id" => 6, "caption" => 'مدیر عامل', "order_status_id" => "304070", "priority_order" => 600],
        ["id" => 8, "caption" => 'هیئت مدیره', "order_status_id" => "304075", "priority_order" => 650],
        ["id" => 7, "caption" => 'پردازش ', "order_status_id" => "304080", "priority_order" => 800],

    ];
    private $table = 'order_permission_types';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
