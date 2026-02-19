<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard\MachineAllocationController;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Production\Production;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsumedProductQuickController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.consumed_product_quick.",
        "view" => "line_product_station.product.product_creation.consumed_product_quick.",
        "enable_status" => ["501"],
        "priority_number" => 700,
        "button" => ["caption" => "ثبت  کالاهای مصرفی (تعریف سریع کالای مشابه)", "class" => "btn-primary"],
        "button_id" => 5231038,
        "sample_id" => 0,

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

        $packing_type_list = Product\ProductPackingType::
        join("packing_types", "packing_types.id", "packing_type_id")->
        where("product_id", $product_creation_process->product_id)->
        where("it_is_possible_extract_production_form_separately", 1)->
        select("packing_type_product.*")->
        get();


        return \App\Http\Controllers\LineProductStation\Product\ConsumedProductController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process, $packing_type_list);
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
            return back()->with($result["error"]);
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

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatusQuick(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();

        if ($result_next_status["status_id"] == 5231020) {
            $result = self::create_production_and_allocation($product_creation_process);
            if ($result["result"]) {
                return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => $product_creation_process["message"]]);
            } else {
                return redirect()->route($this->dashboard_path . "view", $product_creation_process)->withErrors($product_creation_process["error"]);

            }
        } else {
            return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "اطلاعات با موفثیت ذخیره گردید"]);

        }
    }

    public static function create_production(ProductCreationProcess $product_creation_process)
    {


// گرفتن لاگ طراحی و اینکه باید به کدام ماشین تخصیص دهیم.
        $log_process = Product\ProductCreation\ProductCreationProcessLog::
        where([
            "product_creation_process_id" => $product_creation_process->id,
            "event_id" => 5231501
        ])->
        first();
        if (!$log_process) {
            return [
                "result" => false,
                "error" => "لاگ طراحی کالا نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید." . ""
            ];
        }
        $data = json_decode($log_process->message->text);
        $machine = Machine::find($data->machine_id ?? 0);
        if (!$machine) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه ماشین جهت تخصیص نامعتبر است، امکان تایید وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید."
            ];

        }
        $allocation = Allocation::find($data->allocation_id ?? 0);

        $priority_number = $allocation->priority_number ?? 1;

        $priority_number = $priority_number == 1 ? 1 : $priority_number;


        $parent_production = Production::find($data->production_id ?? 0);
        if (!$parent_production) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه کارت جاری جهت تخصیص نامعتبر است، امکان تایید وجود ندارد، لطفا با واحد پشتیبانی تماس بگیرید."
            ];


        }
        // صدور کارت نمونه گیری
        $packing_types = $parent_production->packing_types;
        if (count($packing_types) == 0) {
            return [
                "result" => false,
                "error" => "نوع بسته بندی کارت تولید نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید."
            ];

        }


        $sample_production_result = Production::CreateHandmadeProduction(
            $order ?? null, $new_order_list ?? null,
            $product_creation_process->product,
            null,
            $parent_production->max_delivery_datetime,
            $parent_production->number,
            2,
            $packing_types,
            3);

        if (!$sample_production_result["result"]) {
            $sample_production_result["error"] = $sample_production_result["error"] . " <br/> --مازول صدور کارت تولید-- ";
            return $sample_production_result;
        }

        $sample_production = $sample_production_result["production"];

        $product_creation_process->sample_production_id = $sample_production->id;
        $product_creation_process->save();


        event(new ProductCreationProcessLogEvent($product_creation_process, 5231502, $sample_production->serial()));


        if ($machine->machine_type->machine_module_type_id != 2) {
            return [
                "result" => false,
                "error" => "با توجه به نوع ماشین، امکان تخصیص اتوماتیک وجود ندارد."
            ];

        }

        return [
            "result" => true,
            "machine" => $machine,
            "sample_production" => $sample_production,
            "priority_number" => $priority_number
        ];

    }


    public static function create_production_and_allocation(ProductCreationProcess $product_creation_process)
    {
       $create_production_result = self::create_production($product_creation_process);

        if (!$create_production_result["result"]) {
            return $create_production_result["error"];
        }

        $machine = $create_production_result["machine"];
        $sample_production = $create_production_result["sample_production"];
        $priority_number = $create_production_result["priority_number"];
        // تخصیص به ماشین
        $result = MachineAllocationController::AutoAllocation($machine, $sample_production, $sample_production->amount, Auth::id(), [], $priority_number);

        if (!$result["result"]) {

            event(new ProductCreationProcessLogEvent($product_creation_process, 5231503, $result["error"]));

            return [
                "result" => false,
                "error" => "دستیار دیجیتال دیاکو در زمان تخصیص کارت " . $sample_production->serial . " به ماشین (" . $machine->caption . ") با خطای زیر مواجه شده و نتوانست تخصیص را کامل نماید. " .
                    "<br/>" . " خطای زمان تخصیص:" . "<br/>" .
                    $result["error"]
            ];

        }
        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ثبت و کارت تولید " . $sample_production->serial . " صادر گردید و به ماشین " . $machine->caption . " تخصیص داده شد.."
        ];

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
