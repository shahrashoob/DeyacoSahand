<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralWasteCollectionController;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputMaterialDegree;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class GeneralRequestRawMaterialController extends Controller
{
    // درخواست مواد اولیه
    var $view_path = "goods_kind_process.general.machine.request_raw_material.";
    var $route_path;
    var $dashboard_route;

    public function __construct()
    {

    }

    public function index(Machine $machine)
    {

        $reserve_allocation = $machine->ReserveAllocation()->get();

        $reserve_allocation_list = [];
        foreach ($reserve_allocation as $r_allocation) {
            foreach ($r_allocation->items as $machine_allocation) {
                $reserve_allocation_list[$machine_allocation->allocation_id] = $machine_allocation;
            }

        }

        $current_allocation = $machine->getCurrentAllocation();
        if ($current_allocation) {
            foreach ($current_allocation->items as $machine_allocation) {
                $reserve_allocation_list[$machine_allocation->allocation_id] = $machine_allocation;
            }
        }

        if (count($reserve_allocation_list) == 0) {
            return back()->withErrors("هیچ کارت تولید جهت درخواست مواد اولیه وجود ندارد.");
        }

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;

        return view($this->view_path . "index", compact("reserve_allocation_list", "current_allocation", "machine", "route_path", "dashboard_route"));
    }

    public function submit(Request $request, Machine $machine)
    {

        session(["allocation_request_raw_material" => $request->allocation]);

        return redirect()->route($this->route_path . "select_material", $machine);
    }

    public function select_material(Machine $machine)
    {
        $allocations = session("allocation_request_raw_material");

        if (!isset($allocations)) {
            return redirect()->route($this->route_path . "index", $machine)->withErrors("لطفا حداقل یک تخصیص را انتخاب نمایید.");
        }

        // چک کردن اینکه تمامی نوع تحویل مواد اولیه همه تخصیص ها مثل هم باشد12ض
        $allocation_diff_unit_types = Allocation::whereIn("id", $allocations)->pluck("allocation_unit_type_id")->toArray();
        if (count($allocation_diff_unit_types) > 1) {
            return redirect()->route($this->route_path . "index", $machine)->withErrors(
                "با توجه به اینکه نوع تخصیص های انتخاب شده با هم متفاوت هستند، 
                <br/>
                امکان ثبت درخواست به صورت هم زمان برای تخصیص ها وجود ندارد، لطفا به صورت جداکانه درخواست ثبت نمایید."
            );
        }

        $allocation_diff_unit_type_id = $allocation_diff_unit_types[0];

        $form_status = ProductRequestForm::getFormWaitingStatusList("ids");
        $list_forms = ProductRequestForm::
        join("product_request_form_form", "product_request_form_id", "product_request_forms.id")->
        join("forms", "forms.id", "product_request_form_form.form_id")->
        whereIn("product_request_forms.allocation_id", $allocations)->
        whereIn("forms.status_id", $form_status)->
        selectRaw("forms.code as form_code,product_request_forms.code as prf_code")->
        get();

        if (count($list_forms) > 0) {
            $form_list = "";
            foreach ($list_forms as $item) {
                $form_list = "درخواست " . $item->prf_code . " - برگ خروج" . $item->form_code;
            }

            return back()->withErrors("با توجه به اینکه برگ های خروج زیر در انتظار تایید هستند، امکان درخواست مجدد مواد اولیه وجود ندارد،" .
                "<br/>" . $form_list);
        }

        $current_machine_input_list_material = CurrentMachineInput::
        whereIn("allocation_id", $allocations)->
        groupBy("material_id")->
        selectRaw("id,
          allocation_id,
          material_id,
          product_id,
          goods_kind_id,
          sum(amount_required) as amount_required
          ")->

        get();
        $warehouse_to_option = [];
        $warehouse_option = [];
        // به ازای هر کالا، ماکسیسم تعداد بسته بندی که در هر تخصیص نیاز است، را درخواست می دهمیم.
        foreach ($current_machine_input_list_material as $item) {
            $input_line_code_counts = CurrentMachineInput::
            whereIn("allocation_id", $allocations)->
            where("material_id", $item->material_id)->
            groupBy("allocation_id", "input_line_code")->
            selectRaw("
          allocation_id,
          round(count(input_line_code)) as input_line_code_count,
          min(input_line_code_from) as input_line_code_from,
          max(input_line_code_to) as input_line_code_to
          ")->
            get();

            $max_input_count = 0;
            foreach ($input_line_code_counts as $input_line_code) {
                $count = $input_line_code->input_line_code_from ? $input_line_code->input_line_code_to - $input_line_code->input_line_code_from + 1 : $input_line_code->input_line_code_count;
                $max_input_count = max($count, $max_input_count);
            }

            $item->input_line_code_count = $max_input_count;

            // پیدا کردن انبارک هایی که این کالا در آنها وجود دارد.
            $exist_warehouse_ids = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
            where([
                "packing_forms.status_id" => 7007003,
                "product_id" => $item->material_id,
                "warehouse_status_id" => 4201
            ])->
            whereNotNull("warehouse_id")->
            where("warehouse_id", "!=", $machine->warehouse_id)->
            pluck("warehouse_id", "warehouse_id")->
            toArray();

            // انبار درخواست کالا
            $warehouse_option[$item->material_id] =
                Option::get("warehouse", 0, $exist_warehouse_ids, [
                    2,
                    3,
                    4,
                    5
                ], "انبار پیش فرض");

            // انبار تحویل کالا
            $warehouse_to_option[$item->material_id] =
                Option::get("warehouse", 0, null, [
                    3,
                    4,
                    5
                ], "انبار پیش فرض");


        }

        $route_path = $this->route_path;


        return view($this->view_path . "select_material", compact("current_machine_input_list_material", "machine", "route_path",
            "allocations", "warehouse_option", "warehouse_to_option", "allocation_diff_unit_type_id"));
    }

    public function submit_select_material(Request $request, Machine $machine, $result_check_error = null, $status_id = null, $unit_type_id = 1)
    {

        if ($result_check_error) {
            $result = $result_check_error;
        } else {
            $result = $this->hasError($request, $machine);
        }
        if (!$result["result"]) {
            return $result;
        }

        $other = $result["other"];
        $allocation_ids = $result["allocation_ids"];
        if (count($allocation_ids) == 1) {
//            return [
//                "result"=>false,
//                "error"=>$allocation_ids
//            ];
            foreach ($allocation_ids as $id) { // چون کدش را یادم نبود اینطوری رفتیم.
                $other["allocation_id"] = $id;
            }
        }
        $warehouse_list = $result["warehouse_list"];
        $material_list = $result["material_list"];
        $warehouse_to_id = $result["warehouse_to_id"]; // انبار مقصد، پیش فرض ماشین

        $reserve_allocation_priority = Allocation::whereIn("id", $allocation_ids)->pluck("priority_number", "id")->toArray();

        $allocation_material_list = [];
        $sum_amount_required = [];
        $current_machine_inputs = CurrentMachineInput::whereIn("allocation_id", $allocation_ids)->get();
        foreach ($current_machine_inputs as $current_machine_input) {
            $allocation_material_list[$current_machine_input->allocation_id][$current_machine_input->material_id] =
                $current_machine_input->amount_required;
            if (!isset($sum_amount_required[$current_machine_input->material_id])) {
                $sum_amount_required[$current_machine_input->material_id] = 0;
            }

            $sum_amount_required[$current_machine_input->material_id] += $current_machine_input->amount_required;

        }


        foreach ($warehouse_list as $warehouse_id) {
            $other["warehouse_id"] = $warehouse_id;
            $other["allocation_ids"] = $allocation_ids;
            $other["log_script"] = null;
            $other["material_list"] = $material_list[$warehouse_id];

            // مقدار درخواست را به نسبت روی تخصیص ها می شکنیم.
            foreach ($current_machine_inputs as $current_machine_input) {
                if (isset($material_list[$warehouse_id][$current_machine_input->material_id])) {
                    $allocation_material_list[$current_machine_input->allocation_id][$current_machine_input->material_id] =

                        $allocation_material_list[$current_machine_input->allocation_id][$current_machine_input->material_id] /
                        $sum_amount_required[$current_machine_input->material_id] *
                        $material_list[$warehouse_id][$current_machine_input->material_id];
                }
            }
//return $allocation_material_list;
            $result = ProductRequestForm::newRequest(0, $warehouse_to_id, 40, 1, $other, null, "", $status_id);


            // اضافه کردن مقدار هر ماده اولیه به ازای هر تخصیص در جدول Product_Request_allocation
            Product\ProductRequest\ProductRequestFromAllocation::AddMaterialAmount($allocation_material_list, $reserve_allocation_priority, null, $material_list[$warehouse_id], $result["product_request_form"]);

        }

        return [
            "result" => true,
        ];
    }

    public function hasError(Request $request, Machine $machine)
    {

        if (!isset($request->material)) {
            return [
                "result" => false,
                "error" => "لطفا حداقل یک ماده اولیه انتخاب نمایید."
            ];
        }
        $warehouse_list = [];
        $material_list = [];
        $allocation_ids = json_decode($request->allocation_ids, true);
        $warehouse_from_id = -1;
        $warehouse_to_id = -1;
        $other = [];
        foreach ($request->material as $material_id) {

            $product = Product::find($request->product[$material_id]);


            $current_bom = $product->get_first_bom_from_route($machine);
            if (!$current_bom) {
                return [
                    "result" => false,
                    "error" => "هیچ مسیری محصولی برای کالا یافت نشد."
                ];
            }

            // چون ممکن است که برای کالاهای جایگزین/اصلی BOM تغییر کند، بنابراین انبار درخواست را هم ذخیره کردیم
            $material_current_machine_input = CurrentMachineInput::
            whereIn("allocation_id", $allocation_ids)->
            where("material_id", $material_id)->first();
            $warehouse_id = $material_current_machine_input->request_warehouse_id ?? null;
            if (!$warehouse_id) {
                // از یکجایی به بعد دیگر نیاز به این خط کد نیست، چون در همه تخصیص ها انبار درخواست کالا ذخیره شده است.
                /***********************************/
                // فرض می کنیم که مواد اولیه را فقط به یک انبار درخواست می دهد.
                $warehouse_id = $current_bom->items()->where("material_id", $material_id)->pluck("warehouse_id")->first();
                if (!$warehouse_id) {
                    // احتمالا ماده اولیه جایگزنین است.
                    $bom_replace = Product\BOM\BOMReplace::
                    where([
                        "bill_of_material_id" => $current_bom->id,
                        "replace_product_id" => $material_id
                    ])->first();
                    $warehouse_id = $bom_replace->bom_item->warehouse_id;
                    if (!$warehouse_id) {
                        return [
                            "result" => false,
                            "error" => "انبار مواد اولیه جهت درخواست ماده اولیه در BOM " . $product->caption . " تعریف نشده است. "
                        ];
                    }
                }
                /*********************************/
            }
            $warehouse_list[$warehouse_id] = $warehouse_id;

            // انتخاب انبارک مبدا
            if (isset($request->warehouse_from_ids) && isset($request->warehouse_from_ids[$material_id]) && $request->warehouse_from_ids[$material_id]) {
                if ($warehouse_from_id != -1 && ($warehouse_from_id > 0 && $warehouse_from_id != $request->warehouse_from_ids[$material_id])) {
                    return [
                        "result" => false,
                        "error" => "برای هر درخواست به انبارک فقط باید یک انبار را انتخاب کنید."
                    ];
                }
                $warehouse_from_id = $request->warehouse_from_ids[$material_id];
            } else {
                $warehouse_from_id = $warehouse_id;
            }


            // انتخاب انبارک مقصد
            if (isset($request->warehouse_to_ids) && isset($request->warehouse_to_ids[$material_id]) && $request->warehouse_to_ids[$material_id]) {
                if ($warehouse_to_id != -1 && ($warehouse_to_id > 0 && $warehouse_to_id + 0 != $request->warehouse_to_ids[$material_id] + 0)) {
                    return [
                        "result" => false,
                        "error" => "برای هر درخواست به انبارک فقط باید یک انبار مقصد را انتخاب کنید." . "<br/> " .
                            ($warehouse_to_id == $request->warehouse_to_ids[$material_id] ? 1 : 2)
                    ];
                }

                $warehouse_to_id = $request->warehouse_to_ids[$material_id];
            } else {

                $warehouse_to_id = $machine->warehouse_id;
            }


            $bom_item = $current_bom->
            items()->
            where("warehouse_id", $warehouse_id)->
            where("material_id", $material_id)->first();

            if (!$bom_item) {
                $bom_replace = Product\BOM\BOMReplace::
                where([
                    "bill_of_material_id" => $current_bom->id,
                    "replace_product_id" => $material_id
                ])->first();
                $bom_item = $current_bom->items()->where([
                    "material_id" => $bom_replace->material_id ?? 0
                ])->first();


            }

            // ابتدا درجه های در زمان تخصیص را برمی داریم، اگر نبود از درجه های حال bom استفاده می کنیم.
            $degree_id_list = CurrentMachineInputMaterialDegree::
            whereIn("allocation_id", $allocation_ids)->
            where("material_id", $material_id)->pluck("degree_id")->toArray();
            if (count($degree_id_list) == 0) {

                if (!$bom_item) {
                    return [
                        "result" => false,
                        "error" => "BOM کالا به درستی تعریف نشده است، لطفا با پشتیبانی تماس بگیرید."
                    ];
                }
                $degree_id_list = $bom_item->degrees()->pluck("degree_id")->toArray();
            }

            $other["degree_id_list"][$warehouse_from_id][$material_id] = $degree_id_list;

            if ($request->number_of_packing[$material_id] <= 0) {
                return [
                    "result" => false,
                    "error" => "حداقل تعداد بسته بندی جهت درخواست 1 می باشد."
                ];
            }
            $other["warehouse_material_packing_count"][$warehouse_from_id][$material_id] = $request->number_of_packing[$material_id];

            $allocations = session("allocation_request_raw_material");
            if ($allocations) {
                $allocation_diff_unit_types = Allocation::whereIn("id", $allocations)->pluck("allocation_unit_type_id")->toArray();
                if ($allocation_diff_unit_types[0] == 4) {
                    $other["warehouse_material_packing_count_max"][$warehouse_from_id][$material_id] = $request->number_of_packing[$material_id];
                }
            }

            //ممکن است مقدار درخواست نال باشد
            $material_list[$warehouse_from_id][$material_id] = isset($request->amount_required[$material_id]) ?
                $request->amount_required[$material_id] : null;


            $other["goods_kind_id_list"] [$material_id] = $request->goods_kind[$material_id];
            $other["machine"] = $machine;
            $other["user_id"] = Auth::id();
            $other["product_request_form_is_enabled"] = 1;
        }

        if ($warehouse_from_id > 0) {
            $warehouse_list = [$warehouse_from_id];
        }

        if (count($material_list) == 0) {
            return [
                "result" => false,
                "error" => "لطفا حداقل یک کالا جهت درخواست به انبار انتخاب نمایید."
            ];
        }
        if (count($warehouse_list) == 0) {
            return [
                "result" => false,
                "error" => "لطفا حداقل یک کالا جهت درخواست به انبار انتخاب نمایید."
            ];
        }
        if (!$warehouse_to_id) {
            return [
                "result" => false,
                "error" => "انبارک ماشین مشخص نشده است، لطفا با واحد پشتیبانی تماس بگیرید."
            ];
        }
        return [
            "result" => true,
            "other" => $other,
            "allocation_ids" => $allocation_ids,
            "warehouse_list" => $warehouse_list,
            "material_list" => $material_list,
            "warehouse_from_id" => $warehouse_from_id,
            "warehouse_to_id" => $warehouse_to_id,
        ];
    }
}
