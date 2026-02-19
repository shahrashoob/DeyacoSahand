<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\DashboardController;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Production\Production;
use Illuminate\Http\Request;
use function GuzzleHttp\Psr7\_parse_request_uri;

class SampleProductionOrderController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.sample_production_order.",
        "view" => "line_product_station.product.product_creation.sample_production_order.",
        "enable_status" => ["025","021"],
        "priority_number" => 1900,
        "button" => ["caption" => "دستور تولید نمونه (آزمایشگاهی)", "class" => "btn-primary"],
        "button_id" => 5231022,

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
        $packing_type_list = Product\ProductPackingType::
        join("packing_types", "packing_types.id", "packing_type_id")->
        where("product_id", $product_creation_process->product_id)->
        where("it_is_possible_extract_production_form_separately", 1)->
        select("packing_type_product.*")->
        get();
        $route_path = $this->route_path;
        $dashboard_path = $this->dashboard_path;
        return view($this->view_path . "index", compact("product_creation_process", "route_path", "dashboard_path", "packing_type_list"));

    }

    public function submit(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $product = $product_creation_process->product;
        $amount = $request->amount;
        if ($amount > $product->goods_kind->max_number_for_sampling_production_card) {
            return back()->withErrors("مقدار کارت تولید نمونه گیری بیش از حد مجاز است.");
        }
        if (!$request->packing_type_id) {
            return back()->withErrors("لطفا یکی از بسته بندی های مجاز جهت تولید کارت نمونه گیری را انتخاب نمایید.");
        }


        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }


        $packing_types =PackingType::where("id",$request->packing_type_id)->get();
        $sample_production_result = Production::CreateHandmadeProduction(
            $order ?? null,
                $new_order_list ?? null,
            $product,
            null,
            $request->max_delivery_datetime,
            $amount,
            2,
            $packing_types,
            3);

        if(!$sample_production_result["result"]){
            return back()->withErrors($sample_production_result["error"]);
        }

        $sample_production=$sample_production_result["production"];

        $product_creation_process->sample_production_id = $sample_production->id;
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231024,$sample_production->serial()));

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "عملیات با موفقیت ثبت  گردید."]);


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
