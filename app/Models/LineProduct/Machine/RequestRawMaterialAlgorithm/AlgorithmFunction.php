<?php

namespace App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineType\MachineTypeInputAlgorithm;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Post\Post;
use App\Models\Utility\Script\Script;
use App\Models\Utility\Script\ScriptLog;
use App\Models\Utility\Setting;
use App\Models\Warehouse\Warehouse;
use App\Notifications\SMSNotification;
use Illuminate\Support\Facades\Notification;

class AlgorithmFunction extends Controller
{
    //
    public static function getWarehouseListFromBOM(Product $product, Machine $machine)
    {
        $current_bom = $product->get_first_bom_from_route($machine);
        if (!$current_bom) {
            return [];
        }

        return $current_bom->items()->groupBy("warehouse_id")->pluck("warehouse_id")->toArray();
    }

    public static function rejectWaitingForm($applicant_id, $applicant_type_id, $script, $machine, $warehouse, $user_id)
    {
        // اگر در خواستی در انتظار تایید برگ خروج است، برگ خروج را عدم تایید کند.
        $waiting_for_confirm_requests = ProductRequestForm::where([
            "applicant_type_id" => $applicant_type_id,
            "applicant_id" => $applicant_id,
            "warehouse_id" => $warehouse->id,
        ])->where("status_id", 7005004)-> // در انتظار تایید برگ خروج
        orderByDesc("id")->
        get();

        if (count($waiting_for_confirm_requests) <= 0) {

            return "OK";
        }

        $data_script = json_decode($script->data, true);
        $exit_form_message = "";
        foreach ($waiting_for_confirm_requests as $waiting_for_confirm_request) {

            $product_request_form_forms = ProductRequestFormForm::where("product_request_form_id", $waiting_for_confirm_request->id)->get();
            foreach ($product_request_form_forms as $product_request_form_form) {

                if ($product_request_form_form->form && !in_array($product_request_form_form->form->status_id, [
                        500000100,
                        500000200
                    ])) {

                    // اگر تایید شده یا عدم تایید شده نیستند، آن را عدم تایید می کند
                    $waiting_for_confirm_request->rejectRequest($product_request_form_form->form, $user_id);
                    $exit_form_message .= ($product_request_form_form->form->code ?? "***") . ", ";

                }
            }

        }

        if ($exit_form_message != "") {
            // ارسال پیامک جمع آوری برگ های عدم تایید شده
            $system_name = Setting::getStringValue("company_name");
            foreach ($data_script["post_sms_template"] as $post_id => $template_id) {

                $post = Post::find($post_id);
                if (!$post) {
                    continue;
                }
                foreach ($post->worker as $worker) {

                    $token20 = $post->caption . " " . $system_name;
                    $token10 = $exit_form_message;
                    $token = $machine->caption;
                    $token2 = null;
                    $token3 = null;

                    Notification::send(
                        "00" . ($worker->country->area_code ?? "98") . $worker->mobile,
                        new SMSNotification("script1007tem1",
                            $token,
                            $token2,
                            $token3,
                            $token10,
                            $token20));

                }
            }

        }

    }

    public static function log($script, $other_id, $event_id, $contour, $data, $user_id, $machine_id)
    {

        $log = ScriptLog::create([
            "script_id" => $script->id,
            "other_id" => $other_id,
            "event_id" => $event_id,
            "run_status_id" => $script->run_status_id,
            "user_id" => $user_id,
            "contour" => $contour,
            "data" => json_encode($data),
            "machine_id" => $machine_id
        ]);

        return $log;

    }

