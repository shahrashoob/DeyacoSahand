<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class SampleEndOfProductionController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.sample_end_of_production.",
        "view" => "line_product_station.product.product_creation.sample_end_of_production.",
        "enable_status" => ["020"],
        "priority_number" => 2000,
        "button" => ["caption" => "پایان تولید نمونه (آزمایشگاهی)", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از پایان تولید  نمونه آزمایشگاهی کالا اطمینان دارید؟"],
        "button_id" => 5231023,

    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = self::$info["view"];
        $this->route_path = self::$info["route"];
    }

    public function submit(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = self::PostSubmit($product_creation_process);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "عملیات با موفقیت ثبت  گردید."]);
    }

    public static function PostSubmit(ProductCreationProcess $product_creation_process)
    {

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return [
                "result" => false,
                "error" => $result_next_status["error"]
            ];
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231025));
        // ورژن جدید می گیرد.
        Product\Version\ProductVersion::GetVersion($product_creation_process->product,false,false);
        return [
            "result" => true
        ];
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
