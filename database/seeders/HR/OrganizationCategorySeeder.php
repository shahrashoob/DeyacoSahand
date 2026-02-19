<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "نامشخص"],
        ["id" => 100, "caption" => "مدیران"],
        ["id" => 200, "caption" => "کارکنان"],
        ["id" => 300, "caption" => "مشتریان"],
    ];
    private $table = 'organization_categories';

    public function run() {

        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }

        }

    }
}
