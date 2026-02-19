<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'نا مشخص' ],
        [ "id" => 100,"caption" => 'مدیران ' ],
        [ "id" => 200,"caption" => 'کارکنان ' ],
        [ "id" => 300,"caption" => 'مشتریان ' ],
        [ "id" => 500,"caption" => 'تیم جمع آوری بار ' ],
        [ "id" => 510,"caption" => 'تیم بارگیری ' ],

    ];
    private $table = 'roles';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
