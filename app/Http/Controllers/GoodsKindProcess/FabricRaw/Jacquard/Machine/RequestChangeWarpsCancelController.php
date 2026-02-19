<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use function back;
use function event;
use function redirect;

class RequestChangeWarpsCancelController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.request_change_warps_cancel.",
        "enable_status" => ["021", "022"],
        "button" => ["caption" => "کنسل کردن درخواست تعویض چله", "class" => "btn-danger"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.request_change_warps_cancel.",
        "message" => ["confirm" => "آیا از کنسل کردن درخواست تعویض چله اطمینان دارید؟"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = RequestChangeWarpsCancelController::$info["route"];
        $this->view_path = RequestChangeWarpsCancelController::$info["view_path"];
    }

    public function submit(Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }


        // اگر درخواستی در انتظار تایید برگ خروج است و کالای چله در آن است اجازه کنسل کردن نمی دهد.
        $warps_form_in_delivery_ids = WarpsRequestForm::where([
            "applicant_id" => $machine->warehouse_id,
            "applicant_type_id" => 40,
        ])->
        whereIn("status_id", [7005004])->
        orderByDesc("id")->
        pluck("id")->
        toArray();

        if (count($warps_form_in_delivery_ids) > 0) {
            $list = ProductRequestFormForm::join("form_item", "form_item.form_id", "product_request_form_form.form_id")->
            join("products", "form_item.product_id", "products.id")->
            whereIn("product_request_form_id", $warps_form_in_delivery_ids)->
            where("goods_kind_id", 3)->
            get();

            if (count($list) > 0) {
                $message="";
                foreach ($list as $item){
                    $message.="<br/>";
                    $message.=$item->product_request_form->code??"";
                }
                return back()->withErrors("با توجه به اینکه چله در حال تحویل به تولید می باشد، امکان کنسل کردن درخواست وجود ندارد.".$message);
            }
        }

        // اگر چله ای در انبارک ماشین هست
        // که هنوز در هیچ ورودی ثبت نشده است،
        // با توجه به اینکه چله فلان در انبارک ماشین است، امکان کنسل کردن درخواست وجود ندارد.
        $packing_forms = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "product_id")->
        where("warehouse_id", $machine->warehouse_id)->
        where("packing_forms.status_id", 7007003)->
        where("packing_forms.warehouse_status_id", 4201)->
        where("goods_kind_id", 3)->
        select("packing_forms.*")->
        get();

        foreach ($packing_forms as $packing_form) {
            $current_machine_input = CurrentMachineInput::
            where("machine_id", $machine->id)->
            where("packing_form_id", $packing_form->id)->
            first();

            if (!$current_machine_input) {
                return back()->withErrors("با توجه به اینکه بسته بندی " . $packing_form->getCode() . " توسط تولید دریافت شده است، امکان کنسل کردن درخواست وجود ندارد.");
            }
        }

        // کنسل کردن درخواست ها
        $warps_forms = WarpsRequestForm::
        join("product_request_form_item", "product_request_form_id", "product_request_forms.id")->
        join("products", "products.id", "product_id")->
        where([
            "applicant_id" => $machine->warehouse_id,
            "applicant_type_id" => 40,
            "goods_kind_id" => 3
        ])->
        whereIn("product_request_forms.status_id", [7005001, 7005003, 7005008])->
        select("product_request_forms.*")->
        get();
        foreach ($warps_forms as $warps_form) {
            // کنسل کردن درخواست
            $warps_form->cancelRequest();
        }


        $reserve_allocation = $machine->getFirstReserveAllocation();
        $allocation = $machine->getCurrentAllocation();

        $machine->setStatus(
            null,
            53001,
            RequestChangeWarpsController::GetNextStatusAfterWarping($allocation, $reserve_allocation),
            null,
            "Fabric_Raw"
        );
        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 510; // کنسل شدن درخواست تعویض چله

        event(new MachineLogEvent($machine, $machineLog));

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }


    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, RequestChangeWarpsCancelController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
