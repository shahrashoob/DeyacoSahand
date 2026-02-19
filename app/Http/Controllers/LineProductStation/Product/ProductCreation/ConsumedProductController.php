<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ConsumedProductController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.consumed_product.",
        "view" => "line_product_station.product.product_creation.consumed_product.",
        "enable_status" => ["003"],
        "priority_number" => 700,
        "button" => ["caption" => "ثبت  کالاهای مصرفی", "class" => "btn-primary"],
        "button_id" => 5231010,
        "sample_id" => 0,

    ];
    protected $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = ConsumedProductController::$info["view"];
        $this->route_path = ConsumedProductController::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        // لیست کالاهای در انتظار طراحی.
        $consumed_product_list = ConsumedProduct::
        where("product_id", $product_creation_process->product_id)->
        where("status_id", 3400002)-> // در حال طراحی کالای مصرفی
        get();
        foreach ($consumed_product_list as $item) {

            if ($item->product_creation_process->status_id == 5231201) {

                $item->status_id = 3400001; // طراحی انجام شده است.
                $item->material_id = $item->product_creation_process->product_id;
                $item->save();
            }
        }


        return \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }

    public function submit(Request $request, Product $product, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::PostSubmit($product, $request);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }


    public function replace(ConsumedProduct $consumed_product, Product $product, ProductCreationProcess $product_creation_process)
    {
        return \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::GetReplace($consumed_product, $product, $this->view_path, $this->route_path, $product_creation_process);

    }

    public function store_replace(Request $request, ConsumedProduct $consumed_product, Product $product, ProductCreationProcess $product_creation_process)
    {

        $result = \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::PostSubmitReplace($request, $consumed_product);
        if (!$result["result"]) {

            return back()->withErrors($result["error"]);
        }

        return redirect()->route($this->route_path . "index", $product_creation_process)->with(["success" => "ماده اولیه جدید با موفقیت جایگزین ماده اولیه قبلی گردید."]);
    }

    public function delete(ConsumedProduct $consumed_product, Product $product, $material_id, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::GetDelete($consumed_product, $product, $material_id);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function change_choose_material(Product $product, $material_id, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::GetChangeChooseMaterial($product, $material_id);
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

        $consumed_product_list = ConsumedProduct::
        where("product_id", $product_creation_process->product_id)->
        where("status_id", 3400002)-> // در حال طراحی کالای مصرفی
        get();
        if (count($consumed_product_list) > 0) {

            $message = "با توجه به اینکه کالاهای مصرفی زیر در حال طراحی می باشند، امکان تایید کالای مصرفی برای این کالا فعال نمی باشد.";
            foreach ($consumed_product_list as $item) {
                $message .= "<br/>" . ($item->product_creation_process->caption) . " - (" . ($item->product_creation_process->code ?? "") . ")";
            }

            return back()->withErrors($message);
        }

        $consumed_product_list = ConsumedProduct::
        join("products", "material_id", "=", "products.id")->
        where("product_id", $product_creation_process->product_id)->
        where("active_status_id", 1210)-> //
        select("products.*")->
        get();
        if (count($consumed_product_list) > 0) {

            $message = "با توجه به اینکه کالاهای مصرفی زیر غیرفعال می باشند، امکان تایید کالای مصرفی برای این کالا فعال نمی باشد.";
            foreach ($consumed_product_list as $item) {
                $message .= "<br/>" . ($item->caption) . " - (" . ($item->code ?? "") . ")";
            }

            return back()->withErrors($message);
        }

        $consumed_product_list = ConsumedProduct::
        where("product_id", $product_creation_process->product_id)->
        get();
        $message = "";
        if (count($consumed_product_list) > 0) {

            $message_base = "با توجه به اینکه کالاهای مصرفی زیر غیرفعال می باشند، امکان تایید کالای مصرفی برای این کالا فعال نمی باشد.";
            foreach ($consumed_product_list as $item) {
                if ($item->material->status_id == 1210) {
                    $message .= "<br/>" . ($item->material->caption) . " - (" . ($item->material->code ?? "") . ")";

                }
            }
            if ($message != "") {
                return back()->withErrors($message_base . $message);
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

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231007));

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
