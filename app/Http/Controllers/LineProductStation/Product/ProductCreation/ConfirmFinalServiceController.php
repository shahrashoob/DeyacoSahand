<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class ConfirmFinalServiceController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.confirm_final_service.",
        "view" => "",
        "enable_status" => ["303"],
        "priority_number" => 3002,
        "button" => ["caption" => "تایید نهایی خدمت", "class" => "btn-primary"],
        "message" => [ "confirm" => "آیا تایید نهایی خدمت اطمینان دارید؟" ],
        "button_id" => 5231201,
    ];
    private $view_path;
    private $route_path;
    public $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path =  ConfirmFinalServiceController::$info["view"];
        $this->route_path =  ConfirmFinalServiceController::$info["route"];
    }

    public function submit(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"],$product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->product->active_status_id =1200;//فعال
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/
        event(new ProductCreationProcessLogEvent($product_creation_process, 5231042, "",));//تایید نهایی خدمت

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["خدمت با موفقیت تایید گردید"]);


    }

    public function checkPermission(ProductCreationProcess $product_creation_process)
    {

        $result = DashboardController::checkPermissionConditions($product_creation_process,  ConfirmFinalServiceController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
