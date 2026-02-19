<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingGoodsKindTimeLimit;
use Aws\Api\Operation;
use Illuminate\Http\Request;

class StationController extends Controller
{
    //
    public function index(Line $line)
    {
        $list = Station::where("line_id", $line->id)->paginate(30);

        return view("line_product_station.station.index", compact("list", "line"));
    }

    public function create(Line $line)
    {
        $line_option = Option::get("line", $line->id);
        $status_option = Option::get("active_status", 0);
        $cost_center_option = Option::get("cost_center", 0);
        $station = new Station();
        $station->line_id = $line->id;

        return view("line_product_station.station.create", compact("line", "line_option", "status_option", "station", "cost_center_option"));
    }

    public function store(Request $request, Line $line)
    {
        if ($request->caption == "" || Station::ExistsCaption($request->caption)) {
            return back()->withErrors("کد ایستگاه تکراری است");
        }
        $station = Station::create($request->all());

        return redirect()->route("line_product_station.station.index", $station->line)->with(["success" => "ایستگاه با موفقیت اضافه شد"]);

    }

    public function edit(Station $station)
    {
        $line_option = Option::get("line", $station->line->id);
        $status_option = Option::get("active_status", $station->active_status->id);
        $cost_center_option = Option::get("cost_center", $station->ic);

        // محدودیت های انبارگردانی در ایستگاه کاری
        $goods_kind_list = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        join("machine_types", "machine_types.id", "machine_type_input_bands.machine_type_id")->
        where("station_id", $station->id)->groupBy("goods_kind_id")->
        select("machine_type_id", "goods_kind_id")->
        with("goods_kind")->
        get();

        $warehouse_handling_goods_kind_time_limit = WarehouseHandlingGoodsKindTimeLimit::
        where(["warehouse_type_id" => 4,
            "belonging_to_id" => $station->id
        ])->pluck("warehouse_handling_time_limit", "goods_kind_id")->toArray();


        return view("line_product_station.station.edit", compact("line_option", "status_option", "station", "cost_center_option",
            "goods_kind_list", "warehouse_handling_goods_kind_time_limit"
        ));

    }

