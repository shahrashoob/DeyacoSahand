<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;
use function Sodium\compare;

class ProductImageController extends Controller
{
    // line_product_station/product.product_creation/product_image
    public static $info = [
        "route" => "line_product_station.product.product_creation.product_image.",
        "view" => "line_product_station.product.product_creation.product_image.",
        "enable_status" => ["026"],
        "priority_number" => 2000,
        "button" => ["caption" => "بارگذاری تصویر کالا", "class" => "btn-primary"],
        "button_id" => 5231028,

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

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }

        /*****************************************/
        $validator = $request->validate([
            'image_file' => 'mimes:jpeg,jpg,png|required|max:'.(1024*10),

        ]);
        if ( ! isset( $request->image_file ) ) {
            return back()->withErrors( "لطفا تصویر کالا را بارگذاری نمایید." );
        }

        $file = File::uploadFile( $request->file( 'image_file' ), $product_creation_process->id . "_" . rand( 1000, 9000 ) . ".png", 42, "upload/product/", true );

        $product_creation_process->product->image_id = $file->id;
        $product_creation_process->product->save();

        /****************************************/


        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231030));



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
