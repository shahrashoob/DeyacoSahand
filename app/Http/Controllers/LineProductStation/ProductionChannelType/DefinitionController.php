<?php

namespace App\Http\Controllers\LineProductStation\ProductionChannelType;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorProductionChannelType;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\Production\ProductionChannelNextOne;
use App\Models\Production\ProductionChannelType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Auth;

class DefinitionController extends Controller
{
    // line_product_station/packing/packing_type
    private $view_path = "line_product_station.production_channel_type.definition.";
    private $route_path = "line_product_station.production_channel_type.definition.";

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $goods_kind_id = $request->goods_kind_id;
            $order_by = $request->order_by;
        } else {
            $search = session("search_production_channel_type");
            $order_by = session("order_by_production_channel_type");
            $goods_kind_id = session("goods_kind_id_production_channel_type");
        }
        session([
            "search_production_channel_type" => $search,
            "order_by_production_channel_type" => $order_by,
            "goods_kind_id_production_channel_type" => $goods_kind_id
        ]);


        $list = ProductionChannelType::
        where("caption", "like", "%" . $search . "%")->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->
        when($goods_kind_id != "", function ($query) use ($goods_kind_id) {

            return $query->where("goods_kind_id", $goods_kind_id);
        })->paginate(50);

        $order_by_Option = Option::OrderBy("public", $order_by);
        $goods_kind_Option = Option::get("goods_kind", $goods_kind_id);

