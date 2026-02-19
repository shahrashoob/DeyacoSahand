<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class RegisterServiceInFinancialSoftwareController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.register_service_in_financial_software.",
        "view" => "line_product_station.product.product_creation.register_service_in_financial_software.",
        "enable_status" => ["302"],
        "priority_number" => 3001,
        "button" => ["caption" => "ثبت  خدمت در نرم افزار مالی", "class" => "btn-primary"],
        "button_id" => 5231303,
    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = self::$info["view"];
        $this->route_path = self::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process){
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        return view($this->view_path."index",compact("product_creation_process"));
    }
    public function submit(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام خدمت حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }

        /*****************************************/
        // کدخدمت در نرم افزار مالی
        $old_code=$product_creation_process->product->code;
        $old_caption = $request->caption ;
        $product_creation_process->product->code = $request->code ;
        $product_creation_process->product->caption = $request->caption ;
        $product_creation_process->product->save();

        /****************************************/


        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process,  5231041,"کد آزمایشی: ".$old_code." -- نام خدمت: ".$old_caption));



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
