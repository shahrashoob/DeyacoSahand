<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'پیش فرض (نامشخص)' ],
        [ "id" => 2, "caption" => 'دسترسی به کارت تولید' ],
        [ "id" => 3, "caption" => 'دسترسی به گروه ماشین' ],
        [ "id" => 4, "caption" => 'دسترسی به فرم تولید' ],
        [ "id" => 5, "caption" => 'دسترسی به همکاری' ],

    ];
    private $table = 'permission_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
