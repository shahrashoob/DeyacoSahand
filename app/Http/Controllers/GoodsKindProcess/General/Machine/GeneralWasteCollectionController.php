<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralWasteCollectionController extends Controller
{
    var $view_path = "goods_kind_process.general.machine.waste_collection.";
    var $route_path;
    var $dashboard_route;

    public function __construct()
    {

    }

    public function index(Machine $machine)
    {

        $allowed_machine_ids = Line::getAllowedMachine();
        $allowed_machine_ids[] = -1;
        $machine_type_list = MachineType::
        join("machines", "machine_types.id", "machine_type_id")->
        whereIn("machines.id", $allowed_machine_ids)->
        groupBy("machine_type_id")->
        select("machine_types.*")->
        get();


        $machine_option_list = [];
        $machine_type_list_ids = [];
        foreach ($machine_type_list as $machine_type) {
            $machine_list = Machine::
            where("machine_type_id", $machine_type->id)->
            whereIn("id", $allowed_machine_ids)->
            get();

            $machine_option_list[$machine_type->id] = Option::get("machine", 0, 0, $machine_list);
            $machine_type_list_ids[] = $machine_type->id;
        }
        $machine_type_list_ids[] = -1;
        // همه کالاهای ضایعاتی که در مسیر محصولی وجود دارد که آن ماشین در آن قرار دارد.
        $waste_list = Product::join("product_waste", "product_waste.waste_id", "products.id")->
        join("line_product_station", "product_waste.product_route_id", "line_product_station.product_route_id")->
        whereIn("line_product_station.machine_type_id", $machine_type_list_ids)->
        groupBy("products.id")->
        select("products.*")->
        get();

        if (count($waste_list) == 0) {
            return back()->withErrors("هیچ نوع ضایعاتی برای ماشین ها تعریف نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید.");
        }

        //چک کردن پرینتر
        $worker = Worker::find( Auth::user()->id );
        if ( ! $worker->default_printer_id ) {
            return redirect()->route( "utility.printer.select_default_printer" )->withErrors( "لطفا پرینتر پیش فرض را انتخاب نمایید." );
        }

        $list_goods_kind_product = [];
        $product_ids = [];
        foreach ($waste_list as $product) {
            $list_goods_kind_product[$product->id] = $product->goods_kind_id;
            $product_ids[] = $product->id;
        }
        $has_sub_packing_type = PackingType::HasSubPackingType($product_ids);
        $waste_option = Option::get("product_list", 0, 0, $product_ids, "لطفا یک نوع ضایعات را انتخاب نمایید.");

        $dashboard_route = $this->dashboard_route;
        $route_path = $this->route_path;

        return view($this->view_path . "index", compact("machine", "machine_option_list", "machine_type_list", "waste_option", "dashboard_route", "route_path", 'has_sub_packing_type', "list_goods_kind_product"));

    }

    public function submit(Request $request, Machine $machine)
    {
//        return $request->all();
        $machine_ids = [];
        $current_machine = $machine;

        $allowed_machine_ids = Line::getAllowedMachine();
        $allowed_machine_ids[] = -1;
        $machine_list = Machine::
        whereIn("machines.id", $allowed_machine_ids)->
        get();
        $allocation_list = [];
        $last_machine_log_list = [];
        foreach ($machine_list as $machine) {

            if (isset($request->machine_type[$machine->machine_type_id])) {
                if (in_array($machine->id, $request->machine_type[$machine->machine_type_id])) {
                    $machine_ids[$machine->id] = $machine->id;
                    $last_machine_log_list[$machine->id] = MachineLog::getLastLogWithContour($machine);
                    $allocation_list[$machine->id] = $machine->getCurrentAllocation();
                }
            }
            if (isset($request->all_machine_type[$machine->machine_type_id])) {

                $machine_ids[$machine->id] = $machine->id;
                $last_machine_log_list[$machine->id] = MachineLog::getLastLogWithContour($machine);
                $allocation_list[$machine->id] = $machine->getCurrentAllocation();
            }


        }


        if (count($machine_ids) == 0) {
            return back()->withErrors("لطفا حداقل یک ماشین را انتخاب نمایید.");
        }
        $product = Product::find($request->product_id);
        if (!$product) {
            return back()->withErrors("لطفا کالا را به درستی وارد نمایید.");
        }
        $lot_number = LotNumber::where("product_id", $product->id)->first();
        if (!$lot_number) {
            return back()->withErrors("لات برای کالا تعریف نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید.");
        }

        $packing_type = PackingType::find($request->packing_type_id);

        $packing_type_weight_result = PackingType::getWeight($packing_type);

        if (!$packing_type_weight_result["result"]) {
            return back()->withErrors($packing_type_weight_result["error"]);
        }

        $packing_type_weight = $packing_type_weight_result["weight"];
        if ($packing_type->first_packing_type) {
            $sub_packing_type_weight_result = PackingType::getWeight($packing_type);

            if ($sub_packing_type_weight_result["result"]) {
                return back()->withErrors($sub_packing_type_weight_result["error"]);
            }
            $packing_type_weight += $sub_packing_type_weight_result["weight"] * $request->sub_packing_form_number;
        }

        $product_weight_result = Product::getAmountFromWeight($product, $request->gross_weight, $packing_type_weight);

        if (!$product_weight_result["result"]) {
            return back()->withErrors($product_weight_result["error"]);
        }

        // ایجاد فرم بسته بندی
        $new_packing_form = PackingForm::create([
            "packing_type_id" => $request->packing_type_id,
            "carrier_id" => null,
            "status_id" => "7007005",// در انتظار تحویل به انبار
            "sub_packing_form_number" => $request->sub_packing_form_number ?? 0,
            "weight" => $product_weight_result["weight"],
            "gross_weight" => $product_weight_result["gross_weight"],
        ]);

        event(new PackingLogEvent($new_packing_form, 7007001));

        $new_packing_form_item = PackingFormItem::create([
            "packing_form_id" => $new_packing_form->id,
            "product_id" => $request->product_id,
            "lot_number_id" => $lot_number->id,
            "degree_id" => $request->degree_id,
            "amount" => $product_weight_result["final_amount"],
            "amount_after_control" => $product_weight_result["final_amount"],
            "final_amount" => $product_weight_result["final_amount"],
            "sub_amount" => $product_weight_result["sub_amount"],
            "init_sub_amount" => $product_weight_result["sub_amount"],
            "status_id" => 7006003, // بسته بندی شده
            "band_code" => 1
        ]);

        $waste_collection = Product\Waste\WasteCollection::create([
            "user_id" => Auth::id(),
            "product_id" => $product->id,
            "gross_weight" => $product_weight_result["gross_weight"],
            "weight" => $product_weight_result["weight"],
            "amount" => $product_weight_result["final_amount"],
            "packing_form_id" => $new_packing_form->id
        ]);

        foreach ($machine_ids as $machine_id) {

            Product\Waste\WasteCollectionMachine::create([
                "waste_collection_id" => $waste_collection->id,
                "machine_id" => $machine_id,
                "allocation_id" => $allocation_list[$machine->id]->id ?? null,
                "machine_log_id" => $last_machine_log_list[$machine->id]->id ?? null
            ]);
        }
        // پرینت بسته بندی
        $worker = Worker::find(Auth::id());
        PrintQRController::direct_print($new_packing_form, $worker);

        return redirect()->route($this->dashboard_route . "view", $current_machine)->with(["success" => "ضایعات با موفقیت ثبت گردید، لطفا با ورود به داشبورد بسته بندی نسبت به تحویل کالا به انبار اقدام نمایید."]);


    }


}
