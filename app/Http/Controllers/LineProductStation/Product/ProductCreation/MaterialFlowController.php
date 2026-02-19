<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class MaterialFlowController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.material_flow.",
        "view" => "line_product_station.product.product_creation.material_flow.",
        "enable_status" => ["014"],
        "priority_number" => 1400,
        "button" => ["caption" => "طراحی گراف جریان همبافتی", "class" => "btn-primary"],
        "button_id" => 5231017,
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

        return \App\Http\Controllers\LineProductStation\Product\MaterialFlowController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }
    public function graph(Product $product, Product\BOM\BOM $bom, LineProductStation $line_product_station,ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        return \App\Http\Controllers\LineProductStation\Product\MaterialFlowController::
        GetGraph($product, $bom, $line_product_station, $this->view_path, $this->route_path, $product_creation_process);

    }
    public function draw_graph_one_to_one(Product $product, Product\BOM\BOM $bom, LineProductStation $line_product_station,ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $result = Product\MaterialFlow::AddOneToOneGraph($product,$bom, $line_product_station->machine_type);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        return redirect()->route($this->route_path . "graph", [$product, $bom, $line_product_station,$product_creation_process])->with(["success" => "گراف یک به یک برای مسیر-محصول طراحی گردید."]);

    }

    public function confirm_step(ProductCreationProcess $product_creation_process)
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
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231018));

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
