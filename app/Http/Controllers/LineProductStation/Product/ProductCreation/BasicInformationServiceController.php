<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class BasicInformationServiceController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.basic_information_service.",
        "view" => "line_product_station.product.product_creation.basic_information_service.",
        "enable_status" => ["301"],
        "priority_number" => 3000,
        "button" => ["caption" => "تکمیل اطلاعات خدمت", "class" => "btn-primary"],
        "button_id" => 5231302,
    ];
    private $view_path;
    private $route_path;
    public $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = BasicInformationServiceController::$info["view"];
        $this->route_path = BasicInformationServiceController::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $product_service_type_option = Option::get("product_service_type");
        $unit_option = Option::get("unit", null, 0, []);
        $sub_unit_option = Option::get("unit", null, 0, []);
        $sub_unit2_option = Option::get("unit", null, 0, []);
        $supply_type_option = Option::get("supply_type", null);
        $exist_product_service_type_option = Option::get("product_service_type");
        return view($this->view_path . "index", compact("product_creation_process", "product_service_type_option", "supply_type_option",
            'unit_option', 'sub_unit_option', 'sub_unit2_option','exist_product_service_type_option'));
    }

    public function submit(Request $request, ProductCreationProcess $product_creation_process)
    {
        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"],$product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }

        $product = Product::create([
            'caption'=>$product_creation_process->caption,
            'product_service_type_id' => $product_creation_process->product_service_type_id,
            'exist_product_service_type_id'=>$request->exist_product_service_type_id,
            'unit_id' => $request->unit_id2,
            'sub_unit_id' => $request->sub_unit_id2,
            'sub_unit2_id' => $request->sub_unit2_id2,
            'number_in_carton' => $request->number_in_carton2,
            'supply_type_id' => $request->supply_type_id2,
            'active_status_id'=>1210,//غیر فعال
        ]);
        $product->code = '0/' . $product->id;
        $product->save();
        $product_creation_process->product_id =$product->id;
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/
        event(new ProductCreationProcessLogEvent($product_creation_process, 5231040, "",));// تایید نهایی خدمت

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["اطلاعات با موفقیت ثبت گردید"]);


    }

    public function checkPermission(ProductCreationProcess $product_creation_process)
    {

        $result = DashboardController::checkPermissionConditions($product_creation_process, BasicInformationServiceController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
