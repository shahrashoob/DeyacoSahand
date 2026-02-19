<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;

class RejectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => ' خاتمه یافته   ' ],
        [ "id" => 2, "caption" => ' بازگشت به مرحله قبل ' ],
        [ "id" => 3, "caption" => ' بازگشت به داشبورد مشتری ' ],

    ];
    public function run()
    {
        //
    }
}
