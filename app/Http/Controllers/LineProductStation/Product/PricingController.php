<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\NewTariffProduct;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Accounting\Tariff\ProductTariffPricing;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    // line_product_station/product/actual_cost/
    public $route_path = "line_product_station.product.pricing.";
    public $view_path = "line_product_station.product.pricing.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null, 1, 1, 0, []);
    }

    public function create_product_tariff(Product $product)
    {
        return self::GetCreateProductTariff($product, $this->view_path, $this->route_path, null);

    }

    public function store_product_tariff(Request $request, Product $product)
    {
        $result = self::PostStoreProductTariff($request, $product);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route($this->route_path . "index", $product)->with(["ردیف های تعرفه با موفقیت اضافه گردید و در انتظار قیمت گذاری می باشند."]);
    }

    public function submit_add_pricing_to_tariff(Request $request, Product $product)
    {
        $result = self::PostSubmitAddPricingToTariff($request, $product);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return back()->with(["success" => "لیست قیمت گذاری با موفقیت ثبت گردید."]);
    }

    /**
     * @param Product $product
     * @param ProductTariffPricing $productTariffPricing
     * @param ProductCreationProcess $product_creation_process
     * @return \Illuminate\Http\RedirectResponse|string
     * حذف ردیف تعریف های در انتظار قیمت گذاری
     */
    public function remove_product_tariff_pricing(Product $product, ProductTariffPricing $productTariffPricing)
    {


        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::GetRemoveProductTariffPricing($product, $productTariffPricing);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return back()->with(["success" => "ردیف های تعرفه با موفقیت حذف گردید"]);

    }

    /**
     * @param Product $product
     * @param ProductTariffPricing $productTariffPricing
     * @param ProductCreationProcess $product_creation_process
     * @return \Illuminate\Http\RedirectResponse|string
     * حذف ردیف تعرفه های  اصلی
     */
    public function remove_product_tariff(Product $product, ProductTariff $productTariff)
    {


        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::GetRemoveProductTariff($product, $productTariff);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return back()->with(["success" => "ردیف های تعرفه با موفقیت حذف گردید"]);

    }


    public static function PostSubmitAddPricingToTariff(Request $request, Product $product,$product_ids_creation_process_for_pricing=[])
    {
        $product_ids_creation_process_for_pricing[]=$product->id;
        $product_tariff_pricing = ProductTariffPricing::
        whereIn("product_id", $product_ids_creation_process_for_pricing)->
        orderBy("tariff_id")->
        orderBy("degree_id")->
        orderBy("packing_type_id")->
        orderBy("min_buy")->
        get();

        $error = "";
        $tariff_list = [];
        foreach ($product_tariff_pricing as $item) {
            if (!isset($request["fea_" . $item->id]) || str_replace(",","",$request["fea_" . $item->id]) < 1) {

                $error .= "در تعرفه (" . $item->tariff->caption .
                    ")، بسته بندی (" . $item->packing_type->caption .
                    ")، درجه (" . $item->degree->caption .
                    ") قیمت (".$request["fea_" . $item->id].") به درستی وارد نشده است." . "<br/>";
            }
            if (isset($request["consumer_price_" . $item->id]) &&  str_replace(",","",$request["consumer_price_" . $item->id]) < 0) {
                $error .= "در تعرفه (" . $item->tariff->caption .
                    ")، بسته بندی (" . $item->packing_type->caption .
                    ")، درجه (" . $item->degree->caption .
                    ") قیمت مصرف کننده به درستی وارد نشده است." . "<br/>";
            }

            $predict=Product::ProductPricingPredict($item->product);


            if ($predict["result"] && isset($request["fea_" . $item->id]) &&  str_replace(",","",$request["fea_" . $item->id]) < $predict["predict_price"]) {
                $error .= "در تعرفه (" . $item->tariff->caption .
                    ")، بسته بندی (" . $item->packing_type->caption .
                    ")، درجه (" . $item->degree->caption .
                    ") قیمت مصرف کننده با توجه به بهای تمام شده به دست آمده نامعتبر می باشد." . "<br/>";
            }

            $tariff_list[$item->tariff_id] = $item->tariff;
        }
        if (count($product_tariff_pricing) == 0) {
            $error = "هیچ ردیف قیمت گذاری برای کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد.";
        }
        if ($error != "") {
            return [
                "result" => false,
                "error" => $error
            ];
        }
//        $error=isset($request["fea_" . $item->id])."--".$predict["result"]."--".$predict["predict_price"]."---".str_replace(",","",$request["fea_" . $item->id]);
//        return [
//            "result" => false,
//            "error" => $error
//        ];

        $product_tariff_pricing_where_must_delete = [];
        // اضافه کردن تعرفه های جدید به لیست تعرفه اصلی
        foreach ($product_tariff_pricing as $item) {
            $new_tariff=$item;
            $new_tariff->fea =str_replace(",","", $request["fea_" . $item->id]);
            $new_tariff->consumer_price =str_replace(",","", $request["consumer_price_" . $item->id]);
            ProductTariff::create($new_tariff->toArray());
            $product_tariff_pricing_where_must_delete[] = $new_tariff->id;
        }


        foreach ($tariff_list as $tariff) {
            $list = ProductTariff::
            select("tariff_id","service_id", "product_id", "degree_id", "warehouse_id", "fea", "min_buy", "max_buy", "tax", "fare", "consumer_price", "packing_type_id", "type_of_sale_of_product_id","customer_product_caption","customer_product_code","increase_percentage_deadline_per_day")->
            where("tariff_id", $tariff->id)->
            get()->
            toArray();

            ProductTariffLog::insert($list);

            $tariff_log = $tariff->log(520100540);

            ProductTariffLog::
            where("tariff_log_id", 0)->
            where("tariff_id", $tariff->id)->
            update(["tariff_log_id" => $tariff_log->id]);
        }

        $product_tariff_pricing_where_must_delete[] = -1;
        ProductTariffPricing::whereIn("id", $product_tariff_pricing_where_must_delete)->delete();
        return [
            "result" => true
        ];
    }

    public static function PostStoreProductTariff(Request $request, Product $product)
    {
        $result = self::CheckHasError($product, $request);
        if (!$result["result"]) {
            return $result;
        }
        $tariff_list = $result["tariff_list"];
        $packing_type_list = $result["packing_type_list"];
        $degree_list = $result["degree_list"];
        $service_id = $result["service_id"];
        foreach ($tariff_list as $tariff) {
            foreach ($packing_type_list as $packing_type) {
                foreach ($degree_list as $degree) {
                    ProductTariffPricing::create([
                        "tariff_id" => $tariff->id,
                        "degree_id" => $degree->id,
                        "product_id" => $product->id,
                        "service_id" => $service_id,
                        "packing_type_id" => $packing_type->id,
                        "warehouse_id" => $request->warehouse_id,
                        "min_buy" => $request->min_buy,
                        "max_buy" => $request->max_buy,
                        "tax" => $request->tax,
                        "fare" => $request->fare,
                        "consumer_price" => null,
                        "fea" => null,
                        "increase_percentage_deadline_per_day" => $request->increase_percentage_deadline_per_day,
                        "type_of_sale_of_product_id" => $request->type_of_sale_of_product_id,
                        "customer_product_code"=>$request->customer_product_code,
                        "customer_product_caption"=>$request->customer_product_caption,
                    ]);

                }
            }
        }

        return [
            "result" => true
        ];
    }

    public static function GetRemoveProductTariffPricing(Product $product, ProductTariffPricing $productTariffPricing)
    {
        if ($product->id != $productTariffPricing->product_id) {
            return [
                "result" => false,
                "error" => "کد کالا در تعرفه نامعتبر است، لطفا مجدد تلاش کنید."
            ];
        }

        $productTariffPricing->delete();
        return [
            "result" => true,
        ];
    }

    public static function GetRemoveProductTariff(Product $product, ProductTariff $productTariff)
    {
        if ($product->id != $productTariff->product_id) {
            return [
                "result" => false,
                "error" => "کد کالا در تعرفه نامعتبر است، لطفا مجدد تلاش کنید."
            ];
        }
        // لاگ حذف ردیف
        $productTariff->tariff->log(520100550, "حذف " . $productTariff->product->fullCaption() . " با نوع بسته بندی " . $productTariff->packing_type->code . " از تعرفه");
        $productTariff->delete();

        return [
            "result" => true,
        ];
    }


    public static function CheckHasError(Product $product, Request $request)
    {
        $service_id=null; // شناسه خدمت باری کالاهای کارمزدی
        $error_text = "";
        $error_text .= $product && $product->hasBOM() ? "" : " BOM محصول تعریف نشده است" . " <br/> " . " - لطفا با واحد برنامه ریزی تماس بگیرید ";

        $error_text .= $product && $product->hasLineProduct() ? "" : " برای محصول خط تولید تعریف نشده است " . " <br/> " . "  - لطفا با واحد برنامه ریزی تماس بگیرید   ";


        if ($request->max_buy < 1) {
            $error_text .= "حداکثر خرید باید 1 باشد." . "<br/>";
        }
        if ($request->min_buy < 0) {
            $error_text .= "حداقل خرید باید 0 باشد." . "<br/>";
        }


        if ($request->tax < 0) {
            $error_text .= "مالیات   نمی تواند منفی باشد" . "<br/>";
        }
        if ($request->fea < 0) {
            $error_text .= "عوارض نمی تواند منفی باشد" . "<br/>";
        }
        if($request->type_of_sale_of_product_id ==2){
           $type_of_sale_product_product= Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where([
                "product_id"=>$product->id,
                "type_of_sale_of_product_id"=>2
            ])->first();
            if(!$type_of_sale_product_product){
                $error_text.="با توجه به اینکه نوع فروش کارمزدی است، نوع خدمت برای کالا تعریف نشده است."."<br/>";
            }
            $service_id=$type_of_sale_product_product->service_id;
        }

        $tariff_ids = $request->tariff_ids;
        $tariff_ids[] = -1;
        $tariff_list = Tariff::whereIn("id", $tariff_ids)->get();

        $packing_type_ids = $request->packing_type_ids;
        $packing_type_ids[] = -1;
        $packing_type_list = PackingType::whereIn("id", $packing_type_ids)->get();

        $degree_ids = $request->degree_ids;
        $degree_ids[] = -1;
        $degree_list = Degree::whereIn("id", $degree_ids)->get();


        foreach ($tariff_list as $tariff) {
            foreach ($packing_type_list as $packing_type) {
                foreach ($degree_list as $degree) {

                    $exist = ProductTariff::where([
                        "tariff_id" => $tariff->id,
                        "degree_id" => $degree->id,
                        "product_id" => $product->id,
                        "packing_type_id" => $packing_type->id
                    ])->exists();
                    if ($exist) {

                        $error_text .= "این ردیف در " . $tariff->caption . " برای درجه (" . $degree->caption . ") و نوع بسته بندی (" . $packing_type->caption . ")  تکراری می باشد." . "<br/>";
                    }

                    $exist = ProductTariffPricing::where([
                        "tariff_id" => $tariff->id,
                        "degree_id" => $degree->id,
                        "product_id" => $product->id,
                        "packing_type_id" => $packing_type->id
                    ])->exists();

                    if ($exist) {

                        $error_text .= "این ردیف در " . $tariff->capiton . "برای درجه (" . $degree->capiton . ") و نوع بسته بندی (" . $packing_type->caption . ")  قبلا انتخاب شده است." . "<br/>";

                    }
                }
            }
        }


        if ($error_text) {
            return [
                "result" => false,
                "error" => $error_text
            ];
        } else {
            return [
                "result" => true,
                "tariff_list" => $tariff_list,
                "packing_type_list" => $packing_type_list,
                "degree_list" => $degree_list,
                "service_id"=>$service_id
            ];
        }
    }

    public static function GetCreateProductTariff(Product $product, $view_path, $route_path, $product_creation_process)
    {
        $list = Product\ProductPackingType::
        where([
            "product_id" => $product->id,
            "is_it_salable" => 1 // قابلیت فروش داشته باشد.
        ])->
        pluck("packing_type_id");
        $packing_type_option = Option::get("packing_type_by_list", 0, 0, $list);

        $degree_option = Option::get("degree", 0, $product->goods_kind_id);

        $list = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where("product_id", $product->id)->pluck("type_of_sale_of_product_id");
        $id = count($list) == 1 ? $list[0] : 0;
        $type_of_sale_product_option = Option::get("type_of_sale_of_product", $id, 0, $list);

        $tariff_option = Option::get("tariff");

        $warehouse_option = Option::get("warehouse", 0, 0, [1]);

        return view($view_path . "create_product_tariff", compact("product", "degree_option", "type_of_sale_product_option", "tariff_option", "packing_type_option", "view_path", "route_path", "warehouse_option", "product_creation_process"));

    }

    public static function GetEditProductTariff(Product $product,ProductTariff $product_tariff, $view_path, $route_path, $product_creation_process)
    {
        $list = Product\ProductPackingType::
        where([
            "product_id" => $product->id,
            "is_it_salable" => 1 // قابلیت فروش داشته باشد.
        ])->
        pluck("packing_type_id");
        $packing_type_option = Option::get("packing_type_by_list", $product_tariff->packing_type_id, 0, $list);

        $degree_option = Option::get("degree", $product_tariff->degree_id, $product->goods_kind_id);

        $list = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where("product_id", $product->id)->pluck("type_of_sale_of_product_id");
        $id =$product_tariff->type_of_sale_of_product_id;
        $type_of_sale_product_option = Option::get("type_of_sale_of_product", $id, 0, $list);

        $tariff_option = Option::get("tariff",$product_tariff->tariff_id);

        $warehouse_option = Option::get("warehouse", $product_tariff->warehouse_id, 0, [1]);

        return view($view_path . "edit_product_tariff", compact("product","product_tariff", "degree_option", "type_of_sale_product_option", "tariff_option", "packing_type_option", "view_path", "route_path", "warehouse_option", "product_creation_process"));

    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process, $add_tariff_rows, $add_pricing, $has_actual_cost, $product_ids_creation_process_for_pricing)
    {
        $product_list = $product_ids_creation_process_for_pricing;
        $product_list[] = $product->id;

        $product_tariffs = ProductTariff::
        whereIn("product_id", $product_list)->
        orderBy("tariff_id")->
        orderBy("degree_id")->
        orderBy("packing_type_id")->
        orderBy("min_buy")->
        paginate();
        $product_tariff_pricing = ProductTariffPricing::
        whereIn("product_id", $product_list)->
        orderBy("tariff_id")->
        orderBy("degree_id")->
        orderBy("packing_type_id")->
        orderBy("min_buy")->
        paginate(200000);

        $packing_form_actual_cost = null;
        if ($has_actual_cost) {
            $packing_form_actual_cost = PackingFormActualCost::where("product_id", $product->id)->first();
        }


        // آیا قیمت مصرف کننده دریافت گردد.
        $receive_the_consumer_price_in_the_pricing=Setting::getIntegerValue("receive_the_consumer_price_in_the_pricing");

        $accompanying_product_select=Option::get("accompanying_product_select",$product_creation_process->parent_product_id??"",$product->id);
        return view($view_path . "index", compact(
            "product", "product_tariffs", "view_path", "route_path",
            "product_creation_process", "product_tariff_pricing", "add_pricing",
            "add_tariff_rows", "packing_form_actual_cost", "has_actual_cost",
            "receive_the_consumer_price_in_the_pricing","accompanying_product_select"

        ));
    }

    public static function GetOtherProductIdsForPricing(ProductCreationProcess $product_creation_process, $type)
    {
        $query = ProductCreationProcess::
        when($product_creation_process->parent_product_id, function ($query) use ($product_creation_process) {

            // وقتی پدر دارد.
            return $query->
            orwhere(function ($query) use ($product_creation_process) {
                return $query->where([
                    "product_id" => $product_creation_process->parent_product_id,
                    "status_id" => $product_creation_process->status_id
                ])->
                where("product_id", "!=", $product_creation_process->product_id);
            })->
            orwhere(function ($query) use ($product_creation_process) {
                // وقتی پدر دارد.
                return $query->when($product_creation_process->parent_product_id, function ($query) use ($product_creation_process) {
                    return $query->where([
                        "parent_product_id" => $product_creation_process->parent_product_id,
                        "status_id" => $product_creation_process->status_id
                    ]);
                })->
                where("product_id", "!=", $product_creation_process->product_id);
            });
        })->
        orwhere(function ($query) use ($product_creation_process) {
            return $query->where([
                "parent_product_id" => $product_creation_process->product_id,
                "status_id" => $product_creation_process->status_id
            ])->
            where("product_id", "!=", $product_creation_process->product_id);
        });
        // لیست دیگر مواردی از کالا که می توان با کالای اصلی هم زمان قیمت گذاری کرد.
        switch ($type) {
            case "product_ids":
                return $query->pluck("product_id")->
                toArray();
            case "product_creation_process":
                return $query->get();
        }
        1 / 0;


    }
}
