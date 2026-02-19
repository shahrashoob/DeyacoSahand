<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        ["id" => 1, "caption" => "مدیر عامل","number_of_agent"=>1],
        ["id" => 2, "caption" => "رییس هیئت مدیره","number_of_agent"=>1],
        ["id" => 3, "caption" => "نماینده","number_of_agent"=>1000],
    ];
    private $table = 'agent_types';

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
