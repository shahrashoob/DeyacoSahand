<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class PropertyQuickController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.property_quick.",
        "view" => "line_product_station.product.product_creation.property_quick.",
        "enable_status" => ["502"],
        "priority_number" => 600,
        "button" => ["caption" => "تکمیل مشخصات کالا (طراحی سریع)", "class" => "btn-primary"],
        "button_id" => 5231044,

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

        return \App\Http\Controllers\LineProductStation\Product\PropertyController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

    public function submit(Request $request,Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\PropertyController::PostSubmit($request, $product);
        if ($result["result"]) {


            /********* Next Status ************/
            $result_next_status = ProductCreationProcess::GetNextStatusQuick(self::$info["button_id"],$product_creation_process);
            if (!$result_next_status["result"]) {
                return back()->withErrors($result_next_status["error"]);
            }
            $product_creation_process->status_id = $result_next_status["status_id"];
            $product_creation_process->save();
            /********* End Next Status **********/

            event(new ProductCreationProcessLogEvent($product_creation_process, 5231011));


            //************************************/
            // ثبت کارت تولید و تخصیص به ماشین
            /**************************************/
            $product_creation_process->status_id = $result_next_status["status_id"];
            $product_creation_process->save();

            if ($result_next_status["status_id"] == 5231020) {
                $result = ConsumedProductQuickController::create_production_and_allocation($product_creation_process);
                if ($result["result"]) {
                    return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => $product_creation_process["message"]]);
                } else {
                    return redirect()->route($this->dashboard_path . "view", $product_creation_process)->withErrors($product_creation_process["error"]);

                }
            } else {
                return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "اطلاعات با موفثیت ذخیره گردید"]);

            }

        } else {
            return redirect()->back()->withErrors($result["error"]);
        }
    }

    public function upload_image(Product $product, GoodsKindProperty $goods_kind_property,$product_creation_process_id=0)
    {
        $product_creation_process=ProductCreationProcess::find($product_creation_process_id);
        return \App\Http\Controllers\LineProductStation\Product\PropertyController::
        GetUploadImage($product, $goods_kind_property, $this->view_path, $this->route_path, $product_creation_process);

    }
    public function delete(Product $product, GoodsKindProperty $goods_kind_property)
    {
        $result =\App\Http\Controllers\LineProductStation\Product\PropertyController::GetDelete($product, $goods_kind_property);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

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
