<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\Product\ProductRoute;
use Illuminate\Http\Request;

class ProductRouteController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.route.",
        "view" => "line_product_station.product.product_creation.route.",
        "enable_status" => ["008"],
        "priority_number" => 800,
        "button" => ["caption" => "ثبت  مسیر محصول(ها)", "class" => "btn-primary"],
        "button_id" => 5231011,

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

        return \App\Http\Controllers\LineProductStation\Product\ProductRouteController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

    public function create(Product $product, ProductCreationProcess $product_creation_process)
    {
        return \App\Http\Controllers\LineProductStation\Product\ProductRouteController::GetCreate($product, $this->view_path, $this->route_path, $product_creation_process);
    }

    public function store(Request $request, Product $product, ProductCreationProcess $product_creation_process)
    {

        $result = \App\Http\Controllers\LineProductStation\Product\ProductRouteController::PostStore($request, $product);
        if ($result["result"]) {
            return redirect()->route($this->route_path . "index", $product_creation_process)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public function edit(Product $product, ProductRoute $product_route, ProductCreationProcess $product_creation_process)
    {

        return \App\Http\Controllers\LineProductStation\Product\ProductRouteController::GetEdit($product, $product_route, $this->view_path, $this->route_path, $product_creation_process);
    }

    public function update(Request $request, Product $product, ProductRoute $product_route, ProductCreationProcess $product_creation_process)
    {
        $result = \App\Http\Controllers\LineProductStation\Product\ProductRouteController::PostUpdate($request, $product, $product_route);
        if ($result["result"]) {
            return redirect()->route($this->route_path . "index", $product_creation_process)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function destroy(Product $product, ProductRoute $product_route)
    {
        $result = \App\Http\Controllers\LineProductStation\Product\ProductRouteController::GetDestroy($product, $product_route, $this->view_path, $this->route_path, null);

        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function confirm_step(ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $list = LineProductStation::where("product_id", $product_creation_process->product_id)->get();
        if (count($list) == 0) {
            return back()->withErrors("لطفا حداقل یک مسیر محصول برای کالا ثبت نمایید.");
        }
        if (in_array($product_creation_process->product->supply_type_id, [1, 3])) {
            foreach ($list as $line_product_station) {

                if (!$line_product_station->production_channel_type) {
                    return back()->withErrors("لطفا برای همه مسیر محصول های کالا، کانال تولید مشخص نمایید.");
                }
            }
        }

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231012));

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
