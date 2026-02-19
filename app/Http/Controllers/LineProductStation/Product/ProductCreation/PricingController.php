<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\ProductTariffPricing;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.pricing.",
        "view" => "line_product_station.product.product_creation.pricing.",
        "enable_status" => ["028"],
        "priority_number" => 500,
        "button" => ["caption" => "قیمت گذاری کالا", "class" => "btn-primary"],
        "button_id" => 5231031,

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
        $product_ids_creation_process_for_pricing = \App\Http\Controllers\LineProductStation\Product\PricingController::GetOtherProductIdsForPricing($product_creation_process, "product_ids");
        return \App\Http\Controllers\LineProductStation\Product\PricingController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process, 0, 1, 1, $product_ids_creation_process_for_pricing);
    }

    public function submit_add_pricing_to_tariff(Request $request, Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        /********* Check Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process, false);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }

        $product_creation_process_for_pricing = \App\Http\Controllers\LineProductStation\Product\PricingController::GetOtherProductIdsForPricing($product_creation_process, "product_creation_process");
        $product_ids_creation_process_for_pricing = [];
        foreach ($product_creation_process_for_pricing as $item) {
            $product_ids_creation_process_for_pricing[] = $item->product_id;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\PricingController::PostSubmitAddPricingToTariff($request, $product, $product_ids_creation_process_for_pricing);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/


        event(new ProductCreationProcessLogEvent($product_creation_process, 5231033));

        // وضعیت دیگر درخواست های طراحی را هم بروز می کنیم اگر همه ردیف های تعرفه آنها قیمت گذاری شده بود.
        $product_exist_in_product_tariff_pricing = ProductTariffPricing::
        pluck("product_id", "product_id")->
        toArray();

        foreach ($product_creation_process_for_pricing as $item) {
            if (!isset($product_exist_in_product_tariff_pricing[$item->product_id])) {
                // اگر هیچ ردیفی در انتظار تعرفه گذاری ندارد، وضعیت آن را به وضعیت بعدی تغییر می دهیم.
                $item->status_id = $result_next_status["status_id"];
                $item->save();
                /********* End Next Status **********/

// فعال کردن کالا و ارسال پیامک
                ProductCreationProcess::EndOfCreationProcess($item, true);

                ProductCreationProcess::SmsProductCreationPost($item, $result_next_status["status_id"]);

                event(new ProductCreationProcessLogEvent($item, 5231033));
            }
        }

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "اطلاعات با موفقیت ثبت و تایید گردید."]);


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
