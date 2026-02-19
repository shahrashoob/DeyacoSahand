<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OppKindSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        //
        [ "id" => 1, "caption" => 'طرف حساب عمومی' ],
        [ "id" => 2, "caption" => 'طرف حساب اختصاصی ' ],

    ];
    private $table = 'opp_kinds';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
