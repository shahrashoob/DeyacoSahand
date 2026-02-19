<?php

namespace Database\Seeders\Accounting\Client ;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientTransactionTypeSeeder extends Seeder
{
    private
    $data = [
        //
        ['id' => 1, 'caption' => ' شارژ حساب '],
        ['id' => 2, 'caption' => ' هزینه پیامک '],
        ['id' => 3, 'caption' => ' حدمات مهندسی و فنی'],
        ['id' => 4, 'caption' => 'پشتیبانی سامانه'],
        ['id' => 5, 'caption' => 'خرید آنلاین مشتریان'],
        ['id' => 6, 'caption' => 'ارزش افزوده'],
        ['id' => 7, 'caption' => 'اشتراک ماهیانه کرپلتفرم'],


    ];
    private
    $table = 'client_transaction_types';

    public
    function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
