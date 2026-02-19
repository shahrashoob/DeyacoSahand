<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class Step4DesignController extends Controller
{
    public static $info = [
        "route"         => "line_product_station.product.product_creation.step4_design.",
        "view"          => "line_product_station.product.product_creation.step4_design.",
        "enable_status" => [ "033" ],
        "priority_number" => 2003,
        "button"        => [ "caption" => "تایید گام 4 طراحی", "class" => "btn-primary" ],
        "message"       => [ "confirm" => "آیا تایید گام 4 طراحی اطمینان دارید؟" ],
        "button_id" => 5231035,

    ];
    public $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function submit( ProductCreationProcess $product_creation_process ) {

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"],$product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event( new ProductCreationProcessLogEvent( $product_creation_process, 5231037 ) );

        return redirect()->route( $this->dashboard_path . "view", $product_creation_process )->with( [ "تایید گام 4 طراحی با موفقیت ثبت گردید" ] );

    }
}
