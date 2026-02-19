<?php

namespace Database\Seeders\HR;

use App\Models\HR\Personal\Nationality;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

        public function run(): void
    {
        $Nationality = [
            ['id' => 1, 'caption' => 'ایرانی'],
            ['id' => 2, 'caption' => 'اتباع'],
        ];
        //DB::table('products')->insert($products);
        foreach ($Nationality as $Nationality) {
            Nationality::updateOrCreate(['id' => $Nationality['id']], $Nationality);
        }
    }

}
