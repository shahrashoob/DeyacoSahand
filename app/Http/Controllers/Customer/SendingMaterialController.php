<?php

namespace App\Http\Controllers\Customer;

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
        "route" => "customer_group.order.sending_material.",
        "view" => "customer.group.sending_material.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "customer_group.order.";

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

        $result = self::GetIndex($order, $product);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        } elseif (is_array($result["machine_allocation"])) {
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
        $result = $this->checkPermission($order);
        if ($result != "") {
            return $result;
        }

        return redirect()->route("production.public_module.register_production.index", $machineAllocation);
    }

    public static function GetIndex(Order $order, Product $product)
    {
        $machine_allocations = MachineAllocation::where([
            "order_id" => $order->id,
            "product_id" => $product->id,
        ])->get();

        if (count($machine_allocations) == 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه تاکنون هیچ درخواستی جهت ارسال مواد اولیه از طرف پیمانکار برای این کالا ثبت نگردیده است، 
            امکان ارسال مواد اولیه وجود ندارد، لطفا جهت ثبت و ارسال مواد اولیه با پیمانکار هماهنگی های لازم را به عمل آورید.  "
            ];

        } elseif (count($machine_allocations) == 1) {
            return [
                "result" => true,
                "machine_allocation" => $machine_allocations[0],
            ];
        } else {
            return [
                "result" => true,
                "machine_allocations" => $machine_allocations,
            ];
            $route_path = $route_path . "show_allocation";
        }


    }

    public static function checkPermission($order)
    {

        $worker = Worker::find(\Auth::user()->id);
        $customer = Customer::where("user_id", $worker->id)->first();

        // باید مشتری باشد.
        if (!$customer) {
            return back()->withErrors("صفحه مورد نظر یافت نشد");
        }
        if ($order->customer_id != ($customer->id ?? 0)) {

            return redirect()->route("customer_group.order.index")->withErrors("سفارش مورد نظر یافت نشد.");
        }


    }
}
