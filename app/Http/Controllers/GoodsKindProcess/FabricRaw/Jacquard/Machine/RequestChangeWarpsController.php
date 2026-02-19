<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralRequestRawMaterialController;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use Illuminate\Http\Request;


class RequestChangeWarpsController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.jacquard.machine.request_change_warps.",
        "enable_status" => ["016", "042"],
        "button" => ["caption" => "درخواست تعویض چله", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.fabric_raw.jacquard.machine.request_change_warps.",
//        "message" => ["confirm" => "آیا از درخواست تعویض چله اطمینان دارید؟"],
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.jacquard.machine.dashboard.";

    public function __construct()
    {
        $this->route_path = RequestChangeWarpsController::$info["route"];
        $this->view_path = RequestChangeWarpsController::$info["view_path"];
    }

    public function index(Request $request, Machine $machine)
    {
        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا دکمه پایان بافت کارت تولید را بزنید.");
        }
        $warps_list = CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 3
        ])->
        groupBy("input_line_code","material_id")->
        get();
        return view($this->view_path . "index", compact("machine", "warps_list"));
    }

    public function submit(Request $request, Machine $machine)
    {

        $result = $this->checkPermission($machine);
        if ($result != "") {
            return $result;
        }

        $allocation = $machine->getCurrentAllocation();

        if (!$allocation) {
            return back()->withErrors("تخصیص جاری برای ماشین وجود ندارد، لطفا دکمه پایان بافت کارت تولید را بزنید.");
        }

        // باید حداقل یک ورودی را انتخاب کند
        if (!isset($request->material_selected)) {
            return back()->withErrors("لطفا حداقل یک ورودی را انتخاب نمایید.");
        }

        // در تعویض چله یک کانال تولید مشابه کانال تولید جاری ماشین داریم که بعد از آن رزرو است،
        // کانال تولید جاری را تولید شده و کانال رزرو را به جاری تبدیل می کنیم.
        $current_production_channel = $machine->getCurrentProductionChannel();
        if (!$current_production_channel) {
            return back()->withErrors("کانال تولید جاری برای ماشین، وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }

        $reserve_production_channel = $machine->ReserveProductionChannel()->first();
        if (!$reserve_production_channel) {
            return back()->withErrors("کانال تولید رزور برای ماشین، وجود ندارد. " . "<br/>" .
                SpecialLicense::GetLink(11, $machine->id, "ثبت درخواست ایجاد کانال جدید  ", $current_production_channel->id)

            );
        }
        if (
            $current_production_channel->production_channel_type_id
            !=
            $reserve_production_channel->production_channel_type_id
        ) {
            return back()->withErrors("نوع کانال تولید جاری و کانال تولید رزرو با هم متفاوت است، لطفا با پشتیبانی تماس بگیرید.");
        }

        $result_check_error = $this->request_warps_has_error($request, $allocation, $machine);
        if (!$result_check_error["result"]) {
            return back()->withErrors($result_check_error["error"]);
        }

        $warps_is_in_machine_warehouse = Warps::warpsExistInMachineWarehouse($allocation);
        if ($warps_is_in_machine_warehouse) {
            // چله در انبارک هست و فقط وضعیت ماشین عوض می شود.
            $machine->setStatus(
                null,
                53002,
                DashboardController::$perfix_production_status_code . "022",
                1617);
        } else {
            // پیدا کردن کد چله
            $warps_is_in_warehouse = Warps::warpsExistInWarehouse($allocation);

            if ($warps_is_in_warehouse) {
                // چله در انبار هست
                $machine->setStatus(
                    null,
                    53002,
                    DashboardController::$perfix_production_status_code . "022",
                    1617);

            } else {
                // چله در انبار نیست
                $machine->setStatus(
                    null,
                    53002,
                    DashboardController::$perfix_production_status_code . "021",
                    1616);


            }

            $this->request_warps($request, $allocation, $machine, $result_check_error, $warps_is_in_warehouse ? 7005001 : 7005003);
        }


        // وضعیت ورودی های ماشین را در انتظار تعویص قرار می دهیم.
        $current_input_machine_ids = array_keys($request->material_selected);


// وضعیت ورودی های ماشین را در انتظار تعویص قرار می دهیم.
        $current_input_machine_ids = array_keys($request->material_selected);

        // زمانی که یک چله در دو کارت تولید وجود دارد، باید کد را برای همه در نظر بگیریم.
        $warps_machine_input = CurrentMachineInput::where(
            [
                "allocation_id" => $allocation->id,
                "goods_kind_id" => 3
            ])->
        get();
        $data_other_doblicate=[]; // لیست ورودی هایی که نباید چک شود که تکراری هستند ( مثل چله ها )
        // return $request->all();
        foreach ($warps_machine_input as $item) {
            if (!in_array($item->id,$current_input_machine_ids)) {

                // این در صورتی پیش می آید که ماشین برای یک ورودی
                $c= CurrentMachineInput::
                where("allocation_id", $item->allocation_id)->
                where("material_id", $item->material_id)->
                where("input_line_code", $item->input_line_code)->
                where("id","!=", $item->id)->first();
                if($c && in_array($c->id,$current_input_machine_ids)){
                    $current_input_machine_ids[]=$item->id;
                }
            }
        }



        CurrentMachineInput::whereIn("id",$current_input_machine_ids)->update(["replacement_status_id"=>3359002]); // نیاز به تعویض دارد.

        $machineLog = new MachineLog();
        $machineLog->machine_event_type_id = 270;

        event(new MachineLogEvent($machine, $machineLog));

        return redirect()->route($this->dashboard_route . "view", compact("machine"))->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function request_warps(Request $request, $allocation, $machine, $result_check_error, $status_id)
    {

        $material_ids[] = -1;
        foreach ($result_check_error["material_list"] as $warehouse_list) {
            foreach ($warehouse_list as $key => $material_id) {
                $material_ids[] = $material_id;
            }
        }

        $open_request_count = ProductRequestForm::
        join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        where("applicant_type_id", 40)->
        where("applicant_id", $machine->warehouse_id + 0)->
        whereIn("product_id", $material_ids)->
        whereIn("product_request_forms.status_id", [0, 7005001, 7005003, 7005004, 7005008])->
        count("product_request_forms.id");

        // اگر درخواستی قبلا ارسال شده است، نیاز به ثبت درخواست مجدد وجود ندارد.
        if ($open_request_count > 0) {
            return [
                "result" => true
            ];
        }
        // ران شدن ماژول درخواست مواد اولیه
        $publicController = $this->getGeneralController();

        $result = $publicController->submit_select_material($request, $machine, $result_check_error, $status_id);

        return $result;
    }

    public function product_request_warps_exist(Machine $machine, Allocation $allocation)
    {
        $warps_list = CurrentMachineInput::
        where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 3
        ])->
        pluck("material_id", "material_id");
        $warps_list[] = -1;

        $product_request_form = ProductRequestForm::
        join("product_request_form_item", "product_request_forms.id", "product_request_form_item.id")->
        where([
            "applicant_type_id" => 40,
            "applicant_id" => $machine->warehouse_id
        ])->
        whereIn("product_id", $warps_list)->
        whereIn("product_request_forms.status_id", [7005003, 7005001])->
        select("product_request_forms.*")->
        first();

        return $product_request_form;
    }

    public function request_warps_has_error(Request $request, $allocation, $machine,$all_product=false)
    {
        $warps_list = CurrentMachineInput::where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 3
        ])->get();
        $material = [];
        $allocation_ids = [$allocation->id];
        $product = [];
        $goods_kind = [];
        $amount_required = [];
        $number_of_packing = [];
        foreach ($warps_list as $item) {
            if (isset($request->material_selected[$item->id]) || $all_product) {
                $material[$item->material_id] = $item->material_id;
                $product[$item->material_id] = $item->product_id;
                $goods_kind[$item->material_id] = $item->goods_kind_id;
                $amount_required[$item->material_id] = $item->amount_required;
                $number_of_packing[$item->material_id] = $item->number;
            }
        }
        $request["allocation_ids"] = json_encode($allocation_ids);
        $request["product"] = $product;
        $request["goods_kind"] = $goods_kind;
        $request["amount_required"] = $amount_required;
        $request["material"] = $material;
        $request["number_of_packing"] = $number_of_packing;

        // ران شدن ماژول درخواست مواد اولیه
        $publicController = $this->getGeneralController();

        $result = $publicController->hasError($request, $machine);

        return $result;
    }

    public function getGeneralController()
    {
        $publicController = new GeneralRequestRawMaterialController();
        $publicController->route_path = $this->route_path;
        $publicController->dashboard_route = $this->dashboard_route;

        return $publicController;
    }

    public static function GetNextStatusAfterWarping($allocation, $reserve_allocation)
    {
        $production_status_id = null;
        if ($allocation) {
            $machine_allocation = $allocation->items()->first();
            if ($machine_allocation && $machine_allocation->production->production_type_id == 2) {
                return 7003054; // در حال نمونه گیری
            }
        }
        if (!$production_status_id) {
            return isset($reserve_allocation) ?
                7003042 :// در انتظار پایان بافت (کارت تولید جاری)
                7003016;// در حال بافت
        }
        return 7003016; // در حال بافت
    }

    public function checkPermission(Machine $machine)
    {

        $result = DashboardController::checkPermissionConditions($machine, RequestChangeWarpsController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