    public function update(Request $request, Station $station)
    {
        if ($request->caption == "" || Station::ExistsCaption($request->caption, $station->id)) {
            return back()->withErrors("عنوان ایستگاه تکراری است");
        }

        if ($request->active_status_id == 1210) { // ایستگاه غیر فعال شد
            $count = MachineType::where("station_id", $station->id)->where("active_status_id", 1200)->count();
            if ($count > 0) {
                return back()->withErrors("برای غیر فعال کردن ایستگاه، باید همه گروه های ماشین در ایستگاه در وضعیت غیر فعال باشند");
            }

        }
        $station->update($request->all());

        return redirect()->route("line_product_station.station.index", $station->line)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function property_update(Request $request, Station $station)
    {

        // محدودیت های انبارگردانی در ایستگاه کاری
        $goods_kind_list = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        join("machine_types", "machine_types.id", "machine_type_input_bands.machine_type_id")->
        where("station_id", $station->id)->groupBy("goods_kind_id")->
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
                "warehouse_type_id" => 4,
                "belonging_to_id" => $station->id,
                "goods_kind_id" => $item->goods_kind_id,
            ],
                [
                    "warehouse_handling_time_limit" => $request->warehouse_limit[$item->goods_kind_id]
                ]);
        }


        return redirect()->back()->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    // Operation
    // input_bands
    public function operation_create(Station $station)
    {
        $station_operation_type_option = Option::get("station_operation_type");
        $station_operation_category_option = Option::get("station_operation_category", 0,$station->id);
        $discharge_type_option = Option::get("discharge_type");
        return view("line_product_station.station.operation.create", compact("station","discharge_type_option", "station_operation_type_option","station_operation_category_option"));
    }

    public function operation_store(Request $request, Station $station)
    {

        $result = StationOperation::
        where("station_id", $station->id)->
        where("caption", $request->caption)->
        exists();
        if ($result) {
            return back()->withErrors("عنوان عملیات تکراری است.");
        }

        $station_operation = StationOperation::create([
            "station_id" => $station->id
        ]);

        $station_operation->update($request->all());

        return redirect()->
        route("line_product_station.station.operation.index", $station)->
        with(["success" => "یک عملیات جدید اضافه گردید."]);
    }

    public function operation_index(Station $station)
    {

        return view("line_product_station.station.operation.index", compact("station"));
    }

    public function operation_edit(Station $station, StationOperation $station_operation)
    {

        $station_operation_type_option = Option::get("station_operation_type", $station_operation->station_operation_type_id);
        $station_operation_category_option = Option::get("station_operation_category", $station_operation->station_operation_category_id,$station->id);
        $discharge_type_option = Option::get("discharge_type",$station_operation->discharge_type_id);
        return view("line_product_station.station.operation.edit", compact("station_operation","discharge_type_option", "station_operation_type_option", "station","station_operation_category_option"));
    }

    public function operation_update(Request $request, StationOperation $station_operation)
    {
        $result = StationOperation::
        where("station_id", $station_operation->station_id)->
        where("caption", $request->caption)->
        where("id","!=", $station_operation->id)->
        exists();
        if ($result) {
            return back()->withErrors("عنوان عملیات تکراری است.");
        }
        $station_operation->update($request->all());

        return redirect()->route("line_product_station.station.operation.index", $station_operation->station_id)
            ->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    // Sub Operation
    // input_bands
    public function sub_operation_create(StationOperation $station_operation)
    {


        return view("line_product_station.station.sub_operation.create", compact("station_operation"));
    }

    public function sub_operation_store(Request $request, StationOperation $station_operation)
    {

        $result = Station\Operation\StationSubOperation::
        where("station_operation_id", $station_operation->id)->
        where("caption", $request->caption)->
        exists();
        if ($result) {
            return back()->withErrors("عنوان عملیات فرعی تکراری است.");
        }

        $station_sub_operation = Station\Operation\StationSubOperation::create([
            "station_operation_id" => $station_operation->id
        ]);

        $station_sub_operation->update($request->all());

        return redirect()->
        route("line_product_station.station.sub_operation.index", $station_operation)->
        with(["success" => "یک عملیات فرعی جدید اضافه گردید."]);
    }

    public function sub_operation_index(StationOperation $station_operation)
    {
        return view("line_product_station.station.sub_operation.index", compact("station_operation"));
    }

    public function sub_operation_edit(StationOperation $station_operation, Station\Operation\StationSubOperation $station_sub_operation)
    {

        return view("line_product_station.station.sub_operation.edit", compact("station_operation", "station_sub_operation"));
    }

    public function sub_operation_update(Request $request, Station\Operation\StationSubOperation $station_sub_operation)
    {

        $station_sub_operation->update($request->all());

        return redirect()->route("line_product_station.station.sub_operation.index", $station_sub_operation->station_operation_id)
            ->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }


    public function warehouse_index(Station $station)
    {
        return view("line_product_station.station.warehouse_index", compact("station"));
    }

    public function warehouse_create(Station $station)
    {
        $warehouse = new Warehouse();

        $shift_option = Option::get("shift");
        return view("line_product_station.station.warehouse_create", compact("station", "warehouse", "shift_option"));
    }

    public function warehouse_store(Request $request, Station $station)
    {

        Warehouse::SetMachineWarehouse($station, 4, $request->number);
        return redirect()->route("line_product_station.station.index", $station->line)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);
    }

    // Production Channel Category


    public function station_category_store(Request $request, Station $station)
    {

        if (!$request->categories || count($request->categories) == 0) {
            return back()->withErrors("لطفا حداقل یک دسته کانال برای ایستگاه کاری مشخص نمایید.");
       }
        Station\Operation\StationStationOperationCategory::
        where("station_id", $station->id)->
        delete();
        foreach ($request->categories as $key => $value) {
            Station\Operation\StationStationOperationCategory::create([
                "station_operation_category_id" => $key,
                "station_id" => $station->id
            ]);
        }
        return redirect()->
        route("line_product_station.station.index", $station->line_id)->
        with(["success" => "اطلاعات دسته عملیات ها با موفقیت ذخیره گردید."]);
    }

    public function station_category_index(Station $station)
    {
        $operation_categories = Station\Operation\StationOperationCategory::get();
        $station_categories = Station\Operation\StationStationOperationCategory::
        where("station_id", $station->id)->
        pluck("station_operation_category_id", "station_operation_category_id")->
        toArray();
        return view("line_product_station.station.operation.station_category_index", compact("station", "operation_categories", "station_categories"));
    }
}
