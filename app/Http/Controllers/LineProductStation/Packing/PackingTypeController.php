<?php

namespace App\Http\Controllers\LineProductStation\Packing;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Auth;

class PackingTypeController extends Controller
{
    // line_product_station/packing/packing_type
    private $view_path = "line_product_station.packing.packing_type.";
    private $route_path = "line_product_station.packing.packing_type.";

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_packing_type");
            $order_by = session("order_by_packing_type");
        }
        session(["search_packing_type" => $search, "order_by_packing_type" => $order_by]);


        $list = PackingType::
        where("code", "like", "%" . $search . "%")->
        orWhere("caption", "like", "%" . $search . "%")->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->paginate(50);

        $order_by_Option = Option::OrderBy("public", $order_by);

        $post_user = Auth::user()->posts->first();
        $allow_create = $post_user->checkButtonPermission("line_product_station.packing.packing_type.create_packing_type");


        return view($this->view_path . "index", compact("list", "search", "order_by_Option", "allow_create"));
    }

    public function create()
    {
        $post_user = Auth::user()->posts->first();
        $allow_create = $post_user->checkButtonPermission("line_product_station.packing.packing_type.create_packing_type");

        if (!$allow_create) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید.");
        }

        $status_option = Option::get("status", 0, 1100);
        $carrier_option = Option::get("carrier_type", 0);
        $packing_type = new PackingType();
        $label_packing_type_option = Option::get("label_packing_type_option", $packing_type->packing_type_label_printing_type_id, 1);
        $printer_unit_display_type_option = Option::get("printer_unit_display_type", $packing_type->printer_unit_display_type_id);
        $goods_kind_list = GoodsKind::get();
        $discharge_type_option = Option::get("discharge_type");
        return view($this->view_path . "create", compact("packing_type", "status_option", "discharge_type_option", "carrier_option", "label_packing_type_option", "printer_unit_display_type_option", "goods_kind_list"));
    }

    public function store(Request $request)
    {

        $post_user = Auth::user()->posts->first();
        $allow_create = $post_user->checkButtonPermission("line_product_station.packing.packing_type.create_packing_type");

        if (!$allow_create) {
            return back()->withErrors("شما اجازه دسترسی به عملیات مورد نظر را ندارید.");
        }
        if ($request->caption == "" || PackingType::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان تکراری است");
        }

        $request["many_degrees_can_fit_into_one"] = $request->many_degrees_can_fit_into_one ? 1 : 0;
        $request["it_is_possible_extract_production_form_separately"] = $request->it_is_possible_extract_production_form_separately ? 1 : 0;
        $request["create_sub_packing_form_in_creation"] = $request->create_sub_packing_form_in_creation ? 1 : 0;

        $packing_type = PackingType::create($request->all());

        PackingTypeLayer::create([
            "packing_type_id" => $packing_type->id,
            "layer_code" => 1,
            "carrier_type_id" => $request->carrier_type_id,
        ]);

        $goods_kind_list = GoodsKind::pluck("id", "id");

        // رسته هایی که جدید انتخاب کرده
        $goods_kind_allowed_ids = isset($request->data["goods_kind_ids"]) ? $request->data["goods_kind_ids"] : [];

        foreach ($goods_kind_list as $goods_kind_id) {
            // جدید انتخاب کرده و قبلا نبوده
            if (isset($goods_kind_allowed_ids[$goods_kind_id])) {
                GoodsKind\GoodsKindPackingType::create(["goods_kind_id" => $goods_kind_id, "packing_type_id" => $packing_type->id]);
            }
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "یک نوع بسته بندی با موفقیت اضافه شد"]);

    }

    public function edit(PackingType $packing_type)
    {

        $status_option = Option::get("status", $packing_type->active_status_id, 1100);
        $label_packing_type_option = Option::get("label_packing_type_option", $packing_type->packing_type_label_printing_type_id, 1);
        $printer_unit_display_type_option = Option::get("printer_unit_display_type", $packing_type->printer_unit_display_type_id);
        $discharge_type_option = Option::get("discharge_type", $packing_type->discharge_type_id);
        $goods_kind_list = GoodsKind::where("active_status_id", 1200)->get();
        $goods_kind_packing_type =
            GoodsKind\GoodsKindPackingType::where("packing_type_id", $packing_type->id)->
            pluck("goods_kind_id", "goods_kind_id");


        $normal_operation_unit_type_option = Option::get("unit_type", $packing_type->normal_amount_unit_type_id,0,[1,2]);

        $post_user = Auth::user()->posts->first();
        $allow_edit_main_property_packing_type = $post_user->checkButtonPermission("line_product_station.packing.packing_type.edit_main_property_packing_type");


        return view($this->view_path . "edit", compact("status_option","normal_operation_unit_type_option", "packing_type", "discharge_type_option", "label_packing_type_option", "printer_unit_display_type_option", "goods_kind_list", "goods_kind_packing_type", "allow_edit_main_property_packing_type"));

    }

    public function update(Request $request, PackingType $packing_type)
    {

        // اگر دسترسی به ویرایش کل را ندارد، آنهایی که مجاز نیست را حذف می کنیم.
        unset($request["caption"]);
        unset($request["weight"]);
        unset($request["weight_error_percentage"]);
        unset($request["length"]);
        unset($request["width"]);
        unset($request["height"]);
        unset($request["many_degrees_can_fit_into_one"]);
        unset($request["create_sub_packing_form_in_creation"]);
//            unset($request["discharge_type_id"]);


        $request["it_is_possible_extract_production_form_separately"] = $request->it_is_possible_extract_production_form_separately ? 1 : 0;
        $request["packaging_forms_include_brand"] = $request->packaging_forms_include_brand ? 1 : 0;
        $request["take_amount_from_parent_production_card"] = $request->take_amount_from_parent_production_card ? 1 : 0;


        $packing_type->update($request->all());


        $goods_kind_list = GoodsKind::pluck("id", "id");
        // رسته هایی که از قبل بوده
        $goods_kind_packing_type = GoodsKind\GoodsKindPackingType::where("packing_type_id", $packing_type->id)->
        get()->keyBy("goods_kind_id");

        // رسته هایی که جدید انتخاب کرده
        $goods_kind_allowed_ids = isset($request->data["goods_kind_ids"]) ? $request->data["goods_kind_ids"] : [];

        foreach ($goods_kind_list as $goods_kind_id) {

            // جدید انتخاب کرده و قبلا نبوده
            if (isset($goods_kind_allowed_ids[$goods_kind_id]) && !isset($goods_kind_packing_type[$goods_kind_id])) {
                GoodsKind\GoodsKindPackingType::create(["goods_kind_id" => $goods_kind_id, "packing_type_id" => $packing_type->id]);
            }
            // جدید حذف کرده و قبلا وجود داشته
            if (!isset($goods_kind_allowed_ids[$goods_kind_id]) && isset($goods_kind_packing_type[$goods_kind_id])) {
                $goods_kind_packing_type[$goods_kind_id]->delete();
            }
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

//    public function add_layer(PackingType $packing_type)
//    {
//
//        if ($packing_type->layers()->count() == 2) {
//            return back()->withErrors("امکان ثبت بیش از دولایه برای بسته بندی وجود ندارد.");
//        }
//        $packing_type_options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];
//
//        foreach (PackingType::get() as $item) {
//
//            if ($item->layers()->orderByDesc("id")->first() &&
//                $item->layers()->orderByDesc("id")->first()->carrier_type_id == $packing_type->layers()->first()->carrier_type_id
//                && $item->id != $packing_type->id
//            ) {
//                $option = ["value" => $item->id, "text" => $item->caption];
//
//                $packing_type_options[] = $option;
//            }
//        }
//
//
//        $carrier_option = Option::get("carrier_type", 0);
//
//        return view($this->view_path . "add_layer", compact("packing_type", "carrier_option", "packing_type_options"));
//
//    }
//
//    public function add_layer_store(Request $request, PackingType $packing_type)
//    {
//
//        $exists = PackingTypeLayer::where([
//            "packing_type_id" => $packing_type->id,
//            "carrier_type_id" => $request->carrier_type_id
//        ])->exists();
//        if ($exists) {
//            return back()->withErrors("این نوع حامل قبلا برای لایه دیگر از بسته بندی انتخاب شده است.");
//        }
//        $layer_code = PackingTypeLayer::where("packing_type_id", $packing_type->id)->count();
//        PackingTypeLayer::create([
//            "packing_type_id" => $packing_type->id,
//            "layer_code" => $layer_code + 1,
//            "carrier_type_id" => $request->carrier_type_id
//        ]);
//        $packing_type->update_layer_code();
//
//        $packing_type->first_packing_type_id = $request->first_packing_type_id;
//        $packing_type->save();
//
//
//        return redirect()->route($this->route_path . "index")->with(["success" => "یک لایه با موفقیت اضافه شد"]);
//
//    }

    public function remove_layer(PackingType $packing_type, $layer_code)
    {

        $layer_count = PackingTypeLayer::where("packing_type_id", $packing_type->id)->count();
        if ($layer_count < 2) {
            return back()->withErrors("امکان حذف لایه برای بسته بندی وجود ندارد.");
        }
        PackingTypeLayer::where([
            "packing_type_id" => $packing_type->id,
            "layer_code" => $layer_code
        ])->delete();
        $packing_type->update_layer_code();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک لایه با موفقیت حذف شد"]);

    }

    /*
     * مدیریت بسته بندی در ورودی های گروه ماشین ها
     */
    public function change_machine_type(PackingType $packing_type)
    {
        $goods_kind_ids =
            GoodsKind\GoodsKindPackingType::where("packing_type_id", $packing_type->id)->
            pluck("goods_kind_id", "goods_kind_id");

        // ورودی ها
        $machine_type_input_band_goods_kind = MachineTypeInputBandGoodsKind::whereIn("goods_kind_id", $goods_kind_ids)->get();

        $machine_type_input_band_packing_type = MachineTypeInputBandPackingType::
        where("packing_type_id", $packing_type->id)->
        get()->
        keyBy(function ($item) {
            return $item->machine_type_input_band_id . "_" . $item->goods_kind_id;
        });

        // خروجی ها
        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::whereIn("goods_kind_id", $goods_kind_ids)->get();

        $machine_type_output_band_packing_type = MachineTypeOutputBandPackingType::
        where("packing_type_id", $packing_type->id)->
        get()->
        keyBy(function ($item) {
            return $item->machine_type_output_band_id . "_" . $item->goods_kind_id;
        });

        if (count($machine_type_input_band_goods_kind) == 0 && count($machine_type_output_band_goods_kind) == 0) {
            return back()->withErrors("این بسته بندی در هیچ کدام از ورودی های (خروجی های) گروه ماشین (با توجه به رسته کالایی آن) قابلیت مصرف (تولید) ندارد.");
        }
        return view($this->view_path . "change_machine_type", compact(
            "machine_type_input_band_goods_kind", "machine_type_input_band_packing_type",
            "machine_type_output_band_goods_kind", "machine_type_output_band_packing_type",
            "packing_type",

        ));

    }

    public function submit_change_machine_type(Request $request, PackingType $packing_type)
    {
        $goods_kind_ids =
            GoodsKind\GoodsKindPackingType::where("packing_type_id", $packing_type->id)->
            pluck("goods_kind_id", "goods_kind_id");

        // ورودی ها
        $machine_type_input_band_goods_kind = MachineTypeInputBandGoodsKind::whereIn("goods_kind_id", $goods_kind_ids)->get();

        $machine_type_input_band_goods_kind_old = MachineTypeInputBandPackingType::
        where("packing_type_id", $packing_type->id)->
        get()->
        keyBy(function ($item) {
            return $item->machine_type_input_band_id . "_" . $item->goods_kind_id;
        });

        $machine_type_input_band_goods_kind_new =
            isset($request->data["machine_type_input_band_goods_kind"]) ?
                $request->data["machine_type_input_band_goods_kind"] : [];

        foreach ($machine_type_input_band_goods_kind as $item) {

            $key = $item->machine_type_input_band_id . "_" . $item->goods_kind_id;
            // جدید انتخاب کرده و قبلا نبوده
            if (isset($machine_type_input_band_goods_kind_new[$key]) && !isset($machine_type_input_band_goods_kind_old[$key])) {
                MachineTypeInputBandPackingType::create([
                    "machine_type_input_band_id" => $item->machine_type_input_band_id,
                    "goods_kind_id" => $item->goods_kind_id,
                    "machine_type_id" => $item->machine_type_input_band->machine_type_id,
                    "packing_type_id" => $packing_type->id,
                ]);

            }
            // جدید حذف کرده و قبلا وجود داشته
            if (!isset($machine_type_input_band_goods_kind_new[$key]) && isset($machine_type_input_band_goods_kind_old[$key])) {
                $machine_type_input_band_goods_kind_old[$key]->delete();
            }
        }

        // خروجی ها
        $machine_type_output_band_goods_kind = MachineTypeOutputBandGoodsKind::whereIn("goods_kind_id", $goods_kind_ids)->get();

        $machine_type_output_band_goods_kind_old = MachineTypeOutputBandPackingType::
        where("packing_type_id", $packing_type->id)->
        get()->
        keyBy(function ($item) {
            return $item->machine_type_output_band_id . "_" . $item->goods_kind_id;
        });

        $machine_type_output_band_goods_kind_new =
            isset($request->data["machine_type_output_band_goods_kind"]) ?
                $request->data["machine_type_output_band_goods_kind"] : [];

        foreach ($machine_type_output_band_goods_kind as $item) {

            $key = $item->machine_type_output_band_id . "_" . $item->goods_kind_id;
            // جدید انتخاب کرده و قبلا نبوده
            if (isset($machine_type_output_band_goods_kind_new[$key]) && !isset($machine_type_output_band_goods_kind_old[$key])) {
                MachineTypeOutputBandPackingType::create([
                    "machine_type_output_band_id" => $item->machine_type_output_band_id,
                    "goods_kind_id" => $item->goods_kind_id,
                    "machine_type_id" => $item->machine_type_output_band->machine_type_id,
                    "packing_type_id" => $packing_type->id,
                ]);

            }
            // جدید حذف کرده و قبلا وجود داشته
            if (!isset($machine_type_output_band_goods_kind_new[$key]) && isset($machine_type_output_band_goods_kind_old[$key])) {
                $machine_type_output_band_goods_kind_old[$key]->delete();
            }
        }
        return back()->with(["success" => "تنظیمات ورودی / خروجی گروه های ماشین با موفقیت ثبت گردید."]);
    }
}
