<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class LotNumberController extends Controller
{
    public static $info = [
        "route" => "line_product_station.product.product_creation.lot_number.",
        "view" => "line_product_station.product.product_creation.lot_number.",
        "enable_status" => ["017"],
        "priority_number" => 1500,
        "button" => ["caption" => "تکمیل اطلاعات لات کالا", "class" => "btn-primary"],
        "button_id" => 5231018,

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

        return \App\Http\Controllers\LineProductStation\Product\LotNumberController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

    function store(Request $request, Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $result = \App\Http\Controllers\LineProductStation\Product\LotNumberController::PostStore($request, $product);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function edit(Product $product, LotNumber $lot_number, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        return \App\Http\Controllers\LineProductStation\Product\LotNumberController::
        GetEdit($product, $lot_number, $this->view_path, $this->route_path, $product_creation_process);
    }
    public function update(Request $request,Product $product,LotNumber $lot_number,ProductCreationProcess $product_creation_process){

        $result = \App\Http\Controllers\LineProductStation\Product\LotNumberController::PostUpdate($request, $product,$lot_number,$product_creation_process);
        if ($result["result"]) {
            return redirect()-> route($this->route_path."index",$product_creation_process)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public function confirm_step( ProductCreationProcess $product_creation_process)
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

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231021));

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
