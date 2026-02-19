<?php

namespace Database\Seeders\Utility\Document;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentReceiveStepDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * هر گامی چه مدارکی دریافت گردد
     * @return void
     */
    var $data = [
        ['id' => 1, 'receive_document_step_id' => 1,'document_type_id'=>1],
        ['id' => 2, 'receive_document_step_id' => 1,'document_type_id'=>2],
        ['id' => 3, 'receive_document_step_id' => 1,'document_type_id'=>3],
        ['id' => 4, 'receive_document_step_id' => 1,'document_type_id'=>4],
        ['id' => 5, 'receive_document_step_id' => 1,'document_type_id'=>5],
        ['id' => 7, 'receive_document_step_id' => 2,'document_type_id'=>11],
        ['id' => 8, 'receive_document_step_id' => 3,'document_type_id'=>7],
        ['id' => 9, 'receive_document_step_id' => 4,'document_type_id'=>7],
        ['id' => 10, 'receive_document_step_id' => 5,'document_type_id'=>7],
        ['id' => 11, 'receive_document_step_id' => 6,'document_type_id'=>7],
        ['id' => 12, 'receive_document_step_id' => 7,'document_type_id'=>7],
        ['id' => 13, 'receive_document_step_id' => 8,'document_type_id'=>9],
        ['id' => 14, 'receive_document_step_id' => 9,'document_type_id'=>12],
        ['id' => 15, 'receive_document_step_id' => 10,'document_type_id'=>8],
        ['id' => 16, 'receive_document_step_id' => 1,'document_type_id'=>13],
        ['id' => 17, 'receive_document_step_id' => 11,'document_type_id'=>14],
        ['id' => 18, 'receive_document_step_id' => 12,'document_type_id'=>15],

    ];
    private $table = 'document_receive_step_document_types';

    public function run()
    {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {

            if ( ! DB::table( $this->table )->
            where( "id", $item["id"] )->first() ) {
                DB::table( $this->table )->insert( $item );
            }else{
                DB::table( $this->table ) ->where( "id", $item["id"] )->update( $item );
            }

        }

    }
}
