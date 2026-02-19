<?php

namespace App\Http\Controllers\LineProductStation\Machine;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Station;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use Illuminate\Http\Request;

class MachineModuleTypeController extends Controller
{

    private $view_path = "line_product_station.machine.machine_module_type.";
    private $route_path = "line_product_station.machine.machine_module_type.";

    public function index(MachineType $machine_type, MachineModuleType $machine_module_type)
    {

        $property_list = MachineModuleType\MachineModuleTypeProperty::where([
            "machine_module_type_id" => $machine_module_type->id]
        )->
        get();
        if (count($property_list) == 0) {
            return back()->withErrors("برای هیچ کدام از ماژول ها تنظیمات وجود ندارد.");
        }
        $property_value = MachineModuleType\MachineModuleTypePropertyValue::where([
            "machine_type_id" => $machine_type->id,
            "machine_module_type_id" => $machine_module_type->id,
        ])->
        pluck("value", "machine_module_type_property_id")->toArray();
        $property_text_value = MachineModuleType\MachineModuleTypePropertyValue::where([
            "machine_type_id" => $machine_type->id,
            "machine_module_type_id" => $machine_module_type->id,
        ])->
        pluck("text_value", "machine_module_type_property_id")->toArray();
        return view($this->view_path . "index", compact("machine_type", "machine_module_type", "property_list", "property_value", "property_text_value"));
    }


    public function update(Request $request, MachineType $machine_type, MachineModuleType $machine_module_type)
    {

        $property_list = MachineModuleType\MachineModuleTypeProperty::where("machine_module_type_id", $machine_module_type->id)->get();

        MachineModuleType\MachineModuleTypePropertyValue::where([
            "machine_type_id" => $machine_type->id,
            "machine_module_type_id" => $machine_module_type->id,
        ])->
        delete();

        foreach ($property_list as $item) {
            $key = "mmtpv_" . $item->id;
            MachineModuleType\MachineModuleTypePropertyValue::create([
                "machine_type_id" => $machine_type->id,
                "machine_module_type_id" => $machine_module_type->id,
                "machine_module_type_property_id" => $item->id,
                "value" => $request->$key,
                "text_value" => $request->$key
            ]);
        }

        return back()->with(["success" => "بروزرسانی با موفقیت انجام شد"]);
    }
}