    public static function RequestForMachine(Machine $machine, Script $script, $contour, $user_id, $emergency_time)
    {

        // بررسی اینکه برای کدام رسته کالایی ها درخواست زده شود
        $send_product_request_form_by_robot = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        where("machine_type_id", $machine->machine_type_id)->
        pluck("send_product_request_form_by_robot", "goods_kind_id")->
        toArray();

        // وضعیت اولیه درخواست توسط رباط فعال باشد یا خیر
        $product_request_form_by_robot_is_enabled = MachineTypeInputBandGoodsKind::
        join("machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id")->
        where("machine_type_id", $machine->machine_type_id)->
        pluck("product_request_form_by_robot_is_enabled", "goods_kind_id")->
        toArray();

        $dependency_to_other_goods_kind_called = false; // درخواست همراه با دیگر درخواست ها حداقل یکبار اجرا شده است


        // الگوریتم درخواست را به ازای هر رسته کالایی در ورودی های ماشین اجرا می کنیم.
        foreach ($machine->machine_type->machine_type_input_algorhtim as $machine_type_input_algorithm) {

            // اگر نیاز است تا برای رسته کالایی درخواست به انبار زده شود، درخواست می زنیم.
            if (
                isset($send_product_request_form_by_robot[$machine_type_input_algorithm->goods_kind_id])
                &&
                $send_product_request_form_by_robot[$machine_type_input_algorithm->goods_kind_id]
                &&
                (
                    $machine_type_input_algorithm->dependency_to_other_goods_kind == 1 ||
                    ($machine_type_input_algorithm->dependency_to_other_goods_kind == 0 && $dependency_to_other_goods_kind_called == false)
                )

            ) {

                // اگر حداقل یکبار اجرا شد، کافی است.
                if ($machine_type_input_algorithm->dependency_to_other_goods_kind == 0) {
                    $dependency_to_other_goods_kind_called = 1;
                }
                $product_request_form_is_enabled = $product_request_form_by_robot_is_enabled[$machine_type_input_algorithm->goods_kind_id];

                $result = self::RequestForMachineInGoodsKind($machine, $script, $user_id, $emergency_time, $machine_type_input_algorithm, $product_request_form_is_enabled);
                if (!$result["result"]) {
                    if (isset($result["warning"])) {
                        //نیازی نیست که پیامک ارسال شود، چون هشدار دارد
                    } else {
                        Script::SendSmd($script, $result["log"]->id ?? 0, $result["message"], $user_id);
                    }

                }
            }

        }


    }

