<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Station;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingGoodsKindTimeLimit;
use Illuminate\Http\Request;

class LineController extends Controller
{
    //

    public function index(){
        $list = Line::paginate(30);
        return view("line_product_station.line.index", compact("list"));
    }
    public function create(){
        $status_option = Option::get("active_status");
        $goods_kind_option=Option::get("goods_kind");
        $cost_center_option         = Option::get( "cost_center", 0 );
        $line=new Line();
        return view("line_product_station.line.create",compact("line","status_option","goods_kind_option","cost_center_option"));
    }
    public function store(Request $request){
        if($request->caption=="" || Line::ExistsCaption($request->caption)){
            return back()->withErrors("عنوان خط تکراری است");
        }

        $line = Line::create($request->all());
        return redirect()->route("line_product_station.line.index")->with(["success" => "خط با موفقیت اضافه شد"]);

    }
    public function edit(Line $line){
        $status_option = Option::get("active_status",$line->active_status_id);
        $goods_kind_option=Option::get("goods_kind",$line->goods_kind_id);
        $cost_center_option         = Option::get( "cost_center", $line->ic );

        // محدودیت های انبارگردانی در خط تولید
        $goods_kind_list = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        join("machine_types", "machine_types.id", "machine_type_input_bands.machine_type_id")->
        join("stations", "stations.id", "station_id")->
        where("line_id", $line->id)->groupBy("goods_kind_id")->
        select("machine_type_id", "goods_kind_id")->
        with("goods_kind")->
        get();
        $warehouse_handling_goods_kind_time_limit = WarehouseHandlingGoodsKindTimeLimit::
        where(["warehouse_type_id" => 5,
            "belonging_to_id" => $line->id
        ])->pluck("warehouse_handling_time_limit", "goods_kind_id")->toArray();


        return view("line_product_station.line.edit",compact("status_option","line","goods_kind_option","cost_center_option",
        "goods_kind_list","warehouse_handling_goods_kind_time_limit"
        ));

    }
    public function update(Request $request,Line $line){
        if($request->caption=="" || $line::ExistsCaption($request->caption,$line->id)){
            return back()->withErrors("عنوان خط تکراری است");
        }

        if ( $request->active_status_id == 1210 ) { // خط غیر فعال فعال شد
            $count = Station::where( "line_id", $line->id )->where( "active_status_id", 1200 )->count();
            if ( $count > 0 ) {
                return back()->withErrors( "برای غیر فعال کردن خط، باید همه ایستگاه ها در خط در وضعیت غیر فعال باشند" );
            }

        }
//        if($request->active_status_id==1210){ // خط غیر فعال شد
//
//            Station::where("line_id",$line->id)->update(["active_status_id"=>1210]);
//            $station_list=Station::where("line_id",$line->id)->pluck("id");
//
//            MachineType::whereIn("station_id",$station_list)->update(["active_status_id"=>1210]);
//            $machine_type_list=MachineType::whereIn("station_id",$station_list)->pluck("id");
//
//            Machine::whereIn("machine_type_id",$machine_type_list)->update(["active_status_id"=>1210]);
//        }
        $line->update($request->all());
        return redirect()->route("line_product_station.line.index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function property_update(Request $request, Line $line)
    {

        // محدودیت های انبارگردانی در خط تولید
        $goods_kind_list = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        join("machine_types", "machine_types.id", "machine_type_input_bands.machine_type_id")->
        join("stations", "stations.id", "station_id")->
        where("line_id", $line->id)->groupBy("goods_kind_id")->
        select("machine_type_id", "goods_kind_id")->
        with("goods_kind")->
        get();
        foreach ($goods_kind_list as $item) {
            if (!isset($request->warehouse_limit[$item->goods_kind_id])) {
                return back()->withErrors("لطفا محدودیت زمانی انبارگردانی را برای رسته کالایی " . $item->goods_kind->caption . " ثبت نمایید.");
            }
        }
        foreach ($goods_kind_list as $item) {

            WarehouseHandlingGoodsKindTimeLimit::updateOrCreate([
                "warehouse_type_id" => 5,
                "belonging_to_id" => $line->id,
                "goods_kind_id" => $item->goods_kind_id,
            ],
                [
                    "warehouse_handling_time_limit" => $request->warehouse_limit[$item->goods_kind_id]
                ]);
        }


        return redirect()->back()->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }
    public function warehouse_index(Line $line){
        return view("line_product_station.line.warehouse_index",compact("line"));
    }
    public function warehouse_create (Line $line){
        $warehouse = new Warehouse();

        $shift_option = Option::get( "shift" );
        return view("line_product_station.line.warehouse_create",compact("line","warehouse","shift_option"));
    }
    public function warehouse_store (Request $request, Line $line){

        Warehouse::SetMachineWarehouse($line,5,$request->number);
        return redirect()->route("line_product_station.line.index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);
    }
}
