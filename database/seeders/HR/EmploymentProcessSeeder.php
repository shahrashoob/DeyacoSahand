<?php

namespace Database\Seeders\HR;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $controller_info = \App\Http\Controllers\HR\Employment\Admin\DashboardController::get_controller_info();
        $module_code = 4640;
        foreach ($controller_info as $key => $item) {
            $id = $module_code . $key;
            $name = $item["route"] . "index";
            $caption = $item["button"]["caption"];

            DB::table("buttons")->insert([
                "id" => $id,
                "name" => $name,
                "caption" => $caption,
                "status_type_id" => $module_code
            ]);

        }
    }
}