    public static function RequestForMachineInGoodsKind(Machine $machine, Script $script, $user_id, $emergency_time, MachineTypeInputAlgorithm $machine_type_input_algorithm, $product_request_form_is_enabled)
    {


        $productive_algorithm = $machine_type_input_algorithm->raw_material_request_algorithm_type;

        if (!isset($productive_algorithm->directory_namespace)) {
            return [
                "result" => false,
                "message" => "الگوریتم های تولیدی درخواست مواد اولیه برای  " . $machine->caption . " نامعتبر است. ",
                "log" => [],
            ];
        }

        $goods_kind_ids = [$machine_type_input_algorithm->goods_kind_id];
        if ($machine_type_input_algorithm->dependency_to_other_goods_kind == 0) {
            $goods_kind_ids = MachineTypeInputAlgorithm::where("machine_type_id", $machine_type_input_algorithm->machine_type_id)->
            where("dependency_to_other_goods_kind", 0)->pluck("goods_kind_id")->toArray();
        }
        // اجرای الگوریتم برای کارت های تولیدی
        $result_productive_algorithm = $productive_algorithm->directory_namespace::RequestForMachine($machine, $script, $goods_kind_ids, $user_id, $emergency_time, 1);

        if (!$result_productive_algorithm["result"] || ($result_productive_algorithm["result"] && isset($result_productive_algorithm["warning"]))) {

            return [
                "result" => false,
                "message" => $result_productive_algorithm["log_data"]["message"],
                "log" => $result_productive_algorithm["log"],
                "warning" => isset($result_productive_algorithm["warning"]) ? $result_productive_algorithm["warning"] : null
            ];
        }

        $sampling_algorithm = $machine_type_input_algorithm->raw_material_request_sampling_algorithm_type;
        if (!isset($sampling_algorithm->directory_namespace)) {
            return [
                "result" => false,
                "message" => "الگوریتم های نمونه گیری درخواست مواد اولیه برای  " . $machine->capiton . " نامعتبر است. ",
                "log" => [],
            ];
        }
        // اجرای الگوریتم برای کارت های نمونه گیری
        $result_sampling_algorithm = $sampling_algorithm->directory_namespace::RequestForMachine($machine, $script, $goods_kind_ids, $user_id, $emergency_time, 2);

        if (!$result_sampling_algorithm["result"] || ($result_sampling_algorithm["result"] && isset($result_sampling_algorithm["warning"]))) {
            return [
                "result" => false,
                "message" => $result_sampling_algorithm["log_data"]["message"],
                "log" => $result_sampling_algorithm["log"],
                "warning" => isset($result_sampling_algorithm["warning"]) ? $result_sampling_algorithm["warning"] : null

            ];
        }
        $remove_list_from_sampling_result = [];
        $result_same_material = [];
        $material_ids = [];
        $all_material_ids_in_productive_algorithm = [-1];

        // به دست آوردن کل ماده اولیه هایی که در درخواست تولیدی  است.
        foreach ($result_productive_algorithm["warehouse_material_packing_count"] as $warehouse_id => $material_list) {
            foreach ($material_list as $consume_warehouse_id => $material_list_consume_warehouse) {

                foreach ($material_list_consume_warehouse as $material_id => $count) {
                    if ($count > 0) {
                        $all_material_ids_in_productive_algorithm[] = $material_id;
                    }
                }
            }
        }


        // اگر در نتیجه الگوریتم نمونه گیری کالایی بود که در الگوریتم تولید درخواست می دادیم، آن را از الگوریتم نمونه گیری حذف و به الگوریتم تولیدی اضافه می کنیم.
        foreach ($result_productive_algorithm["material_list"] as $warehouse_id => $material_list) {

            foreach ($material_list as $consume_warehouse_id => $material_list_consume_warehouse) {

                $material_ids = array_keys($material_list_consume_warehouse);
                $material_ids[] = -1;

                $result_same_material = self::HasAnySameRequest($warehouse_id, $consume_warehouse_id, $material_ids, $result_sampling_algorithm);
                foreach ($result_same_material as $key => $value) {
                    $remove_list_from_sampling_result[$key] = $value;
                }

            }
        }


        // لیست کل آیتم هایی که باید حذف شود را حذف می کند
        foreach ($remove_list_from_sampling_result as $item) {

            $warehouse_id = $item["warehouse_id"];
            $consume_warehouse_id = $item["consume_warehouse_id"];
            $consume_warehouse_id_old = $item["consume_warehouse_id"];
            $material_id = $item["material_id"];
            $amount = $item["amount"];

            // ممکن است یک ماده اولیه در لیست مواد اولیه تولید نباشد ولی باید با یک درخواست ارسال شود.
            if (!isset($result_productive_algorithm["material_list"][$warehouse_id][$consume_warehouse_id][$material_id])) {

                // انبار درخواست متفاوت است ولی کالا یکی است، بنابراین به دنبال انبار مصرف صیحیح می گردیم.
                foreach ($result_productive_algorithm["material_list"][$warehouse_id] as $consume_warehouse_id_new => $material_list_new) {

                    if (isset($result_productive_algorithm["material_list"][$warehouse_id][$consume_warehouse_id_new][$material_id])) {

                        $consume_warehouse_id = $consume_warehouse_id_new;
                    }
                }
                if (!isset($result_productive_algorithm["material_list"][$warehouse_id][$consume_warehouse_id][$material_id])) {

                    $result_productive_algorithm["material_list"][$warehouse_id][$consume_warehouse_id][$material_id] = 0;
                }

            }

            // اضافه کردن مقدار ماده اولیه در درخواست های تولیدی
            $result_productive_algorithm["material_list"][$warehouse_id][$consume_warehouse_id][$material_id] += $amount;

            $result_productive_algorithm["material_list_for_sampling"][$warehouse_id][$consume_warehouse_id][$material_id] = $amount;


            // بروز رسانی تعداد بسته بندی ها
            $result_productive_algorithm["warehouse_material_packing_count"][$warehouse_id][$consume_warehouse_id][$material_id] =
                max(
                    $result_productive_algorithm["warehouse_material_packing_count"][$warehouse_id][$consume_warehouse_id][$material_id],
                    $result_sampling_algorithm["warehouse_material_packing_count"][$warehouse_id][$consume_warehouse_id][$material_id]
                );


            // مقدار تخصیص های ماده اولیه هایی که جابجا می شوند را هم باید اضافه کنیم.
            foreach ($result_sampling_algorithm["log_data"]["allocation_material_list"] as $allocation_id => $material_list) {
                foreach ($material_list as $allocation_material_id => $value) {
                    if ($material_id == $allocation_material_id) {
                        $result_productive_algorithm["log_data"]["allocation_material_list"][$allocation_id][$material_id] = $value;
                    }
                }
            }

            // حذف ماده اولیه از لیست درخواست های نمونه گیری
            unset(
                $result_sampling_algorithm["material_list"][$warehouse_id][$consume_warehouse_id_old][$material_id]
            );

        }


        $log["product_request_form_is_enabled"] = $product_request_form_is_enabled;


        $log["special_goods_kind_id"] = $goods_kind_ids;

        $log["ProductAlgorithmResult"] = $result_productive_algorithm;

        $log["SamplingAlgorithmResult"] = $result_sampling_algorithm;


        $contour = 0;

        // اجرا کردن درخواست های تولید
        $contour += self::CreateRequestFromWarehouse($result_productive_algorithm, $machine, $user_id, $contour, $script, $log, true, $product_request_form_is_enabled, $machine_type_input_algorithm->goods_kind_id);
        // return     self::CreateRequestFromWarehouse( $result_productive_algorithm, $machine, $user_id, $contour, $script, $log, true );


        // اگر در نتیجه الگوریتم نمونه گیری کالایی بود که در الگوریتم تولید درخواست وجود داشت
        // و درخواست نمی دادیم، لازم نیست، برای نمونه گیری درخواست دهیم و از کالاهای نمونه گیری حذف می کنیم.
        foreach ($result_sampling_algorithm["material_list"] as $warehouse_id => $material_list) {

            foreach ($material_list as $consume_warehouse_id => $material_list_consume_warehouse) {

                foreach ($material_list_consume_warehouse as $material_id => $value) {
                    if (in_array($material_id, $all_material_ids_in_productive_algorithm)) {

                        unset($result_sampling_algorithm["material_list"][$warehouse_id][$consume_warehouse_id][$material_id]);
                    }
                }

            }
        }

        // اجرا کردن درخواست های نمونه گیری
        $contour += self::CreateRequestFromWarehouse($result_sampling_algorithm, $machine, $user_id, $contour, $script, $log, false, $product_request_form_is_enabled, $machine_type_input_algorithm->goods_kind_id);

        // اگر هیچ درخواستی نرست، موجودی کافی است.
        if ($contour == 0) {
            $log_data = self::UnsetData($log);
            $goods_kind_ids[] = -1;
            $product_request_forms = ProductRequestForm::
            join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
            join("products", "product_id", "products.id")->
            where([
                "applicant_type_id" => 40,
                "applicant_id" => $machine->warehouse_id,
                "product_request_forms.status_id" => 7005001
            ])->whereIn("goods_kind_id", $goods_kind_ids)->
            select("product_request_forms.*")->
            get();
            foreach ($product_request_forms as $product_request_form_item) {
                $product_request_form_item->status_id =7005009;
                $product_request_form_item->save();

                event(new ProductRequestFormLogEvent($product_request_form_item, "", null, 7005016, $user_id));

            }

                $log_data["message"] = "موجودی انبارک برای ماشین " . $machine->fullCaption() . "به مقدار کافی می باشد.";
            AlgorithmFunction::log($script, 0, 41002, $contour, $log_data, $user_id, $machine->id);

        }

        return [
            "result" => true,
            "log" => $log
        ];

    }


