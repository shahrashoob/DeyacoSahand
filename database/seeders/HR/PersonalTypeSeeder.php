<?php

namespace Database\Seeders\HR;

use App\Models\HR\Personal\PersonalType;
use Illuminate\Database\Seeder;

class PersonalTypeSeeder extends Seeder
{

    public function run(): void
    {
        $PersonalType = [
            ['id' => 1, 'caption' => 'حقیقی'],
            ['id' => 2, 'caption' => 'حقوقی'],
        ];
        //DB::table('products')->insert($products);
        foreach ($PersonalType as $PersonalType) {
            PersonalType::updateOrCreate(['id' => $PersonalType['id']], $PersonalType);
        }
    }

}
