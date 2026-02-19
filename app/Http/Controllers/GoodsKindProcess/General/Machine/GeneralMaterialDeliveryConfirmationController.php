<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\ProductionWarehouse\DashboardController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class GeneralMaterialDeliveryConfirmationController extends Controller
{

    var $view_path = "goods_kind_process.general.machine.material_delivery_confirmation.";
    var $route_path;
    var $dashboard_route;

    public function __construct()
    {
    }

    public function index(Machine $machine)
    {


        $product_request_form = ProductRequestForm::where([
            "applicant_type_id" => 40,
            "applicant_id" => $machine->warehouse_id,
            "status_id" => 7005004,// در انتظار تایید برگ خروج
        ])->first();

        if (!$product_request_form) {
            return back()->withErrors("هیچ برگ خروجی در انتظار تایید نمی باشد.");
        }

        $product_request_form_forms = $product_request_form->forms;

        $result = DashboardController::GetPackingListFroConfirm($product_request_form_forms, "material_delivery_" . $machine->id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $form_info = $result["form_info"];
        $packing_form_codes = $result["packing_form_codes"];
        $packing_codes_reading = $result["packing_codes_reading"];
        $goods_kind_ids = $result["goods_kind_ids"];

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;

        // آیا لازم است که کل بسته بندی ها را وارد کند و یا یکی را وارد کند کافی است.
        $warehouse_entry_confirmation_in_altogether = MachineTypeInputBandGoodsKind::join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_goods_kind.machine_type_input_band_id")->
        whereIn("goods_kind_id", $goods_kind_ids)->
        where("machine_type_id", $machine->machine_type_id)->
        sum("warehouse_entry_confirmation_in_altogether");


        return view($this->view_path . "index", compact("product_request_form", "machine", "form_info", "route_path",
            "warehouse_entry_confirmation_in_altogether",
            "dashboard_route", "packing_form_codes", "packing_codes_reading"));


    }

    public
    function submit(
        Request $request, Machine $machine
    )
    {

        $data = $request->data;
        if (!isset($data["packing_code"]) && !$request->warehouse_entry_confirmation_in_altogether) {
            return back()->withErrors("لطفا کد بسته بندی ها جهت تایید برگ خروج را وارد نمایید.");
        }

        $packing_codes = [];
        if (!$request->warehouse_entry_confirmation_in_altogether) {
            $packing_codes = $data["packing_code"];
        }

        $var_session = "material_delivery_" . $machine->id;
        session([$var_session => $packing_codes]);

        $product_request_form = ProductRequestForm::where([
            "applicant_type_id" => 40,
            "applicant_id" => $machine->warehouse_id,
            "status_id" => 7005004,// در انتظار تایید برگ خروج
        ])->first();

        if (!$product_request_form) {
            return redirect()->route($this->dashboard_route, $machine)->withErrors("هیچ فرمی برای تایید وجود ندارد،");
        }

        $product_request_form_forms = $product_request_form->forms;

         $result = DashboardController::SubmitPackingListFromConfirm($request, $product_request_form_forms, $machine->warehouse, "material_delivery_" . $machine->id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


}
