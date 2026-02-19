<?php

namespace Database\Seeders\HR;

use App\Models\HR\Personal\Nationality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MilitaryInformationSeesder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ['id' => 1, 'caption' => "در حین خدمت سربازی",'document_caption'=>"تصویر اعزام به سربازی"],
        ['id' => 2, 'caption' => "معافیت تحصیلی",'document_caption'=>"تصویر معافیت تحصیلی"],
        ['id' => 3, 'caption' => "اتمام خدمت سربازی",'document_caption'=>"کارت پایان خدمت"],
        ['id' => 4, 'caption' => "معاف از سربازی",'document_caption'=>"تصویر معافیت از سربازی"],
    ];

    private $table = 'military_informations';

    public function run()
    {

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
