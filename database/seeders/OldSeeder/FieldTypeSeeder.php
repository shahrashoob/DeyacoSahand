<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 1,"label"=>"int", "caption" => 'عدد  ' ],
        [ "id" => 2,"label"=>"char", "caption" => 'کاراکتر  ' ],
        [ "id" => 3,"label"=>"select", "caption" => 'لیست انتخابی' ],
        [ "id" => 4,"label"=>"image", "caption" => 'تصویر' ],
        [ "id" => 5,"label"=>"bool", "caption" => 'بولین (T/F)' ],
    ];
    private $table = 'field_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
