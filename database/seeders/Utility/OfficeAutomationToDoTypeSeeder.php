<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeAutomationToDoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1, "caption" => 'جهت رونوشت' ],
        [ "id" => 2, "caption" => 'جهت اقدام' ],
        [ "id" => 3, "caption" => 'جهت بررسی' ],
        [ "id" => 4, "caption" => 'جهت دستور' ],
        [ "id" => 5, "caption" => 'جهت مشاهده' ],

    ];
    private $table = 'office_automation_to_do_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