    public static function CreateRequestFromWarehouse(
        $result_algorithm, $machine, $user_id, $contour, $script, $log_data, $allow_terminate, $product_request_form_is_enabled, $goods_kind_id
    )
    {

        $log_data = self::UnsetData($log_data);
        // به ازای هر انبار مبدا - مقصد اگر لازم است، درخواست می زنیم.
        foreach ($result_algorithm["material_list"] as $warehouse_id => $material_list) {

            $warehouse = Warehouse::find($warehouse_id);
            foreach ($material_list as $consume_warehouse_id => $material_list_consume_warehouse) {

                if (count($material_list_consume_warehouse) > 0 && array_sum($material_list_consume_warehouse) > 0) {
                    $other["warehouse_id"] = $warehouse_id;
                    $other["material_list"] = $material_list[$consume_warehouse_id];
                    $other["degree_id_list"] = $result_algorithm["degree_id_list"];
                    $other["allocation_id"] = isset($result_algorithm["allocation_id"]) ? $result_algorithm["allocation_id"] : null;
                    $other["warehouse_material_packing_count"] = [$warehouse_id => $result_algorithm["warehouse_material_packing_count"][$warehouse_id][$consume_warehouse_id]];
                    $other["goods_kind_id_list"] = $result_algorithm["goods_kind_id_list"];
                    $other["machine"] = $machine;
                    $other["user_id"] = $user_id;
                    $other["product_request_form_is_enabled"] = $product_request_form_is_enabled;


                    $contour++;

                    $log_script = AlgorithmFunction::log($script, 0, 41002, $contour, $log_data, $user_id, $machine->id);

                    $other["log_script"] = $log_script;
                    $other["script"] = $script;

                    // اگر درخواستی در انتظار تایید برگ خروج است، ان برگ خروج را کنسل می کند.
                    AlgorithmFunction::rejectWaitingForm($consume_warehouse_id, 40, $script, $machine, $warehouse, $user_id);

                    Product\ProductRequest\ProductRequestForm::newRequest(0, $consume_warehouse_id, 40, 1, $other, null, "");

                    // اضافه کردن مقدار هر ماده اولیه به ازای هر تخصیص در جدول Product_Request_allocation
                    Product\ProductRequest\ProductRequestFromAllocation::AddMaterialAmount(
                        $result_algorithm["log_data"]["allocation_material_list"],
                        $result_algorithm["reserve_allocation_priority"],
                        $log_script,
                        $result_algorithm["material_list"][$warehouse->id][$consume_warehouse_id]
                    );

                } elseif ($allow_terminate) {

                    // موجودی انبارک کافی است، اگر درخواستی رقته آن را خاتمه یافته می کند
                    $old_requests = ProductRequestForm::
                    join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
                    join("products", "products.id", "product_id")->
                    where([
                        "product_request_forms.applicant_type_id" => 40,
                        "product_request_forms.applicant_id" => $consume_warehouse_id,
                        "product_request_forms.warehouse_id" => $warehouse->id
                    ])->
                    whereNotIn("product_request_forms.status_id", [7005002, 7005009])-> // خاتمه یافته , تحویل شده
                    where("goods_kind_id", $goods_kind_id)->
                    select("product_request_forms.*")->
                    groupBy("product_request_forms.id")->
                    get();
                    foreach ($old_requests as $request) {
                        $request->status_id = 7005009; // خاتمه یافته
                        $request->save();
                        event(new ProductRequestFormLogEvent($request, "", null, 7005016, $user_id));
                    }


                    // اگر درخواستی در انتظار تایید برگ خروج است، ان برگ خروج را کنسل می کند.
                    AlgorithmFunction::rejectWaitingForm($consume_warehouse_id, 40, $script, $machine, $warehouse, $user_id);

                }

            }
        }

        return $contour;

    }

