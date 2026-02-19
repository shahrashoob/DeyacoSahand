<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class QualityControlController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.quality_control.",
        "view" => "line_product_station.product.product_creation.quality_control.",
        "enable_status" => ["035"],
        "priority_number" => 1701,
        "button" => ["caption" => "تنظیمات کنترل کیفیت", "class" => "btn-primary"],
        "button_id" => 5231042,

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

        return \App\Http\Controllers\LineProductStation\Product\QualityControlController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

    public function submit(Request $request,Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\QualityControlController::PostSubmit($request, $product);
        if ($result["result"]) {


            /********* Next Status ************/
            $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"],$product_creation_process);
            if (!$result_next_status["result"]) {
                return back()->withErrors($result_next_status["error"]);
            }
            $product_creation_process->status_id = $result_next_status["status_id"];
            $product_creation_process->save();
            /********* End Next Status **********/

            event(new ProductCreationProcessLogEvent($product_creation_process, 5231035));

            return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "اطلاعات با موفقیت ثبت و تایید گردید."]);

        } else {
            return redirect()->back()->withErrors($result["error"]);
        }
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
