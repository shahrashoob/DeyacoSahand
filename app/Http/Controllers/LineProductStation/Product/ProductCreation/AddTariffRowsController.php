<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffPricing;
use App\Models\LineProduct\Packing\PackingTypeProduct;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class AddTariffRowsController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.add_tariff_rows.",
        "view" => "line_product_station.product.product_creation.add_tariff_rows.",
        "enable_status" => ["029", "201"],
        "priority_number" => 300,
        "button" => ["caption" => "افزودن کالا به تعرفه", "class" => "btn-primary"],
        "button_id" => 5231030,

    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = self::$info["view"];
        $this->route_path = self::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        return \App\Http\Controllers\LineProductStation\Product\PricingController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process, 1, 0, 0, []);
    }

    public function create_product_tariff(Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        return \App\Http\Controllers\LineProductStation\Product\PricingController::GetCreateProductTariff($product, $this->view_path, $this->route_path, $product_creation_process);

    }

    public function store_product_tariff(Request $request, Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::PostStoreProductTariff($request, $product);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route($this->route_path . "index", $product_creation_process)->with(["ردیف های تعرفه با موفقیت اضافه گردید و در انتظار قیمت گذاری می باشند."]);
    }

    /**
     * @param Product $product
     * @param ProductTariffPricing $productTariffPricing
     * @param ProductCreationProcess $product_creation_process
     * @return \Illuminate\Http\RedirectResponse|string
     * حذف ردیف تعریف های در انتظار قیمت گذاری
     */
    public function remove_product_tariff_pricing(Product $product, ProductTariffPricing $productTariffPricing, ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::GetRemoveProductTariffPricing($product, $productTariffPricing);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route($this->route_path . "index", $product_creation_process)->with(["success" => "ردیف های تعرفه با موفقیت حذف گردید"]);

    }

    /**
     * @param Product $product
     * @param ProductTariffPricing $productTariffPricing
     * @param ProductCreationProcess $product_creation_process
     * @return \Illuminate\Http\RedirectResponse|string
     * حذف ردیف تعرفه های  اصلی
     */
    public function remove_product_tariff(Product $product, ProductTariff $productTariff, ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::GetRemoveProductTariff($product, $productTariff);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route($this->route_path . "index", $product_creation_process)->with(["success" => "ردیف های تعرفه با موفقیت حذف گردید"]);

    }

    public function edit_product_tariff_pricing(Product $product, ProductTariff $productTariff, ProductCreationProcess $product_creation_process)
    {


        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $view_path=$this->view_path;
        $route_path=$this->route_path;
        return \App\Http\Controllers\LineProductStation\Product\PricingController::GetEditProductTariff($product, $productTariff,$view_path,$route_path, $product_creation_process);

    }

    public function remove_add_product_tariff(Request $request, Product $product, ProductTariff $productTariff, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        // ردیف را از تعرفه حذف می کنیم و یک ردیف جدید به تعرفه های در انتظار قیمت گذاری اضافه می کنیم.


        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::GetRemoveProductTariff($product, $productTariff);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::PostStoreProductTariff($request, $product);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route($this->route_path . "index", $product_creation_process)->with(["ردیف های تعرفه با موفقیت ویرایش گردید و در انتظار قیمت گذاری می باشند."]);

    }

    public function confirm_step(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $product_tariff_pricing_count = ProductTariffPricing::where("product_id", $product_creation_process->product_id)->
        count();
        if ($product_tariff_pricing_count == 0) {
            return back()->withErrors("لطفا حداقل یک ردیف برای تعرفه گذاری ثبت نمایید.");
        }

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231032));

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "اطلاعات با موفقیت ثبت و تایید گردید."]);


    }

    public function change_packing_type(Product $product, ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $view_path = $this->view_path;
        $route_path = $this->route_path;
        $product = $product_creation_process->product;
        return view($this->view_path . "change_packing_type", compact("product", "product_creation_process", "view_path", "route_path"));

//        $packing_type_controller = new PackingTypeController();
//        $view_path = $packing_type_controller->view_path;
//        $route_path = $this->route_path;
//        $custom_route_path = "submit_change_packing_type";
//        return \App\Http\Controllers\LineProductStation\Product\PackingTypeController::GetIndex($product_creation_process->product, $view_path, $route_path, $product_creation_process);

    }

    public function submit_change_packing_type(Request $request, Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\PackingTypeController::PostSubmit($request, $product_creation_process->product);
        if ($result["result"]) {
            return redirect()->route($this->route_path . "create_product_tariff", [$product_creation_process->product, $product_creation_process])->with(["success" => $result["message"]]);
        }
        return back()->withErrors($result["error"]);
    }

    public function set_parent_product(Request $request, ProductCreationProcess $productCreationProcess)
    {
        if (!$request->parent_product_id || $request->parent_product_id == $productCreationProcess->product_id) {
            return back()->withErrors("کالای همراه انتخاب شده نامعتبر است، لطفا کالای دیگری انتخاب نمایید.");
        }
        $productCreationProcess->parent_product_id = $request->parent_product_id;
        $productCreationProcess->save();
        return back()->with(["success" => "کالای همراه با موفقیت ثبت گردید"]);
    }

    /** تعرفه گذاری همراه */
    public static function GetTariffTogether(Product $product, ProductTariffPricing $productTariffPricing, ProductCreationProcess $product_creation_process)
    {
        $product_ids = ProductCreationProcess::where("id", "!=", $product_creation_process->id)-> //خود کالا را در لیست نباشد
        whereIn("status_id", [5231029, 5231201])-> // در انتظار تعرفه گذاری یا تکمیل شده
        where("goods_kind_id", $product_creation_process->goods_kind_id)->
        pluck("product_id")->toArray();

        if (count($product_ids) == 0) {
            return [
                "result" => false,
                "error" => "هیچ طراحی مشابهی وجود ندارد که در انتظار تعرفه گذاری باشد به گونه ای که بتوان با این کالا تعرفه گذاری شود."
            ];
        }

//     لیست کالاهایی که نوع فروش آنها هم با کالای اصلی یکی است.
        $product_ids_type_of_sale = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::whereIn("product_id", $product_ids)->
        where("type_of_sale_of_product_id", $productTariffPricing->type_of_sale_of_product_id)->
        when($productTariffPricing->service, function ($query) use ($productTariffPricing) {
            // اگر نوع فروش خدمت است، نوع خدمت دو کالا یکی باشد.
            return $query->where("service_id", $productTariffPricing->service_id);
        })->
        pluck("product_id")->toArray();

        if (count($product_ids_type_of_sale) == 0) {
            return [
                "result" => false,
                "error" => count($product_ids) . " طراحی مشابهی وجود دارد که در انتظار تعرفه گذاری می باشد ولی هیچ کدام نوع فروش آنها با نوع فروش " . $product->fullCaption() . " مشابه نیست."
            ];

        }


        // لیست کالاهایی که نوع بسته بندی آنها با بسته بندی تعرفه یکی است.
        $product_ids_packing_type_id = PackingTypeProduct::whereIn("product_id", $product_ids_type_of_sale)->
        where("packing_type_id", $productTariffPricing->packing_type_id)->
        pluck("product_id")->toArray();

        if (count($product_ids_packing_type_id) == 0) {
            return [
                "result" => false,
                "error" => count($product_ids) . " طراحی مشابهی وجود دارد که در انتظار تعرفه گذاری می باشد ولی هیچ کدام نوع بسته بندی آنها با نوع بسته بندی  " . $product->fullCaption() . " مشابه نیست."
            ];

        }

        return [
            "result" => true,
            "product_ids" => $product_ids_packing_type_id,
        ];
    }

    public function add_tariff_together(Product $product, ProductTariffPricing $productTariffPricing, ProductCreationProcess $product_creation_process)
    {

        $result = self::GetTariffTogether($product, $productTariffPricing, $product_creation_process);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $product_ids = $result["product_ids"];
        $product_option = Option::get("tariff_together_product", 0, [], $product_ids);

        $view_path = $this->view_path;
        $route_path = $this->route_path;
        return view($this->view_path . "add_tariff_together", compact("product", "product_creation_process", "productTariffPricing", "product_option", "route_path", "view_path"));


    }

    public function show_tariff_together(Request $request, Product $product, ProductTariffPricing $productTariffPricing, ProductCreationProcess $product_creation_process)
    {


        if (!$request->product_ids || count($request->product_ids) == 0) {
            return redirect()->route($this->route_path . "add_tariff_together", [$product, $productTariffPricing, $product_creation_process])->
            withErrors("لطفا حداقل یک کالا را انتخاب نمایید.");
        }
        $result = self::GetTariffTogether($product, $productTariffPricing, $product_creation_process);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $product_ids_permission = $result["product_ids"];
        foreach ($request->product_ids as $product_id) {
            if (!in_array($product_id, $product_ids_permission)) {
                return back()->withErrors("کد کالا جهت افزودن تعرفه نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
            }
        }

        $product_ids = $request->product_ids;
        $products = Product::whereIn("id", $product_ids)->
        whereIn("id", $request->product_ids)->
        get();

        if (count($products) == 0) {
            return back()->withErrors("لطفا حداقل یک کالا را انتخاب نمایید.");
        }


        $other_product_tariff_pricing = ProductTariffPricing::
        where("product_id", $productTariffPricing->product_id)->get();

        $view_path = $this->view_path;
        $route_path = $this->route_path;
        $products_json = json_encode($product_ids);
        return view($this->view_path . "show_tariff_together", compact("product", 'other_product_tariff_pricing', "products_json", "product_creation_process", "productTariffPricing", "products", "route_path", "view_path"));


    }

    public function submit_tariff_together(Request $request, Product $product, ProductTariffPricing $productTariffPricing, ProductCreationProcess $product_creation_process)
    {
        $product_ids = json_decode($request->product_ids);
        if (!$request->product_ids || count($product_ids) == 0) {
            return redirect()->route($this->route_path . "add_tariff_together", [$product, $productTariffPricing, $product_creation_process])->
            withErrors("لطفا حداقل یک کالا را انتخاب نمایید.");
        }

        $product_creation_process_list = ProductCreationProcess::whereIn("product_id", $product_ids)->get();
        if (count($product_creation_process_list) == 0) {
            return redirect()->route($this->route_path . "add_tariff_together", [$product, $productTariffPricing, $product_creation_process])->
            withErrors("هیچ درخواست طراحی یافت نشد، لطفا مجدد تلاش کنید.");
        }

        // این کالا در کدام

        // دیگر تعرفه ها که این کالاها باید به آنها اضافه شوند.
        if (!$request->other_product_tariff_pricing || count($request->other_product_tariff_pricing) == 0) {
            return back()->withErrors("لطفا حداقل یک تعرفه را انتخاب نمایید");
        }

        $other_product_tariff_pricing_list = ProductTariffPricing::
        whereIn("id", array_keys($request->other_product_tariff_pricing))->get();

        if (count($product_ids) * count($other_product_tariff_pricing_list) > 100) {
            return back()->withErrors("تعداد ردیف هایی که به صورت اشتراکی می توان ثبت کرد حداکثر 100 مورد است،
             لطفا تعداد کالاهای یا تعرفه ها را کاهش دهید و تعرفه گذاری را در دو یا چند مرحله ثبت نمایید.");
        }


        // ردیف تعرفه به همه کالاها اضافه شود.
        foreach ($product_ids as $product_id) {

            foreach ($other_product_tariff_pricing_list as $other_product_tariff_pricing) {
                // حذف ردیف های تعرفه تکراری در تعرفه ها
                ProductTariffPricing::where([
                    "tariff_id" => $other_product_tariff_pricing->tariff_id,
                    "product_id" => $product_id,
                    "degree_id" => $other_product_tariff_pricing->degree_id,
                    "packing_type_id" => $other_product_tariff_pricing->packing_type_id,
                ])->delete();

                ProductTariff::where([
                    "tariff_id" => $other_product_tariff_pricing->tariff_id,
                    "product_id" => $product_id,
                    "degree_id" => $other_product_tariff_pricing->degree_id,
                    "packing_type_id" => $other_product_tariff_pricing->packing_type_id,
                ])->delete();

                ProductTariffPricing::create([
                    "tariff_id" => $other_product_tariff_pricing->tariff_id,
                    "product_id" => $product_id,
                    "service_id" => $other_product_tariff_pricing->service_id,
                    "degree_id" => $other_product_tariff_pricing->degree_id,
                    "warehouse_id" => $other_product_tariff_pricing->warehouse_id,
                    "fea" => $other_product_tariff_pricing->fea,
                    "min_buy" => $other_product_tariff_pricing->min_buy,
                    "max_buy" => $other_product_tariff_pricing->max_buy,
                    "tax" => $other_product_tariff_pricing->tax,
                    "fare" => $other_product_tariff_pricing->fare,
                    "consumer_price" => $other_product_tariff_pricing->consumer_price,
                    "packing_type_id" => $other_product_tariff_pricing->packing_type_id,
                    "type_of_sale_of_product_id" => $other_product_tariff_pricing->type_of_sale_of_product_id,
                    "increase_percentage_deadline_per_day" => $other_product_tariff_pricing->increase_percentage_deadline_per_day,
                ]);
            }
        }


        // همه کالاهای همراه با هم قیمت گذاری شوند.
        ProductCreationProcess::whereIn("product_id", $product_ids)->update([
            "parent_product_id" => $product->id,
            "status_id" => 5231028, // قیمت گذاری
        ]);

        // ثبت لاگ
        foreach ($product_creation_process_list as $product_creation_process_item) {
            event(new ProductCreationProcessLogEvent($product_creation_process_item, 5231032));
        }

        return redirect()->route($this->route_path . "index", $product_creation_process)->with([
            "success" => "برای " . count($product_creation_process_list) . " کالا به صورت همزمان تعرفه گذاری انجام شد."
        ]);
    }

    public function checkPermission(ProductCreationProcess $product_creation_process)
    {

        $result = DashboardController::checkPermissionConditions($product_creation_process, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
