<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineProperty;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Machine\MachineTypeLineInputGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOperation;
use App\Models\LineProduct\Station;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingGoodsKindTimeLimit;
use Illuminate\Http\Request;

class MachineTypeController extends Controller
{

    public function index(Station $station)
    {
        $list = MachineType:: where("station_id", $station->id)->paginate(30);

        return view("line_product_station.machine_type.index", compact("station", "list"));
    }

    public function create(Station $station)
    {

        $machine_type = new MachineType();
        $machine_type->station_id = $station->id;
        $status_option = Option::get("active_status", 0);
        $station_option = Option::get("station", $machine_type->station->id);
        $machine_module_type_option = Option::get("machine_module_type");
        $cost_center_option = Option::get("cost_center", 0);
        $machine_type_consumption_type_option = Option::get("machine_type_consumption_type", 0);

        $option = [];
        foreach ($machine_type->machine_property() as $item) {
            if ($item->field_type_id == 3) {
                $option[$item->id] = Option::get("special_unit", $machine_type->get_property_value($item->id, "", "value"), 2);
            }
        }

        return view("line_product_station.machine_type.create", compact("option", "machine_module_type_option", "cost_center_option", "status_option", "station", "machine_type", "station_option", "machine_type_consumption_type_option"));
    }

    public function store(Request $request, Station $station)
    {

        //چک کردن تکراری

        $request["station_id"] = $station->id;
        $machine_type = MachineType::create($request->all());
        $current_machine_count = $machine_type->machine->count();
        for ($i = 1; $i <= $request->count; $i++) {
            $machine = new Machine();
            $machine->station_id = $station->id;
            $machine->machine_type_id = $machine_type->id;
            $machine->caption = $request->caption . " " . ($i + $current_machine_count);
            $machine->save();
            $result = $machine->setStatus($request->active_status_id,
                53002,
                $machine_type->machine_module_type->machine_production_status_id,
                $machine_type->machine_module_type->machine_off_reason
            );
            if (!$result["result"]) {
                return redirect()->back()->withErrors($result["message"]);
            }
        }

        $this->property_update($request, $machine_type);

        return redirect()->route("line_product_station.machine_type.index", $station)->with(["success" => "ماشین با موفقیت اضافه شد"]);

    }

    public function edit(MachineType $machine_type)
    {

        $cost_center_option = Option::get("cost_center", $machine_type->ic);
        $station_option = Option::get("station", $machine_type->station->id);
        $status_option = Option::get("active_status", $machine_type->active_status_id);
        $machine_module_type_option = Option::get("machine_module_type", $machine_type->machine_module_type_id);
        $machine_type_consumption_type_option = Option::get("machine_type_consumption_type", $machine_type->machine_type_consumption_type_id);

        $option = [];
        foreach ($machine_type->machine_property() as $item) {
            if ($item->field_type_id == 3) {
                $option[$item->id] = Option::get("special_unit", $machine_type->get_property_value($item->id, "", "value"), 2);
            }
        }

        // محدودیت های انبارگردانی در گروه ماشین
         $goods_kind_list = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        where("machine_type_id", $machine_type->id)->groupBy("goods_kind_id")->
        select("machine_type_id", "goods_kind_id")->
            with("goods_kind")->
        get();

        $warehouse_handling_goods_kind_time_limit = WarehouseHandlingGoodsKindTimeLimit::
        where(["warehouse_type_id" => 3,
            "belonging_to_id" => $machine_type->id
        ])->pluck("warehouse_handling_time_limit", "goods_kind_id")->toArray();

        return view("line_product_station.machine_type.edit",
            compact("option", "machine_module_type_option",
                "station_option", "status_option", "machine_type", "cost_center_option",
                "machine_type_consumption_type_option","goods_kind_list","warehouse_handling_goods_kind_time_limit"));

    }

    public function update(Request $request, MachineType $machine_type)
    {
        if ($request->code == "" || MachineType::ExistsCaption($request->code, $machine_type->id)) {
            return back()->withErrors("کد ماشین تکراری است");
        }
        if ($request->active_status_id == 1210) {
            $count = Machine::where("machine_type_id", $machine_type->id)->where("active_status_id", 1200)->count();
            if ($count > 0) {
                return back()->withErrors("برای غیر فعال کردن گروه ماشین، باید همه ماشین های گروه در وضعیت غیر فعال باشند");
            }
        }

        $machine_type->update($request->all());

        return redirect()->route("line_product_station.machine_type.index", $machine_type->station)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function property_update(Request $request, MachineType $machine_type)
    {

        // ویرایش مشخصات گروه ماشین
        foreach ( $machine_type->machine_property() as $item ) {
            $id = "m_p_" . $item->id;
            if ( isset( $request->$id ) ) {
                $property_value        = MachinePropertyValue::firstOrCreate( [
                    "machine_property_id" => $item->id,
                    "machine_type_id"     => $machine_type->id
                ] );
                $property_value->value = $request->$id;
                $property_value->save();
            }
        }

        return redirect()->back()->with( [ "success" => "تغییرات با موفقیت ثبت گردید" ] );

    }

public function warehouse_handling_update(Request $request, MachineType $machine_type)
{

    // محدودیت های انبارگردانی در گروه ماشین
    $goods_kind_list = MachineTypeInputBandGoodsKind::
    join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
    where("machine_type_id", $machine_type->id)->groupBy("goods_kind_id")->
    select("machine_type_id", "goods_kind_id")->
    with("goods_kind")->
    get();
    foreach ($goods_kind_list as $item){
        if(!isset($request->warehouse_limit[$item->goods_kind_id])){
            return back()->withErrors("لطفا محدودیت زمانی انبارگردانی را برای رسته کالایی ".$item->goods_kind->caption." ثبت نمایید.");
        }
    }
    foreach ($goods_kind_list as $item){

        WarehouseHandlingGoodsKindTimeLimit::updateOrCreate([
            "warehouse_type_id"=>3,
            "belonging_to_id"=>$machine_type->id,
            "goods_kind_id"=>$item->goods_kind_id,
        ],
            [
                "warehouse_handling_time_limit"=>$request->warehouse_limit[$item->goods_kind_id]
            ]);
    }
    return redirect()->back()->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);
}
    public function warehouse_index(MachineType $machine_type)
    {
        return view("line_product_station.machine_type.warehouse_index", compact("machine_type"));
    }

    public function warehouse_create(MachineType $machine_type)
    {
        $warehouse = new Warehouse();

        $shift_option = Option::get("shift");
        return view("line_product_station.machine_type.warehouse_create", compact("machine_type", "warehouse", "shift_option"));
    }

    public function warehouse_store(Request $request, MachineType $machine_type)
    {

        Warehouse::SetMachineWarehouse($machine_type, 3, $request->number);
        return redirect()->route("line_product_station.machine_type.index", $machine_type->station)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);
    }
}
