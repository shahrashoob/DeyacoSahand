<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCreationProcessPrioritySeeder extends Seeder
{
    private $data = [
    ];
    private $table = 'packing_type_label_printing_types';

    public function run()
    {
       // اطلاعات این seed در seed زیر تکمیل می گردد.
        ProductCreationProcessSeeder::class;
    }
}
