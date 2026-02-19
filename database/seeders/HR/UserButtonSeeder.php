<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserButtonSeeder extends Seeder
{

    public function getPersonalButton(){
        return  [
            "module_code"     => "4620",
            "controller_info" => \App\Http\Controllers\HR\PersonalController::get_controller_info()

        ];
    }

    public function run() {



        // Packing Form Button
        DB::table( "buttons" )->where( [ "status_type_id" => $this->getPersonalButton()["module_code"] ] )->delete();

        foreach ( $this->getPersonalButton()["controller_info"] as $key => $item ) {
            $id      = $this->getPersonalButton()["module_code"] . $key;
            $name    = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table( "buttons" )->insert( [
                "id"             => $id,
                "name"           => $name,
                "caption"        => $caption,
                "menu_id"        => 515, // current_user
                "status_type_id" => $this->getPersonalButton()["module_code"]
            ] );

        }
    }


}
