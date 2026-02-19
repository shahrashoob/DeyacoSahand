<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineProperty;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Station;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class MachinePropertyController extends Controller {
    //
    var $route_path="";
    var $view_path="line_product_station.station.machine_property.";
    public function index( Station $station ) {
        $field_type_option=Option::get("field_type");
        $special_units=Option::get("special_unit",0,2);
        return view( $this->view_path."index", compact( "station","special_units","field_type_option" ) );
    }

    public function store( Request $request, Machine $machine ) {
        if (
        MachineProperty::where( [ "machine_id" => $machine->id, "caption" => $request->caption ] )->exists()
        ) {
            return back()->withErrors( " این ویژگی برای ماشین قبلا ثبت شده است." );
        }

        MachineProperty::create([ "machine_id" => $machine->id, "caption" => $request->caption ]);
        return back()->with(["success"=>"یک ویژگی جدید ثبت گردید"]);
    }
    public function delete(Machine $machine, MachineProperty $machine_property){
        if($machine->id!=$machine_property->machine_id){
            return back()->withErrors("ماشین به درستی انتخاب نشده است.");
        }
        if(MachinePropertyValue::where(["machine_property_id"=>$machine_property->id])->exists()){
            return back()->withErrors("به دلیل استفاده شدن در جداول، امکان حذف ویژگی وجود ندارد.");
        }
        $machine_property->delete();
        return back()->with(["success"=>"ویژگی با موفقیت حذف گردید"]);
    }
}
