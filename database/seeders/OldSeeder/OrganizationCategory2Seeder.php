<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationCategory2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'نامشخص'  ],

        // role_id > 100 => امکان افزودن شاغل وجود دارد
        [ "id" => 100, "caption" => 'مدیران'  ],
        [ "id" => 200, "caption" => 'کارکنان'  ],
        [ "id" => 300, "caption" => 'مشتریان'  ],

    ];
    private $table = 'organization_categories';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