    public
    static function HasAnySameRequest(
        $warehouse_id, $main_consume_warehouse_id, $material_ids, $result_algorithm
    )
    {

        $material_ids[] = -1;
        // آیا در نتیجه الگوریتم مواد اولیه ای هست که باید از انبار warehouse_id (مواد اولیه) درخواست دهیم.
        $list = [];
        foreach ($result_algorithm["material_list"][$warehouse_id] as $consume_warehouse_id => $material_list) {

            if ($consume_warehouse_id != $main_consume_warehouse_id) {
                // آنبار مصرف آنهای یکی نیست ولی ماده اولیه یکی است.
                foreach ($material_list as $material_id => $amount) {

                    if (in_array($material_id, $material_ids)) {
                        $list[$warehouse_id . "_" . $consume_warehouse_id . "_" . $material_id] = [
                            "warehouse_id" => $warehouse_id,
                            "consume_warehouse_id" => $consume_warehouse_id,
                            "material_id" => $material_id,
                            "amount" => $material_list[$material_id]
                        ];
                    }
                }
            } else {
                foreach ($material_list as $m_id => $amount) {

                    // انبار مصرف آنها یکی است و ماده اولیه آنها با هم برابر نیست.
                    $list[$warehouse_id . "_" . $consume_warehouse_id . "_" . $m_id] = [
                        "warehouse_id" => $warehouse_id,
                        "consume_warehouse_id" => $consume_warehouse_id,
                        "material_id" => $m_id,
                        "amount" => $amount
                    ];


                }
            }
        }

        return $list;
    }

