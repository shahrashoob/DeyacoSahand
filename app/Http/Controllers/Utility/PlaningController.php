<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\Order\Order;
use App\Models\Order\OrderList;
use App\Models\Production\Production;
use App\Models\Production\ProductionType;
use App\Models\Utility\Option;
use App\Models\Utility\Priority;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaningController extends Controller
{
    //
    public function index()
    {
        return view("utility.planing.index");
    }

    public function view_production(Request $request, Production $production = null)
    {

        if ($production == null) {
            $production = Production::where("serial", $request->serial)->first();
        }

        if (!$production) {
            return back()->withErrors("شماره سریال کارت نامعتبر می باشد.");
        }

        return view("utility.planing.view_production", compact("production"));

    }

    public function view_order(Request $request, Order $order = null)
    {

        if ($order == null) {
            $order = Order::
            where("code", $request->code)->
            where("series", $request->series)->
            first();
        }
        if (!$order) {
            return back()->withErrors("شماره سفارش نامعتبر می باشد.");
        }

        return view("utility.planing.view_order", compact("order"));

    }

    public function view_order_list(OrderList $order_list)
    {

        $production = Production::where("order_list_id", $order_list->id)->first();

        return view("utility.planing.view_order_list", compact("production", "order_list"));
    }

    public function production_order()
    {
        $product_option = Option::get("product_all");
        $warehouse_option = Option::get("warehouse");
        $priority_option = Option::get("priority");

        return view("utility.planing.production_order", compact("priority_option", "product_option", "warehouse_option"));
    }

    public function production_order_submit(Request $request)
    {


        $new_order_list = new OrderList();

        $new_order_list->product_id = $request->product_id;

        $order = Order::where(["code" => 2, "series" => 10])->first();
        if (!$order) {
            return back()->withErrors("سفارش دستی در سیستم تعریف نشده است");
        }
        $new_order_list->order_id = $order->id;
        $new_order_list->customer_id = 0;
        $new_order_list->carton = $request->carton;

        $product = Product::find($request->product_id);
        $new_order_list->number_in_carton = $product->number_in_carton;
        $new_order_list->amount = $new_order_list->carton;

        $new_order_list->order_type_id = 200;
        $new_order_list->order_status_id = 100;
        $new_order_list->erp_status_id = 305;
        $new_order_list->status_id = 300;
        $new_order_list->prefactor_number = 0;
        $new_order_list->priority_id = $request->priority_id;

        $new_order_list->save();

        return back()->with(["success" => "یک سفارش تولید در انتظار پردازش قرار گرفت"]);

    }

    public function production_order_demo()
    {
        $options = null;
        $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => ""];
        $selectedText = "";
        $product_list = Product::
        join("goods_kinds", "goods_kinds.id", "goods_kind_id")->
        where(function ($query) {
            return $query->where("possibility_of_issuing_a_production_manually", 1)->
            orWhere("possibility_of_issuing_a_sample_production_manually", 1);
        })->
        whereNotIn("supply_type_id", [2])-> // سفارش خرید
        select("products.id", "products.caption", "products.code")->
        get();
        foreach ($product_list as $item) {
            $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
            $options[] = $option;
        }

        $product_option = [

            "id" => "product_id",
            "items" => $options,
            "value" => 0,
            "text" => $selectedText,
        ];
        $priority_option = Option::get("priority");
        $production_type_option = Option::get("production_type", 1);

        $list_required_parent = [];
        $list_allow_production = [];
        $list_required_order = [];

        $goods_kind_ids = GoodsKind::where("possibility_of_issuing_a_production_manually", 1)->
        orWhere("possibility_of_issuing_a_sample_production_manually", 1)->pluck("id")->toArray();

        $list = Product::
        whereIn("goods_kind_id", $goods_kind_ids)->
        whereNotIn("supply_type_id", [2])-> // سفارش خرید
        where("products.active_status_id", 1200)-> // فعال باشد
        with("goods_kind")->
        get();

        foreach ($list as $item) {
            if (!isset($item->goods_kind->required_parent_production)) {
                return back()->withErrors("رسته کالایی برای  کالای " . $item->caption . " مشخص نشده است");
            }
            $list_required_parent[$item->id] = $item->goods_kind->required_parent_production;
            $list_required_order[$item->id] = $item->goods_kind->production_algorithm_type_id == 1;
            $list_allow_production["sample"][$item->id] = $item->goods_kind->possibility_of_issuing_a_sample_production_manually;
            $list_allow_production["production"][$item->id] = $item->goods_kind->possibility_of_issuing_a_production_manually;

        }


        return view("utility.planing.production_order_demo", compact("production_type_option", "product_option", "list_required_parent", "list_allow_production", "list_required_order", "priority_option"));
    }

    public function production_order_demo_submit(Request $request)
    {

        $order_exists = Order::where(["code" => $request->order_code, "series" => $request->series])->exists();

        session([
            "production_order_demo_submit" => true, // برای اینکه بیشتر از یکبار بنتواند کارت تحصیص دهد.
        ]);

        $product = Product::find($request->product_id);
        if (!$product) {
            return back()->withErrors("اطلاعات کالا نامعتبر است، لطفا مجدد تلاش کنید.");
        }
        $priority = Priority::find($request->priority_id);
        $production_type = ProductionType::find($request->production_type_id);
        $carton = $request->carton;
        $order_code = $request->order_code;
        $series = $request->series;
        $max_delivery_datetime1 = $request->max_delivery_datetime1;
        $max_delivery_datetime1_fa = jdate(Carbon::make($request->max_delivery_datetime1))->format('H:i Y/m/d ');

        $production_card = null;
        $parent_production_code = $request->parent_production_code;


        // اگر برای کالا خط محصول تعریف نشده است، امکان ثبت کارت برای آن وجود ندارد
        $line_product_station_exists = LineProductStation::where("product_id", $product->id)->exists();
        if (!$line_product_station_exists) {
            return back()->withErrors("خط محصول برای کالا تعریف نشده است.");
        }
        //اگر کالا قابلیت فروش دارد باید بسته بندی مجاز فروش داشته باشد.
        if ($product->possibility_of_sale) {
            $exists = ProductPackingType::where([
                "product_id" => $product->id,
                "is_it_salable" => 1
            ])->exists();
            if (!$exists) {
                return back()->withErrors("با توجه به اینکه محصول قابلیت فروش دارد، هیچ بسته بندی مجاز فروش برای آن تعریف نشده است،");
            }

        }

// اگر بچ وابسته به تعداد بسته بندی باشد باید تعداد بسته بندی هم در زمان ثبت کارت تولید دریافت گردد.
        $line_product_station_for_number_of_packing_type = LineProductStation::where("product_id", $product->id)->
        where("material_unit_type_id_dependent_to_batch", 4)->
        first();


        if ($production_type->id == 1 && !$product->goods_kind->possibility_of_issuing_a_production_manually) {
            return back()->withErrors("امکان صدور کارت تولید به صورت دستی برای این کالا وجود ندارد.");
        }
        if ($production_type->id == 2 && !$product->goods_kind->possibility_of_issuing_a_sample_production_manually) {
            return back()->withErrors("امکان صدور کارت تولید نمونه گیری به صورت دستی برای این کالا وجود ندارد.");
        }

        if ($production_type->id == 2 && $carton > $product->goods_kind->max_number_for_sampling_production_card) {
            return back()->withErrors("مقدار کارت تولید نمونه گیری بیش از حد مجاز است.");
        }

        $production_card = Production::where("serial", $parent_production_code ?? -1)->first();

        if ($product->goods_kind->required_parent_production && $production_type->id == 1) {

            $production_card = Production::where("serial", $parent_production_code ?? -1)->first();
            if (!$production_card) {
                return back()->withErrors("سریال کارت تولید وارد شده معتبر نمی باشد.");
            }

            //بررسی ارتباط بین کارت اصلی و کارت فرعی
            $bom_item_list = Product\BOM\BOMItem::where([
                "product_id" => $production_card->product_id,
                "material_id" => $product->id
            ])->
            groupBy("bill_of_material_id")->
            get();
            if (count($bom_item_list) == 0) {
                return back()->withErrors("ارتباطی بین کالای کارت تولید سطح بالا و کالای انتخاب شده وجود ندارد. ");
            }

            // بررسی انیکه مقدار ثبت شده برای کارت فرعی از مقدار کارت اصلی بیشتر نباشد.
            $max_amount_in_bom = 0;
            foreach ($bom_item_list as $item) {
                $max_amount_in_bom = max(
                    $max_amount_in_bom,
                    Product\BOM\BOMItem::where([
                        "product_id" => $production_card->product_id,
                        "material_id" => $product->id,
                        "bill_of_material_id" => $item->bill_of_material_id
                    ])->
                    sum(DB::raw("amount * percent_of_use/100"))
                );
            }
            $max_amount = $max_amount_in_bom * $production_card->number;
            if ($carton > $max_amount) {
                $error_message =
                    "برای تولید " .
                    $production_card->number . " " . $production_card->product->unit->caption . " " . $production_card->product->caption
                    . " حداکثر به " .
                    $max_amount . " " . $product->unit->caption . " " . $product->caption
                    . " نیاز است، لطفا تعداد(واحد اصلی)  را اصلاح فرمایید.";

            }

            // بررسی اینکه جمع کل کارت های فرعی از مقدار کارت اصلی بیشتر نباشد.
            $sum_amount_production_before = Production::where("parent_production_id", $production_card->id)->sum("number");
            if ($sum_amount_production_before + $carton > $max_amount) {
                $error_message = "برای تولید " .
                    $production_card->number . " " . $production_card->product->unit->caption . " " . $production_card->product->caption
                    . " حداکثر به " .
                    $max_amount . " " . $product->unit->caption . " " . $product->caption
                    . " نیاز است، لطفا تعداد(واحد اصلی)  را اصلاح فرمایید.";
                $error_message .= "<br/>" . "کارت های فرعی اختصاص داده شده به کارت سطح بالای " . $production_card->serial() . ":";
                $production_list = Production::where("parent_production_id", $production_card->id)->get();
                foreach ($production_list as $item) {
                    $error_message .= "<br/>" . $item->serial() . " (" . $item->number . " " . $item->product->unit->caption . ")";
                }
            }

            if (isset($error_message)) {

                $product_option = Option::get("product_all", $product->id);
                $priority_option = Option::get("priority", $priority->id);
                $production_type_option = Option::get("production_type", $request->production_type_id);

                $list_required_parent = [];
                $list_allow_production = [];
                $list_required_order = [];
                $list = Product::
                join("goods_kinds", "goods_kinds.id", "goods_kind_id")->
                where(function ($query) {
                    return $query->where("possibility_of_issuing_a_production_manually", 1)->
                    orWhere("possibility_of_issuing_a_sample_production_manually", 1);
                })->
                whereNotIn("supply_type_id", [2])-> // سفارش خرید
                select("products.*")->
                get();
                foreach ($list as $item) {
                    if (!isset($item->goods_kind->required_parent_production)) {
                        return back()->withErrors("رسته کالایی برای  کالای " . $item->caption . " مشخص نشده است");
                    }
                    $list_required_parent[$item->id] = $item->goods_kind->required_parent_production;
                    $list_required_order[$item->id] = $item->goods_kind->production_algorithm_type_id == 1;
                    $list_allow_production["sample"][$item->id] = $item->goods_kind->possibility_of_issuing_a_sample_production_manually;
                    $list_allow_production["production"][$item->id] = $item->goods_kind->possibility_of_issuing_a_production_manually;

                }

                return view("utility.planing.production_order_demo", compact(
                        "product_option", "series",
                        "list_required_parent", "list_required_order", "list_allow_production",
                        "priority_option", "carton", "parent_production_code",
                        "max_delivery_datetime1", "order_code", "error_message", "production_type", "production_type_option")
                );
            }

        }


        // بررسی اینکه خود کالا یا کالاهای سطح بالا فابلیت فروش دارند یا خیر، اگر ندارد خطا دهد.
        $possibility_of_sale = false;
        if (!$product->goods_kind->required_parent_production) { // شرط کارت بالاسری و فروش با هم چک می شود
            $possibility_of_sale = true;
        }
        if ($product->goods_kind->production_algorithm_type_id == 2) {
            $possibility_of_sale = true;
        }
        if ($production_type->id == 2) {
            //کارت نمونه گیری :لازم نیستف قابلیت فروش بررسی شود.
            $possibility_of_sale = true;
        }
        if ($product->possibility_of_sale == 1) {
            $possibility_of_sale = true;
        } elseif ($production_card) {
            $production_sale = $production_card;
            while ($production_sale) {
                if ($production_sale->product->possibility_of_sale == 1) {
                    $possibility_of_sale = true;
                    break;
                } elseif ($production_sale->parent_production_id) {
                    $production_sale = $production_sale->parent_production;
                } else {
                    break;
                }
            }

        }

        if (!$possibility_of_sale) {
            return back()->withErrors("با توجه به اینکه کالای مورد نظر قابلیت فروش ندارد، و کالای هیچ کدام از کارت های سطح بالا قابلیت فروش ندارد، امکان ثبت کارت تولید وجود ندارد.");
        }

        $packing_type_list = Product\ProductPackingType::
        join("packing_types", "packing_types.id", "packing_type_id")->
        where("product_id", $product->id)->
        when($product->goods_kind->production_algorithm_type_id == 1 && $product->possibility_of_sale && $production_type->id == 1 && $product->goods_kind->required_parent_production, function ($query) {
            return $query->
            where("is_it_salable", 1);
        })->
        when($production_type->id == 2, function ($query) {
            return $query->
            where("it_is_possible_extract_production_form_separately", 1);
        })->
        select("packing_type_product.*")->
        get();

        return view("utility.planing.production_order_demo_confirm", compact(
            "product", "priority", "order_exists", "carton",
            "order_code", "series", "max_delivery_datetime1",
            "max_delivery_datetime1_fa", "production_card", "packing_type_list", "production_type",
            "line_product_station_for_number_of_packing_type"
        ));

    }

    public function production_order_demo_confirm(Request $request)
    {


        $product = Product::find($request->product_id);

        if ($request->production_type_id == 1 && $product->goods_kind->production_algorithm_type_id == 1) {
            $new_order_list = new OrderList();
            $request->max_delivery_datetime = $request->max_delivery_datetime1 . $request->max_delivery_datetime3;


            $new_order_list->product_id = $request->product_id;

            $order = Order::firstOrCreate(["code" => $request->order_code, "series" => $request->series]);
            //        if ( ! $order ) {
            //            return back()->withErrors( "سفارش ویژه در سیستم تعریف نشده است" );
            //        }
            $new_order_list->order_id = $order->id;
            $new_order_list->customer_id = 0;
            $new_order_list->carton = $request->carton;


            $new_order_list->number_in_carton = $product->number_in_carton;
            $new_order_list->amount = $new_order_list->carton;

            $new_order_list->order_type_id = 200;
            $new_order_list->order_status_id = 100;
            $new_order_list->erp_status_id = 305;
            $new_order_list->status_id = 300;
            $new_order_list->prefactor_number = 0;
            $new_order_list->priority_id = $request->priority_id;


            $new_order_list->save();
        }

        $amount = $request->carton;
        $packing_type_ids = $request->packing_type_ids;
        $priority_id = $request->priority_id;
        if (!$packing_type_ids || count($packing_type_ids) == 0) {
            return redirect()->route("utility.planing.production_order_demo")->withErrors("نوع بسته بندی نا معتبر است.(کد 1)");
        }
        $packing_types = PackingType::whereIn("id", $packing_type_ids)->get();
        if (count($packing_types) == 0) {
            return redirect()->route("utility.planing.production_order_demo")->withErrors("نوع بسته بندی نا معتبر است. (کد2)");
        }

        $has_brand_packing_form=PackingType::whereIn("id", $packing_type_ids)
            ->where("packaging_forms_include_brand",1)
            ->first();
        $normal_amount=null;
        if($has_brand_packing_form){
            if(!$request->normal_amount){
                return redirect()->route("utility.planing.production_order_demo")->withErrors("با توجه به نوع بسته بندی انتخاب شده، مشخص کردن مقدار لوگو الزامی است.");
            }

            if($has_brand_packing_form->normal_amount_unit_type_id==1){
                $normal_amount=$request->normal_amount;
            }

           elseif( $has_brand_packing_form->normal_amount_unit_type_id==2 && $product->sub_unit_id ==300){ // اگر واحد فرعی است و واحد فرعی وزنی است.
               $normal_amount=$request->normal_amount/ $product->weight;
               if ($product->frame_ratio_unit2 && $product->sub_unit2_id == 1400) {
                   $normal_amount=round($normal_amount/ $product->frame_ratio_unit2) * $product->frame_ratio_unit2;
               }
           }
            else {
                return redirect()->route("utility.planing.production_order_demo")->withErrors("با توجه به تنظیمات نوع بسته بندی و مشخصات کالا امکان محاسبه مقدار لوگو وجود ندارد، لطفا با واحد اطلاعات پایه تماس بگیرید.");

            }

            if ($product->frame_ratio_unit2 && $product->sub_unit2_id == 1400) {
                $ratio = $normal_amount /$product->frame_ratio_unit2+0;
                if (abs($ratio - round($ratio)) >0.00001) {
                    return redirect()->route("utility.planing.production_order_demo")->withErrors("با توجه به اینکه واحد فرعی 2 کالا از نوع قاب می باشد، مقدار لوگو باید مضربی از واحد فرعی باشد.");

                }
            }
        }


// اگر بچ وابسته به تعداد بسته بندی باشد باید تعداد بسته بندی هم در زمان ثبت کارت تولید دریافت گردد.
        $line_product_station_for_number_of_packing_type = LineProductStation::where("product_id", $product->id)->
        where("material_unit_type_id_dependent_to_batch", 4)->
        first();
        if ($line_product_station_for_number_of_packing_type && !$request->number_of_packing_form) {
            return back()->withErrors("با توجه به اینکه مسیر محصول کالا وابسته به بچ می باشد، لطفا تعداد بسته بندی را مشخص نمایید.");
        }

        // بررسی اینکه یکبار درخواست بیشتر ارسال نشود.
        $production_order_demo_submit = session("production_order_demo_submit");
        if (!$production_order_demo_submit) {
            return redirect()->route('utility.planing.production_order_demo')->withErrors("ثبت درخواست تکراری، لطفا بررسی بفرمایید در صورتی که کارت مورد نظر صادر نگردید بود، یکبار دیگر فرم را تکمیل نمایید.");
        }


        $production_result = Production::CreateHandmadeProduction(
            $order ?? null, $new_order_list ?? null, $product,
            $request->parent_production_id,
            $request->max_delivery_datetime, $amount,
            $request->production_type_id,
            $packing_types,
            $priority_id,
            $request->number_of_packing_form, "", 7008007,$normal_amount
        );

        if (!$production_result["result"]) {

            return redirect()->route('utility.planing.production_order_demo')->withErrors($production_result["error"]);
        }

        $production = $production_result["production"];
        session()->forget('production_order_demo_submit');

        if ($product->supply_type_id != 3) {
            return redirect()->route("utility.planing.production_order_demo")->with(["success" => "کارت تولید " . $production->serial() . " با موفقیت صادر گردید."]);
        } else {
            return redirect()->route("utility.planing.production_order_demo")->with(["success" => "کارت تولید " . $production->serial() . " با موفقیت صادر گردید."]);

        }
    }
}
