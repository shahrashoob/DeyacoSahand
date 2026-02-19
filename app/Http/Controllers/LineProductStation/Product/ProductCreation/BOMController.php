<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class BOMController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.bom.",
        "view" => "line_product_station.product.product_creation.bom.",
        "enable_status" => ["010"],
        "priority_number" => 1000,
        "button" => ["caption" => "ثبت  اطلاعات BOM", "class" => "btn-primary"],
        "button_id" => 5231013,

    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = self::$info["view"];
        $this->route_path = self::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process,$show_route_code="01")
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process,$show_route_code);
    }
    public function create(Request $request, Product\ProductRoute $product_route,ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMController::GetCreate($request, $product_route, $product_creation_process);
    }
    public function edit(Product\BOM\BOM $bom,ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMController::GetEdit($bom, $this->view_path, $this->route_path, $product_creation_process);
    }
    public function update(Request $request, Product\BOM\BOM $bom,ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMController::PostUpdate($request, $bom, $this->route_path, $product_creation_process);
    }
    public function destroy(Product $product, Product\BOM\BOM $bom,ProductCreationProcess $product_creation_process,$delete_all=0)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $route_url = route($this->route_path . "destroy", [$product, $bom,$product_creation_process]);
        return \App\Http\Controllers\LineProductStation\Product\BOM\BOMController::GetDestroy($product, $bom,$delete_all,$route_url);

    }

    public function confirm_step( ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $consumed_product_list = ConsumedProduct::
        where("product_id", $product_creation_process->product_id)->
        where("status_id", 3400002)-> // در حال طراحی کالای مصرفی
        get();
        if (count($consumed_product_list) > 0) {
            $message = "با توجه به اینکه کالاهای مصرفی زیر در حال طراحی می باشند، امکان تایید BOM برای این کالا فعال نمی باشد.";
            foreach ($consumed_product_list as $item) {
                $message .= "<br/>" . ($item->product_creation_process->caption) . " - (" . ($item->product_creation_process->code ?? "") . ")";
            }

            return back()->withErrors($message);
        }
        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"],$product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231014));

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
