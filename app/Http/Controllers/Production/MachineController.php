<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\DashboardController;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\User;
use App\Models\Utility\Menu\MenuPost;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class MachineController extends Controller
{
    var $view_path = "production.machine.";
    var $route_path = "production.machine.";

    public function index(Request $request)
    {

        $dashboard_type_of_machines = Setting::getIntegerValue('dashboard_type_of_machines');

        if ($dashboard_type_of_machines > 0) {
            $name = "index_" . $dashboard_type_of_machines;
            return redirect()->route($this->route_path . $name);
        }
        /*
         * محاسبه وضعیت های مجاز
         */
        $allowed_status_ids = PostStatus::getAllowedStatus(3);
        if ($request->status_id != 0 && !in_array($request->status_id, $allowed_status_ids)) {
            return back()->withErrors("شما اجازه دسترسی به مشاهده ماشین با وضعیت انتخاب شده را ندارید");
        }

        if ($request->page) {
            session(["page" => $request->page]);
        }
        if (session("page") && !$request->page && $request->isMethod('get')) {
            $page = session("page");
            if ($page != 1) {
                return redirect(URL::current() . "?page=" . $page);
            }
        }


        if ($request->isMethod('post')) {
            $search = $request->search;
            $status_id = $request->status_id;
            $machine_type_id = $request->machine_type_id;
        } else {
            $search = session("search_machine");
            $status_id = session("machine_status_id");
            $machine_type_id = session("machine_type_id");
        }
        session([
            "search_machine" => $search,
            "machine_status_id" => $status_id,
            "machine_type_id" => $machine_type_id,
        ]);

        $allowed_machine_ids = Line::getAllowedMachine();
        $allowed_machine_ids[] = -1;
        // search
        if ($status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $status_id;
        }

        $list = Machine::join("stations", "stations.id", "station_id")->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("machines.code", "like", "%" . $search . "%")->
                orWhere("machines.caption", "like", "%" . $search . "%");
            });

        })->
        when($machine_type_id != 0, function ($query) use ($machine_type_id) {
            return $query->where("machine_type_id", $machine_type_id);
        })->
        //   whereIn( "production_status_id", $allowed_status_ids )->
        whereIn("machines.id", $allowed_machine_ids)->
        where("machines.active_status_id", 1200)->
        select("machines.id", "machines.code", "machines.caption", "machines.station_id", "machine_type_id", "production_status_id")->
        orderBy("station_id")->
        orderBy("machines.number_code")->
        orderBy("machines.station_id")->
        paginate(50);;

        $status_option = Option::get("machine_status_for_filter", $status_id);


        return view($this->view_path . "index", compact("list", "search", "status_option"));
    }


    /*
     *
     * داشبورد ستونی نوع 1
     */
    public function index_1(Request $request)
    {

        /*
         * محاسبه وضعیت های مجاز
         */
        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ($request->status_id != 0 && !in_array($request->status_id, $allowed_status_ids)) {
            return back()->withErrors("شما اجازه دسترسی به مشاهده ماشین با وضعیت انتخاب شده را ندارید");
        }

        if ($request->page) {
            session(["page" => $request->page]);
        }
        if (session("page") && !$request->page && $request->isMethod('get')) {
            $page = session("page");
            if ($page != 1) {
                return redirect(URL::current() . "?page=" . $page);
            }
        }


        if ($request->isMethod('post')) {
            $search = $request->search;
            $status_id = $request->status_id;
            $machine_type_id = $request->machine_type_id;
        } else {
            $search = session("search_machine");
            $status_id = session("machine_status_id");
            $machine_type_id = session("machine_type_id");
        }
        session([
            "search_machine" => $search,
            "machine_status_id" => $status_id,
            "machine_type_id" => $machine_type_id,
        ]);

        $allowed_machine_ids = Line::getAllowedMachine();
        $allowed_machine_ids[] = -1;
        // search
        if ($status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $status_id;
        }

        $list = Machine::join("stations", "stations.id", "station_id")->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("machines.code", "like", "%" . $search . "%")->
                orWhere("machines.caption", "like", "%" . $search . "%");
            });

        })->
        when($machine_type_id != 0, function ($query) use ($machine_type_id) {
            return $query->where("machine_type_id", $machine_type_id);
        })->

        whereIn("machines.id", $allowed_machine_ids)->
        where("machines.active_status_id", 1200)->
        select("machines.id", "machines.code", "machines.caption", "machines.station_id", "machine_type_id", "production_status_id")->
        orderBy("station_id")->
        orderBy("machines.number_code")->
        orderBy("machines.station_id")->
        paginate(50, ["*"], "machine_page");;

        $status_option = Option::get("machine_status_for_filter", $status_id);

        $machine_allocations =
            MachineAllocation::whereIn("allocations.status_id", [5310010, 5310040])->
            join("allocations", "allocations.id", "allocation_id")->
            with("product", "production")->orderBy("priority_number", "asc")->get();


        $lines_id_permission = \Auth::user()->posts->first()->post->get_lines_id_permission();
        // کارت های تولید

        $allowed_status_ids = [
            500,
            7001001, 7001002,
            7201001, 7201002,
            7301001, 7201002, 7301005,
        ];
        $list_production = Production::search("", "production_cards.updated_at__desc", $lines_id_permission, [
            "parent_production_id",
            "production_cards.number_in_carton",
            "production_cards.product_id",
            "serial",
            "production_cards.number",
            "production_cards.status_id",
            "order_list_id",
            "nth_in_day",
            "production_cards.created_at",
            "production_cards.prioriry_id",
            "production_cards.waiting_status_id",
            "production_type_id", "order_id", "production_cards.customer_id"
        ], $allowed_status_ids,
            1,
        );

        $list_production = $list_production->paginate(30, ["*"], "production_page");


        $setting_list = Setting::getIntegerValueList([
            "property1_show_in_production_dashboard",
            "property2_show_in_production_dashboard",
            "production_channel_type_show_in_production_dashboard"
        ]);
        $property1_show_in_production_dashboard = $setting_list["property1_show_in_production_dashboard"];
        $property2_show_in_production_dashboard = $setting_list["property2_show_in_production_dashboard"];
        $production_channel_type_show_in_production_dashboard = $setting_list["production_channel_type_show_in_production_dashboard"];
        $goods_kind_property_values_products = [];

        if ($property1_show_in_production_dashboard || $property2_show_in_production_dashboard) {

            $property_product_ids = [];
            foreach ($machine_allocations as $item) {
                $property_product_ids[$item->product_id] = $item->product_id;
            }
            foreach ($list_production as $item) {
                $property_product_ids[$item->product_id] = $item->product_id;
            }
            $property_ids_goods_kind = GoodsKind::where("active_status_id", 1200)->select("property1_id", "property2_id")->get();
            $property_ids = [];
            foreach ($property_ids_goods_kind as $item) {
                if ($item->property1_id)
                    $property_ids[] = $item->property1_id;
                if ($item->property2_id)
                    $property_ids[] = $item->property2_id;
            }

            $goods_kind_property_values = GoodsKindPropertyValue::
            leftJoin('goods_kind_property_options', function ($join) {
                $join->on("goods_kind_property_values.goods_kind_property_id", "=", "goods_kind_property_options.goods_kind_property_id")
                    ->on("goods_kind_property_values.value", "=", "goods_kind_property_options.id");
            })->
            whereIn("goods_kind_property_values.product_id", $property_product_ids)->
            whereIn("goods_kind_property_values.goods_kind_property_id", $property_ids)->select("goods_kind_property_values.product_id", "goods_kind_property_values.goods_kind_property_id", "value", "color", "caption")->get();

            $goods_kind_property_values_products = [];
            foreach ($goods_kind_property_values as $goods_kind_property_value) {
                $goods_kind_property_values_products[$goods_kind_property_value->product_id][$goods_kind_property_value->goods_kind_property_id] = $goods_kind_property_value;
            }
//        return $goods_kind_property_values_products;

        }

        $production_channel = [];
        $production_channel_color = [];
        $production_channel_type_list = [];
        $production_channel_type_option = null;
        // آیا رنگ کانال تولید در صفحه ماشین نمایش داده شود؟
        if ($production_channel_type_show_in_production_dashboard) {
            //  $production_channel_type_option = Option::get("production_channel_type");
            $production_channel = ProductionChannel::
            join("production_channel_types", "production_channel_type_id", "production_channel_types.id")->
            whereIn("machine_id", $allowed_machine_ids)->
            where("status_id", 3358001)-> // کانال جاری
            select("machine_id", "color", "production_channel_types.caption", "production_channel_types.id")->
            get();

            foreach ($production_channel as $item) {
                $production_channel[$item->id] = $item->caption;
                $production_channel_color[$item->machine_id] = $item->color;
            }

            // رنک کانال تولید کالاها
            $product_ids = [];
            $product_ids[] = -1;
            foreach ($list_production as $item) {
                $product_ids[] = $item->product_id;
            }

            $production_channel_type_list_query = LineProductStation::whereIn("product_id", $product_ids)->
            join("production_channel_types", "production_channel_type_id", "production_channel_types.id")->
            select("production_channel_types.*", "product_id")->
            get();
            foreach ($production_channel_type_list_query as $production_channel_type) {
                $production_channel_type_list[$production_channel_type->product_id] = [
                    "caption" => $production_channel_type->production_channel_type->caption ?? "",
                    "color" => $production_channel_type->production_channel_type->color ?? ""
                ];
            }
        }


        /**************************/
        $machine_list=[];

        foreach (Machine::where("active_status_id",1200)->get() as $item) {
                $machine_list[$item->machine_type_id][]=["id"=>$item->id, "caption"=>$item->caption];
        }


        // اگر دسترسی به داشبورد مدیریت تولید داشت، بتواند، کارت های تولید را هم ببیند.
        $menu_list = User::find(Auth::id())->getNavBars(null, 111);
        $allow_show_production = count($menu_list) > 0;
        return view($this->view_path . "index_1", compact("list", "production_channel_type_list",
            "production_channel", "production_channel_color", "allow_show_production", "list_production","machine_list",
            "search", "status_option", "machine_allocations", "goods_kind_property_values_products", "production_channel_type_option",
            "property1_show_in_production_dashboard", "property2_show_in_production_dashboard", "production_channel_type_show_in_production_dashboard"));
    }

    public function view(Machine $machine)
    {

        $machine_allocation_info = ($machine->machine_type->machine_module_type->directory_namespace . "\Machine\DashboardController")::$info;

        return redirect()->route($machine_allocation_info["route"] . "view", $machine);
    }

    public function short_link(Machine $machine)
    {


        // آیا ماژول فرم تولید در ماشین فعال است.
        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);


        $controller_info = ($machine->machine_type->machine_module_type->directory_namespace . "\Machine\DashboardController")::get_controller_info("view");
        $special_condition = ($machine->machine_type->machine_module_type->directory_namespace . "\Machine\DashboardController")::enable_special_condition($machine, $value_202);

        $current_allocation = $machine->getCurrentAllocation();

        $reserve_allocation_list = [];
        $current_input_list = [];
        $current_machine_allocation = null;
        $product_fault_list = [];


        if ($current_allocation) {
            $current_machine_allocation = $current_allocation->items()->first();
            $current_input_list[$current_machine_allocation->allocation_id] = CurrentMachineInput::
            where([
                "machine_id" => $machine->id,
                "allocation_id" => ($current_allocation->id ?? -1),
                //"goods_kind_id" => 2
            ])->
            groupBy("input_line_code", "material_id")->
            orderBy("goods_kind_id")->
            orderBy("input_line_code")->
            get();


            $production = $current_machine_allocation->production;
            $product_fault_list[$current_machine_allocation->id] = BOMFaultIllegal::
            where("product_id", $production->parent_production->product_id ?? 0)->
            groupBy("product_fault_id")->
            get();


        }

        $reserve_allocation = $machine->ReserveAllocation()->orderBy("priority_number")->get();


        foreach ($reserve_allocation as $r_allocation) {
            foreach ($r_allocation->items as $machine_allocation) {
                if (!isset($reserve_allocation_list[$machine_allocation->allocation_id])) {
                    $reserve_allocation_list[$machine_allocation->allocation_id] = $machine_allocation;
                } elseif ($machine_allocation->production_id != $reserve_allocation_list[$machine_allocation->allocation_id]->production_id) {
                    if (!isset($reserve_allocation_list[$machine_allocation->allocation_id]->other_allocation_count)) {
                        $reserve_allocation_list[$machine_allocation->allocation_id]->other_allocation_count = 0;
                    }

                    // دیگر تخصیص های همراه کالا اگر کارت تولید آن متفاوت بود
                    $reserve_allocation_list[$machine_allocation->allocation_id]->other_allocation_count++;
                    $other = "other_allocation_" . $reserve_allocation_list[$machine_allocation->allocation_id]->other_allocation_count;
                    $reserve_allocation_list[$machine_allocation->allocation_id]->$other = $machine_allocation;
                }


                $current_input_list[$machine_allocation->allocation_id] = CurrentMachineInput::
                where([
                    "machine_id" => $machine->id,
                    "allocation_id" => ($r_allocation->id ?? -1),
                    //"goods_kind_id" => 2
                ])->
                groupBy("input_line_code", "material_id")->
                orderBy("goods_kind_id")->
                orderBy("input_line_code")->
                get();

                $production = $machine_allocation->production;
                $product_fault_list[$machine_allocation->id] = BOMFaultIllegal::
                where("product_id", $production->parent_production->product_id ?? 0)->
                groupBy("product_fault_id")->
                get();

            }

        }
        $software_name = Setting::getStringValue("software_name");
        $machine_allocation_info = ($machine->machine_type->machine_module_type->directory_namespace . "\Machine\DashboardController")::$info;

        return view($this->view_path . "short_link.index", compact("controller_info", "machine_allocation_info", "special_condition", "machine", "current_input_list", "reserve_allocation_list", "software_name", "current_machine_allocation", "current_allocation", "product_fault_list"));


        // return redirect()->route( $machine_allocation_info["route"] . "short_link", $machine );
    }

    public function Machine_ShortLink(Machine $machine, $code)
    {

        $machine_allocation_info = ($machine->machine_type->machine_module_type->directory_namespace . "\Machine\DashboardController")::$info;

        return redirect()->route($machine_allocation_info["route"] . "short_link", $machine);
    }
}