    public
    static function UnsetData(
        $log_data
    )
    {
        // حذف اطلاعاتی که مورد نیاز نیست
        unset($log_data["ProductAlgorithmResult"]["degree_id_list"]);
        unset($log_data["ProductAlgorithmResult"]["goods_kind_id_list"]);
        unset($log_data["ProductAlgorithmResult"]["log_data"]["machine"]);
        unset($log_data["ProductAlgorithmResult"]["log_data"]["send_product_request_form_by_robot"]);
        unset($log_data["ProductAlgorithmResult"]["log_data"]["product_request_form_by_robot_is_enabled"]);
        unset($log_data["ProductAlgorithmResult"]["log_data"]["allocation_bands"]);
        unset($log_data["ProductAlgorithmResult"]["reserve_allocation_priority"]);

        unset($log_data["SamplingAlgorithmResult"]["degree_id_list"]);
        unset($log_data["SamplingAlgorithmResult"]["goods_kind_id_list"]);
        unset($log_data["SamplingAlgorithmResult"]["log_data"]["machine"]);
        unset($log_data["SamplingAlgorithmResult"]["log_data"]["send_product_request_form_by_robot"]);
        unset($log_data["SamplingAlgorithmResult"]["log_data"]["product_request_form_by_robot_is_enabled"]);
        unset($log_data["SamplingAlgorithmResult"]["log_data"]["allocation_bands"]);
        unset($log_data["SamplingAlgorithmResult"]["reserve_allocation_priority"]);

        return $log_data;
    }

    public static function PackingCount($allocation_id, Product $material)
    {

        $current_form_to_input = CurrentMachineInput::where("allocation_id", $allocation_id)->
        where("material_id", $material->id)->
        whereNotNull("input_line_code_from")->
        first();
        // اگر ورودی ماشین به صورت از تایی باشد، تعداد بسته بندی می شود، اختلات از تا برای ماده اولیه
        if ($current_form_to_input) {
            $current_form_to_inputs = CurrentMachineInput::where("allocation_id", $allocation_id)->
            where("material_id", $material->id)->
            whereNotNull("input_line_code_from")->
            get();
            $material_packing_count = 0;
            foreach ($current_form_to_inputs as $current_input_item) {
                $material_packing_count +=
                    $current_input_item->input_line_code_to -
                    $current_input_item->input_line_code_from + 1;
            }
        } else {
            // تعداد رکورد های ورودی ماشین، به عنوان تعداد بسته بندی مشخص می شود.
            $material_packing_count = CurrentMachineInput::where("allocation_id", $allocation_id)->
            where("material_id", $material->id)->
            count();
        }

        return $material_packing_count;
    }
}
