<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Message;
use Illuminate\Http\Request;
use function Sodium\compare;

class RegisterInFinancialSoftwareController extends Controller
{
    // line_product_station/product.product_creation/register_in_financial_software
    public static $info = [
        "route" => "line_product_station.product.product_creation.register_in_financial_software.",
        "view" => "line_product_station.product.product_creation.register_in_financial_software.",
        "enable_status" => ["027"],
        "priority_number" => 2000,
        "button" => ["caption" => "ثبت کالا در نرم افزار مالی", "class" => "btn-primary"],
        "button_id" => 5231029,

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
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }
        if ($request->code == "" || Product::ExistsCode($request->code, $product_creation_process->product->id)) {
            return back()->withErrors("کد محصول تکراری/ نامعتبر است");
        }
        if ($request->caption == "" || Product::ExistsCode($request->caption, $product_creation_process->product->id, "caption")) {
            return back()->withErrors("نام محصول تکراری/ نامعتبر است");
        }

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }

        $new_caption=Message::convert_farsi_digits_to_english($request->caption);
        $new_code=Message::convert_farsi_digits_to_english($request->code);
        /*****************************************/
        // کد کالا در نرم افزار مالی
        $old_code=$product_creation_process->product->code;
        $old_caption = $request->caption ;
        $product_creation_process->product->code = $new_code ;
        $product_creation_process->product->caption = $new_caption; ;
        $product_creation_process->product->save();

        /****************************************/


        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231031,"کد آزمایشی: ".$old_code." -- نام کالا: ".$old_caption));



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