//        $post_user = Auth::user()->posts->first();
//        $allow_create = $post_user->checkButtonPermission("line_product_station.packing.packing_type.create_packing_type");

        return view($this->view_path . "index", compact("list", "search", "order_by_Option", "goods_kind_Option"));
    }

    public function create()
    {
        $machine_type_production_channel_type_option = Option::get("production_channel_category", 0);

        return view($this->view_path . "create", compact("machine_type_production_channel_type_option"));
    }

    public function store(Request $request)
    {

        if ($request->caption == "" || ProductionChannelType::ExistsCaption($request->caption)) {
            return back()->withErrors("عنوان کانال تکراری است");
        }
        ProductionChannelType::create($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "یک نوع بسته بندی با موفقیت اضافه شد"]);

    }

    public function edit(ProductionChannelType $productionChannelType)
    {

        $machine_type_production_channel_type_option = Option::get("production_channel_category", $productionChannelType->production_channel_category_id);

        $machine_type_list = MachineType::where("active_status_id", 1200)->with("Station", "Station.line")->get();

        $contractor_list = Contractor::get();

        $machine_type_production_channel_type = MachineTypeProductionChannelType::where("production_channel_type_id", $productionChannelType->id)->pluck("machine_type_id", "machine_type_id")->toArray();
        $contractor_production_channel_type = ContractorProductionChannelType::where("production_channel_type_id", $productionChannelType->id)->pluck("contractor_id", "contractor_id")->toArray();

        return view($this->view_path . "edit", compact("contractor_list", "contractor_production_channel_type", "productionChannelType", "machine_type_list", "machine_type_production_channel_type_option", "machine_type_production_channel_type"));

    }

    public function update(Request $request, ProductionChannelType $productionChannelType)
    {

        if ($request->caption == "" || ProductionChannelType::ExistsCaption($request->caption, $productionChannelType->id)) {
            return back()->withErrors("عنوان کانال تکراری است");
        }
        $productionChannelType->update($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function update_machine_types(Request $request, ProductionChannelType $productionChannelType)
    {

        MachineTypeProductionChannelType::where("production_channel_type_id", $productionChannelType->id)->delete();
        ContractorProductionChannelType::where("production_channel_type_id", $productionChannelType->id)->delete();

        if (isset($request->machine_type)) {
            foreach ($request->machine_type as $machine_type_id => $val) {
                MachineTypeProductionChannelType::create([
                    "machine_type_id" => $machine_type_id,
                    "production_channel_type_id" => $productionChannelType->id
                ]);
            }
        }


        if (isset($request->contractor)) {

            foreach ($request->contractor as $contractor_id => $val) {
                ContractorProductionChannelType::create([
                    "contractor_id" => $contractor_id,
                    "production_channel_type_id" => $productionChannelType->id
                ]);
            }
        }


        return back()->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function edit_next_ones(MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        return self::EditNextNone($machine_type, $production_channel_type, $this->view_path);
    }

    public function update_next_ones(Request $request, MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        return self::UpdateNextOne($request, $machine_type, $production_channel_type);
    }

    public function delete_next_ones(MachineType $machine_type, ProductionChannelType $production_channel_type, $next_production_channel_type_id)
    {
        return self::DeleteNextOne($machine_type, $production_channel_type, $next_production_channel_type_id);
    }


    public function edit_before_ones(MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        return self::EditBeforeNone($machine_type, $production_channel_type, $this->view_path);
    }

    public function update_before_ones(Request $request, MachineType $machine_type, ProductionChannelType $production_channel_type)
    {

        return self::UpdateBeforeOne($request, $machine_type, $production_channel_type);
    }

    public function edit_machine_production_channel_type(MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        $machine_ids = $machine_type->machine_production_channel_type()->where("production_channel_type_id", $production_channel_type->id)->pluck("machine_id", "machine_id");

        return view($this->view_path . "edit_machine_production_channel_type", compact("machine_ids", "machine_type", "production_channel_type"));

    }

    public function update_machine_production_channel_type(Request $request, MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        $machine_ids = $machine_type->machine_production_channel_type()->where("production_channel_type_id", $production_channel_type->id)->pluck("machine_id", "machine_id");

        MachineProductionChannelType::
        where("machine_type_id", $machine_type->id)->
        where("production_channel_type_id", $production_channel_type->id)->
        delete();
        if ($request->machine) {
            foreach ($request->machine as $machine_id => $val) {

                MachineProductionChannelType::create([
                    'machine_id' => $machine_id,
                    'production_channel_type_id' => $production_channel_type->id,
                    "machine_type_id" => $machine_type->id
                ]);
            }
        }
        return redirect()->route($this->route_path . "edit",$production_channel_type)
            ->with('success', 'کانال تولید های ماشین با موفقیت بروز رسانی گردید.');

    }

    public function delete_before_ones(MachineType $machine_type, ProductionChannelType $production_channel_type, $next_production_channel_type_id)
    {
        return self::DeleteBeforeOne($machine_type, $production_channel_type, $next_production_channel_type_id);
    }

    public static function EditNextNone(MachineType $machine_type, ProductionChannelType $production_channel_type, $view_path)
    {
        $production_channel_type_option = Option::get("station_production_channel_type", 0, $machine_type->station_id);

        return view($view_path . "edit_next_ones", compact("machine_type", "production_channel_type_option", "production_channel_type"));

    }

    public static function UpdateNextOne(Request $request, MachineType $machine_type, ProductionChannelType $production_channel_type)
    {

        if (!$request->next_production_channel_type_id) {
            return back()->withErrors("لطفا کانال تولید مجاز بعدی را انتخاب نمایید.");
        }
        $next_one = ProductionChannelNextOne::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $production_channel_type->id,
            "next_production_channel_type_id" => $request->next_production_channel_type_id
        ])->first();
        if ($next_one) {
            return back()->withErrors("کانال تولید انتخاب شده  تکراری است.");
        }
        $next_one = ProductionChannelNextOne::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $production_channel_type->id,
            "priority_number" => $request->priority_number
        ])->first();
        if ($next_one) {
            return back()->withErrors("اولویت انتخاب شده تکراری است.");
        }

        ProductionChannelNextOne::create([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $production_channel_type->id,
            "next_production_channel_type_id" => $request->next_production_channel_type_id,
            "priority_number" => $request->priority_number
        ]);
        return back()->with(["success" => "کانال تولید مجاز بعدی با موفقیت اضافه گردید."]);
    }

    public static function DeleteNextOne(MachineType $machine_type, ProductionChannelType $production_channel_type, $next_production_channel_type_id

    )
    {
        $next_one = ProductionChannelNextOne::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $production_channel_type->id,
            "next_production_channel_type_id" => $next_production_channel_type_id,
        ])->first();
        if (!$next_one) {
            return back()->withErrors("اطلاعات به صورت صحیح وارد نشده است، لطفا مجدد تلاش کنید.");
        }
        $next_one->delete();
        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }

    public static function EditBeforeNone(MachineType $machine_type, ProductionChannelType $production_channel_type, $view_path)
    {
        $production_channel_type_option = Option::get("station_production_channel_type", 0, $machine_type->station_id);

        return view($view_path . "edit_before_ones", compact("machine_type", "production_channel_type_option", "production_channel_type"));

    }

    public static function UpdateBeforeOne(Request $request, MachineType $machine_type, ProductionChannelType $production_channel_type)
    {

        if (!$request->before_production_channel_type_id) {
            return back()->withErrors("لطفا کانال تولید مجاز بعدی را انتخاب نمایید.");
        }
        $before_one = ProductionChannelNextOne::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $request->before_production_channel_type_id,
            "next_production_channel_type_id" => $production_channel_type->id
        ])->first();
        if ($before_one) {
            return back()->withErrors("کانال تولید انتخاب شده  تکراری است.");
        }
        $before_one = ProductionChannelNextOne::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $request->before_production_channel_type_id,
            "priority_number" => $request->priority_number
        ])->first();
        if ($before_one) {
            return back()->withErrors("اولویت انتخاب شده تکراری است.");
        }

        ProductionChannelNextOne::create([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $request->before_production_channel_type_id,
            "next_production_channel_type_id" => $production_channel_type->id,
            "priority_number" => $request->priority_number
        ]);
        return back()->with(["success" => "کانال تولید مجاز بعدی با موفقیت اضافه گردید."]);
    }

    public static function DeleteBeforeOne(MachineType $machine_type, ProductionChannelType $production_channel_type, $before_production_channel_type_id

    )
    {
        $next_one = ProductionChannelNextOne::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $before_production_channel_type_id,
            "next_production_channel_type_id" => $production_channel_type->id,
        ])->first();
        if (!$next_one) {
            return back()->withErrors("اطلاعات به صورت صحیح وارد نشده است، لطفا مجدد تلاش کنید.");
        }
        $next_one->delete();
        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }
}