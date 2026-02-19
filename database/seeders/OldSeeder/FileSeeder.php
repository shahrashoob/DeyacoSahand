<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        //
        [ "id" => 1, "caption" => 'لوگو شرکت', "filename" => "dashboard_logo.png", "path" => "" ],
        [ "id" => 2, "caption" => 'لوگو صفحه لاگین', "filename" => "dashboard_logo.png", "path" => "" ],
        [ "id" => 200, "caption" => 'favicon', "filename" => "favicon.ico", "path" => "" ]
    ];
    private $table = 'files';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }

}
