<?php

namespace App\Http\Controllers\Sales;

use App\Events\Form\PackingLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductFormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductReasonType;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SendingMaterialController extends Controller
{
    // ارسال مواد اولیه به پیمانکار
    public static $info = [
        "route" => "sales.sending_material.",
        "view" => "sales.sending_material.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "sales.dashboard.index";

    //
    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view"];
    }

    public function index(Order $order, Product $product)
    {

        $result = self::checkPermission($order);
        if ($result != "") {
            return $result;
        }

        $result = \App\Http\Controllers\Customer\SendingMaterialController::GetIndex($order, $product);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        } elseif (isset($result["machine_allocation"])) {
            $machine_allocation = $result["machine_allocation"];

            return redirect()->route($this->route_path . "show_allocation", [$order, $machine_allocation->id]);

        } else {
            $machine_allocations = $result["machine_allocations"];
            $route_path = $this->route_path;
            return view($this->view_path . "select_allocation", compact("order", "machine_allocations", "route_path"));

        }
    }

    public function show_allocation(Order $order, MachineAllocation $machineAllocation)
    {
        //چون که در کنترل تولید دسترسی چک می شود، اینجا لازم به بررسی نمی باشد.
//        $result = $this->checkPermission($order);
//        if ($result != "") {
//            return $result;
//        }

        return redirect()->route("production.public_module.register_production.index", $machineAllocation);
    }

    public static function checkPermission($allow_production_check = false)
    {
//        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "sales.reject_product.index" ) ) {
//            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
//        }

    }
}
