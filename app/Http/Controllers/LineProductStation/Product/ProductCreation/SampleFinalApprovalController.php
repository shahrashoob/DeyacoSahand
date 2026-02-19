<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class SampleFinalApprovalController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.sample_final_approval.",
        "view" => "line_product_station.product.product_creation.sample_final_approval.",
        "enable_status" => ["022"],
        "priority_number" => 2200,
        "button" => ["caption" => "تایید نهایی نمونه (آزمایشگاهی)", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از تایید نمونه آزمایشگاهی کالا اطمینان دارید؟"],
        "button_id" => 5231025,

    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = self::$info["view"];
        $this->route_path = self::$info["route"];
    }

    public function submit(Request $request,  ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }


        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231027));

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
