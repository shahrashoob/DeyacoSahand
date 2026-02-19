<?php

namespace Database\Seeders\Accounting\Client;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientFactorTypeSeeder extends Seeder
{
    private
        $data = [
        //

        ['id' => 1, 'caption' => ' هزینه پیامک '],
        ['id' => 2, 'caption' => 'خرید ماژول'],
        ['id' => 3, 'caption' => ' هزینه پشتیبانی'],
        ['id' => 4, 'caption' => ' هزینه اشتراک ماهیانه کر پلتفرم'],



    ];
    private
        $table = 'client_factor_types';

    public
    function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
