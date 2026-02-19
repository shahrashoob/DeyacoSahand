<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineFaultTypeSeeder extends Seeder
{
    private $data = [
        [ "id" => 1, "caption" => 'نقص کالا' ],
        [ "id" => 2, "caption" => 'نقص های فنی نرم افزار' ],
        [ "id" => 3, "caption" => 'نقص های فنی سخت افزار' ],
        [ "id" => 4, "caption" => 'عوامل محیطی' ],
        [ "id" => 5, "caption" => 'تعمیرات دوره ای' ],
        [ "id" => 6, "caption" => 'عوامل مدیریتی' ]
    ];

    private $table = 'machine_fault_types';

    public function run(): void
    {
        DB::table($this->table)->truncate();

        foreach ($this->data as $item) {
            DB::table($this->table)->insert($item);
        }
    }
}
