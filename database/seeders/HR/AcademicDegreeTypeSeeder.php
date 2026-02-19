<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicDegreeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [

        ["id" => 1, "caption" => "کاردانی ناپیوسته",'receive_document_step_id'=>4],
        ["id" => 2, "caption" => "دکتری تخصصی",'receive_document_step_id'=>7],
        ["id" => 3, "caption" => "کارشناسی ارشد ناپیوسته",'receive_document_step_id'=>6],
        ["id" => 4, "caption" => "کارشناسی پیوسته",'receive_document_step_id'=>5],
        ["id" => 5, "caption" => "کارشناسی ناپیوسته",'receive_document_step_id'=>5],
        ["id" => 6, "caption" => "دکتری عمومی",'receive_document_step_id'=>7],
        ["id" => 7, "caption" => "دیپلمای دانشگاهی",'receive_document_step_id'=>4],
        ["id" => 8, "caption" => "کارشناسی ارشد پیوسته",'receive_document_step_id'=>6],
        ["id" => 9, "caption" => "دکتری دستیاری",'receive_document_step_id'=>7],
        ["id" => 10, "caption" => "کاردانی پیوسته",'receive_document_step_id'=>4],
        ["id" => 11, "caption" => "دوره ابتدایی",'receive_document_step_id'=>3],
        ["id" => 12, "caption" => "سیکل",'receive_document_step_id'=>3],
        ["id" => 13, "caption" => "دیپلم",'receive_document_step_id'=>4],
    ];
    private $table = 'academic_degree_types';

    public function run()
    {

        foreach ($this->data as $item) {

            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( "id", $item["id"] )->update($item);
            }


        }

    }
}
